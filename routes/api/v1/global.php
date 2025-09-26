<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\IndexController;
use App\Http\Controllers\Api\V1\User\SettingController;
use App\Http\Controllers\Api\V1\User\DoctorBookingController;

// Settings
Route::controller(SettingController::class)->prefix("settings")->group(function () {
    Route::get("basic-settings", "basicSettings");
    Route::get("splash-screen", "splashScreen");
    Route::get("onboard-screens", "onboardScreens");
    Route::get("languages", "getLanguages");
    Route::get("country-list", "countryList");
    Route::get("search/by/data", "searchByData");
    Route::post("search/doctor", "searchDoctor");
});


// index
Route::controller(IndexController::class)->prefix("frontend")->group(function () {
    Route::get("/", "index");
    Route::get("doctor/list", "doctorList");
    Route::post("schedule/list", "availableSchedule");
    Route::post("schedule/check", "checkSchedule");
    Route::get("investigation/list", "investigation");
});


