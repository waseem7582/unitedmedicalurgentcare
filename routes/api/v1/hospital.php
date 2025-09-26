<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Hospital\Auth\AuthController;
use App\Http\Controllers\Api\V1\Hospital\Auth\AuthorizationController;
use App\Http\Controllers\Api\V1\Hospital\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V1\Hospital\BookingRequestController;
use App\Http\Controllers\Api\V1\Hospital\BranchController;
use App\Http\Controllers\Api\V1\Hospital\ProfileController;
use App\Http\Controllers\Api\V1\Hospital\DashboardController;
use App\Http\Controllers\Api\V1\Hospital\DepartmentController;
use App\Http\Controllers\Api\V1\Hospital\DoctorController;
use App\Http\Controllers\Api\V1\Hospital\HealthPackageController;
use App\Http\Controllers\Api\V1\Hospital\InvestigationController;
use App\Http\Controllers\Api\V1\Hospital\ServiceRequestController;
use App\Http\Controllers\Api\V1\Hospital\WithdrawController;

Route::name('api.v1.')->group(function () {

    // User
    Route::group(['prefix' => 'hospitals', 'as' => 'hospitals.'], function () {
        Route::post('login', [AuthController::class, 'login']);
        Route::post('register', [AuthController::class, 'register'])->middleware('register.check');

        Route::group(['prefix' => 'forgot/password'], function () {
            Route::post('send/otp', [ForgotPasswordController::class, 'sendCode']);
            Route::post('verify',  [ForgotPasswordController::class, 'verifyCode']);
            Route::post('resend/code',  [ForgotPasswordController::class, 'resendCode']);
            Route::post('reset', [ForgotPasswordController::class, 'resetPassword']);
        });

        Route::middleware('hospital.api')->group(function () {
            Route::post('logout', [AuthorizationController::class, 'logout']);
            Route::get('kyc', [AuthorizationController::class, 'getKycInputFields']);
            Route::post('kyc/submit', [AuthorizationController::class, 'kycSubmit']);
            Route::post('email/otp/verify', [AuthorizationController::class, 'verifyEmailCode']);
            Route::post('email/resend/code', [AuthorizationController::class, 'emailResendCode']);
            Route::post('google-2fa/otp/verify', [AuthorizationController::class, 'verify2FACode']);
            Route::get('google-2fa/otp/status', [AuthorizationController::class, 'get2FaStatus']);
            Route::post('google-2fa/status/update', [AuthorizationController::class, 'google2FAStatusUpdate'])->middleware('app.mode');
            Route::get('dashboard', [DashboardController::class, 'dashboard']);
            Route::get('notification', [DashboardController::class, 'notification']);


            // Hospital Profile
            Route::controller(ProfileController::class)->prefix('profile')->group(function () {
                Route::get('/', 'profile');
                Route::post('update', 'profileUpdate')->middleware('app.mode');
                Route::post('delete', 'deleteAccount')->middleware('app.mode');
                Route::post('password/update', 'passwordUpdate')->middleware('app.mode');

                Route::controller(AuthorizationController::class)->prefix('kyc')->group(function () {
                    Route::get('input-fields', 'getKycInputFields');
                    Route::post('submit', 'KycSubmit');
                });
            });

            Route::middleware(['hospital.kyc.verify'])->group(function () {
                // department
                Route::controller(DepartmentController::class)->prefix('department')->group(function () {
                    Route::get('/', 'index');
                    Route::post('store', 'store');
                    Route::post('update', 'update')->middleware('app.mode');
                    Route::post('status/update', 'statusUpdate')->middleware('app.mode');
                    Route::post('delete', 'delete')->middleware('app.mode');
                });

                // branch
                Route::controller(BranchController::class)->prefix('branch')->group(function () {
                    Route::get('/', 'index');
                    Route::post('store', 'store');
                    Route::post('update', 'update')->middleware('app.mode');
                    Route::post('status/update', 'statusUpdate')->middleware('app.mode');
                    Route::post('delete', 'delete')->middleware('app.mode');
                });

                // doctor
                Route::controller(DoctorController::class)->middleware('hospital.kyc.verify')->prefix('doctor')->group(function () {
                    Route::get('/', 'index');
                    Route::post('store', 'store')->middleware('hospital.kyc.verify');
                    Route::post('update', 'update')->middleware('app.mode');
                    Route::post('status/update', 'statusUpdate')->middleware('app.mode');
                    Route::post('delete', 'delete')->middleware('app.mode');
                });


                // investigation
                Route::controller(InvestigationController::class)->prefix('investigation')->group(function () {
                    Route::get('/', 'index');
                    Route::get('category', 'category');
                    Route::post('store', 'store');
                    Route::post('update', 'update')->middleware('app.mode');
                    Route::post('status/update', 'statusUpdate')->middleware('app.mode');
                    Route::post('delete', 'delete')->middleware('app.mode');
                });

                // branch
                Route::controller(HealthPackageController::class)->prefix('package')->group(function () {
                    Route::get('/', 'index');
                    Route::post('store', 'store');
                    Route::post('update', 'update')->middleware('app.mode');
                    Route::post('status/update', 'statusUpdate')->middleware('app.mode');
                    Route::post('delete', 'delete')->middleware('app.mode');
                });

                // booking request
                Route::controller(BookingRequestController::class)->prefix('booking/request')->group(function () {
                    Route::get('/', 'index');
                    Route::post('details', 'bookingDetails');
                    Route::post('update', 'bookingUpdate');
                });

                // service request
                Route::controller(ServiceRequestController::class)->prefix('service/request')->group(function () {
                    Route::get('/', 'index');
                    Route::post('details', 'serviceDetails');
                    Route::post('update', 'serviceUpdate');
                });

                //Withdraw Money Routes
                Route::controller(WithdrawController::class)->prefix("withdraw")->name('withdraw.')->group(function () {
                    Route::get("wallet-gateways", "walletGateways");
                    Route::get("gateway/input-fields", "gatewayInputFields");
                    Route::post("submit", "submit");
                    Route::get("log", "log");
                });
            });
        });
    });
});
