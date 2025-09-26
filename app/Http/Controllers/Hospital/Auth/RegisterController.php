<?php

namespace App\Http\Controllers\Hospital\Auth;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\Admin\Currency;
use App\Models\Hospital\Hospital;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use App\Traits\Hospital\LoggedInHospitals;
use App\Traits\Hospital\RegisteredHospitals;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Foundation\Auth\RegistersUsers;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers, RegisteredHospitals,LoggedInHospitals;

    protected $basic_settings;

    public function __construct()
    {
        $this->basic_settings = BasicSettingsProvider::get();
    }

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm() {
        $client_ip = request()->ip() ?? false;
        $hospital_country = geoip()->getLocation($client_ip)['country'] ?? "";

        $page_title = setPageTitle("Hospital Registration");
        return view('hospital.auth.register',compact(
            'page_title',
            'hospital_country',
        ));
    }


    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        $validated                          = $this->validator($request->all())->validate();
        $basic_settings                     = $this->basic_settings;
        $validated['email_verified']        = ($basic_settings->hospital_email_verification == true) ? false : true;
        $validated['sms_verified']          = ($basic_settings->hospital_sms_verification == true) ? false : true;
        $validated['kyc_verified']          = ($basic_settings->hospital_kyc_verification == true) ? false : true;
        $validated['password']              = Hash::make($validated['password']);
        $validated['username']              = make_username_hospital(Str::slug($validated['hospital_name']),"hospitals");


        if($this->basic_settings->hospital_registration == 0){
            return redirect()->back()->with(['error' => ['User Registration not Available Now! Please try again']]);
        }else{
            event(new Registered($user = $this->create($validated)));
            $this->guard()->login($user);
            return $this->registered($request, $user);
        }
    }
    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public function validator(array $data) {

        $basic_settings = $this->basic_settings;
        $passowrd_rule = "required|string|min:6";
        if($basic_settings->hospital_secure_password) {
            $passowrd_rule = ["required",Password::min(8)->letters()->mixedCase()->numbers()->symbols()->uncompromised()];
        }
        $agree_rule = $basic_settings->hospital_agree_policy ? 'required|in:on' : 'in:on';

        return Validator::make($data,[
            'hospital_name' => 'required|string|max:60',
            'email'         => 'required|string|email|max:150|unique:hospitals,email',
            'password'      => $passowrd_rule,
            'agree'         => $agree_rule,
        ]);
    }

    protected function createUserWallets($hospital) {
        $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
        $wallets = [];
        foreach($currencies as $currency_id) {
            $wallets[] = [
                'hospital_id'   => $hospital->id,
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

    protected function createUserWalletsOffline($hospital) {

        $wallets = [];

            $wallets[] = [
                'hospital_id'   => $hospital->id,
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


    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        return Hospital::create($data);
    }


    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard('hospital');
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $hospital)
    {
        try{
            $this->createUserWallets($hospital);
            $this->createUserWalletsOffline($hospital);
            return redirect()->intended(route('hospitals.dashboard'));
        }catch(Exception $e){

        }

    }
}
