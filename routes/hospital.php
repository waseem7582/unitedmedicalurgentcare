<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Hospital\DashboardController;
use App\Http\Controllers\Hospital\ProfileController;
use App\Http\Controllers\Hospital\SupportTicketController;
use App\Http\Controllers\Hospital\SecurityController;
use App\Http\Controllers\Hospital\BookingRequestController;
use App\Http\Controllers\Hospital\BranchController;
use App\Http\Controllers\Hospital\DepartmentController;
use App\Http\Controllers\Hospital\DoctorController;
use App\Http\Controllers\Hospital\HealthPackageController;
use App\Http\Controllers\Hospital\InvestigationController;
use App\Http\Controllers\Hospital\WalletController;
use App\Http\Controllers\Hospital\WithdrawalController;

Route::prefix("hospitals")->name("hospitals.")->group(function () {
    //dashboard
    Route::controller(DashboardController::class)->group(function () {
        Route::get('dashboard', 'index')->name('dashboard');
        Route::post('logout', 'logout')->name('logout');
        Route::delete('delete/account', 'deleteAccount')->name('delete.account')->middleware('app.mode');
    });
    //profile
    Route::controller(ProfileController::class)->prefix("profile")->name("profile.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::put('password/update', 'passwordUpdate')->name('password.update')->middleware('app.mode');
        Route::put('update', 'update')->name('update')->middleware('app.mode')->middleware('app.mode');
    });

    //support ticket
    Route::controller(SupportTicketController::class)->prefix("support-ticket")->name("support.ticket.")->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('conversation/{encrypt_id}', 'conversation')->name('conversation');
        Route::post('message/send', 'messageSend')->name('message.send');
    });

    //Branch section
    Route::controller(BranchController::class)->prefix('branch')->middleware('hospital.kyc.verify')->name('branch.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{uuid}', 'edit')->name('edit')->middleware('app.mode');
        Route::post('update/{uuid}', 'update')->name('update')->middleware('app.mode');
        Route::delete('delete', 'delete')->name('delete')->middleware('app.mode');
        Route::put('status/update', 'statusUpdate')->name('status.update');
    });

    //Department section
    Route::controller(DepartmentController::class)->prefix('department')->middleware('hospital.kyc.verify')->name('department.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{uuid}', 'edit')->name('edit')->middleware('app.mode');
        Route::post('update/{uuid}', 'update')->name('update')->middleware('app.mode');
        Route::delete('delete', 'delete')->name('delete')->middleware('app.mode');
        Route::put('status/update', 'statusUpdate')->name('status.update');
    });

    //doctor section
    Route::controller(DoctorController::class)->prefix('doctor')->middleware('hospital.kyc.verify')->name('doctor.')->group(function () {
        Route::get('/', 'index')->name('index')->middleware('hospital.kyc.verify');
        Route::get('create', 'create')->name('create')->middleware('hospital.kyc.verify');
        Route::post('store', 'store')->name('store')->middleware('hospital.kyc.verify');
        Route::get('edit/{slug}', 'edit')->name('edit');
        Route::post('update/{slug}', 'update')->name('update')->middleware('app.mode');
        Route::delete('delete', 'delete')->name('delete')->middleware('app.mode');
        Route::put('status/update', 'statusUpdate')->name('status.update');
    });

    // Investigation section
    Route::controller(InvestigationController::class)->prefix('investigation')->middleware('hospital.kyc.verify')->name('investigation.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{uuid}', 'edit')->name('edit');
        Route::post('update/{uuid}', 'update')->name('update')->middleware('app.mode');
        Route::delete('delete', 'delete')->name('delete')->middleware('app.mode');
        Route::put('status/update', 'statusUpdate')->name('status.update');
    });

    // health package section
    Route::controller(HealthPackageController::class)->prefix('health-package')->middleware('hospital.kyc.verify')->name('health-package.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('edit/{uuid}', 'edit')->name('edit');
        Route::post('update/{uuid}', 'update')->name('update');
        Route::delete('delete', 'delete')->name('delete')->middleware('app.mode');
        Route::put('status/update', 'statusUpdate')->name('status.update');
    });

    //Withdraw Money
    Route::controller(WithdrawalController::class)->prefix('withdraw-money')->middleware('hospital.kyc.verify')->name('withdraw.money.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('submit', 'submit')->name('submit')->middleware('hospital.kyc.verify');
        Route::get('instruction/{token}', 'instruction')->name('instruction');
        Route::post('instruction/submit/{token}', 'instructionSubmit')->name('instruction.submit');
        Route::get('/logs', 'withdrawLogs')->name('logs');
    });

    Route::controller(WalletController::class)->prefix("wallets")->name("wallets.")->group(function () {
        Route::post("balance", "balance")->name("balance");
    });

      //booking request
      Route::controller(BookingRequestController::class)->prefix('booking-request')->middleware('hospital.kyc.verify')->name('booking.request.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/booking/details/{booking_id}', 'bookingDetails')->name('details');
        Route::put('booking/request/update/{uuid}', 'bookingUpdate')->name('update.booking.request');

        Route::get('home/service', 'homeService')->name('home.service');
        Route::get('home/service/details/{booking_id}', 'serviceDetails')->name('service.details');
        Route::put('service/booking/request/update/{uuid}', 'serviceUpdate')->name('update.service.booking.request');

    });


    //security
    Route::controller(SecurityController::class)->prefix("security")->name('security.')->group(function () {
        Route::get('google/2fa', 'google2FA')->name('google.2fa');
        Route::post('google/2fa/status/update', 'google2FAStatusUpdate')->name('google.2fa.status.update')->middleware('app.mode');
    });

});
