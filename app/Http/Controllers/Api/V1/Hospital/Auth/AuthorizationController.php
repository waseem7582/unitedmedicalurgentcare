<?php

namespace App\Http\Controllers\Api\V1\Hospital\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use App\Constants\GlobalConst;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Carbon;
use App\Models\UserAuthorization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Models\Hospital\HospitalAuthorization;
use App\Notifications\User\Auth\SendAuthorizationCode;
use App\Http\Helpers\Response;

class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;
    /**
     * Email resend code
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function emailResendCode(Request $request)
    {

        $user =authGuardApi()['user'];
        $resend = HospitalAuthorization::where("hospital_id",$user->id)->first();
        if($resend){
            if(Carbon::now() <= $resend->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
                $error = ['error'=>['You can resend verification code after '.Carbon::now()->diffInSeconds($resend->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)). ' seconds']];
                return ApiResponse::onlyError($error);
            }
        }
        $data = [
            'hospital_id'       => $user->id,
            'code'          => generate_random_code(),
            'token'         => generate_unique_string("hospital_authorizations","token",200),
            'created_at'    => now(),
        ];
        DB::beginTransaction();
        try{
            if($resend) {
                HospitalAuthorization::where("hospital_id", $user->id)->delete();
            }
            DB::table("hospital_authorizations")->insert($data);
            try{
                $user->notify(new SendAuthorizationCode((object) $data));
            }catch(Exception $e){}
            DB::commit();
            $message =  ['success'=>[__('Email verification code resend successfully')]];
            return ApiResponse::onlySuccess($message);
        }catch(Exception $e) {
            DB::rollBack();
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::onlyError($error);
        }
    }


    /**
     * Verify user mail
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyEmailCode(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);
        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }
        $code = $request->otp;
        $user =authGuardApi()['user'];

        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = HospitalAuthorization::where("code",$code)->where('hospital_id', $user->id)->first();
        if(!$auth_column){
             $error = ['error'=>[__('Verification code does not match')]];
            return ApiResponse::onlyError($error);
        }
        if($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $error = ['error'=>[__('Session expired. Please try again')]];
            return ApiResponse::onlyError($error);
        }
        try{
            $auth_column->hospital->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        }catch(Exception $e) {
            $error = ['error'=>[__('Something went wrong! Please try again')]];
            return ApiResponse::onlyError($error);
        }
        $message =  ['success'=>[__('Email successfully verified')]];
        return ApiResponse::onlySuccess($message);
    }


     // Get KYC Input Fields
     public function getKycInputFields() {

        $user =authGuardApi()['user'];

        $user_kyc = SetupKyc::hospitalKyc()->first();
        $kyc_data = $user_kyc->fields;
        $kyc_fields = array_reverse($kyc_data);

        $data = [
            'status_info'  => '0: Unverified, 1: Verified, 2: Pending, 3: Rejected',
            'kyc_status'   => $user->kyc_verified,
            'input_fields' => $kyc_fields
        ];

        if(!$user_kyc) return ApiResponse::success(['success' => ['Hospital KYC section is under maintenance']], $data);
        if($user->kyc_verified == GlobalConst::VERIFIED) return ApiResponse::success(['success' => [__('You are already KYC Verified User')]], $data);
        if($user->kyc_verified == GlobalConst::PENDING) return ApiResponse::success(['success' => [__('Your KYC information is submitted. Please wait for admin confirmation')]], $data);

        return ApiResponse::success(['success' => [__('Hospital KYC input fields fetch successfully')]], $data);
    }


    public function kycSubmit(Request $request) {
        $user =authGuardApi()['user'];
        if($user->kyc_verified == GlobalConst::VERIFIED) return ApiResponse::onlyWarning(['warning' => [__('You are already KYC Verified User')]]);
        $user_kyc_fields = SetupKyc::hospitalKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);

        $validated = Validator::make($request->all(),$validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields,$validated);

        $create = [
            'hospital_id'       => $user->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try{
            DB::table('hospital_kyc_data')->updateOrInsert(["hospital_id" => $user->id],$create);
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            $user->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return ApiResponse::onlyError(['success' => [__('KYC information successfully submitted')]]);
        }

       return ApiResponse::onlySuccess(['success' => [__('KYC information successfully submitted')]]);
    }

    /**
     * Google 2FA Verification
     *
     * @method GET
     * @return \Illuminate\Http\Response
     */

    public function verify2FACode(Request $request) {

        $validator = Validator::make($request->all(), [
            'otp' => 'required',
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }

        $code = $request->otp;
        $user =authGuardApi()['user'];

        if(!$user->two_factor_secret) {
            return ApiResponse::onlyError(['error' => [__('Your secret key not stored properly. Please contact with system administrator')]]);
        }

        if(google_2fa_verify($user->two_factor_secret,$code)) {
            $user->update([
                'two_factor_verified'   => true,
            ]);
            return ApiResponse::onlySuccess(['success' => [__('Two factor verified successfully')]]);
        }

        return ApiResponse::onlyError(['error' => [__('Failed to login. Please try again')]]);
    }

        // hospital 2Fa authorization
        public function get2FaStatus() {
            $user = auth()->user();
            $qr_code = generate_google_2fa_auth_qr();
           $qr_secret = $user->two_factor_secret;
            $message = __("Your account secure with google 2FA");
            if($user->two_factor_status == false) $message = __("To enable two factor authentication (powered by google) please visit your web dashboard Click here:") . " " . setRoute("user.authorize.google.2fa");

            return Response::success([__('Request response fetch successfully!')],[
                'qr_secret' =>  $qr_secret,
                'qr_code'    => $qr_code,
                'status' => $user->two_factor_status,
                'message'   => $message,
            ],200);
        }


    public function google2FAStatusUpdate(Request $request){
        $validator = Validator::make($request->all(),[
            'status'        => "required|numeric",
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors()->all(),[],400);
        }
        $validated = $validator->validated();
        $user = auth()->user();
        try{
            $user->update([
                'two_factor_status'         => $validated['status'],
                'two_factor_verified'       => true,
            ]);
        }catch(Exception $e) {
            return Response::error([__('Something went wrong! Please try again')],[],500);
        }
        return Response::success([__('Google 2FA Updated Successfully!')],[],200);
    }

    /**
     * Verify user mail
     *
     * @method GET
     * @return \Illuminate\Http\Response
     */

     public function logout(){
        Auth::user()->token()->revoke();
        $message = ['success'=>[__('Logout Successful')]];
        return ApiResponse::onlySuccess($message);
     }
}
