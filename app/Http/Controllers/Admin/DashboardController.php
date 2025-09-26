<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Http\Controllers\Controller;
use App\Providers\Admin\BasicSettingsProvider;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Pusher\PushNotifications\PushNotifications;
use App\Models\Admin\AdminNotification;
use App\Constants\NotificationConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Blog;
use App\Models\User;
use App\Models\UserSupportTicket;
use App\Models\Admin\BlogCategory;
use App\Constants\SupportTicketConst;
use App\Models\Hospital\Doctor;
use App\Models\Hospital\DoctorBooking;

class DashboardController extends Controller
{

    public function getAllMonthNames(){
        $monthNames = collect([]);

        for ($monthNumber = 1; $monthNumber <= 12; $monthNumber++) {
            $monthName = Carbon::createFromDate(null, $monthNumber, null)->format('M');
            $monthNames->push($monthName);
        }

        return $monthNames;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title         = "Dashboard";
        $last_month_start   = date('Y-m-01', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $last_month_end     = date('Y-m-31', strtotime('-1 month', strtotime(date('Y-m-d'))));
        $this_month_start   = date('Y-m-01');
        $this_month_end     = date('Y-m-d');

        $total_users     = (User::toBase()->count() == 0) ? 1 : User::toBase()->count();
        $unverified_user = User::toBase()->where('email_verified',0)->count();
        $active_user     = User::toBase()->where('status',true)->count();
        $banned_user     = User::toBase()->where('status',false)->count();
        $user_percent    = (($active_user * 100 ) / $total_users);

        if ($user_percent > 100) {
            $user_percent = 100;
        }

        $total_doctors     = (Doctor::toBase()->count() == 0) ? 1 : Doctor::toBase()->count();
        $active_doctor     = Doctor::toBase()->where('status',true)->count();
        $pending_doctor    = Doctor::toBase()->where('status',false)->count();
        $doctor_percent    = (($active_doctor * 100) / $total_doctors);

        if($doctor_percent > 100){
            $doctor_percent = 100;
        }


        $total_bookings     = (DoctorBooking::toBase()

                                ->count() == 0) ? 1 : DoctorBooking::toBase()->count();
        $pending_booking    = DoctorBooking::toBase()->where('status',global_const()::STATUS_PENDING) ->count();
        $confirm_booking    = DoctorBooking::toBase()->where('status',global_const()::STATUS_SUCCESS)->count();
        $booking_percent    = ((($pending_booking + $confirm_booking) * 100) / $total_bookings);

        if($booking_percent > 100){
            $booking_percent  = 100;
        }

        $total_money        = DoctorBooking::toBase()
                                ->sum('price');
        $total_charges      = DoctorBooking::toBase()
                                ->sum('total_charge');

        $this_month_money = DoctorBooking::toBase()
                            ->whereDate('created_at',">=" , $this_month_start)
                            ->whereDate('created_at',"<=" , $this_month_end)
                            ->sum('price');


        $last_month_money = DoctorBooking::toBase()
                            ->whereDate('created_at',">=" , $last_month_start)
                            ->whereDate('created_at',"<=" , $last_month_end)
                            ->sum('price');

        $this_month_charge = DoctorBooking::toBase()

                            ->whereDate('created_at',">=" , $this_month_start)
                            ->whereDate('created_at',"<=" , $this_month_end)
                            ->sum('total_charge');

        $last_month_charge = DoctorBooking::toBase()

                            ->whereDate('created_at',">=" , $last_month_start)
                            ->whereDate('created_at',"<=" , $last_month_end)
                            ->sum('total_charge');

        $total_ticket       = (UserSupportTicket::toBase()->count() == 0) ? 1 : UserSupportTicket::toBase()->count();
        $active_ticket      = UserSupportTicket::toBase()->where('status',SupportTicketConst::ACTIVE)->count();
        $pending_ticket     = UserSupportTicket::toBase()->where('status',SupportTicketConst::PENDING)->count();

        if($pending_ticket == 0 && $active_ticket != 0){
            $percent_ticket = 100;
        }elseif($pending_ticket == 0 && $active_ticket == 0){
            $percent_ticket = 0;
        }else{
            $percent_ticket = ($active_ticket / ($active_ticket + $pending_ticket)) * 100;
        }
        $user_chart = [$active_user, $banned_user,$unverified_user,$total_users];
        $start = strtotime(date('Y-m-01'));
        $end = strtotime(date('Y-m-31'));


        $pending_data  = [];
        $complete_data  = [];
        $month_day  = [];

        while ($start <= $end) {
            $start_date = date('Y-m-d', $start);


            $pending = DoctorBooking::where('status',global_const()::STATUS_PENDING)
                                        ->whereDate('created_at',$start_date)
                                        ->count();
            $complete = DoctorBooking::where('status',global_const()::STATUS_SUCCESS)
                                        ->whereDate('created_at',$start_date)
                                        ->count();

            $pending_data[]  = $pending;
            $complete_data[]  = $complete;
            $month_day[] = date('Y-m-d', $start);
            $start = strtotime('+1 day',$start);
        }
        // Chart one
        $chart_one_data = [
            'pending_data'  => $pending_data,
            'complete_data'  => $complete_data,

        ];
        $booking_data               = DoctorBooking::    latest()->take(3)->get();



        $total_categories           = (BlogCategory::toBase()->count() == 0) ? 1 : BlogCategory::toBase()->count();
        $active_category            = BlogCategory::toBase()->where('status',true)->count();
        $inactive_category          = BlogCategory::toBase()->where('status',false)->count();
        $category_percent           = (($active_category * 100) / $total_categories);

        if($category_percent > 100){
            $category_percent = 100;
        }

        $total_blogs           = (Blog::toBase()->count() == 0) ? 1 : Blog::toBase()->count();
        $active_blog            = Blog::toBase()->where('status',true)->count();
        $inactive_blog          = Blog::toBase()->where('status',false)->count();
        $blog_percent           = (($active_blog * 100) / $total_blogs);

        if($blog_percent > 100){
            $blog_percent = 100;
        }

        $data                       = [
            'unverified_user'       => $unverified_user,
            'active_user'           => $active_user,
            'user_percent'          => $user_percent,
            'total_user_count'      => User::all()->count(),
            'user_chart_data'       => $user_chart,

            'active_doctor'        => $active_doctor,
            'pending_doctor'       => $pending_doctor,
            'doctor_percent'       => $doctor_percent,
            'total_doctor_count'   => Doctor::all()->count(),

            'pending_booking'        => $pending_booking,
            'confirm_booking'        => $confirm_booking,
            'booking_percent'        => $booking_percent,
            'total_booking_count'    => DoctorBooking::all()->count(),
            'chart_one_data'         => $chart_one_data,
            'month_day'              => $month_day,
            'total_money'           => $total_money,
            'total_charges'         => $total_charges,
            'this_month_money'      => $this_month_money,
            'last_month_money'      => $last_month_money,
            'this_month_charge'     => $this_month_charge,
            'last_month_charge'     => $last_month_charge,

            'active_ticket'         => $active_ticket,
            'pending_ticket'        => $pending_ticket,
            'percent_ticket'        => $percent_ticket,
            'total_ticket_count'    => UserSupportTicket::all()->count(),

            'active_category'        => $active_category,
            'inactive_category'      => $inactive_category,
            'category_percent'       => $category_percent,
            'total_category_count'   => BlogCategory::all()->count(),

            'active_blog'            => $active_blog,
            'inactive_blog'          => $inactive_blog,
            'blog_percent'           => $blog_percent,
            'total_blog_count'       => Blog::all()->count(),

        ];
        $months = $this->getAllMonthNames();

        return view('admin.sections.dashboard.index',compact(
            'page_title',
            'data',
            'months',
            'booking_data',
        ));
    }


    /**
     * Logout Admin From Dashboard
     * @return view
     */
    public function logout(Request $request) {

        $admin = auth()->user();
        pusher_unsubscribe("admin", $admin->id);

        try{
            $admin->update([
                'last_logged_out'   => now(),
                'login_status'      => false,
            ]);
        }catch(Exception $e) {
            // Handle Error
        }

        Auth::guard()->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Function for clear admin notification
     */
    public function notificationsClear() {
        $admin = auth()->user();

        if(!$admin) {
            return false;
        }

        try{
            $admin->update([
                'notification_clear_at'     => now(),
            ]);
        }catch(Exception $e) {
            $error = ['error' => [__('Something went wrong! Please try again.')]];
            return Response::error($error,null,404);
        }

        $success = ['success' => [__('Notifications clear successfully!')]];
        return Response::success($success,null,200);
    }
}
