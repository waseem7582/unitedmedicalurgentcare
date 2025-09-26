<?php

namespace App\Http\Controllers\Api\V1;

use Exception;
use Illuminate\Support\Str;

use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Models\Hospital\Doctor;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Hospital\DoctorBooking;
use App\Models\Hospital\DoctorHasSchedule;
use App\Models\Hospital\Investigation;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;


class IndexController extends Controller
{

    protected $languages;
    public function __construct()
    {
        $this->languages = Language::get();
    }



    public function doctorList()
    {
        $doctor = Doctor::where('status', true)
            ->get();

        return Response::success(
            'Doctor List retrieved successfully',
            [
                'doctor' => $doctor
            ]
        );
    }
    public function availableSchedule(Request $request)
    {
        $schedule = Doctor::with('schedules')
            ->where([
                ['status', '=', true],
                ['slug', '=', $request->slug],
            ])
            ->get();


        $schedule_list = '';
        foreach ($schedule as $value) {
            $schedule_list =  $value->schedules;
        }


        return Response::success([__('Schedule List retrieved successfully.')], [
            'schedule' => $schedule_list
        ]);
    }

    public function checkSchedule(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id'          => 'required',
            'date'               => 'required',
            'schedule_id'       => 'required',
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), []);
        }

        $doctor_booking = DoctorBooking::where('doctor_id', $request->doctor_id)
            ->where('date', $request->date)
            ->first();

        if ($doctor_booking) {

            $schedule_id = $doctor_booking->schedule_id;

            $schedule = DoctorHasSchedule::where('doctor_id', $request->doctor_id)
                ->where('id', $schedule_id)
                ->first();

            if (!$schedule) {
                return Response::error([__('Schedule not found.')], []);
            }

            $schedule_max_client = $schedule->max_client;

            $current_bookings = DoctorBooking::where('doctor_id', $request->doctor_id)
                ->where('schedule_id', $schedule_id)
                ->where('date', $request->date)
                ->count();

            // Calculate available slots
            $available_slot = $schedule_max_client - $current_bookings;
        } else {
            $schedule = DoctorHasSchedule::where('doctor_id', $request->doctor_id)
                ->where('id', $request->schedule_id)
                ->first();
                if (!$schedule) {
                    return Response::error([__('Schedule not found.')], []);
                }

            $schedule_max_client = $schedule->max_client;
            // Calculate available slots
            $available_slot = $schedule_max_client;
        }



        return Response::success([__('Schedule List retrieved successfully.')], [
            'available_slot' => max($available_slot, 0),
            'schedule_id'    => $schedule->id,
            'from_time'      => $schedule->from_time,
            'to_time'        => $schedule->to_time,
        ]);
    }


    public function investigation()
    {
        try {
            $investigation = Investigation::with('investigationCategory')->where('status', true)->get();
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }

        return Response::success([__("Investigation data fetch successfully!")], [
            'investigation' => $investigation,
        ], 200);
    }
}
