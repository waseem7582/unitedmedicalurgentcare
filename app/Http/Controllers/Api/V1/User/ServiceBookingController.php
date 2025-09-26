<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Hospital\Hospital;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Admin\BookingTempData;
use App\Constants\PaymentGatewayConst;
use App\Models\Hospital\Investigation;
use App\Models\Hospital\ServiceBooking;
use App\Notifications\EmailNotification;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Notification;

class ServiceBookingController extends Controller
{
    public function checkout(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'hospital_id'    => 'nullable',
            'schedule_id'    => 'nullable',
            'gender'         => 'required|string',
            'age'            => 'required|string',
            'shift'          => 'required|string',
            'time'           => 'required|string',
            'age_type'       => 'required|string',
            'number'         => 'required|integer',
            'email'          => 'required|string|email',
            'date'           => 'required|date_format:Y-m-d|after_or_equal:today',
            'investigations' => 'required|array',
            'message'        => 'nullable'
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all());
        }

        $validated = $validator->validated();

        $already_appointed = ServiceBooking::where('hospital_id', $request->hospital_id)->where('date', $request->date)->where('booking_data->data->time', $request->time)->count();

        if ($already_appointed > 0) {
            return Response::error([__('already booked on this schedule!')]);
        }

        // Fetch investigations and calculate total price
        $investigations = Investigation::whereIn("id", $validated["investigations"])->get();

        if ($investigations->isEmpty()) {
            return Response::error(['Invalid investigation(s) selected.']);
        }

        $price = $investigations->sum(function ($investigation) {
            return $investigation->offer_price ?? $investigation->regular_price;
        });

        $validated['price']        = $price;
        $validated['user_id']      = auth()->id();
        $validated['slug']         = Str::slug($validated['name']);
        $validated['uuid']         = Str::uuid();
        $validated['data']         = $validated;

        try {
            $booking = BookingTempData::create($validated);
        } catch (Exception $e) {
            return Response::error(['Something went wrong! Please try again.']);
        }

        return Response::success(['Booking created successfully.'], [
            'uuid' => $booking->uuid,
            'price' => $booking->price,
            'slug' => $booking->slug,
            'data' => $booking->data,
        ]);
    }

    public function bookingConfirm($uuid)
    {
        $booking = BookingTempData::where('uuid', $uuid)->first();

        if (!$booking) {
            return Response::error(['Booking not found.'], null, 404);
        }

        $hospital      = Hospital::find($booking->data->hospital_id);
        $otp_exp_sec   = GlobalConst::BOOKING_EXP_SEC;
        $basic_setting = BasicSettings::first();
        $user          = auth()->user();

        if (!$user) {
            return Response::error(['Unauthorized. Please login first.'], null, 401);
        }

        // Check if booking has expired
        if ($booking->created_at->addSeconds($otp_exp_sec)->lt(now())) {
            $booking->delete();
            return Response::error(['Booking Time Out!']);
        }

        try {
            $trx_id = generateTrxString('doctor_bookings', 'trx_id', 'PB', 8);

            ServiceBooking::create([
                'trx_id'          => $trx_id,
                'hospital_id'     => $booking->data->hospital_id,
                'booking_data'    => ['data' => $booking->data],
                'payment_method'  => GlobalConst::CASH_PAYMENT,
                'date'            => $booking->data->date,
                'slug'            => $booking->slug,
                'uuid'            => str::uuid(),
                'type'            => GlobalConst::CASH_PAYMENT,
                'user_id'         => $user->id,
                'total_charge'    => null,
                'price'           => $booking->data->price,
                'payable_price'   => null,
                'remark'          => GlobalConst::CASH_PAYMENT,
                'status'          => PaymentGatewayConst::STATUS_PENDING,
            ]);

        } catch (Exception $e) {
            return Response::error(['Something went wrong! Please try again.']);
        }

        return Response::success(['Booking confirmed successfully.'], [
            'trx_id' => $trx_id,
            'uuid' => $booking->uuid,
            'hospital' => $hospital->hospital_name ?? null,
            'price' => $booking->data->price,
            'date' => $booking->data->date,
        ]);
    }

    public function homeService()
    {
        try {
            $investigations = Investigation::join('investigation_has_categories', 'investigations.id', '=', 'investigation_has_categories.investigation_id')
            ->join('investigation_categories', 'investigation_has_categories.investigation_category_id', '=', 'investigation_categories.id')
            ->join('hospitals', 'investigations.hospital_id', '=', 'hospitals.id')
            ->where('investigation_categories.id', GlobalConst::Home_Service)
            ->select(
                'investigations.id',
                'investigations.name',
                'hospitals.id as hospital_id',
                'hospitals.hospital_name as hospital_name',
                'hospitals.address as hospital_address' // Add more fields if needed
            )
            ->get();

        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }

        return Response::success([__("Home Service data fetch successfully!")], [
            'homeService' => $investigations,
        ], 200);
    }
}
