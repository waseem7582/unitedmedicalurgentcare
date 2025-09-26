<?php

use App\Http\Controllers\Api\V1\Hospital\DoctorController;
use App\Http\Controllers\Api\V1\User\BookingRequestController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\User\ProfileController;
use App\Http\Controllers\Api\V1\User\DashboardController;
use App\Http\Controllers\Api\V1\User\DoctorBookingController;
use App\Http\Controllers\Api\V1\User\ServiceBookingController;
use App\Http\Controllers\Api\V1\User\TransactionController;


Route::prefix("user")->name("api.user.")->group(function () {
    // doctor booking

    Route::controller(ProfileController::class)->prefix('profile')->group(function () {
        Route::get('info', 'profileInfo');
        Route::post('info/update', 'profileInfoUpdate')->middleware('app.mode');
        Route::post('password/update', 'profilePasswordUpdate')->middleware('app.mode');
        Route::post('delete', 'deleteAccount')->middleware('app.mode');
    });

    // Logout Route
    Route::post('logout', [ProfileController::class, 'logout']);

    // Dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get("dashboard", "dashboard");
        Route::get("home", "home");
        Route::get("notifications", "notifications");
    });

    Route::middleware(['hospital.kyc.verify'])->group(function () {

        // booking request
        Route::controller(BookingRequestController::class)->group(function () {
            Route::get("doctor/booking", "doctorBooking");
            Route::get("service/booking", "serviceBooking");
        });
        // doctor booking
        Route::controller(DoctorBookingController::class)->group(function () {
            Route::post("doctor/booking/checkout", "checkout");
            Route::post("cash/payment/submit/{slug}", "cashPayment");
            // Submit with automatic gateway
            Route::post("automatic/submit/{slug}", "automaticSubmit");
            Route::post('authorize-payment-submit', 'authorizePaymentSubmit')->name('authorize.payment.submit');

            // Automatic Gateway Response Routes
            Route::get('success/response/{gateway}', 'success')->withoutMiddleware(['auth:api'])->name("payment.success");
            Route::get("cancel/response/{gateway}", 'cancel')->withoutMiddleware(['auth:api'])->name("payment.cancel");

            // POST Route For Unauthenticated Request
            Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth:api']);
            Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth:api']);

            //redirect with Btn Pay
            Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth:api']);

            // Automatic gateway additional fields
            Route::get('payment-gateway/additional-fields', 'gatewayAdditionalFields');

            Route::prefix('payment')->name('payment.')->group(function () {
                Route::post('crypto/confirm/{trx_id}', 'cryptoPaymentConfirm')->name('crypto.confirm');
            });
        });

        Route::controller(ServiceBookingController::class)->group(function () {
            Route::post("service/booking/checkout", "checkout");
            Route::post("service/booking/confirm/{uuid}", "bookingConfirm");
            Route::get("home/service/list", "homeService");
        });

        // Transaction
        Route::controller(TransactionController::class)->prefix("transaction")->group(function () {
            Route::get("history", "bookingHistory");
            Route::get("booking/details/{slug}", "details");
            Route::get("log", "log");
        });
    });
});
