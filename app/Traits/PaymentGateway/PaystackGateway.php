<?php

namespace App\Traits\PaymentGateway;

use App\Constants\GlobalConst;
use Exception;
use App\Models\TemporaryData;
use App\Http\Helpers\Response;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\Doctor;
use App\Models\Hospital\DoctorHasSchedule;
use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaystackNotification;



trait PaystackGateway
{

    public function paystackInit($output = null)
    {

        $gateway = new \stdClass();

        foreach ($output['gateway']->credentials as $credential) {
            if ($credential->name === 'secret-key') {
                $gateway->secret_key = $credential->value;
            } elseif ($credential->name === 'email') {
                $gateway->email = $credential->value;
            }
        }

        $amount = get_amount($output['amount']->total_payable_amount, null, 2) * 100;
        $temp_record_token = generate_unique_string('temporary_datas', 'identifier', 60);

        $junkData       = $this->paystackJunkInsert($output, $temp_record_token);
        $url = "https://api.paystack.co/transaction/initialize";
        if (get_auth_guard() == 'api') {

            $fields             = [
                'email'         => auth()->user()->email,
                'amount'        => $amount,
                'currency'      => $output['currency']->currency_code,
                'callback_url'  => route('api.paystack.pay.callback') . '?output=' . $junkData->identifier
            ];
        } else {
            $fields             = [
                'email'         => auth()->user()->email,
                'amount'        => $amount,
                'currency'      => $output['currency']->currency_code,
                'callback_url'  => route('frontend.doctor.booking.paystack.pay.callback') . '?output=' . $junkData->identifier
            ];
        }

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $gateway->secret_key",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $response   = json_decode($result);
        if ($response->status == true) {
            if (get_auth_guard() == 'api') {

                $response->data = [
                    'redirect_url' => $response->data->authorization_url,
                    'redirect_links' => '',
                    'gateway_type' => PaymentGatewayConst::AUTOMATIC,
                    'access_code' => $response->data->access_code,
                    'reference' => $response->data->reference,
                ];
                return $response->data;
            } else {
                return redirect($response->data->authorization_url)->with('output', $output);
            }
        } else {
            $output['status'] = 'error';
            $output['message'] = $response->message;
            return back()->with(['error' => [$output['message']]]);
        }
    }
    public function paystackInitApi($output = null)
    {
        $gateway = new \stdClass();

        foreach ($output['gateway']->credentials as $credential) {
            if ($credential->name === 'secret-key') {
                $gateway->secret_key = $credential->value;
            } elseif ($credential->name === 'email') {
                $gateway->email = $credential->value;
            }
        }


        $amount = get_amount($output['amount']->total_payable_amount, null, 2) * 100;
        $temp_record_token = generate_unique_string('temporary_datas', 'identifier', 60);
        $junkData       = $this->paystackJunkInsert($output, $temp_record_token);

        $url = "https://api.paystack.co/transaction/initialize";
        if (get_auth_guard() == 'api') {
            $fields             = [
                'email'         => auth()->user()->email,
                'amount'        => $amount,
                'currency'      => $output['currency']->currency_code,
                'callback_url'  => route('api.paystack.pay.callback') . '?output=' . $junkData->identifier
            ];
        } else {
            $fields             = [
                'email'         => auth()->user()->email,
                'amount'        => $amount,
                'currency'      => $output['currency']->currency_code,
                'callback_url'  => route('paystack.pay.callback') . '?output=' . $junkData->identifier
            ];
        }

        $fields_string = http_build_query($fields);

        //open connection
        $ch = curl_init();

        //set the url, number of POST vars, POST data
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: Bearer $gateway->secret_key",
            "Cache-Control: no-cache",
        ));

        //So that curl_exec returns the contents of the cURL; rather than echoing it
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        //execute post
        $result = curl_exec($ch);
        $response   = json_decode($result);

        if ($response->status == true) {


            $response->data = [
                'links'     => [
                    'redirect_url' => $response->data->authorization_url,
                    'redirect_links' => '',
                    'gateway_type' => PaymentGatewayConst::AUTOMATIC,
                    'access_code' => $response->data->access_code,
                    'reference' => $response->data->reference,
                ],
                'id'        => $temp_record_token
            ];
            return $response->data;
        } else {
            $output['status'] = 'error';
            $output['message'] = $response->message;
            return Response::error([$output['message']], [], 400);
        }
    }
    /**
     * function for junk insert
     */
    public function paystackJunkInsert($output, $temp_identifier)
    {
        $output = $this->output;

        if ($output['form_data']['booking_data']->data->hospital_id == null) {
            $wallet_table  = null;
            $wallet_id     = null;
        } else {
            $wallet_table  = $output['wallet']->getTable();
            $wallet_id     = $output['wallet']->id;
        }


        $data = [
            'gateway'           => $output['gateway']->id,
            'currency'          => $output['currency']->id,
            'amount'            => json_decode(json_encode($output['amount']), true),
            'response'          => $output,
            'wallet_table'      => $wallet_table,
            'wallet_id'         => $wallet_id,
            'creator_table'     => auth()->guard(get_auth_guard())->user()->getTable(),
            'creator_id'        => auth()->guard(get_auth_guard())->user()->id,
            'creator_guard'     => get_auth_guard(),
            'user_record'       => $output['form_data'],
            'payment_method'    => "Paystack",
        ];

        return TemporaryData::create([
            'user_id'       => Auth::id(),
            'type'          => PaymentGatewayConst::PAYSTACK,
            'identifier'    => $temp_identifier,
            'data'          => $data,
        ]);
    }
    // function paystack success
    function paystackSuccess($request)
    {
        $reference = $request['reference'];
        $identifier = $request['output'];
        $temp_data  = TemporaryData::where('identifier', $identifier)->first();

        $curl = curl_init();
        $secret_key = '';
        foreach ($temp_data->data->response->gateway->credentials as $credential) {
            if ($credential->name === 'secret-key') {
                $secret_key = $credential->value;
                break;
            }
        }
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
                "Authorization: Bearer $secret_key",
                "Cache-Control: no-cache",
            ),
        ));

        $result = curl_exec($curl);
        $response   = json_decode($result);
        $responseArray = [
            'gateway' => $temp_data->data->response->gateway, // Converts the object to an array
            'currency' => $temp_data->data->response->currency, // Converts the object to an array
            'amount' => $temp_data->data->response->amount, // Converts the object to an array
            'form_data' => [
                'identifier' => $temp_data->data->user_record->booking_data->slug,
            ], // Assuming this is already an array
            'distribute' => $temp_data->data->response->distribute,
            'capture' => $response->data->reference,
            'junk_identifier' => $identifier
        ];
        if ($response->status == true) {
            $status = global_const()::STATUS_SUCCESS;
            try {
                $transaction_response = $this->createPaystackTransaction($responseArray, $status);
            } catch (Exception $e) {

                throw new Exception($e->getMessage());
            }
            return $transaction_response;
        }
    }
    // Update Code (Need to check)
    public function createPaystackTransaction($output, $status)
    {
        $basic_setting = BasicSettings::first();
        $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);

        $inserted_id = $this->insertPaystackRecord($output, $trx_id, $status);
        $user = auth()->user();

        if ($basic_setting->email_notification == true) {
            try {
                Notification::route("mail", $user->email)->notify(new PaystackNotification($user, $inserted_id, $trx_id));
            } catch (Exception $e) {
            }
        }


        if ($this->requestIsApiUser()) {
            // logout user
            $api_user_login_guard = $this->output['api_login_guard'] ?? null;
            if ($api_user_login_guard != null) {
                auth()->guard($api_user_login_guard)->logout();
            }
        }
        return $this->output['trx_id'] ?? "";
    }
    public function requestIsApiUser()
    {
        $request_source = request()->get('r-source');
        if ($request_source != null && $request_source == PaymentGatewayConst::APP) return true;
        return false;
    }

    public function insertPaystackRecord($output, $trx_id, $status)
    {
      
        $temp_data      = TemporaryData::where('identifier', $output['junk_identifier'])->first();

        $user = auth()->guard('web')->user();
        $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);

        DB::beginTransaction();
        try {
            $id = DB::table("doctor_bookings")->insertGetId([
                'type'                          => $temp_data['data']->response->type,
                'doctor_id'                     => $temp_data['data']->response->form_data->booking_data->doctor_id,
                'hospital_id'                   => $temp_data['data']->response->form_data->booking_data->data->hospital_id,
                'schedule_id'                   => $temp_data['data']->response->form_data->booking_data->schedule_id,
                'booking_data'                  => json_encode($temp_data['data']->response->form_data->booking_data),
                'trx_id'                        => $trx_id,
                'user_id'                       => $temp_data->data->user_record->booking_data->user->id,
                'payment_method'                => $temp_data['data']->response->gateway->name,
                'payment_gateway_currency_id'   => $temp_data['data']->response->currency->id,
                'booking_exp_seconds'           => global_const()::BOOKING_EXP_SEC,
                'slug'                          => $temp_data['data']->response->form_data->booking_data->data->slug,
                'uuid'                          => $temp_data['data']->response->form_data->booking_data->data->uuid,
                'type'                          => global_const()::ONLINE_PAYMENT,
                'price'                         => $temp_data['data']->amount->price,
                'total_charge'                  => $temp_data['data']->amount->total_charge,
                'payable_price'                 => $temp_data['data']->amount->payable_amount,
                'gateway_payable_price'         => $temp_data['data']->amount->total_payable_amount,
                'date'                          => $temp_data['data']->response->form_data->booking_data->data->date,
                'payment_currency'              => $temp_data['data']->response->currency->currency_code,
                'remark'                        => ucwords(remove_special_char($temp_data['data']->response->type, " ")) . " With " . $output['gateway']->name,
                'details'                       => json_encode(['gateway_response' => $output['capture']]),
                'status'                        => GlobalConst::STATUS_PENDING,
                'callback_ref'                  => $output['callback_ref'] ?? null,
                'created_at'                    => now(),
            ]);

            if ($status === PaymentGatewayConst::STATUS_SUCCESS) {
                if ($temp_data['data']->wallet_table != null) {
                    $this->updateWalletBalance($output, $temp_data);
                }
            }

            try {
                if (auth()->check()) {
                    $user = auth()->guard('web')->user();
                    $doctor_data   = Doctor::where('id', $temp_data['data']->response->form_data->booking_data->doctor_id,)->first();
                    $schedule_data  = DoctorHasSchedule::where('id', $temp_data['data']->response->form_data->booking_data->schedule_id,)->first();

                    UserNotification::create([
                        'user_id'  => $user->id,
                        'message'  => [
                            'title'         => "Your Booking",
                            'doctor'        => $doctor_data->name,
                            'date'          => $temp_data['data']->response->form_data->booking_data->data->date,
                            'from_time'     => $schedule_data->from_time,
                            'to_time'       => $schedule_data->to_time,
                            'success'       => "Successfully Booked."
                        ],

                    ]);
                }


            } catch (Exception $e) {

            }

            $temp_data->delete();

            DB::commit();
        } catch (Exception $e) {

            DB::rollBack();
            throw new Exception($e);
        }
        return $id;
    }

    public function updateWalletBalance($output, $temp_data)
    {
        $hospital_data        = HospitalWallet::where('hospital_id', $temp_data['data']->wallet_id)->first();
        $balance              = $hospital_data->balance;
        $update_amount        = $balance + $output['amount']->price;
        $hospital_data->update([
            'balance'   => $update_amount,
        ]);
    }
}
