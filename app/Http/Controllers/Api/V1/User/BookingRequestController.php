<?php

namespace App\Http\Controllers\Api\V1\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\ServiceBooking;

class BookingRequestController extends Controller
{
    public function doctorBooking()
    {
        $doctorBooking = DoctorBooking::with('doctor')->where('user_id',auth()->user()->id)
            ->get();

        return Response::success(
            'Doctor Booking retrieved successfully',
            [
                'doctorBooking' => $doctorBooking
            ]
        );
    }

    public function serviceBooking()
    {
        $serviceBooking = ServiceBooking::with('hospital')->where('user_id',auth()->user()->id)
            ->get();

        return Response::success(
            'Service Booking retrieved successfully',
            [
                'serviceBooking' => $serviceBooking
            ]
        );
    }
}
