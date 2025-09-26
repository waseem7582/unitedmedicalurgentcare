<?php

namespace App\Http\Controllers\Api\V1\Hospital\Auth;

use Exception;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Currency;
use App\Models\Hospital\Hospital;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Traits\Hospital\LoggedInHospitals;
use App\Traits\Hospital\RegisteredHospitals;
use App\Models\Hospital\HospitalAuthorization;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Providers\Admin\BasicSettingsProvider;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Http\Resources\Hospital\HospitalResouce;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use App\Notifications\Hospital\Auth\SendAuthorizationCode;

class AuthController extends Controller
{
    use LoggedInHospitals, RegisteredHospitals,AuthenticatesUsers;
    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Mehtod for hospital login
     * @method POST
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request  Response
     */
    public function login(Request $request){
        $validator = Validator::make($request->all(),[
            'email'    => 'required|max:40',
            'password' => 'required|min:6',
        ]);

        if($validator->fails()){
            $error = ['error' => $validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }

        $user = Hospital::where('username', trim(strtolower($request->email)))->orWhere('email', $request->email)->first();

        if(!$user){
            $error = ['error' => [__('The credentials does not match')]];
            return ApiResponse::onlyValidation($error);
        }

        $user->two_factor_verified = false;
        $user->save();

        $token = $user->createToken('Auth_token')->accessToken;

        $user_data = [
            'token'         => $token,
            'image_path'    => get_files_public_path('user-profile'),
            'default_image' => get_files_public_path('default'),
            "base_ur"       => url('/'),
            'user'          => $user
        ];

        if(Hash::check($request->password, $user->password)){
            if($user->status == 0){
                $error = ['error'=>[__('Account Has been Suspended')]];
                return ApiResponse::onlyValidation($error);
            }elseif($user->email_verified == 0){
                $user_authorize = HospitalAuthorization::where("hospital_id",$user->id)->first();
                $resend_code = generate_random_code();
                $user_authorize->update([
                    'code'          => $resend_code,
                    'created_at'    => now(),
                ]);
                $data = $user_authorize->toArray();
                $user->notify(new SendAuthorizationCode((object) $data));
                $message = ['success' => [__('Please check email and verify your account')]];
                return ApiResponse::success($message, $user_data);
            }

            $this->createLoginLog($user);

            $message = ['success' => [__('Login Successful')]];
            return ApiResponse::success($message,$user_data);
        }else{
            $error = ['error'=>[__('The credentials does not match')]];
            return ApiResponse::onlyError($error);
        }
    }

    /**
     * Mehtod for hospital register
     * @method POST
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Request  Response
     */

    public function register(Request $request){

        $basic_settings = $this->basic_settings;
        $passowrd_rule = "required|string|min:6";

        if($basic_settings->hospital_secure_password) {
            $passowrd_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()];
        }

        $agree_policy = $this->basic_settings->hospital_agree_policy == 1 ? 'required|in:on' : 'nullable';

        $validator = Validator::make($request->all(), [
            'hospital_name'   => 'required|string|max:50',
            'email'        => 'required|email|max:160|unique:hospitals',
            'password'     => $passowrd_rule,
            'policy'       => $agree_policy,
        ]);

        if($validator->fails()){
            $error =  ['error'=>$validator->errors()->all()];
            return ApiResponse::onlyValidation($error);
        }

        $validated = $validator->validated();
        $basic_settings             = $this->basic_settings;
        //User Create

        $validated = Arr::except($validated,['agree']);

        $validated['hospital_name']  = $validated['hospital_name'];
        $validated['email_verified'] = ($basic_settings->hospital_email_verification == true) ? 0 : 1;
        $validated['kyc_verified']   = ($basic_settings->hospital_kyc_verification == true) ? 0 : 1;
        $validated['sms_verified']   = ($basic_settings->hospital_sms_verification == true) ? 0 : 1;
        $validated['status']         = 1;
        $validated['password']       = Hash::make($validated['password']);
        $validated['username']       = make_username_hospital(Str::slug($validated['hospital_name']),"hospitals");

        $user = Hospital::create($validated);

        $token = $user->createToken('Auth_token')->accessToken;

        if ($basic_settings->hospital_email_verification == true) {
            $data = [
                'hospital_id'       => $user->id,
                'code'          => generate_random_code(),
                'token'         => generate_unique_string("hospital_authorizations","token",200),
                'created_at'    => now(),
            ];
            DB::beginTransaction();
            try{
                HospitalAuthorization::where("hospital_id",$user->id)->delete();
                DB::table("hospital_authorizations")->insert($data);
                try {
                    $user->notify(new SendAuthorizationCode((object) $data));
                } catch (Exception $e) {

                }
                DB::commit();
            }catch(Exception $e) {
                DB::rollBack();
                $error = ['error'=>[__('Something went wrong! Please try again')]];
                return ApiResponse::error($error);
            }
        }

        if ($basic_settings->hospital_email_verification == 1) {
            $message =  ['success' => [__('Please check email and verify your account')]];
        } else {
            $message =  ['success' => [__('Registration successful')]];
        }


        $this->createUserWallets($user);
        $this->createUserWalletsOffline($user);

        $data = [
            'token' => $token,
            'image_path' => get_files_public_path('user-profile'),
            'default_image' => get_files_public_path('default'),
            "base_ur"       => url('/'),
            'user' => $user,


        ];

        return ApiResponse::success($message, $data);
    }
    protected function guard()
    {
        return Auth::guard("hospital_api");
    }

    protected function createUserWallets($user) {
        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $wallets = [];
        foreach($currencies as $currency_id) {
            $wallets[] = [
                'hospital_id'   => $user->id,
                'currency_id'   => $currency_id,
                'balance'       => 0,
                'status'        => true,
                'created_at'    => now(),
            ];
        }

        try{
            HospitalWallet::insert($wallets);
        }catch(Exception $e) {
            // handle error
            throw new Exception("Failed to create wallet! Please try again");
        }
    }
    protected function createUserWalletsOffline($user) {
        $wallets = [];
            $wallets[] = [
                'hospital_id'   => $user->id,
                'balance'       => 0,
                'status'        => true,
                'created_at'    => now(),
            ];


        try{
            HospitalOfflineWallet::insert($wallets);
        }catch(Exception $e) {
            // handle error
            throw new Exception("Failed to create wallet! Please try again");
        }
    }
}
