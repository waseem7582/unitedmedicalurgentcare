<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use App\Constants\GlobalConst;
use App\Models\Hospital\Branch;
use App\Models\Hospital\Doctor;
use App\Http\Controllers\Controller;
use App\Models\Hospital\Departments;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\HospitalWallet;
use App\Models\Hospital\ServiceBooking;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Http\Helpers\Api\Helpers as ApiResponse;
use App\Models\Hospital\Investigation;

class DashboardController extends Controller
{
    public function dashboard()
    {
        try {
            $hospital_id        = auth()->user()->id;
            $start              = strtotime(date('Y-m-01'));
            $end                = strtotime(date('Y-m-t'));

            $complete_data      = [];
            $month_day          = [];

            while ($start <= $end) {
                $start_date = date('Y-m-d', $start);

                $complete = DoctorBooking::where('status', GlobalConst::STATUS_SUCCESS)
                    ->auth()
                    ->whereDate('created_at', $start_date)
                    ->count();

                $complete_data[]    = $complete;
                $month_day[]        = $start_date;
                $start              = strtotime('+1 day', $start);
            }

            $chart_one_data          = ['complete_data' => $complete_data];
            $data                    = ['chart_one_data' => $chart_one_data, 'month_day' => $month_day];

            $doctor                  = Doctor::auth()->get();
            $total_doctor            = $doctor->count();
            $total_service           = $doctor->pluck('services')->flatten()->count();


            $doctor = Doctor::auth()->get();

            $total_doctor       = $doctor->count();
            $total_branch       = Branch::auth()->count();
            $total_departments  = Departments::auth()->count();
            $hospital_wallet    = HospitalWallet::auth()->first();

            $hospital_offline_wallet    = HospitalOfflineWallet::auth()->first();
            $total_service_booking      = ServiceBooking::auth()->count();
            $total_service              = Investigation::auth()->count();


            $total_online_transactions = DoctorBooking::where('hospital_id', $hospital_id)
                ->whereNot('payment_method', GlobalConst::CASH_PAYMENT)
                ->count();

            $hospital_wallet = HospitalWallet::auth()->first();

            if ($total_doctor == 0 && $total_service == 0 && $total_online_transactions == 0) {
                $response_data = [
                    'total_doctor'               => null,
                    'total_service'              => null,
                    'total_online_transactions'  => null,
                    'total_branch'               => null,
                    'total_departments'          => null,
                    'hospital_offline_wallet'    => null,
                    'total_service_booking'      => null,
                    'hospital_wallet'            => null,
                    'chart_data'                 => null,
                ];
            }


            $user = authGuardApi()['user'];

            $response_data = [
                'hospital_name'    => auth()->user()->hospital_name,
                'default_image' => "backend/images/default/profile-default.webp",
                "image_path"    => "frontend/user",
                "base_ur"       => url('/'),
                'hospital_image'          => $user->image,
                'total_doctor' => $total_doctor,
                'total_branch' => $total_branch,
                'total_departments' => $total_departments,
                'hospital_offline_wallet' => $hospital_offline_wallet,
                'total_service' => $total_service,
                'total_online_transactions' => $total_online_transactions,
                'total_service_booking' => $total_service_booking,
                'hospital_wallet' => $hospital_wallet,
                'chart_data' => $data
            ];

            $message = ['success' => [__('Hospital Dashboard')]];
            return ApiResponse::success($message, $response_data);
        } catch (\Exception $e) {
            $message = ['error' => [__('Something went wrong! Please try again')]];
            return ApiResponse::onlyError($message);
        }
    }


    function notification()
    {
        $hospital_id = auth()->user()->id;
        $notifications = DoctorBooking::where('hospital_id', $hospital_id)
            ->with('doctor', 'user')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($notification) {
                $message = [
                    'title' => 'Your Booking',
                    'success' => 'Successfully Booked.'
                ];

                if ($notification->doctor) {
                    $message['doctor'] = $notification->doctor->name;
                }

                if ($notification->date) {
                    $message['date'] = $notification->date;
                }

                if ($notification->schedule) {
                    $message['from_time'] = $notification->schedule->from_time;
                    $message['to_time'] = $notification->schedule->to_time;
                }

                return [
                    'id' => $notification->id,
                    'user_id' => $notification->user_id,
                    'message' => $message,
                    'created_at' => $notification->created_at,
                    'updated_at' => $notification->updated_at
                ];
            });

        $message = ['success' => [__('Notification data fetch successfully!')]];

        $data = [
            'image_path' => [
                'base_url' => config('app.url'),
                'path_location' => 'public/frontend/images/site-section',
                'default_image' => 'public/backend/images/default/default.webp'
            ],
            'notification_data' => $notifications
        ];

        return ApiResponse::success($message, $data);
    }
}
