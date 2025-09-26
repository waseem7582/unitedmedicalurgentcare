<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use App\Http\Controllers\Controller;
use App\Models\Hospital\DoctorBooking;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\HospitalOfflineWallet;

class BookingRequestController extends Controller
{
    public function index()
    {
        $bookings = DoctorBooking::auth()->with('doctor')
            ->get();

        return Response::success(
            'Booking requests retrieved successfully',
            [
                'bookings' => $bookings
            ]
        );
    }

    public function bookingDetails(Request $request)
    {
        $booking = DoctorBooking::auth()
            ->where('uuid', $request->uuid)
            ->first();

        if (!$booking) {
            return Response::error(
                'Booking not found',
                null,
                404
            );
        }

        return Response::success(
            'Booking details retrieved successfully',
            [
                'booking' => $booking
            ]
        );
    }

    public function bookingUpdate(Request $request)
    {
        $booking = DoctorBooking::where('uuid', $request->uuid)->first();

        if (!$booking) {
            return Response::error(
                'Booking not found',
                null,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|integer|in:0,1,2,3', // Example status codes
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422
            );
        }

        $validated = $validator->validated();
        $basicSetting = BasicSettings::first();
        $amount = $booking->price;

        DB::beginTransaction();

        try {
            // Update booking status
            $booking->update([
                'status' => $validated['status'],
                'updated_at' => now()
            ]);

            // Handle cash payment wallet update
            if ($booking->payment_method == 'cash') {
                $wallet = HospitalOfflineWallet::auth()->first();

                if (!$wallet) {
                    throw new Exception('Hospital wallet not found');
                }
                if ($request->status == GlobalConst::STATUS_SUCCESS) {

                    $wallet->update([
                        'balance' => $wallet->balance + $amount,
                        'updated_at' => now()
                    ]);
                }
            }

            DB::commit();

            return Response::success(
                'Booking status updated successfully',
                [
                    'booking' => $booking->fresh(),
                    'wallet_updated' => $booking->payment_method == 'cash'
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update booking',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
