<?php

namespace App\Http\Controllers\Hospital;

use Exception;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Hospital\Doctor;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Models\Hospital\HospitalWallet;
use App\Models\Hospital\ServiceBooking;
use Illuminate\Support\Facades\Validator;

class BookingRequestController extends Controller
{
    public function index()
    {
        $page_title         = __('Booking Request');
        $booking_data       = DoctorBooking::auth()->with('doctor')->orderByDesc('id')->paginate(5);
        return view('hospital.sections.booking-request.index', compact(
            'page_title',
            'booking_data'
        ));
    }


    public function bookingDetails($uuid)
    {
        $page_title   = __("Details");
        $booking_data = DoctorBooking::auth()->with('doctor')
            ->where('uuid', $uuid)
            ->orderByDesc('id')
            ->first();

        return view('hospital.sections.booking-request.details', compact(
            'page_title',
            'booking_data',
        ));
    }

    public function bookingUpdate(Request $request, $uuid)
    {
        $data           = DoctorBooking::where('uuid', $uuid)->first();

        if (!$data) return back()->with(['error' =>  ['Data Not Found!']]);

        $validator      = Validator::make($request->all(), [
            'status'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(['error' => $validator->errors()]);
        }
        $validated     =  $validator->validate();
        $basic_setting = BasicSettings::first();

        $amount        = $data->price;
        $wallet        = HospitalOfflineWallet::auth()->first();

        $balance = $wallet->balance;
        try {
            $data->update([
                'status'    => $validated['status'],
            ]);

            if ($data->payment_method == 'cash') {
                $wallet->update([
                    'balance'    => $balance + $amount,
                ]);
            }
        } catch (Exception $e) {

            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return redirect()->route('hospitals.booking.request.index')->with(['success'  => ['Booking Status Updated Successfully.']]);
    }

    public function homeService()
    {
        $page_title         = __('Home Service Request');
        $booking_data       = ServiceBooking::auth()->orderByDesc('id')->paginate(5);;
        return view('hospital.sections.home-service-request.index', compact(
            'page_title',
            'booking_data'
        ));
    }

    public function serviceDetails($uuid)
    {
        $page_title   = __("Details");
        $booking_data = ServiceBooking::auth()->with('hospital')
            ->where('uuid', $uuid)
            ->orderByDesc('id')
            ->first();

        return view('hospital.sections.home-service-request.details', compact(
            'page_title',
            'booking_data',
        ));
    }

    public function serviceUpdate(Request $request, $uuid)
    {
        $data           = ServiceBooking::where('uuid', $uuid)->first();

        if (!$data) return back()->with(['error' =>  ['Data Not Found!']]);

        $validator      = Validator::make($request->all(), [
            'status'    => 'required|integer',
        ]);

        if ($validator->fails()) {
            return Response::error(['error' => $validator->errors()]);
        }
        $validated     =  $validator->validate();
        $basic_setting = BasicSettings::first();

        $amount        = $data->price;
        $wallet        = HospitalOfflineWallet::auth()->first();

        $balance = $wallet->balance;

        try {
            $data->update([
                'status'    => $validated['status'],
            ]);

            if ($data->payment_method == 'cash') {
                $wallet->update([
                    'balance'    => $balance + $amount,
                ]);
            }
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success'  => ['Booking Status Updated Successfully.']]);
    }
}
