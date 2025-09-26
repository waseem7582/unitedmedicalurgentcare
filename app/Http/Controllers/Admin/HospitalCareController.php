<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Carbon\Carbon;
use App\Models\Transaction;
use Illuminate\Support\Arr;
use App\Models\Hospital\Hospital;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\UserSupportTicket;
use Illuminate\Support\Facades\DB;
use App\Constants\NotificationConst;
use App\Models\Hospital\HospitalMailLog;
use App\Notifications\User\SendMail;
use Illuminate\Support\Facades\Auth;
use App\Constants\SupportTicketConst;
use App\Models\Hospital\HospitalLoginLog;
use App\Constants\PaymentGatewayConst;
use App\Events\Admin\NotificationEvent;
use App\Models\Hospital\HospitalNotification;
use App\Models\Hospital\HospitalWallet;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;
use Jenssegers\Agent\Agent;
use App\Models\Admin\BasicSettings;
use App\Models\Admin\Currency;
use Illuminate\Support\Str;

use Illuminate\Support\Facades\Hash;
use App\Notifications\Admin\NewUserNotification;
class HospitalCareController extends Controller


{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("All Hospitals");
        $users = Hospital::orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
            'users'
        ));
    }

    /**
     * Display Active Users
     * @return view
     */
    public function active()
    {
        $page_title = __("Active Hospitals");
        $users = Hospital::active()->orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
            'users'
        ));
    }


    /**
     * Display Banned Users
     * @return view
     */
    public function banned()
    {
        $page_title = __("Banned Hospitals");
        $users = Hospital::banned()->orderBy('id', 'desc')->paginate(12);
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
            'users',
        ));
    }

    /**
     * Display Email Unverified Users
     * @return view
     */
    public function emailUnverified()
    {
        $page_title = __("Email Unverified Hospitals");
        $users = Hospital::active()->orderBy('id', 'desc')->emailUnverified()->paginate(12);
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
            'users'
        ));
    }

    /**
     * Display SMS Unverified Users
     * @return view
     */
    public function SmsUnverified()
    {
        $page_title = __("SMS Unverified Hospitals");
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
        ));
    }

    /**
     * Display KYC Unverified Users
     * @return view
     */
    public function KycUnverified()
    {
        $page_title = __("KYC Unverified Hospitals");
        $users = Hospital::kycUnverified()->orderBy('id', 'desc')->paginate(8);
        return view('admin.sections.hospital-care.index', compact(
            'page_title',
            'users'
        ));
    }

    /**
     * Display Send Email to All Users View
     * @return view
     */
    public function emailAllHospitals()
    {
        $page_title = __("Email To Hospitals");
        return view('admin.sections.hospital-care.email-to-hospitals', compact(
            'page_title',
        ));
    }

    /**
     * Display Specific User Information
     * @return view
     */
    public function hospitalDetails($username)
    {
        $page_title = __("Hospitals Details");
        $users = Hospital::where('username', $username)->first();
        $support_ticket = UserSupportTicket::where("status",SupportTicketConst::PENDING)->orWhere("status",SupportTicketConst::DEFAULT)->Where('hospital_id', $users->id)->count('status');

        if(!$users) return back()->with(['error' => ['Opps! User not exists']]);
        return view('admin.sections.hospital-care.details', compact(
            'page_title',
            'users',
            'support_ticket',

        ));
    }

    public function sendMailHospitals(Request $request) {
        $request->validate([
            'user_type'     => "required|string|max:30",
            'subject'       => "required|string|max:250",
            'message'       => "required|string|max:2000",
        ]);

        $users = [];
        switch($request->user_type) {
            case "active";
                $users = Hospital::active()->get();
                break;
            case "all";
                $users = Hospital::get();
                break;
            case "email_verified";
                $users = Hospital::emailVerified()->get();
                break;
            case "kyc_verified";
                $users = Hospital::kycVerified()->get();
                break;
            case "banned";
                $users = Hospital::banned()->get();
                break;
        }

        try{
            Notification::send($users,new SendMail((object) $request->all()));
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => ['Email successfully sended']]);

    }

    public function sendMail(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $validator = Validator::make($request->all(),[
            'subject'       => 'required|string|max:200',
            'message'       => 'required|string|max:2000',
            'username'      => 'required|string|exists:hospitals,username',
        ]);
        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with("modal","email-send");
        }
        $validated = $validator->validate();
        $hospital = Hospital::where("username",$username)->first();
        $validated['hospital_id'] = $hospital->id;
        $validated = Arr::except($validated,['username']);
        $validated['method']   = "SMTP";
        try{
            HospitalMailLog::create($validated);
            $hospital->notify(new SendMail((object) $validated));
        }catch(Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => ['Mail successfully sended']]);
    }

    public function userDetailsUpdate(Request $request, $username)
    {
        $request->merge(['username' => $username]);
        $validator = Validator::make($request->all(),[
            'username'              => "required|exists:hospitals,username",
            'hospital_name'         => "required|string|max:60",
            'mobile_code'           => "nullable|string|max:10",
            'mobile'                => "nullable|string|max:20",
            'address'               => "nullable|string|max:250",
            'country'               => "nullable|string|max:50",
            'state'                 => "nullable|string|max:50",
            'city'                  => "nullable|string|max:50",
            'zip_code'              => "nullable|numeric|max_digits:8",
            'email_verified'        => 'required|boolean',
            'two_factor_verified'   => 'required|boolean',
            'kyc_verified'          => 'required|boolean',
            'status'                => 'required|boolean',
        ]);
        $validated = $validator->validate();
        $validated['address']  = [
            'country'       => $validated['country'] ?? "",
            'state'         => $validated['state'] ?? "",
            'city'          => $validated['city'] ?? "",
            'zip'           => $validated['zip_code'] ?? "",
            'address'       => $validated['address'] ?? "",
        ];
        $validated['mobile_code']       = remove_speacial_char($validated['mobile_code']);
        $validated['mobile']            = remove_speacial_char($validated['mobile']);
        $validated['full_mobile']       = $validated['mobile_code'] . $validated['mobile'];

        $user = Hospital::where('username', $username)->first();
        if(!$user) return back()->with(['error' => ['Opps! User not exists']]);

        try {
            $user->update($validated);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Profile Information Updated Successfully!')]]);
    }

    public function loginLogs($username)
    {
        $page_title = __("Login Logs");
        $user = Hospital::where("username",$username)->first();
        if(!$user) return back()->with(['error' => ['Opps! User doesn\'t exists']]);
        $logs = HospitalLoginLog::where('hospital_id',$user->id)->paginate(12);
        return view('admin.sections.hospital-care.login-logs', compact(
            'logs',
            'page_title',
        ));
    }

    public function mailLogs($username) {
        $page_title = __("Hospital Email Logs");
        $hospital = Hospital::where("username",$username)->first();
        if(!$hospital) return back()->with(['error' => ['Opps! User doesn\'t exists']]);
        $logs = HospitalMailLog::where("hospital_id",$hospital->id)->paginate(12);
        return view('admin.sections.hospital-care.mail-logs',compact(
            'page_title',
            'logs',
        ));
    }

    public function loginAsMember(Request $request,$username) {
        $request->merge(['username' => $username]);
        $request->validate([
            'target'            => 'required|string|exists:hospitals,username',
            'username'          => 'required_without:target|string|exists:hospitals',
        ]);

        try{
            $user = Hospital::where("username",$request->username)->first();
            Auth::guard("hospital")->login($user);
        }catch(Exception $e) {
            return back()->with(['error' => [$e->getMessage()]]);
        }
        return redirect()->intended(route('hospitals.dashboard'));
    }

    public function kycDetails($username) {
        $users = Hospital::with('kyc')->where("username",$username)->first();
        if(!$users) return back()->with(['error' => ['Opps! User doesn\'t exists']]);

        $page_title = __("KYC Profile");
        return view('admin.sections.hospital-care.kyc-details',compact("page_title","users"));
    }

    public function kycApprove(Request $request, $username) {
        $request->merge(['username' => $username]);
        $request->validate([
            'target'        => "required|exists:hospitals,username",
            'username'      => "required_without:target|exists:hospitals,username",
        ]);
        $user = Hospital::where('username',$request->target)->orWhere('username',$request->username)->first();
        if($user->kyc_verified == GlobalConst::VERIFIED) return back()->with(['warning' => ['Hospital already KYC verified']]);
        if($user->kyc == null) return back()->with(['error' => ['User KYC information not found']]);

        try{
            $user->update([
                'kyc_verified'  => GlobalConst::APPROVED,
            ]);
        }catch(Exception $e) {
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
        return back()->with(['success' => [__('Hospital KYC successfully approved')]]);
    }

    public function kycReject(Request $request, $username) {
        $request->validate([
            'target'        => "required|exists:hospitals,username",
            'reason'        => "required|string|max:500"
        ]);
        $user = Hospital::where("username",$request->target)->first();
        if(!$user) return back()->with(['error' => ['Hospital doesn\'t exists']]);
        if($user->kyc == null) return back()->with(['error' => [__('Hospital KYC information not found')]]);

        try{
            $user->update([
                'kyc_verified'  => GlobalConst::REJECTED,
            ]);
            $user->kyc->update([
                'reject_reason' => $request->reason,
            ]);
        }catch(Exception $e) {
            $user->update([
                'kyc_verified'  => GlobalConst::PENDING,
            ]);
            $user->kyc->update([
                'reject_reason' => null,
            ]);

            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }

        return back()->with(['success' => [__('Hospital KYC information is rejected')]]);
    }


    public function search(Request $request) {
        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);

        if($validator->fails()) {
            $error = ['error' => $validator->errors()];
            return Response::error($error,null,400);
        }

        $validated = $validator->validate();
        $users = Hospital::search($validated['text'])->limit(10)->get();
        return view('admin.components.search.user-search',compact(
            'users',
        ));
    }

    public function walletBalanceUpdate(Request $request,$username) {
        $validator = Validator::make($request->all(),[
            'type'      => "required|string|in:add,subtract",
            'wallet'    => "required|numeric|exists:hospital_wallets,id",
            'amount'    => "required|numeric",
            'remark'    => "required|string|max:200",
        ]);

        if($validator->fails()) {
            return back()->withErrors($validator)->withInput()->with('modal','wallet-balance-update-modal');
        }

        $validated = $validator->validate();
        $user_wallet = HospitalWallet::whereHas('hospital',function($q) use ($username){
            $q->where('username',$username);
        })->find($validated['wallet']);
        if(!$user_wallet) return back()->with(['error' => [__("Hospital wallet not found!")]]);
        DB::beginTransaction();
        try{

            $user_wallet_balance = 0;

            switch($validated['type']){
                case "add":
                    $type = "Added";
                    $user_wallet_balance = $user_wallet->balance + $validated['amount'];
                    $user_wallet->balance += $validated['amount'];
                    break;

                case "subtract":
                    $type = "Subtracted";
                    if($user_wallet->balance >= $validated['amount']) {
                        $user_wallet_balance = $user_wallet->balance - $validated['amount'];
                        $user_wallet->balance -= $validated['amount'];
                    }else {
                        return back()->with(['error' => [__("Hospital do not have sufficient balance")]]);
                    }
                    break;
            }

            $inserted_id = DB::table("transactions")->insertGetId([
                'user_id'           => $user_wallet->hospital->id,
                'wallet_id'         => $user_wallet->id,
                'type'              => PaymentGatewayConst::TYPEADDSUBTRACTBALANCE,
                'trx_id'            => generate_unique_string("transactions","trx_id",16),
                'request_amount'    => $validated['amount'],
                'total_charge'      => $validated['amount'],
                'available_balance' => $user_wallet_balance,
                'remark'            => $validated['remark'],
                'status'            => GlobalConst::SUCCESS,
                'request_currency'  => $user_wallet->currency->code,
                'created_at'                    => now(),
            ]);

            $client_ip = request()->ip() ?? false;
            $location = geoip()->getLocation($client_ip);
            $agent = new Agent();

            // $mac = exec('getmac');
            // $mac = explode(" ",$mac);
            // $mac = array_shift($mac);
            $mac = "";

            $user_wallet->save();
            DB::commit();
        }catch(Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__("Transaction Failed!")]]);
        }

        return back()->with(['success' => [__("Transaction success")]]);
    }
     /**
     * Method for view create new user page
     * @return view
     */
    public function create()
    {
        $page_title             = __("Create New Hospital");

        return view('admin.sections.hospital-care.create', compact(
            'page_title'
        ));
    }

    /**
     * Method for store agent information
     * @param Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $basic_settings         = BasicSettings::first();

        $validator      = Validator::make($request->all(), [
            'hospital_name'       => 'required|string',
         
            'email'             => 'required|email',
            'password'          => 'required|min:8',
            'email_verified'    => 'nullable',
           
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }
        $validated              = $validator->validate();

        $user_name              = make_username_hospital(Str::slug($validated['hospital_name']),"hospitals");

        $validated['username']      = $user_name;
        $validated['password']      = Hash::make($validated['password']);
        $validated['status']        = true;
        try {
            $vendor = Hospital::create($validated);
            
            $currencies = Currency::active()->roleHasOne()->pluck("id")->toArray();
         
            $wallets = [];
            foreach ($currencies as $currency_id) {
                $wallets[] = [
                    'hospital_id'     => $vendor->id,
                    'currency_id'   => $currency_id,
                    'balance'       => 0,
                    'status'        => true,
                    'created_at'    => now(),
                ];
            }

            HospitalWallet::insert($wallets);
          
        } catch (Exception $e) {
            return back()->with(['error' => [__("Something went wrong! Please try again.")]]);
        }
        return redirect()->route('admin.hospitals.index')->with(['success' => [__('Hospital created successfully.')]]);
    }

}
