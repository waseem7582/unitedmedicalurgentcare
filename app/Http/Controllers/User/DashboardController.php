<?php

namespace App\Http\Controllers\User;

use App\Models\Admin\Area;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Hospital\Doctor;
use App\Models\Hospital\Hospital;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\ServiceBooking;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class DashboardController extends Controller
{
    public function index(BasicSettings $basic_settings)
    {

        $page_title                     = __("Dashboard");

        $total_transactions             = DoctorBooking::where('user_id', auth()->user()->id)->count();
        $total_service_booking          = ServiceBooking::where('user_id', auth()->user()->id)->count();
        $total_online_transactions      = DoctorBooking::where('user_id', auth()->user()->id)->whereNot('payment_method', GlobalConst::CASH_PAYMENT)->with(['doctor', 'schedule', 'payment_gateway', 'user'])->count();
        $total_cash_transactions        = DoctorBooking::where('user_id', auth()->user()->id)->where('payment_method', GlobalConst::CASH_PAYMENT)->with(['doctor', 'schedule', 'payment_gateway', 'user'])->count();
        $total_transactions_amount      = DoctorBooking::where('user_id', auth()->user()->id)->where('payment_method', GlobalConst::CASH_PAYMENT)->with(['doctor', 'schedule', 'payment_gateway', 'user'])->sum('price');
        $booking_data                   = DoctorBooking::where('user_id', auth()->user()->id)->with(['doctor', 'schedule', 'payment_gateway', 'user'])
            ->orderByDesc('id')
            ->paginate(10);

        $complete_data                  = [];
        $month_day                      = [];

        $start                          = strtotime(date('Y-m-01'));
        $end                            = strtotime(date('Y-m-t'));

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);

            $complete = DoctorBooking::where('status', global_const()::STATUS_SUCCESS)
                ->where('user_id', auth()->user()->id)
                ->whereDate('created_at', $start_date)
                ->count();

            $complete_data[]     = $complete;
            $month_day[]         = date('Y-m-d', $start);
            $start               = strtotime('+1 day', $start);
        }
        // Chart one
        $chart_one_data = [
            'complete_data'  => $complete_data,
        ];

        $data = [
            'chart_one_data'         => $chart_one_data,
            'month_day'              => $month_day,
        ];


        return view('user.dashboard', compact(
            "page_title",
            'data',
            "booking_data",
            "total_transactions",
            "total_online_transactions",
            "total_cash_transactions",
            "total_transactions_amount",
            'total_service_booking'
        ));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');
    }

    /**
     * Method for view the findDoctor page
     * @return view
     */
    public function findDoctor(Request $request)
    {
        $page_title             = __("Find Doctor");
        $hospital               = Hospital::with('branch.departments')->where('status', true)->get();
        $doctor                 = Doctor::where('status', true)->paginate(6);

        $message                = Session::get('message');

        $validator = Validator::make($request->all(), [
            'hospital'        => 'nullable',
            'branch'          => 'nullable',
            'department'      => 'nullable',
            'name'            => 'nullable',
        ]);
        if ($validator->fails()) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        if ($request->hospital && $request->branch && $request->department && $request->name) {
            $doctor    = Doctor::where('hospital_id', $request->hospital)->where('branch_id', $request->branch)->where('department_id', $request->department)->where('name', 'like', '%' . $request->name . '%')->get();
        } else {
            $doctor    = Doctor::where('name', 'like', '%' . $request->name . '%')->get();
        }


        $hospitalString      = $request->hospital;
        $nameString          = $request->name;
        return view('user.sections.find-parlour.index', compact(
            'page_title',
            'hospitalString',
            'nameString',
            'hospital',
            'doctor',
            'message'
        ));
    }
}
