<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Hospital\Doctor;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\BookingTempData;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\DoctorBooking;
use App\Models\Admin\CryptoTransaction;
use App\Models\Hospital\HospitalWallet;
use App\Models\Admin\TransactionSetting;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\DoctorHasSchedule;
use App\Models\Admin\PaymentGatewayCurrency;
use App\Traits\Hospital\PaymentGateway\Gpay;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;
use App\Models\Admin\PaymentGateway;
use App\Models\Hospital\HospitalOfflineWallet;
use Illuminate\Support\Facades\Notification;
use App\Notifications\EmailNotification;
use Srmklive\PayPal\Facades\PayPal;
use App\Traits\PaymentGateway\PaystackGateway;
use net\authorize\api\controller as AnetController;
use App\Traits\PaymentGateway\Authorize;
use net\authorize\api\contract\v1 as AnetAPI;

class DoctorBookingController extends Controller
{
    use PaystackGateway, Authorize;
    public function checkout(Request $request)
    {
        $charge_data = TransactionSetting::where('slug', 'doctor')->where('status', 1)->first();
        $payment_gateway  = PaymentGateway::with('currencies')->where('status', true)->get();

        $validator = Validator::make($request->all(), [
            'name'         => 'required|string',
            'doctor_id'    => 'nullable|integer',
            'schedule_id'  => 'nullable|integer',
            'gender'       => 'required|string',
            'age_type'     => 'required|string',
            'age'          => 'required|string',
             'number'              => "required|regex:/^\+?[0-9]+$/",
            'email'        => 'required|string',
            'date'         => 'required|date_format:Y-m-d|after_or_equal:today',
            'visit_type'   => 'required|string',
            'message'      => 'nullable'
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), null, 422);
        }
        $already_appointed = DoctorBooking::where('doctor_id', $request->doctor_id)->where('schedule_id', $request->schedule_id)->where('date', $request->date)->count();


        $schedule = DoctorHasSchedule::where('id', $request->schedule_id)->first();
        if ($already_appointed > 0) {
            return Response::error([__('already booked on this schedule!')]);
        }

        $validated = $validator->validated();

        $schedule = DoctorHasSchedule::find($validated['schedule_id']);
        if (!$schedule) {
            return Response::error([__('Schedule Not Found!')]);
        }

        $doctor = Doctor::find($validated['doctor_id']);
        if (!$doctor) {
            return Response::error([__('Doctor Not Found!')]);
        }

        $price = floatval($doctor->fees);
        $fixed_charge = floatval($charge_data->fixed_charge ?? 0);
        $percent_charge = floatval($charge_data->percent_charge ?? 0);
        $total_percent_charge = ($percent_charge / 100) * $price;
        $total_charge = $fixed_charge + $total_percent_charge;
        $total_price = $price + $total_charge;
        $validated['total_charge']  = $total_charge;
        $validated['price']         = $price;
        $validated['payable_price'] = $total_price;
        $validated['hospital_id']   = $doctor->hospital_id;

        $already_appointed = DoctorBooking::where('doctor_id', $doctor->id)
            ->where('schedule_id', $validated['schedule_id'])
            ->where('date', $validated['date'])
            ->count();

        if ($already_appointed >= $schedule->max_client) {
            return Response::warning([__('Booking Limit is over!')]);
        }

        $validated['slug']         = Str::slug($validated['name']);
        $validated['uuid']         = Str::uuid();
        $validated['data']         = $validated;
        $validated['user_id']      = auth()->id();
        $validated['doctor_id']    = $validated['doctor_id'];
        $validated['schedule_id']  = $validated['schedule_id'];

        try {
            $booking = BookingTempData::create($validated);
        } catch (Exception $e) {
            return Response::error([__('Something went wrong! Please try again.')]);
        }


        return Response::success([__('Booking Created Successfully.')], [
            'default_image' => files_asset_path_basename("default"),
            "image_path"    => "backend/images/payment-gateways",
            "base_ur"       => url('/'),
            'uuid' => $booking->uuid,
            'payment_gateway' => $payment_gateway,
            'booking' => $booking,
        ]);
    }

    public function cashPayment(Request $request, $uuid)
    {
        $booking = BookingTempData::with(['payment_gateway', 'doctor', 'schedule', 'user'])
            ->where('uuid', $uuid)
            ->first();

        if (!$booking) {
            return Response::error(['Booking not found!'], null, 404);
        }

        $otp_exp_sec = GlobalConst::BOOKING_EXP_SEC;

        if ($booking->created_at->addSeconds($otp_exp_sec) < now()) {
            $booking->delete();
            return Response::error(['Booking Time Out!']);
        }

        $validator = Validator::make($request->all(), [
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }

        $validated = $validator->validated();
        $from_time = $booking->schedule->from_time ?? '';
        $to_time   = $booking->schedule->to_time ?? '';
        $user      = auth()->user();

        $basic_setting = BasicSettings::first();
        $hospital_wallet = HospitalOfflineWallet::where('hospital_id', $booking->data->hospital_id)->first();
        $wallet_balance = $hospital_wallet->balance ?? 0;

        if ($validated['payment_method'] == GlobalConst::CASH_PAYMENT) {
            try {
                $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);

                $createdBooking = DoctorBooking::create([
                    'trx_id'            => $trx_id,
                    'doctor_id'         => $booking->doctor_id,
                    'schedule_id'       => $booking->schedule_id,
                    'hospital_id'       => $booking->data->hospital_id,
                    'booking_data'      => ['data' => $booking->data],
                    'payment_method'    => GlobalConst::CASH_PAYMENT,
                    'date'              => $booking->data->date,
                    'slug'              => $booking->slug,
                    'uuid'              => str::uuid(),
                    'type'              => GlobalConst::CASH_PAYMENT,
                    'user_id'           => $user->id,
                    'total_charge'      => $booking->data->total_charge,
                    'price'             => $booking->data->price,
                    'payable_price'     => $booking->data->payable_price,
                    'remark'            => GlobalConst::CASH_PAYMENT,
                    'status'            => PaymentGatewayConst::STATUS_PENDING,
                ]);

                UserNotification::create([
                    'user_id' => $user->id,
                    'message' => [
                        'title'     => "Your Booking",
                        'doctor'    => $booking->doctor->name,
                        'date'      => $booking->data->date,
                        'from_time' => $from_time,
                        'to_time'   => $to_time,
                        'success'   => "Successfully Booked."
                    ],
                ]);

                try {
                    if (!empty($user->email) && $basic_setting->email_notification) {
                        Notification::route("mail", $user->email)
                            ->notify(new EmailNotification($user, $booking, $trx_id));
                    }
                } catch (Exception $e) {
                }

                return Response::success(['Congratulations! Doctor Booking Confirmed Successfully.'], [
                    'booking' => $createdBooking,
                ]);
            } catch (Exception $e) {
                return Response::error(['Something went wrong! Please try again.']);
            }
        }

        return Response::error(['Invalid payment method.']);
    }


    public function automaticSubmit(Request $request, $slug)
    {
        $data      = BookingTempData::with(['payment_gateway', 'doctor', 'schedule', 'user'])->where('uuid', $slug)->first();

        $request->merge([
            'booking_data' => $data,
        ]);

        try {

            $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::PAYMENTMETHOD)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->gateway()->api()->render();
        } catch (Exception $e) {

            return Response::error([$e->getMessage()], [], 500);
        }

        if ($request->currency == "payment-method-authorize-usd-automatic") {

            return Response::success([__('Payment gateway response successful')], [
                'identifier'        => $instance['identifier'],
                'gateway_alias'        => $instance['gateway_alias'],
                'action_type'           => $instance['type']  ?? false,
                'address_info'          => $instance['address_info'] ?? [],
            ], 200);
        } else {

            return Response::success([__('Payment gateway response successful')], [
                'redirect_url'          => $instance['redirect_url'],
                'redirect_links'        => $instance['redirect_links'],
                'action_type'           => $instance['type']  ?? false,
                'address_info'          => $instance['address_info'] ?? [],
            ], 200);
        }
    }

    public function success(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();

            if (!$temp_data) {
                if (Transaction::where('callback_ref', $token)->exists()) {
                    return Response::success([__('Transaction request sended successfully!')], [], 400);
                } else {
                    return Response::error([__("Transaction failed. Record didn't saved properly. Please try again")], [], 400);
                }
            }

            $update_temp_data = json_decode(json_encode($temp_data->data), true);

            $update_temp_data['callback_data']  = $request->all();
            $temp_data->update([
                'data'  => $update_temp_data,
            ]);
            $temp_data = $temp_data->toArray();

            $instance = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::PAYMENTMETHOD)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->responseReceive();
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }
        return Response::success([__('Successfully doctor booked')], [], 200);
    }

    public function cancel(Request $request, $gateway)
    {
        $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
        $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();
        try {
            if ($temp_data != null) {
                $temp_data->delete();
            }
        } catch (Exception $e) {
            // Handel error
        }
        return Response::success([__('Payment process cancel successfully!')], [], 200);
    }

    public function postSuccess(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();
            if ($temp_data && $temp_data->data->creator_guard != 'api') {
                Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
            }
        } catch (Exception $e) {
            return Response::error([$e->getMessage()]);
        }

        return $this->success($request, $gateway);
    }

    public function postCancel(Request $request, $gateway)
    {
        try {
            $token = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();
            if ($temp_data && $temp_data->data->creator_guard != 'api') {
                Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
            }
        } catch (Exception $e) {
            return Response::error([$e->getMessage()]);
        }

        return $this->cancel($request, $gateway);
    }




    public function gatewayAdditionalFields(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency'          => "required|string|exists:payment_gateway_currencies,alias",
        ]);
        if ($validator->fails()) return Response::error($validator->errors()->all(), [], 400);
        $validated = $validator->validate();

        $gateway_currency = PaymentGatewayCurrency::where("alias", $validated['currency'])->first();

        $gateway = $gateway_currency->gateway;

        $data['available'] = false;
        $data['additional_fields']  = [];
        if (Gpay::isGpay($gateway)) {
            $gpay_bank_list = Gpay::getBankList();
            if ($gpay_bank_list == false) return Response::error([__('Gpay bank list server response failed! Please try again')], [], 500);
            $data['available']  = true;

            $gpay_bank_list_array = json_decode(json_encode($gpay_bank_list), true);

            $gpay_bank_list_array = array_map(function ($array) {

                $data['name']       = $array['short_name_by_gpay'];
                $data['value']      = $array['gpay_bank_code'];

                return $data;
            }, $gpay_bank_list_array);

            $data['additional_fields'][] = [
                'type'      => "select",
                'label'     => "Select Bank",
                'title'     => "Select Bank",
                'name'      => "bank",
                'values'    => $gpay_bank_list_array,
            ];
        }

        return Response::success([__('Request response fetch successfully!')], $data, 200);
    }

    public function cryptoPaymentConfirm(Request $request, $trx_id)
    {
        $transaction = Transaction::where('trx_id', $trx_id)->where('status', PaymentGatewayConst::STATUSWAITING)->firstOrFail();

        $dy_input_fields = $transaction->details->payment_info->requirements ?? [];
        $validation_rules = $this->generateValidationRules($dy_input_fields);

        $validated = [];
        if (count($validation_rules) > 0) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        }

        if (!isset($validated['txn_hash'])) return Response::error([__('Transaction hash is required for verify')]);

        $receiver_address = $transaction->details->payment_info->receiver_address ?? "";

        // check hash is valid or not
        $crypto_transaction = CryptoTransaction::where('txn_hash', $validated['txn_hash'])
            ->where('receiver_address', $receiver_address)
            ->where('asset', $transaction->gateway_currency->currency_code)
            ->where(function ($query) {
                return $query->where('transaction_type', "Native")
                    ->orWhere('transaction_type', "native");
            })
            ->where('status', PaymentGatewayConst::NOT_USED)
            ->first();

        if (!$crypto_transaction) return Response::error([__('Transaction hash is not valid! Please input a valid hash')], [], 404);

        if ($crypto_transaction->amount >= $transaction->total_payable == false) {
            if (!$crypto_transaction) Response::error([__('Insufficient amount added. Please contact with system administrator')], [], 400);
        }

        DB::beginTransaction();
        try {

            // Update user wallet balance
            DB::table($transaction->creator_wallet->getTable())
                ->where('id', $transaction->creator_wallet->id)
                ->increment('balance', $transaction->receive_amount);

            // update crypto transaction as used
            DB::table($crypto_transaction->getTable())->where('id', $crypto_transaction->id)->update([
                'status'        => PaymentGatewayConst::USED,
            ]);

            // update transaction status
            $transaction_details = json_decode(json_encode($transaction->details), true);
            $transaction_details['payment_info']['txn_hash'] = $validated['txn_hash'];

            DB::table($transaction->getTable())->where('id', $transaction->id)->update([
                'details'       => json_encode($transaction_details),
                'status'        => PaymentGatewayConst::STATUS_SUCCESS,
            ]);

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('Payment Confirmation Success!')], [], 200);
    }

    /**
     * Redirect Users for collecting payment via Button Pay (JS Checkout)
     */
    public function redirectBtnPay(Request $request, $gateway)
    {
        try {
            return PaymentGatewayHelper::init([])->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->handleBtnPay($gateway, $request->all());
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }
    }

    public function manualInputFields(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'alias'         => "required|string|exists:payment_gateway_currencies",
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }

        $validated = $validator->validate();
        $gateway_currency = PaymentGatewayCurrency::where("alias", $validated['alias'])->first();

        $gateway = $gateway_currency->gateway;

        if (!$gateway->isManual()) return Response::error([__("Can't get fields. Requested gateway is automatic")], [], 400);

        if (!$gateway->input_fields || !is_array($gateway->input_fields)) return Response::error([__("This payment gateway is under constructions. Please try with another payment gateway")], [], 503);

        try {
            $input_fields = json_decode(json_encode($gateway->input_fields), true);
            $input_fields = array_reverse($input_fields);
        } catch (Exception $e) {
            return Response::error([__("Something went wrong! Please try again")], [], 500);
        }

        return Response::success([__('Payment gateway input fields fetch successfully!')], [
            'gateway'           => [
                'desc'          => $gateway->desc
            ],
            'input_fields'      => $input_fields,
            'currency'          => $gateway_currency->only(['alias']),
        ], 200);
    }
    /**
     * Method for paystack pay callback
     */
    public function paystackPayCallBack(Request $request)
    {
        $instance = $this->paystackSuccess($request->all());
        return Response::success(["Payment successful, please go back your app"], [], 200);
    }

    /**
     * Method function authorize payment submit
     * @param Illuminate\Http\Request $request
     */
    public function authorizePaymentSubmit(Request $request)
    {
        $validator          = Validator::make($request->all(), [
            'identifier'    => 'required',
            'card_number'   => 'required',
            'date'          => 'required',
            'code'          => 'required'
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 400);
        }
        $validated          = $validator->validate();

        $temp_data          = TemporaryData::where('identifier', $validated['identifier'])->first();
        if (!$temp_data) {
            return Response::error([__('Sorry ! Data not found.')], [], 400);
        }

        $request->merge([
            'token' => $validated['identifier']
        ]);

        $request_data = $request->all();


        $gateway_credentials          = $this->authorizeCredentials($temp_data);
        $basic_settings               = BasicSettings::first();

        $validated['card_number']     = str_replace(' ', '', $validated['card_number']);

        $convert_date   = explode('/', $validated['date']);
        $month          = $convert_date[1];
        $year           = $convert_date[0];

        $current_year   = substr(date('Y'), 0, 2);
        $full_year      = $current_year . $year;

        $validated['date'] = $full_year . '-' . $month;
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($gateway_credentials->app_login_id);
        $merchantAuthentication->setTransactionKey($gateway_credentials->transaction_key);
        $amount = $temp_data->data->amount->payable_amount;


        // Set the transaction's refId
        $refId = 'ref' . time();

        // Create the payment data for a credit card
        $creditCard = new AnetAPI\CreditCardType();
        $creditCard->setCardNumber($validated['card_number']);
        $creditCard->setExpirationDate($validated['date']);
        $creditCard->setCardCode($validated['code']);


        // Add the payment data to a paymentType object
        $paymentOne = new AnetAPI\PaymentType();
        $paymentOne->setCreditCard($creditCard);

        $generate_invoice_number        = generate_random_string_number(10) . time();

        // Create order information
        $order = new AnetAPI\OrderType();
        $order->setInvoiceNumber($generate_invoice_number);
        $order->setDescription("Add Money");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName(auth()->user()->firstname);
        $customerAddress->setLastName(auth()->user()->lastname);
        $customerAddress->setCompany($basic_settings->site_name);
        $customerAddress->setAddress(auth()->user()->address->address ?? '');
        $customerAddress->setCity(auth()->user()->address->city ?? '');
        $customerAddress->setState(auth()->user()->address->state ?? '');
        $customerAddress->setZip(auth()->user()->address->zip ?? '');
        $customerAddress->setCountry(auth()->user()->address->country ?? '');

        $make_customer_id       = auth()->user()->id . $gateway_credentials->code;
        // Set the customer's identifying information
        $customerData = new AnetAPI\CustomerDataType();
        $customerData->setType("individual");
        $customerData->setId($make_customer_id);
        $customerData->setEmail(auth()->user()->email);

        // Add values for transaction settings
        $duplicateWindowSetting = new AnetAPI\SettingType();
        $duplicateWindowSetting->setSettingName("duplicateWindow");
        $duplicateWindowSetting->setSettingValue("60");

        // Create a TransactionRequestType object and add the previous objects to it
        $transactionRequestType = new AnetAPI\TransactionRequestType();
        $transactionRequestType->setTransactionType("authCaptureTransaction");
        $transactionRequestType->setAmount($amount);
        $transactionRequestType->setOrder($order);
        $transactionRequestType->setPayment($paymentOne);
        $transactionRequestType->setBillTo($customerAddress);
        $transactionRequestType->setCustomer($customerData);
        $transactionRequestType->addToTransactionSettings($duplicateWindowSetting);

        // Assemble the complete transaction request
        $request = new AnetAPI\CreateTransactionRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setRefId($refId);
        $request->setTransactionRequest($transactionRequestType);


        // Create the controller and get the response
        $controller = new AnetController\CreateTransactionController($request);

        if ($gateway_credentials->mode == GlobalConst::ENV_SANDBOX) {
            $environment = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
        } else {
            $environment = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
        }
        $response   = $controller->executeWithApiResponse($environment);
        if ($response != null) {
            // Check to see if the API request was successfully received and acted upon
            if ($response->getMessages()->getResultCode() == "Ok") {
                // Since the API request was successful, look for a transaction response
                // and parse it to display the results of authorizing the card
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getMessages() != null) {

                    $request_data = new Request($request_data);
                    return $this->success($request_data, PaymentGatewayConst::AUTHORIZE);
                } else {
                    return Response::error([__('Transaction Failed')], [], 400);
                    if ($tresponse->getErrors() != null) {
                        return Response::error([__($tresponse->getErrors()[0]->getErrorText())], [], 400);
                    }
                }
            } else {
                return Response::error([__('Transaction Failed')], [], 400);

                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return Response::error([__($tresponse->getErrors()[0]->getErrorText())], [], 400);
                } else {
                    return Response::error([__($response->getMessages()->getMessage()[0]->getText())], [], 400);
                }
            }
        } else {
            return Response::error([__('No response returned')], [], 400);
        }
    }
}
