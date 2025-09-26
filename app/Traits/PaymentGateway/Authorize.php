<?php
namespace App\Traits\PaymentGateway;

use Exception;
use App\Models\TemporaryData;
use App\Http\Helpers\Api\Helpers;
use App\Constants\PaymentGatewayConst;
use App\Models\Admin\PaymentGateway;
use Illuminate\Support\Str;

trait Authorize{

    public function authorizeInit($output = null){
       
        if(!$output) $output = $this->output;
        if($output['type'] === PaymentGatewayConst::PAYMENTMETHOD){
            return $this->setupAuthorizeInitAddMoney($output);
        }
    }

    public function setupAuthorizeInitAddMoney($output){
        $temp_record_token = generate_unique_string('temporary_datas','identifier',60);
        $junk_data = $this->authorizeJunkInsert($temp_record_token);
        if(request()->expectsJson()) {
      
            $this->output['redirection_response'] = [];
            $this->output['redirect_links']       = [];
            $this->output['gateway_alias']        = "authorize";
            $this->output['identifier']           = $temp_record_token ?? '';
            $this->output['redirect_url']         = route('api.user.authorize.payment.submit');

            return $this->get();
        }
        return redirect()->route('frontend.doctor.booking.authorize.card.info',$junk_data->identifier);
    }
    function authorizeCredentials($temp_data){
        $gateway             = PaymentGateway::where('id',$temp_data->data->gateway)->first() ?? null;
        if(!$gateway) throw new Exception(__("Payment gateway not available"));
        $credentials         = $gateway->credentials;
        $app_login_id        = getPaymentCredentials($credentials,'App Login ID');
        $transaction_key     = getPaymentCredentials($credentials,'Transaction Key');
        $signature_key       = getPaymentCredentials($credentials,'Signature Key');

        $mode           = $gateway->env;

        $authorize_register_mode = [
            PaymentGatewayConst::ENV_SANDBOX => "sandbox",
            PaymentGatewayConst::ENV_PRODUCTION => "live",
        ];
        if(array_key_exists($mode,$authorize_register_mode)) {
            $mode = $authorize_register_mode[$mode];
        }else {
            $mode = "sandbox";
        }

        return (object) [
            'app_login_id'          => $app_login_id,
            'transaction_key'       => $transaction_key,
            'signature_key'         => $signature_key,
            'mode'                  => $mode,
            'code'                  => $gateway->code
        ];
    }

    public function authorizeJunkInsert($temp_record_token) {
        $output = $this->output;
        if($output['form_data']['booking_data']->data->hospital_id == null){
            $wallet_table  = null;
            $wallet_id     = null;
        }else{
            $wallet_table  = $output['wallet']->getTable();
            $wallet_id     = $output['wallet']->id;
        }

        $data = [
            'gateway'       => $output['gateway']->id,
            'currency'      => $output['currency']->id,
            'amount'        => json_decode(json_encode($output['amount']),true),
            'response'      => [],
            'wallet_table'  => $wallet_table,
            'wallet_id'     => $wallet_id,
            'booking_data'  => $output['form_data']['booking_data'],
            'creator_table' => auth()->guard(get_auth_guard())->user()->getTable(),
            'creator_id'    => auth()->guard(get_auth_guard())->user()->id,
            'creator_guard' => get_auth_guard(),
        ];
        return TemporaryData::create([
            'type'          => PaymentGatewayConst::PAYMENTMETHOD,
            'identifier'    => $temp_record_token,
            'data'          => $data,
        ]);
    }

    public function authorizeSuccess($output) {
        
        $output['capture']      = $output['tempData']['data']->response ?? "";
   

        // need to insert new transaction in database
        try{
            $this->createTransaction($output);
        }catch(Exception $e) {
            throw new Exception($e->getMessage());
        }
    }


   

    public function isAuthorize($gateway)
    {
        $search_keyword = ['authorize','authorize gateway','gateway authorize','authorize payment gateway'];
        $gateway_name = $gateway->name;

        $search_text = Str::lower($gateway_name);
        $search_text = preg_replace("/[^A-Za-z0-9]/","",$search_text);
        foreach($search_keyword as $keyword) {
            $keyword = Str::lower($keyword);
            $keyword = preg_replace("/[^A-Za-z0-9]/","",$keyword);
            if($keyword == $search_text) {
                return true;
                break;
            }
        }
        return false;
    }
}

?>
