<?php

namespace App\Traits\Hospital\PaymentGateway;

use Exception;
use App\Models\TemporaryData;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use Illuminate\Support\Facades\Auth;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Facades\Notification;
use App\Notifications\PaystackNotification;



trait  PaystackGateway
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
        $amount = get_amount($output['amount']->total_amount, null, 2) * 100;
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
                'callback_url'  => route('hospitals.admin.charges.paystack.pay.callback') . '?output=' . $junkData->identifier
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


        $amount = get_amount($output['amount']->total_amount, null, 2) * 100;
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
                'callback_url'  => route('hospitals.admin.charges.paystack.pay.callback') . '?output=' . $junkData->identifier
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
        $data = [
            'gateway'       => $output['gateway']->id,
            'currency'      => $output['currency']->id,
            'amount'        => json_decode(json_encode($output['amount']), true),
            'response'      => $output,
            'creator_table'     => auth()->guard(get_auth_guard())->user()->getTable(),
            'creator_id'        => auth()->guard(get_auth_guard())->user()->id,
            'creator_guard'     => get_auth_guard(),
            'user_record'       => $output['form_data'],
            'payment_method'    => $output['form_data']['payment_method'],
        ];

        return TemporaryData::create([
            'user_id'       => Auth::id(),
            'type'          => PaymentGatewayConst::PAYSTACK,
            'identifier'    => $temp_identifier,
            'data'          => $data,
        ]);
    }
    // function paystack success
    function hospitalPaystackSuccess($request)
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
            'type'      => $temp_data->data->response->type,
            'wallet'    => $temp_data->data->response->wallet,
            'gateway' => $temp_data->data->response->gateway, // Converts the object to an array
            'currency' => $temp_data->data->response->currency, // Converts the object to an array
            'amount' => $temp_data->data->response->amount, // Converts the object to an array
            'form_data' => [
                'identifier' => $identifier,
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
        $trx_id = generateTrxString('transactions', 'trx_id', 'PB', 8);

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


        $trx_id = generate_unique_string("transactions", "trx_id", 16);
        DB::beginTransaction();
        try {
            $id = DB::table("transactions")->insertGetId([
                'type'                          => $output['type'],
                'trx_id'                        => $trx_id,
                'user_type'                     => 'Hospital',
                'user_id'                       => null,
                'wallet_id'                     => $output['wallet']->id,
                'payment_gateway_currency_id'   => $output['currency']->id,
                'request_amount'                => $output['amount']->requested_amount,
                'request_currency'              => $output['amount']->default_currency,
                'exchange_rate'                 => $output['amount']->exchange_rate,
                'percent_charge'                => $output['amount']->percent_charge,
                'fixed_charge'                  => $output['amount']->fixed_charge,
                'total_charge'                  => $output['amount']->total_charge,
                'total_payable'                 => $output['amount']->total_amount,
                'receive_amount'                => $output['amount']->requested_amount,
                'receiver_type'                 => 'Admin',
                'receiver_id'                   => null,
                'available_balance'             => $output['wallet']->balance + $output['amount']->will_get,
                'payment_currency'              => $output['currency']->currency_code,
                'remark'                        => ucwords(remove_special_char($output['type'], " ")) . " With " . $output['gateway']->name,
                'details'                       => json_encode(['gateway_response' => $output['capture']]),
                'status'                        => $status,
                'callback_ref'                  => $output['callback_ref'] ?? null,
                'created_at'                    => now(),
            ]);

            if ($status === PaymentGatewayConst::STATUS_SUCCESS) {
                $this->updateWalletBalance($output);
            }

            $temp_data->delete();

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            throw new Exception($e->getMessage());
        }
        return $id;
    }

    public function updateWalletBalance($output)
    {
        $update_amount = $output['wallet']->balance + $output['amount']->requested_amount;

        // Convert stdClass to Eloquent Model
        $wallet = HospitalWallet::find($output['wallet']->id);

        if ($wallet) {
            $wallet->update(['balance' => $update_amount]);

        } else {
            return response()->json(['error' => 'Wallet not found'], 404);
        }
    }
}
