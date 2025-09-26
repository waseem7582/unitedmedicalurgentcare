<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\IndexController;
use App\Http\Controllers\Hospital\DoctorBookingController;
use App\Http\Controllers\Hospital\HomeServiceController;

Route::name('frontend.')->group(function () {
    Route::controller(IndexController::class)->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('about', 'about')->name('about');
        Route::get('health-package', 'healthPackage')->name('package');
        Route::get('search/package', 'searchPackage')->name('package.search');

        Route::get('branch', 'branch')->name('branch');
        Route::get('search/branch', 'branch')->name('branch.search');

        Route::get('investigation', 'investigation')->name('investigation');
        Route::get('search/investigation', 'investigation')->name('investigation.search');

        Route::get('find-doctor', 'findDoctor')->name('find.doctor');
        Route::get('search/doctor', 'searchDoctor')->name('doctor.search');
        Route::get('services', 'services')->name('services');
        Route::get('contact', 'contact')->name('contact');
        Route::post("contact-request", 'contactRequest')->name("contact.request");
        Route::get('blog', 'blog')->name('blog');
        Route::get('home/hospital', 'hospital')->name('hospital');
        Route::get('blog-details/{slug}', 'blogDetails')->name('blog.details');
        Route::get('blog-category/{slug}', 'blogCategory')->name('blog.category');
        Route::post("subscribe", "subscribe")->name("subscribe");
        Route::post("contact/message/send", "contactMessageSend")->name("contact.message.send");
        Route::get('link/{slug}', 'link')->name('link');
        Route::post('languages/switch', 'languageSwitch')->name('languages.switch');
        Route::get('change/{lang?}', 'changeLanguage')->name('lang');
    });


    Route::controller(HomeServiceController::class)->name('home.service.')->group(function () {
        Route::get('home-service', 'homeService')->name('index')->middleware('auth');
        Route::post('get-home-service',  'getHomeService')->name('get.home.service')->middleware('auth');
        Route::post('confirm',  'confirm')->name('confirm');
        Route::get('service/preview/{uuid}', 'preview')->name('preview')->middleware('auth');
        Route::post('booking/confirm/{uuid}',  'bookingConfirm')->name('booking.confirm');
    });

    //doctor booking
    Route::controller(DoctorBookingController::class)->name('doctor.booking.')->group(function () {
        Route::get('get-service/{slug}', 'getService')->name('index')->middleware('auth');
        Route::post('store', 'store')->name('store');
        Route::get('preview/{uuid}', 'preview')->name('preview')->middleware('auth');
        Route::post('confirm/{uuid}', 'confirm')->name('confirm');
        Route::get('success/response/{gateway}', 'success')->name('payment.success');
        Route::get("cancel/response/{gateway}", 'cancel')->name('payment.cancel');
        Route::post("callback/response/{gateway}", 'callback')->name('payment.callback')->withoutMiddleware(['web', 'auth', 'verification.guard', 'user.google.two.factor']);

        // POST Route For Unauthenticated Request
        Route::post('success/response/{gateway}', 'postSuccess')->name('payment.success')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);
        Route::post('cancel/response/{gateway}', 'postCancel')->name('payment.cancel')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        // redirect with HTML form route
        Route::get('redirect/form/{gateway}', 'redirectUsingHTMLForm')->name('payment.redirect.form')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        //redirect with Btn Pay
        Route::get('redirect/btn/checkout/{gateway}', 'redirectBtnPay')->name('payment.btn.pay')->withoutMiddleware(['auth', 'verification.guard', 'user.google.two.factor']);

        Route::get('paystack/pay/callback','paystackPayCallBack')->name('paystack.pay.callback');

         // authorize payment
        Route::get('authorize-card-info/{identifier}','authorizeCardInfo')->name('authorize.card.info');
        Route::post('authorize-payment-submit/{identifier}','authorizePaymentSubmit')->name('authorize.payment.submit');
    });
});
