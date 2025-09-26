<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Hospital\Doctor;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\DoctorHasSchedule;
use Illuminate\Support\Str;
use Exception;
use Illuminate\Support\Arr;

class DoctorController extends Controller
{
    public function index()
    {
        $doctors = Doctor::with('schedules')->auth()
            ->get();

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("doctor"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__('Doctors retrieved successfully')], [

            'image'   => $image_paths,
            'doctors' => $doctors

        ]);
    }


    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'branch_id'      => 'required|integer|exists:branches,id',
            'departments_id' => 'required|integer|exists:departments,id',
            'title'          => 'required|string',
            'qualification'  => 'required|string',
            'specialty'      => 'required|string',
            'designation'    => 'required|string',
            'contact'        => 'required|string',
            'floor_number'   => 'required|integer',
            'room_number'    => 'required|integer',
            'address'        => 'required|string',
            'fees'          => 'required|numeric',
            'off_days'      => 'required|string',
            'language'      => 'required|string',
            'image'         => 'required|mimes:png,jpg,jpeg,webp,svg|max:2048',

            'schedule_days'  => 'required|array',
            'schedule_days.*' => 'required',
            'from_time'      => 'required|array',

            'from_time.*'  => 'required|date_format:H:i',
            'to_time'        => 'required|array',

            'to_time.*'    => 'required|date_format:H:i|after:from_time.*',
            'max_patient'    => 'required|array',

            'max_patient.*' => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return Response::error($validator->errors()->all(), null, 422);
        }

        try {
            $validated                         = $validator->validated();
            $validated['slug']                 = Str::uuid();
            $validated['hospital_id']          = auth()->user()->id;
            $validated['department_id']        = $validated['departments_id'];
            $validated['status']               = GlobalConst::STATUS_SUCCESS;

            $schedule_days                     = $validated['schedule_days'];
            $from_time                         = $validated['from_time'];
            $to_time                           = $validated['to_time'];
            $max_patient                       = $validated['max_patient'];

            $validated = Arr::except($validated, [
                'schedule_days',
                'from_time',
                'to_time',
                'max_patient',
                'departments_id'
            ]);

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('frontend/images/doctor'), $imageName);
                $validated['image'] = $imageName;
            }

            $doctor = Doctor::create($validated);

            if (!empty($schedule_days)) {
                $days_schedule = [];

                foreach ($schedule_days as $key => $days) {
                    foreach ($days as $day) {
                        $days_schedule[] = [
                            'doctor_id'  => $doctor->id,
                            'day'        => $day,
                            'from_time'  => $from_time[$key],
                            'to_time'    => $to_time[$key],
                            'max_client' => $max_patient[$key],
                            'created_at' => now(),
                        ];
                    }
                }

                DoctorHasSchedule::insert($days_schedule);
            }

            return Response::success([__('Doctor created successfully')], [
                'doctors' => $doctor
            ]);
        } catch (Exception $e) {
            return Response::error('Something went wrong! Please try again.', ['error' => $e->getMessage()]);
        }
    }

    public function update(Request $request)
    {
        $doctor = Doctor::where('slug', $request->slug)->first();

        if (!$doctor) {
            return Response::error(
                'Doctor not found',
                null,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'branch_id'      => 'required|integer|exists:branches,id',
            'departments_id' => 'required|integer|exists:departments,id',
            'title'          => 'required|string',
            'qualification'  => 'required|string',
            'specialty'      => 'required|string',
            'designation'    => 'required|string',
            'contact'        => 'required|string',
            'floor_number'   => 'required|integer',
            'room_number'    => 'required|integer',
            'address'        => 'required|string',
            'fees'           => 'required|numeric',
            'off_days'       => 'required|string',
            'language'       => 'required|string',
            'image'          => 'nullable|mimes:png,jpg,jpeg,webp,svg|max:2048',

            'schedule_days'  => 'required|array',
            'schedule_days.*' => 'required|string',
            'from_time'      => 'required|array',
            'from_time.*'    => 'required|date_format:H:i',
            'to_time'        => 'required|array',
            'to_time.*'      => 'required|date_format:H:i|after:from_time.*',
            'max_patient'    => 'required|array',
            'max_patient.*'  => 'required|integer|min:1'
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                $request->all(),
                422
            );
        }

        DB::beginTransaction();

        try {
            $validated = $validator->validated();

            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageName = Str::uuid() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('frontend/images/doctor'), $imageName);
                $validated['image'] = $imageName;
            }


            $doctor->update($validated);

            $schedule_days = $validated['schedule_days'];
            $from_time = $validated['from_time'];
            $to_time = $validated['to_time'];
            $max_patient = $validated['max_patient'];

            $existingSchedules = DoctorHasSchedule::where('doctor_id', $doctor->id)->get();
            $existingScheduleDays = $existingSchedules->pluck('day')->toArray();

            $schedulesToDelete = array_diff($existingScheduleDays, $schedule_days);
            if (!empty($schedulesToDelete)) {
                DoctorHasSchedule::where('doctor_id', $doctor->id)
                    ->whereIn('day', $schedulesToDelete)
                    ->delete();
            }

            foreach ($schedule_days as $key => $day) {
                $scheduleData = [
                    'doctor_id' => $doctor->id,
                    'day' => $day,
                    'from_time' => $from_time[$key],
                    'to_time' => $to_time[$key],
                    'max_client' => $max_patient[$key],
                    'updated_at' => now()
                ];

                $existingSchedule = DoctorHasSchedule::where('doctor_id', $doctor->id)
                    ->where('day', $day)
                    ->first();

                if ($existingSchedule) {
                    $existingSchedule->update($scheduleData);
                } else {
                    $scheduleData['created_at'] = now();
                    DoctorHasSchedule::create($scheduleData);
                }
            }

            DB::commit();

            return Response::success([__('Doctor updated successfully')], [
                'doctor' => $doctor->load(['schedules'])
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Something went wrong! Please try again',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    /**
     * Method for delete doctor
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'doctor_id' => 'required|numeric|exists:doctors,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422 // Unprocessable Entity
            );
        }

        $doctor = Doctor::find($request->doctor_id);

        DB::beginTransaction();

        try {
            // Delete associated image if exists
            if ($doctor->image) {
                $imagePath = public_path('frontend/images/doctor/' . $doctor->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }


            DoctorHasSchedule::where('doctor_id', $doctor->id)->delete();


            $doctor->delete();

            DB::commit();

            return Response::success([__('Doctor deleted successfully')], []);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Something went wrong! Please try again',
                ['error_details' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target' => 'required|string|exists:doctors,id',
            'status' => 'required|boolean',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->first(),
                null,
                422 // Unprocessable Entity
            );
        }

        $validated = $validator->validated();
        $doctor   = Doctor::find($validated['data_target']);

        if (!$doctor) {
            return Response::error(
                'doctor not found',
                null,
                404
            );
        }

        DB::beginTransaction();

        try {
            $doctor->update([
                'status' => $validated['status'], // Toggle the status
                'updated_at' => now()
            ]);

            DB::commit();

            return Response::success([__('Doctor status updated successfully')], [
                'doctors' => [
                    'id' => $doctor->id,
                    'status' => $doctor->status
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update doctor status',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
