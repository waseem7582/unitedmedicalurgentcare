<?php

namespace App\Http\Controllers\Hospital;

use Exception;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Models\Admin\PaymentGateway;
use App\Models\Hospital\HospitalWallet;
use App\Constants\GlobalConst;
use App\Models\Hospital\Branch;
use App\Models\Hospital\Departments;
use App\Models\Hospital\Doctor;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Models\Hospital\Investigation;
use App\Models\Hospital\ServiceBooking;

class DashboardController extends Controller
{

    public function index()
    {
        $hospital_id      = auth()->user()->id;

        $page_title     = __("Dashboard");
        $complete_data  = [];
        $month_day      = [];

        $start          = strtotime(date('Y-m-01'));
        $end            = strtotime(date('Y-m-t'));

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);

            $complete = DoctorBooking::where('status', GlobalConst::STATUS_SUCCESS)
                ->where('hospital_id', $hospital_id)
                ->whereDate('created_at', $start_date)
                ->count();

            $complete_data[]  = $complete;
            $month_day[] = date('Y-m-d', $start);
            $start = strtotime('+1 day', $start);
        }
        // Chart one
        $chart_one_data = [
            'complete_data'  => $complete_data,
        ];

        $data = [
            'chart_one_data'         => $chart_one_data,
            'month_day'              => $month_day,
        ];

        $doctor = Doctor::auth()->get();

        $total_doctor       = $doctor->count();
        $total_service      = $doctor->pluck('services')->flatten()->count();
        $total_branch       = Branch::auth()->count();
        $total_departments  = Departments::auth()->count();
        $hospital_wallet    = HospitalWallet::where('hospital_id',auth()->user()->id)->first();

        $hospital_offline_wallet    = HospitalOfflineWallet::where('hospital_id',auth()->user()->id)->first();
        $total_service_booking      = ServiceBooking::where('hospital_id',auth()->user()->id)->count();
        $total_service              = Investigation::where('hospital_id',auth()->user()->id)->count();


        return view('hospital.dashboard', compact(
            "page_title",
            "data",
            'total_doctor',
            'hospital_wallet',
            'total_branch',
            'total_departments',
            'hospital_offline_wallet',
            'total_service_booking',
            'total_service'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('hospitals.login');
    }




    public function deleteAccount(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'target'        => 'required',
        ]);
        $validated = $validator->validate();
        $user = auth()->user();
        try {
            $user->status = 0;
            $user->save();
            Auth::logout();
            return redirect()->route('frontend.index')->with(['success' => ['Your account deleted successfully!']]);
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again')]]);
        }
    }
}
