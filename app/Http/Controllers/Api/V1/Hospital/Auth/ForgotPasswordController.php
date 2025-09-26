<?php

namespace App\Http\Controllers\Api\V1\Hospital\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Exception;
use App\Constants\GlobalConst;
use Illuminate\Support\Carbon;
use App\Models\UserPasswordReset;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Response;
use App\Models\Hospital\Hospital;
use App\Models\Hospital\HospitalPasswordReset;
use App\Notifications\User\Auth\PasswordResetEmail;

class ForgotPasswordController extends Controller
{
    public function sendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'credentials' => "required|string|max:100",
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 422);
        }

        $column = check_email($request->credentials) ? "email" : "mobile";
        $user = Hospital::where($column, $request->credentials)->first();

        if (!$user) {
            return Response::error(["Hospital doesn't exist"], [], 404);
        }

        $token = generate_unique_string("hospital_password_resets", "token", 80);
        $code = generate_random_code();

        try {
            HospitalPasswordReset::where("hospital_id", $user->id)->delete();

            $password_reset = HospitalPasswordReset::create([
                'hospital_id' => $user->id,
                'token'       => $token,
                'code'        => $code,
            ]);

            $user->notify(new PasswordResetEmail($user, $password_reset));
        } catch (Exception $e) {
            info($e);
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('Verification OTP code sent to your email')], ['user' => $password_reset]);
    }

    public function resendCode(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'token' => "required|string|exists:hospital_password_resets,token"
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 422);
        }

        $validated = $validator->validate();
        $password_reset = HospitalPasswordReset::where('token', $validated['token'])->first();

        if (!$password_reset) {
            return Response::error(['Request token is invalid'], [], 404);
        }

        if (Carbon::now() <= $password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $seconds = Carbon::now()->diffInSeconds($password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
            return Response::error([__('You can resend verification code after') . " $seconds " . __('seconds')], [], 429);
        }

        DB::beginTransaction();
        try {
            $update_data = [
                'code'       => generate_random_code(),
                'created_at' => now(),
                'token'      => $validated['token'],
            ];

            DB::table('hospital_password_resets')->where('token', $validated['token'])->update($update_data);
            $password_reset->hospital->notify(new PasswordResetEmail($password_reset->hospital, (object) $update_data));

            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('OTP resend successful')], ['token' => $validated['token']]);
    }

    public function verifyCode(Request $request)
    {
        $token = $request->token;
        $request->merge(['token' => $token]);

        $rules = [
            'token' => "required|string|exists:hospital_password_resets,token",
            'otp'   => "required|numeric|exists:hospital_password_resets,code",
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 422);
        }

        $basic_settings = BasicSettingsProvider::get();
        $otp_exp_seconds = $basic_settings->otp_exp_seconds ?? 0;

        $password_reset = HospitalPasswordReset::where("token", $token)->first();

        if (Carbon::now() >= $password_reset->created_at->addSeconds($otp_exp_seconds)) {
            foreach (HospitalPasswordReset::get() as $item) {
                if (Carbon::now() >= $item->created_at->addSeconds($otp_exp_seconds)) {
                    $item->delete();
                }
            }

            return Response::error([__('Session expired. Please try again')], [], 410);
        }

        if ($password_reset->code != $request->otp) {
            return Response::error([__('Verification OTP invalid')], [], 400);
        }

        return Response::success([__('OTP verification successful')], ['password_reset_data' => $password_reset]);
    }

    public function resetPassword(Request $request)
    {
        if ($request->password != $request->password_confirmation) {
            return Response::error([__('Oops password does not match')], [], 422);
        }

        $token = $request->token;
        $basic_settings = BasicSettingsProvider::get();

        $password_rule = "required|string|min:6|confirmed";

        if ($basic_settings->secure_password) {
            $password_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), "confirmed"];
        }

        $request->merge(['token' => $token]);

        $validator = Validator::make($request->all(), [
            'token'    => "required|string|exists:hospital_password_resets,token",
            'password' => $password_rule,
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), [], 422);
        }

        $password_reset = HospitalPasswordReset::where("token", $token)->first();

        if (!$password_reset) {
            return Response::error([__('Invalid Request. Please try again')], [], 404);
        }

        try {
            $password_reset->hospital->update(['password' => Hash::make($request->password)]);
            $password_reset->delete();
        } catch (Exception $e) {
            info($e);
            return Response::error([__('Something went wrong! Please try again')], [], 500);
        }

        return Response::success([__('Password reset successful. Please login with new password')]);
    }
}
