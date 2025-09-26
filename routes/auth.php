<?php

use App\Http\Controllers\Admin\Auth\LoginController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\Auth\ForgotPasswordController;
use App\Http\Controllers\Admin\Auth\ResetPasswordController;
use App\Http\Controllers\User\Auth\ForgotPasswordController as UserForgotPasswordController;
use App\Http\Controllers\User\Auth\LoginController as UserLoginController;
use App\Http\Controllers\User\Auth\RegisterController as UserRegisterController;


use App\Http\Controllers\User\AuthorizationController;
use App\Http\Controllers\Admin\AuthorizationController as AdminAuthorizationController;
use App\Http\Controllers\Hospital\Auth\RegisterController as HospitalRegisterController;
use App\Http\Controllers\Hospital\Auth\LoginController as HospitalLoginController;
use App\Http\Controllers\Hospital\AuthorizationController as hospitalAuthorizationController;
use App\Http\Controllers\Hospital\Auth\ForgotPasswordController as hospitalForgotPasswordController;



Route::name('user.')->group(function(){
    Route::get('login',[UserLoginController::class,"showLoginForm"])->name('login');
    Route::post('login',[UserLoginController::class,"login"])->name('login.submit');

    Route::get('register/{refer?}',[UserRegisterController::class,"showRegistrationForm"])->name('register');
    Route::post('register',[UserRegisterController::class,"register"])->name('register.submit');

    Route::controller(UserForgotPasswordController::class)->prefix("password")->name("password.")->group(function(){
        Route::get('forgot','showForgotForm')->name('forgot');
        Route::post('forgot/send/code','sendCode')->name('forgot.send.code');
        Route::get('forgot/code/verify/form/{token}','showVerifyForm')->name('forgot.code.verify.form');
        Route::post('forgot/verify/{token}','verifyCode')->name('forgot.verify.code');
        Route::get('forgot/resend/code/{token}','resendCode')->name('forgot.resend.code');
        Route::get('forgot/reset/form/{token}','showResetForm')->name('forgot.reset.form');
        Route::post('forgot/reset/{token}','resetPassword')->name('reset');
    });

    Route::controller(AuthorizationController::class)->prefix("authorize")->name('authorize.')->middleware("auth")->group(function(){
        Route::get('mail/{token}','showMailFrom')->name('mail');
        Route::post('mail/verify/{token}','mailVerify')->name('mail.verify');
        Route::get('mail/resend/{token}','mailResend')->name('mail.resend');
        Route::post('kyc/submit','kycSubmit')->name('kyc.submit');
        Route::get('google/2fa','showGoogle2FAForm')->name('google.2fa');
        Route::post('google/2fa/submit','google2FASubmit')->name('google.2fa.submit');
    });
});


Route::prefix("hospitals")->name('hospitals.')->group(function(){
    Route::get('/',function(){
        return redirect()->route('hospitals.login');
    });
    Route::get('login',[HospitalLoginController::class,"showLoginForm"])->name('login');
    Route::post('login',[HospitalLoginController::class,"login"])->name('login.submit');

    Route::get('register',[HospitalRegisterController::class,"showRegistrationForm"])->name('register')->middleware("register.check");
    Route::post('register',[HospitalRegisterController::class,"register"])->name('register.submit');


    Route::controller(hospitalAuthorizationController::class)->prefix("authorize")->name('authorize.')->middleware("auth:hospital")->group(function(){
        Route::get('mail/{token}','showMailFrom')->name('mail');
        Route::get('mail/resend/{token}', 'mailResend')->name('mail.resend');
        Route::post('mail/verify/{token}','mailVerify')->name('mail.verify');
        Route::get('kyc','showKycFrom')->name('kyc');
        Route::post('kyc/submit','kycSubmit')->name('kyc.submit');

        Route::get('google/2fa','showGoogle2FAForm')->name('google.2fa');
        Route::post('google/2fa/submit','google2FASubmit')->name('google.2fa.submit');

    });

    Route::controller(hospitalForgotPasswordController::class)->prefix("password")->name("password.")->group(function(){
        Route::get('forgot','showForgotForm')->name('forgot');
        Route::post('forgot/send/code','sendCode')->name('forgot.send.code');
        Route::get('forgot/code/verify/form/{token}','showVerifyForm')->name('forgot.code.verify.form');
        Route::post('forgot/verify/{token}','verifyCode')->name('forgot.verify.code');
        Route::get('forgot/resend/code/{token}','resendCode')->name('forgot.resend.code');
        Route::get('forgot/reset/form/{token}','showResetForm')->name('forgot.reset.form');
        Route::post('forgot/reset/{token}','resetPassword')->name('reset');
    });



});

