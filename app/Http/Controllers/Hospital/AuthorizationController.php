<?php

namespace App\Http\Controllers\Hospital;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\SetupKyc;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Traits\ControlDynamicInputFields;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\HospitalAuthorization;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Validation\ValidationException;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Models\Hospital\Hospital;
use Carbon\Carbon;
use App\Notifications\User\Auth\SendAuthorizationCode;

class AuthorizationController extends Controller
{
    use ControlDynamicInputFields;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function showMailFrom($token)
    {
        $page_title = setPageTitle(__("Mail Authorization"));
        $email = HospitalAuthorization::where('token', $token)
            ->join('hospitals', 'hospital_authorizations.hospital_id', '=', 'hospitals.id')
            ->value('hospitals.email');
        return view('hospital.auth.authorize.verify-mail', compact("page_title", "token", "email"));
    }

    /**
     * Verify authorizaation code.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function mailVerify(Request $request, $token)
    {
        $request->merge(['token' => $token]);
        $request->validate([
            'token'     => "required|string|exists:hospital_authorizations,token",
            'code'      => "required",
        ]);

        $code = implode($request->code);
        $otp_exp_sec = BasicSettingsProvider::get()->otp_exp_seconds ?? GlobalConst::DEFAULT_TOKEN_EXP_SEC;
        $auth_column = HospitalAuthorization::where("token", $request->token)->where("code", $code)->first();
        if (!$auth_column) {
            return redirect()->back()->with(['error' => ['Invalid otp code']]);
        }
        if ($auth_column->created_at->addSeconds($otp_exp_sec) < now()) {
            $this->authLogout($request);
            return redirect()->route('hospitals.login')->with(['error' => [__('Session expired. Please try again')]]);
        }

        try {
            $auth_column->hospital->update([
                'email_verified'    => true,
            ]);
            $auth_column->delete();
        } catch (Exception $e) {
            $this->authLogout($request);
            return redirect()->route('hospitals.login')->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->intended(route("hospitals.dashboard"))->with(['success' => [__('Account successfully verified')]]);
    }



    public function authLogout(Request $request)
    {
        auth()->guard("hospital")->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
    }


    public function mailResend($token)
    {
        $hospital_authorize = HospitalAuthorization::where("token", $token)->first();
        if (!$hospital_authorize) return back()->with(['error' => ['Request token is invalid']]);
        if (Carbon::now() <= $hospital_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) {
            throw ValidationException::withMessages([
                'code'      => 'You can resend verification code after ' . Carbon::now()->diffInSeconds($hospital_authorize->created_at->addMinutes(GlobalConst::USER_PASS_RESEND_TIME_MINUTE)) . ' seconds',
            ]);
        }
        $resend_code = generate_random_code();
        try {
            $hospital_authorize->update([
                'code'          => $resend_code,
                'created_at'    => now(),
            ]);
            $data = $hospital_authorize->toArray();
            try {
                $hospital_authorize->hospital->notify(new SendAuthorizationCode((object) $data));
            } catch (Exception $e) {
            }
        } catch (Exception $e) {
            throw ValidationException::withMessages([
                'code'      => "Something went wrong! Please try again.",
            ]);
        }

        return redirect()->route('hospitals.authorize.mail', $token)->with(['success' => ['Mail Resend Success!']]);
    }



    //  2fa Verify

    public function showGoogle2FAForm()
    {
        $page_title =  __("Authorize Google Two Factor");
        return view('hospital.auth.authorize.verify-g2fa', compact('page_title'));
    }

    public function google2FASubmit(Request $request)
    {
        $request->validate([
            'code*'    => "required|numeric",
        ]);
        $code = implode($request->code);
        $hospital = auth()->user();
        if (!$hospital->two_factor_secret) {
            return back()->with(['warning' => [__('Your secret key not stored properly. Please contact with system administrator')]]);
        }

        if (google_2fa_verify($hospital->two_factor_secret, $code)) {
            $hospital->update([
                'two_factor_verified'   => true,
            ]);
            return redirect()->intended(route('hospitals.dashboard'));
        }
        return back()->with(['warning' => [__('Failed to login. Please try again')]]);
    }

    public function showKycFrom()
    {
        $username = auth()->user()->username;

        $users = Hospital::with('kyc')->where("username", $username)->first();
        $page_title = setPageTitle(__("KYC Verification"));
        $hospital_kyc = SetupKyc::HospitalKyc()->first();


        if (!$hospital_kyc) return back();
        $kyc_data = $hospital_kyc->fields;
        $kyc_fields = [];
        if ($kyc_data) {
            $kyc_fields = array_reverse($kyc_data);
        }
        return view('hospital.sections.verify-kyc', compact(
            "page_title",
            "kyc_fields",
            "users"
        ));
    }

    public function kycSubmit(Request $request)
    {
        $hospital = auth()->user();
        if ($hospital->kyc_verified == GlobalConst::VERIFIED) return back()->with(['success' => [__('You are already KYC Verified User')]]);

        $user_kyc_fields = SetupKyc::HospitalKyc()->first()->fields ?? [];
        $validation_rules = $this->generateValidationRules($user_kyc_fields);

        $validated = Validator::make($request->all(), $validation_rules)->validate();
        $get_values = $this->placeValueWithFields($user_kyc_fields, $validated);

        $create = [
            'hospital_id'      => auth()->user()->id,
            'data'          => json_encode($get_values),
            'created_at'    => now(),
        ];

        DB::beginTransaction();
        try {
            DB::table('hospital_kyc_data')->updateOrInsert(["hospital_id" => $hospital->id], $create);
            $hospital->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            $hospital->update([
                'kyc_verified'  => GlobalConst::DEFAULT,
            ]);
            $this->generatedFieldsFilesDelete($get_values);
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return redirect()->route('hospitals.authorize.kyc')->with(['success' => [__('KYC information successfully submitted')]]);
    }


    public function logout()
    {

        Auth::user()->token()->revoke();
        $message = ['success' => [__('Logout Successful')]];
        return ApiResponse::success($message);
    }
}
