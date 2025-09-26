<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use App\Constants\GlobalConst;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Models\Admin\BasicSettings;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\HospitalOfflineWallet;
use App\Models\Hospital\ServiceBooking;
use Exception;

class ServiceRequestController extends Controller
{
    public function index()
    {
        $service = ServiceBooking::auth()->with('hospital')
            ->get();

        return Response::success(
            'Service requests retrieved successfully',
            [
                'service' => $service
            ]
        );
    }

    public function serviceDetails(Request $request)
    {
        $service = ServiceBooking::auth()
            ->where('uuid', $request->uuid)
            ->first();

        if (!$service) {
            return Response::error(
                'Service not found',
                null,
                404
            );
        }

        return Response::success(
            'Service details retrieved successfully',
            [
                'service' => $service
            ]
        );
    }

    public function serviceUpdate(Request $request)
    {
        $service = ServiceBooking::where('uuid', $request->uuid)->first();

        if (!$service) {
            return Response::error(
                'Service not found',
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
        $amount = $service->price;

        DB::beginTransaction();

        try {
            // Update booking status
            $service->update([
                'status' => $validated['status'],
                'updated_at' => now()
            ]);

            // Handle cash payment wallet update
            if ($service->payment_method == 'cash') {
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
                'Service status updated successfully',
                [
                    'service' => $service->fresh(),
                    'wallet_updated' => $service->payment_method == 'cash'
                ]
            );
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update service',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
