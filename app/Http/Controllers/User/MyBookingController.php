<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Helpers\Response;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\ServiceBooking;
use Illuminate\Http\Request;
use App\Providers\Admin\BasicSettingsProvider;
use Illuminate\Support\Facades\Validator;

class MyBookingController extends Controller
{
    /**
     * Method for view the users history page
     */
    public function index(BasicSettingsProvider  $basic_settings){
        $page_title             = __("My Bookings");
        $transactions           = DoctorBooking::where('user_id',auth()->user()->id)->with(['doctor','schedule','payment_gateway','user'])
                                ->orderByDesc('id')
                                ->paginate(10);

        return view('user.sections.my-booking.index',compact(
            'page_title',
            'transactions',
        ));
    }
    /**
     * Method for showing the history details
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function details(Request $request,$slug){
        $page_title     = __("Booking Details");
        $user_id        = auth()->user()->id;
        $data           = DoctorBooking::where('user_id',$user_id)->with(['doctor','schedule','payment_gateway'])->where('slug',$slug)->first();

        return view('user.sections.my-booking.details',compact(
            'page_title',
            'data',
        ));
    }
    /**
    * Method for search booking log
    */
    public function search(Request $request){

        $validator = Validator::make($request->all(),[
            'text'  => 'required|string',
        ]);
        if($validator->fails()) {
            return Response::error($validator->errors()->all(),[],400);
        }

        $validated = $validator->validate();

        $transactions    = DoctorBooking::auth()->with(['doctor','schedule','payment_gateway'])->search($validated['text'])->get();


        return view('user.components.data-table.doctor-booking-table',compact('transactions'));

    }
    /**
     * Method for view the users history page
     */
    public function service(BasicSettingsProvider  $basic_settings){
        $page_title             = __("My Bookings");
        $transactions           = ServiceBooking::where('user_id',auth()->user()->id)->with(['user','hospital'])
                                ->orderByDesc('id')
                                ->paginate(10);

        return view('user.sections.service-booking.index',compact(
            'page_title',
            'transactions',
        ));
    }
}

