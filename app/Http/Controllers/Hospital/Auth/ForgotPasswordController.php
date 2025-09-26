<?php

namespace App\Http\Controllers\Hospital\Auth;


use App\Constants\GlobalConst;
use App\Http\Controllers\Controller;
use App\Models\Hospital\Hospital;
use App\Models\Hospital\HospitalPasswordReset;
use App\Notifications\User\Auth\PasswordResetEmail;
use App\Providers\Admin\BasicSettingsProvider;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\DB;

class ForgotPasswordController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showForgotForm()
    {
        $page_title = setPageTitle("Forgot Password");
        return view('hospital.auth.forgot-password.forgot', compact('page_title'));
    }

    /**
     * Send Verification code to user email/phone.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendCode(Request $request)
    {
        $request->validate([
            'credentials'   => "required|string|max:100",
        ]);
        $column = "username";
        if (check_email($request->credentials)) $column = "email";
        $hospital = Hospital::where($column, $request->credentials)->first();
        if (!$hospital) {
            throw ValidationException::withMessages([
                'credentials'       => __("Hospital doesn't exists"),
            ]);
        }

        $token = generate_unique_string("hospital_password_resets", "token", 80);
        $code = generate_random_code();

        try {
            HospitalPasswordReset::where("hospital_id", $hospital->id)->delete();
            $password_reset = HospitalPasswordReset::create([
                'hospital_id'       => $hospital->id,
                'token'         => $token,
                'code'          => $code,
            ]);
            try{

                $hospital->notify(new PasswordResetEmail($hospital, $password_reset));
            }catch(Exception $e){}
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->route('hospitals.password.forgot.code.verify.form', $token)->with(['success' => [__('Verification code sended to your email address')]]);
    }


    public function showVerifyForm($token)
    {
        $page_title = setPageTitle("Verify Hospital");
        $password_reset = HospitalPasswordReset::where("token", $token)->first();
        if (!$password_reset) return redirect()->route('hospitals.password.forgot')->with(['error' => [__('Password Reset Token Expired')]]);
        $resend_time = 0;
        if (Carbon::now() <= $password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            $resend_time = Carbon::now()->diffInSeconds($password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE));
        }
        $hospital_email = $password_reset->hospital->email ?? "";
        return view('hospital.auth.forgot-password.verify', compact('page_title', 'token', 'hospital_email', 'resend_time'));
    }

    /**
     * OTP Verification.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function verifyCode(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $validated = Validator::make($request->all(), [
            'token'         => "required|string|exists:hospital_password_resets,token",
            'code'          => "required",
        ])->validate();

        $code = implode('', $request->input('code'));
        $hospital_code = HospitalPasswordReset::first();

        if ($code == $hospital_code->code) {
            $basic_settings = BasicSettingsProvider::get();
            $otp_exp_seconds = $basic_settings->otp_exp_seconds ?? 0;

            $password_reset = HospitalPasswordReset::where("token", $token)->first();

            if (Carbon::now() >= $password_reset->created_at->addSeconds($otp_exp_seconds)) {
                foreach (HospitalPasswordReset::get() as $item) {
                    if (Carbon::now() >= $item->created_at->addSeconds($otp_exp_seconds)) {
                        $item->delete();
                    }
                }
                return redirect()->route('hospitals.password.forgot')->with(['error' => [__('Session expired. Please try again')]]);
            }
        } else {
            throw ValidationException::withMessages([
                'code'      => __("Verification Otp is Invalid"),
            ]);
        }

        return redirect()->route('hospitals.password.forgot.reset.form', $token);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function resendCode($token)
    {
        $password_reset = HospitalPasswordReset::where('token', $token)->first();
        if (!$password_reset) return back()->with(['error' => ['Request token is invalid']]);
        if (Carbon::now() <= $password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            throw ValidationException::withMessages([
                'code'      => 'You can resend verification code after ' . Carbon::now()->diffInSeconds($password_reset->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) . ' seconds',
            ]);
        }

        DB::beginTransaction();
        try {
            $update_data = [
                'code'          => generate_random_code(),
                'created_at'    => now(),
                'token'         => $token,
            ];
            DB::table('hospital_password_resets')->where('token', $token)->update($update_data);
            try{
                $password_reset->hospital->notify(new PasswordResetEmail($password_reset->hospital, (object) $update_data));
            }catch(Exception $e){}
            DB::commit();
        } catch (Exception $e) {
            DB::rollback();
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return redirect()->route('hospitals.password.forgot.code.verify.form', $token)->with(['success' => [__('Verification code resend success')]]);
    }


    public function showResetForm($token)
    {
        $page_title = setPageTitle("Reset Password");
        return view('hospital.auth.forgot-password.reset', compact('page_title', 'token'));
    }


    public function resetPassword(Request $request, $token)
    {
        $basic_settings = BasicSettingsProvider::get();
        $passowrd_rule = "required|string|min:6|confirmed";
        if ($basic_settings->secure_password) {
            $passowrd_rule = ["required", Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised(), "confirmed"];
        }

        $request->merge(['token' => $token]);
        $validated = Validator::make($request->all(), [
            'token'         => "required|string|exists:hospital_password_resets,token",
            'password'      => $passowrd_rule,
        ])->validate();

        $password_reset = HospitalPasswordReset::where("token", $token)->first();
        if (!$password_reset) {
            throw ValidationException::withMessages([
                'password'      => __("Invalid Request. Please try again"),
            ]);
        }

        try {

            $password_reset->hospital->update([
                'password'      => Hash::make($validated['password']),
            ]);
            $password_reset->delete();
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('hospitals.login')->with(['success' => [__('Password reset success. Please login with new password')]]);
    }
}
