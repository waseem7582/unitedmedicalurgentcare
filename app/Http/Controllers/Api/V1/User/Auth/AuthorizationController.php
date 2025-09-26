<?php

namespace App\Http\Controllers\Api\V1\User\Auth;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Carbon;
use App\Models\UserAuthorization;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;
use App\Notifications\User\Auth\SendAuthorizationCode;

class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;

    public static function sendCodeToMail($user = null) {

        if(!$user && auth()->guard("api")->check() == false) throw new Exception(__("Access denied! Unauthenticated"));
        if(!$user) $user = auth()->guard("api")->user();

        $data = [
            'user_id'       => $user->id,
            'code'          => generate_random_code(),
            'token'         => generate_unique_string("user_authorizations","token",200),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try{
            UserAuthorization::where("user_id",$user->id)->delete();
            DB::table("user_authorizations")->insert($data);
            $user->notify(new SendAuthorizationCode((object) $data));
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            throw new Exception(__("Something went wrong! Please try again"));
        }

        return $data;
    }

    public function resendCodeToMail(Request $request) {
        $validator = Validator::make($request->all(),[
            'token'     => "required|string|exists:user_authorizations,token"
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();
        $user_authorize = UserAuthorization::where("token",$validated['token'])->first();

        if(!$user_authorize) return Response::error([__("Request token is invalid")],[],404);

        if(Carbon::now() <= $user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            return Response::error([__('You can resend verification code after ').Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)). __(' seconds')],['token' => $validated['token'], 'wait_time' => (string) Carbon::now()->diffInSeconds($user_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE))],400);
        }

        $resend_code = generate_random_code();
        try{
            $user_authorize->update([
                'code'          => $resend_code,
                'created_at'    => now(),
            ]);
            $data = $user_authorize->toArray();
            $user_authorize->user->notify(new SendAuthorizationCode((object) $data));
        }catch(Exception $e) {
            return Response::error([__("Something went wrong! Please try again")],[],500);
        }

        return Response::success([__("Verification code resend successfully!")],['token' => $validated['token'],'wait_time' => ""],200);
    }

    public function verifyMailCode(Request $request) {
        $validator = Validator::make($request->all(),[
            'token'     => "required|string|exists:user_authorizations,token",
            'code'      => "required|integer",
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors()->all(),[],400);
        }
        $validated = $validator->validate();

        if(!UserAuthorization::where("code",$request->code)->exists()) {
            return Response::error([__("Invalid OTP. Please try again")],[],404);
        }

        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = UserAuthorization::where("token",$request->token)->where("code",$request->code)->first();
        if($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $auth_column->delete();
            $this->authLogout($request);
            return Response::error([__("Session expired. Please try again")],[],440);
        }

        try{
            $auth_column->user->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        }catch(Exception $e) {
            $auth_column->delete();
            $this->authLogout($request);
            return Response::error([__("Something went wrong! Please try again")],[],500);
        }

        return Response::success([__("Account successfully verified")],[],200);
    }

    public function authLogout(Request $request) {
        $user_token = Auth::guard(get_auth_guard())->user()->token();
        $user_token->revoke();
    }

    // User 2Fa authorization
    public function get2FaStatus() {
        $user = auth()->guard(get_auth_guard())->user();
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
        $user = Auth::guard(get_auth_guard())->user();
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

    public function verifyGoogle2Fa(Request $request) {
        $validator = Validator::make($request->all(),[
            'code'      => "required|integer",
        ]);
        if($validator->fails()) return Response::error($validator->errors()->all(),[]);
        $validated = $validator->validate();

        $code = $validated['code'];

        $user = auth()->guard(get_auth_guard())->user();

        if(!$user->two_factor_secret) {
            return Response::error([__('Your secret key not stored properly. Please contact with system administrator')],[],400);
        }

        if(google_2fa_verify($user->two_factor_secret,$code)) {

            $user->update([
                'two_factor_verified'   => true,
            ]);

            return Response::success([__('Google 2FA successfully verified!')],[],200);
        }else if(google_2fa_verify($user->two_factor_secret,$code) === false) {
            return Response::error([__('Invalid authentication code')],[],400);
        }

        return Response::error([__('Failed to login. Please try again')],[],500);
    }
}
