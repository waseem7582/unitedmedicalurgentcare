<?php

namespace App\Http\Controllers\Hospital;

use Exception;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\TemporaryData;
use App\Constants\GlobalConst;
use App\Models\Hospital\Doctor;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Facades\Auth;
use App\Models\Admin\BookingTempData;
use Illuminate\Http\RedirectResponse;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\DoctorBooking;
use App\Models\Admin\CryptoTransaction;
use App\Models\Hospital\HospitalWallet;
use App\Models\Admin\TransactionSetting;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\DoctorHasSchedule;
use App\Models\Admin\PaymentGatewayCurrency;
use Illuminate\Support\Facades\Notification;
use App\Providers\Admin\BasicSettingsProvider;
use App\Traits\PaymentGateway\PaystackGateway;
use App\Http\Helpers\PaymentGateway as PaymentGatewayHelper;


use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;
use App\Traits\PaymentGateway\Authorize;

class DoctorBookingController extends Controller
{
    use PaystackGateway,Authorize;
    /**
     * Method for show doctor booking page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function getService(BasicSettingsProvider $basic_settings, Request $request, $slug)
    {
        $page_title         = __('Make Appointment');
        $doctor             = Doctor::with('schedules','booking')->where('slug', $slug)->first();

        return view('frontend.pages.doctor-booking.index', compact(
            'page_title',
            'doctor',
        ));
    }

    /**
     * Method for store appointment booking information and passed it to preview page
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {

        if (auth()->check() == false) return back()->with(['error' => [__('Please Login First.')]]);

        $charge_data            = TransactionSetting::where('slug', 'doctor')->where('status', 1)->first();

        $validator              = Validator::make($request->all(), [
            'name'               => 'required|string',
            'doctor_id'          => 'nullable',
            'schedule_id'        => 'nullable',
            'gender'             => 'required|string',
            'age_type'           => "required|string",
            'age'                => "required|string",
           'number'              => "required|regex:/^\+?[0-9]+$/",
            'email'              => "required|string",
            'date'               => 'required|date_format:Y-m-d|after_or_equal:today',
            'visit_type'         => 'required|string',
            'message'            => "nullable"
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }
        $validated                  = $validator->validate();

        $schedule = DoctorHasSchedule::where('id', $validated['schedule_id'])->first();

        if (!$schedule) {
            return back()->with(['error' => [__('Schedule Not Found!')]]);
        }

        $doctor = Doctor::where('id', $validated['doctor_id'])->first();

        if (!$doctor) return back()->with(['error' => [__('Doctor Not Found!')]]);

        $price                      = floatval($doctor->fees);
        $fixed_charge               = floatval($charge_data->fixed_charge);
        $percent_charge             = floatval($charge_data->percent_charge);
        $total_percent_charge       = ($percent_charge / 100) * $price;
        $total_charge               = $fixed_charge + $total_percent_charge;
        $total_price                = $price + $total_charge;
        $validated['total_charge']  = $total_charge;
        $validated['price']         = $price;
        $validated['payable_price'] = $total_price;
        $validated['hospital_id']   = $doctor->hospital_id;

        $already_appointed = DoctorBooking::where('doctor_id', $doctor->id)->where('schedule_id', $validated['schedule_id'])->where('date', $validated['date'])->count();

        if ($already_appointed >= $schedule->max_client) {
            return back()->with(['error' => ['Booking Limit is over!']]);
        }

        $validated['slug']          = Str::slug($validated['name']);
        $validated['uuid']          = Str::uuid();
        $validated['data']          = $validated;
        $validated['user_id']       = auth()->user()->id;
        $validated['doctor_id']     = $validated['doctor_id'];
        $validated['schedule_id']   = $validated['schedule_id'];

        try {
            $booking =  BookingTempData::create($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('frontend.doctor.booking.preview', $booking->data->uuid);
    }

    /**
     * Method for show the preview page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function preview(Request $request, $uuid)
    {
        $page_title         = __("Appointment Preview");
        $booking            = BookingTempData::with(['doctor', 'schedule'])->where('uuid', $uuid)->first();

        if (!$booking) {
            return redirect()->route('frontend.find.doctor')->with(['error' => [__('Booking not found')]]);
        }

        $payment_method = PaymentGateway::where('type', 'AUTOMATIC')->with('currencies')->get();

        return view('frontend.pages.doctor-booking.preview', compact(
            'page_title',
            'booking',
            'payment_method'
        ));
    }


    /**
     * Method for confirm the booking
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function confirm(Request $request, PaymentGatewayCurrency $gateway_currency, $uuid)
    {
        $booking        = BookingTempData::with(['payment_gateway', 'doctor', 'schedule', 'user'])->where('uuid', $uuid)->first();
        $otp_exp_sec    = GlobalConst::BOOKING_EXP_SEC;

        if ($booking->created_at->addSeconds($otp_exp_sec) < now()) {
            $booking->delete();
            return redirect()->route('frontend.find.doctor')->with(['error' => [__('Booking Time Out!')]]);
        }

        $validator  = Validator::make($request->all(), [
            'payment_method'    => 'required',
        ]);

        $validated       = $validator->validate();
        $from_time       = $booking->schedule->from_time ?? '';

        $to_time         = $booking->schedule->to_time ?? '';
        $user            = auth()->user();
        $basic_setting   = BasicSettings::first();

        $hospital_wallet  = HospitalWallet::where('hospital_id', $booking->data->hospital_id)->first();
        $wallet_balance   = $hospital_wallet->balance ?? '';

        if ($validated['payment_method'] == GlobalConst::CASH_PAYMENT) {

            try {
                $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);
                DoctorBooking::create([
                    'trx_id'            => $trx_id,
                    'doctor_id'         => $booking->doctor_id,
                    'schedule_id'       => $booking->schedule_id,
                    'hospital_id'       => $booking->data->hospital_id,
                    'booking_data'      => ['data' => $booking->data],
                    'payment_method'    => GlobalConst::CASH_PAYMENT,
                    'date'              => $booking->data->date,
                    'slug'              => $booking->slug,
                    'uuid'              => $booking->uuid,
                    'type'              => GlobalConst::CASH_PAYMENT,
                    'user_id'           => $user->id,
                    'total_charge'      => $booking->data->total_charge,
                    'price'             => $booking->data->price,
                    'payable_price'     => $booking->data->payable_price,
                    'remark'            => GlobalConst::CASH_PAYMENT,
                    'status'            => PaymentGatewayConst::STATUS_PENDING,
                ]);
                UserNotification::create([
                    'user_id'        => auth()->user()->id,
                    'message'          => [
                        'title'         => "Your Booking",
                        'doctor'        => $booking->doctor->name,
                        'date'          => $booking->data->date,
                        'from_time'     => $from_time,
                        'to_time'       => $to_time,
                        'success'       => "Successfully Booked."
                    ],
                ]);

                try {
                    $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);
                    if ($basic_setting->email_notification == true) {
                        Notification::route("mail", $user->email)->notify(new EmailNotification($user, $booking, $trx_id));
                    }
                } catch (Exception $e) {
                }
            } catch (Exception $e) {

                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }
            return redirect()->route('user.my.booking.index')->with(['success' => [__('Congratulations! Doctor Booking Confirmed Successfully.')]]);
        } else {

            $validated = Validator::make($request->all(), [
                'amount'            => 'required|numeric|gt:0',
                'gateway_currency'  => 'required|string|exists:' . $gateway_currency->getTable() . ',alias',
            ])->validate();

            $request->merge([
                'currency' => $validated['gateway_currency'],
                'booking_data' => $booking,
            ]);

            try {
                $instance = PaymentGatewayHelper::init($request->all())->type(PaymentGatewayConst::PAYMENTMETHOD)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->gateway()->render();
            } catch (Exception $e) {
                return back()->with(['error' => [$e->getMessage()]]);
            }

            return $instance;
        }
    }

    public function success(Request $request, $gateway)
    {
        try {

            $token      = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data  = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();

            if (Transaction::where('callback_ref', $token)->exists()) {
                if (!$temp_data) return redirect()->route('user.my.booking.index')->with(['success' => ['Transaction request sended successfully!']]);;
            } else {
                if (!$temp_data) return redirect()->route('frontend.find.doctor')->with(['error' => ['Transaction failed. Record didn\'t saved properly. Please try again.']]);
            }

            $update_temp_data                   = json_decode(json_encode($temp_data->data), true);
            $update_temp_data['callback_data']  = $request->all();

            $temp_data->update([
                'data'  => $update_temp_data,
            ]);

            $temp_data           = $temp_data->toArray();
            $instance            = PaymentGatewayHelper::init($temp_data)->type(PaymentGatewayConst::PAYMENTMETHOD)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->responseReceive();
            if ($instance instanceof RedirectResponse) return $instance;

            $uuid                = $temp_data['data']->booking_data->uuid;
            $data                = BookingTempData::with(['payment_gateway', 'doctor', 'schedule', 'user'])->where('uuid', $uuid)->first();

            $from_time           = $data->schedule->from_time ?? '';

            $to_time             = $data->schedule->to_time ?? '';

            $basic_setting       = BasicSettings::first();
            $user                = auth()->user();

            UserNotification::create([
                'user_id'  => auth()->user()->id,
                'message'  => [
                    'title' => "Your Booking",
                    'doctor'        => $data->doctor->name,
                    'date'          => $data->date,
                    'from_time'     => $from_time,
                    'to_time'       => $to_time,
                    'success'       => "Successfully Booked."
                ],
            ]);

            if ($basic_setting->email_notification == true) {

                try {
                    $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);
                    Notification::route("mail", $user->email)->notify(new EmailNotification($user, $data, $trx_id));
                } catch (Exception $e) {
                }
            }
            $data->delete();
        } catch (Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }

        return redirect()->route("user.my.booking.index")->with(['success' => [__('Congratulations! Doctor Booking Confirmed Successfully.')]]);
    }

    public function cancel(Request $request, $gateway)
    {
        $token         = PaymentGatewayHelper::getToken($request->all(), $gateway);
        if ($temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first()) {
            $temp_data->delete();
        }

        return redirect()->route('frontend.find.doctor');
    }

    public function postSuccess(Request $request, $gateway)
    {
        try {
            $token     = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        } catch (Exception $e) {
            return redirect()->route('frontend.index');
        }

        return $this->success($request, $gateway);
    }

    public function postCancel(Request $request, $gateway)
    {
        try {
            $token     = PaymentGatewayHelper::getToken($request->all(), $gateway);
            $temp_data = TemporaryData::where("type", PaymentGatewayConst::PAYMENTMETHOD)->where("identifier", $token)->first();
            Auth::guard($temp_data->data->creator_guard)->loginUsingId($temp_data->data->creator_id);
        } catch (Exception $e) {
            return redirect()->route('frontend.index');
        }

        return $this->cancel($request, $gateway);
    }

    public function callback(Request $request, $gateway)
    {

        $callback_token = $request->get('token');
        $callback_data  = $request->all();

        try {
            PaymentGatewayHelper::init([])->type(PaymentGatewayConst::PAYMENTMETHOD)->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->handleCallback($callback_token, $callback_data, $gateway);
        } catch (Exception $e) {
            // handle Error
            logger($e);
        }
    }



    public function cryptoPaymentAddress(Request $request, $trx_id)
    {

        $page_title  = "Crypto Payment Address";
        $transaction = Transaction::where('trx_id', $trx_id)->firstOrFail();

        if ($transaction->gateway_currency->gateway->isCrypto() && $transaction->details?->payment_info?->receiver_address ?? false) {
            return view('user.sections.add-money.payment.crypto.address', compact(
                'transaction',
                'page_title',
            ));
        }

        return abort(404);
    }

    public function cryptoPaymentConfirm(Request $request, $trx_id)
    {
        $transaction       = Transaction::where('trx_id', $trx_id)->where('status', PaymentGatewayConst::STATUS_WAITING)->firstOrFail();

        $dy_input_fields   = $transaction->details->payment_info->requirements ?? [];
        $validation_rules   = $this->generateValidationRules($dy_input_fields);

        $validated = [];
        if (count($validation_rules) > 0) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        }

        if (!isset($validated['txn_hash'])) return back()->with(['error' => ['Transaction hash is required for verify']]);

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

        if (!$crypto_transaction) return back()->with(['error' => ['Transaction hash is not valid! Please input a valid hash']]);

        if ($crypto_transaction->amount >= $transaction->total_payable == false) {
            if (!$crypto_transaction) return back()->with(['error' => ['Insufficient amount added. Please contact with system administrator']]);
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
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Payment Confirmation Success!']]);
    }

    public function redirectUsingHTMLForm(Request $request, $gateway)
    {
        $temp_data          = TemporaryData::where('identifier', $request->token)->first();
        if (!$temp_data || $temp_data->data->action_type != PaymentGatewayConst::REDIRECT_USING_HTML_FORM) return back()->with(['error' => ['Request token is invalid!']]);
        $redirect_form_data = $temp_data->data->redirect_form_data;
        $action_url         = $temp_data->data->action_url;
        $form_method        = $temp_data->data->form_method;

        return view('payment-gateway.redirect-form', compact('redirect_form_data', 'action_url', 'form_method'));
    }

    /**
     * Redirect Users for collecting payment via Button Pay (JS Checkout)
     */
    public function redirectBtnPay(Request $request, $gateway)
    {
        try {
            return PaymentGatewayHelper::init([])->setProjectCurrency(PaymentGatewayConst::PROJECT_CURRENCY_SINGLE)->handleBtnPay($gateway, $request->all());
        } catch (Exception $e) {
            return redirect()->route('user.dashboard')->with(['error' => [$e->getMessage()]]);
        }
    }

    /**
     * Method for paystack pay callback
     */
    public function paystackPayCallBack(Request $request)
    {
        $instance = $this->paystackSuccess($request->all());
        return redirect()->route("user.my.booking.index")->with(['success' => [__('Congratulations! Doctor Booking Confirmed Successfully.')]]);
    }

             /**
     * Method for view authorize card view page
     * @param Illuminate\Http\Request $request,$identifier
     */
    public function authorizeCardInfo(Request $request,$identifier){
        $page_title         = __("Authorize card Information");
        $temp_data          = TemporaryData::where('identifier',$identifier)->first();

        return view('frontend.parlour-booking.automatic.authorize',compact(
            'page_title',
            'temp_data'
        ));
    }
  /**
     * Method function authorize payment submit
     * @param Illuminate\Http\Request $request, $identifier
     */
    public function authorizePaymentSubmit(Request $request,$identifier){

        $temp_data          = TemporaryData::where('identifier',$identifier)->first();
        if(!$temp_data) return back()->with(['error' => ['Sorry ! Data not found.']]);

        $validator          = Validator::make($request->all(),[
            'card_number'   => 'required',
            'date'          => 'required',
            'code'          => 'required'
        ]);

        if($validator->fails()) return back()->withErrors($validator)->withInput($request->all());
        $validated          = $validator->validate();


        $request->merge([
            'token' => $identifier
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
        $amount = $temp_data->data->amount->total_payable_amount;


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
        $order->setDescription("Transfer Money");

        // Set the customer's Bill To address
        $customerAddress = new AnetAPI\CustomerAddressType();
        $customerAddress->setFirstName(auth()->user()->firstname ?? '');
        $customerAddress->setLastName(auth()->user()->lastname ?? '');
        $customerAddress->setCompany($basic_settings->site_name ?? '');
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

        if($gateway_credentials->mode == GlobalConst::ENV_SANDBOX){
            $environment = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
            
        }else{
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
                }else {
                    return back()->with(['error' => ['Transaction Failed']]);
                    if ($tresponse->getErrors() != null) {
                        return back()->with(['error' => [$tresponse->getErrors()[0]->getErrorText()]]);
                    }
                }
            }else {
                return back()->with(['error' => ['Transaction Failed']]);
                $tresponse = $response->getTransactionResponse();

                if ($tresponse != null && $tresponse->getErrors() != null) {
                    return back()->with(['error' => [$tresponse->getErrors()[0]->getErrorText()]]);
                } else {
                    return back()->with(['error' => [$response->getMessages()->getMessage()[0]->getText()]]);
                }
            }
        }else {
            return redirect()->back()->with(['error' => ['No response returned']]);
        }


    }

}
