<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Models\User;
use Carbon\CarbonPeriod;
use App\Models\Transaction;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Carbon;
use App\Models\Hospital\Doctor;
use App\Models\UserNotification;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\HealthPackage;
use App\Models\Hospital\ServiceBooking;
use App\Providers\Admin\CurrencyProvider;
use App\Models\Hospital\DoctorHasSchedule;
use App\Http\Helpers\Api\Helpers as ApiResponse;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $default_currency = CurrencyProvider::default();
        $total_transactions             = DoctorBooking::where('user_id', auth()->user()->id)->count();
        $total_online_transactions      = DoctorBooking::where('user_id', auth()->user()->id)->whereNot('payment_method', GlobalConst::CASH_PAYMENT)->count();
        $total_cash_transactions        = DoctorBooking::where('user_id', auth()->user()->id)->where('payment_method', GlobalConst::CASH_PAYMENT)->count();
        $total_transactions_amount      = DoctorBooking::where('user_id', auth()->user()->id)->where('payment_method', GlobalConst::CASH_PAYMENT)->sum('price');
        $total_service_booking          = ServiceBooking::where('user_id', auth()->user()->id)->count();
        // Transaction logs
        $transactions = Transaction::auth()->latest()->take(10)->get();
        $transactions->makeHidden([
            'id',
            'user_type',
            'user_id',
            'wallet_id',
            'payment_gateway_currency_id',
            'request_amount',
            'exchange_rate',
            'percent_charge',
            'fixed_charge',
            'total_charge',
            'total_payable',
            'receiver_type',
            'receiver_id',
            'available_balance',
            'payment_currency',
            'input_values',
            'details',
            'reject_reason',
            'remark',
            'stringStatus',
            'callback_ref',
            'updated_at',
        ]);

        // Chart Data
        $monthly_day_list = CarbonPeriod::between(now()->startOfDay()->subDays(30), today()->endOfDay())->toArray();
        $define_day_value = array_fill_keys(array_values($monthly_day_list), "0.00");

        // User Information
        $user_info = auth()->user()->only([
            'id',
            'firstname',
            'lastname',
            'fullname',
            'username',
            'email',
            'image',
            'mobile_code',
            'mobile',
            'full_mobile',
            'email_verified',
            'two_factor_verified',
            'two_factor_status',
            'two_factor_secret',
        ]);

        $profile_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("user-profile"),
            'default_image'     => files_asset_path_basename("profile-default"),
        ];

        // chart data

        $start              = strtotime(date('Y-m-01'));
        $end                = strtotime(date('Y-m-t'));

        $complete_data      = [];
        $month_day          = [];

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);

            $complete = DoctorBooking::where('status', GlobalConst::STATUS_SUCCESS)
                ->where('user_id', auth()->user()->id)
                ->whereDate('created_at', $start_date)
                ->count();

            $complete_data[]    = $complete;
            $month_day[]        = $start_date;
            $start              = strtotime('+1 day', $start);
        }

        $chart_one_data          = ['complete_data' => $complete_data];
        $data                    = ['chart_one_data' => $chart_one_data, 'month_day' => $month_day];





        return Response::success([__('User dashboard data fetch successfully!')], [
            'instructions'  => [
                'transaction_types' => [
                    PaymentGatewayConst::PAYMENTMETHOD,
                ],
                'recent_transactions'   => [
                    'status'        => '1: Success, 2: Pending',
                ],
            ],

            'user_info'     => $user_info,
            'recent_transactions'   => $transactions,
            'chart_data'        => $data,
            'total_transactions'    => $total_transactions,
            'total_online_transactions' => $total_online_transactions,
            'total_cash_transactions'   => $total_cash_transactions,
            'total_transactions_amount' => $total_transactions_amount,
            'profile_image_paths'   => $profile_image_paths,
            'profile_image_paths'   => $profile_image_paths,
            'total_service_booking' => $total_service_booking,
        ]);
    }

    public function home()
    {
        $today = Carbon::today()->format('Y-m-d');
        $currentTime = Carbon::now()->format('H:i:s');
        $recent_booking = DoctorBooking::with('doctor')->where('user_id', auth()->user()->id)->latest()->take(5)->get();

        $doctorBookings = DoctorBooking::with(['doctor', 'schedule'])
            ->where('date', '>=', $today)
            ->whereHas('schedule', function ($query) use ($today, $currentTime) {
                $query->where(function ($q) use ($today, $currentTime) {
                    $q->whereRaw("(doctor_bookings.date > ?)", [$today])
                        ->orWhereRaw("(doctor_bookings.date = ? AND doctor_has_schedules.from_time > ?)", [$today, $currentTime]);
                });
            })
            ->where('user_id', auth()->id())
            ->orderBy('date', 'asc')
            ->orderBy(
                DoctorHasSchedule::select('from_time')
                    ->whereColumn('doctor_has_schedules.id', 'doctor_bookings.schedule_id')
                    ->limit(1)
            )
            ->take(1)
            ->get();


        $serviceBookings = ServiceBooking::where('date', '>=', $today)
            ->where('user_id', auth()->id())
            ->where(function ($query) use ($today, $currentTime) {
                $query->where('date', '>', $today)
                    ->orWhere(function ($q) use ($today, $currentTime) {
                        $q->where('date', '=', $today)
                            ->where('booking_data->data->time', '>', $currentTime);
                    });
            })
            ->orderBy('date', 'asc')
            ->orderByRaw("JSON_UNQUOTE(JSON_EXTRACT(booking_data, '$.data.time')) ASC")
            ->take(1)
            ->get();

        $serviceBookings = $serviceBookings->map(function ($booking) {
            $booking->doctor = null;
            return $booking;
        });


        $allBookings = $serviceBookings->concat($doctorBookings);

        $allBookings = $allBookings->sortBy('date');

        $allBookings = $allBookings->values();


        $user_profile   = auth()->user();
        $health_package = HealthPackage::where('status', true)->get();
        $doctor_list    = Doctor::where('status', true)->get();
        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("doctor"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success('Home data fetched successfully.', [
            'currency symbol'  => get_default_currency_symbol(),
            'currency code'    => get_default_currency_code(),
            'doctor_image_paths'     => $image_paths,
            'user'            => $user_profile,
            'recent_booking'  => $recent_booking,
            'upcoming_appointment'  => $allBookings,
            'health_package'  => $health_package,
            'doctor_list'     => $doctor_list,
        ]);
    }

    public function notifications()
    {
        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("site-section"),
            'default_image'     => files_asset_path_basename("default"),
        ];
        $notifications  = UserNotification::auth()->get();

        $message =  ['success' => [__('Notification data  fetch successfully!')]];
        $data = [
            'image_path'        => $image_paths,
            'notification_data' => $notifications,

        ];
        return ApiResponse::success($message, $data);
    }
}
