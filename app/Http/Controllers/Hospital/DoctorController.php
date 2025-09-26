<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Admin\Language;
use App\Models\Hospital\Branch;
use App\Models\Hospital\Doctor;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use App\Constants\GlobalConst;
use App\Models\Hospital\DoctorHasSchedule;
use Exception;
use Illuminate\Support\Arr;
use App\Http\Helpers\Response;
use App\Models\Hospital\Departments;

class DoctorController extends Controller
{
    /**
     * Method for show the  page
     * return view
     */
    public function index()
    {
        $page_title          = "Setup Doctor";
        $doctor              = Doctor::auth()->orderByDesc('id')->paginate(10);
        return view('hospital.sections.doctor.index', compact(
            'page_title',
            'doctor',
        ));
    }

    /**
     * Method for show create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title          = "Doctor Add";
        $branch              = Branch::auth()->with('departments')->orderByDesc("id")->paginate(10);
        $language            = Language::get();
        return view('hospital.sections.doctor.create', compact(
            'page_title',
            'branch',
            'language'
        ));
    }

    /**
     * Method for store
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'branch_id'      => 'required|integer',
            'departments_id' => 'required|integer',
            'title'          => 'required|string',
            'qualification'  => 'required|string',
            'specialty'      => 'required|string',
            'designation'    => 'required|string',
            'contact'        => 'required|string',
            'floor_number'   => 'required|integer',
            'room_number'    => 'required|integer',
            'address'        => 'required|string',
            'fees'           => 'required|string',
            'off_days'       => 'required|array',
            'language'       => 'required|array',
            'image'          => "required|mimes:png,jpg,jpeg,webp,svg",

            'schedule_days'   => 'required|array',
            'schedule_days.*' => 'required|array',
            'from_time'       => 'required|array',
            'from_time.*'     => 'required|array',
            'from_time.*.*'   => 'required|date_format:H:i',
            'to_time'         => 'required|array',
            'to_time.*'       => 'required|array',
            'to_time.*.*'     => 'required|date_format:H:i|after:from_time.*.*',
            'max_patient'     => 'required|array',
            'max_patient.*'   => 'required|array',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }


        $validated                         = $validator->validate();
        $validated['slug']                 = Str::uuid();
        $validated['hospital_id']          = auth()->user()->id;
        $validated['branch_id']            = $validated['branch_id'];
        $validated['department_id']        = $validated['departments_id'];
        $validated['off_days']             = implode(',', $request->input('off_days', []));
        $validated['language']             = implode(',', $request->input('language', []));
        $validated['status']               = GlobalConst::STATUS_SUCCESS;

        $schedule_days                     = $validated['schedule_days'];
        $from_time                         = $validated['from_time'];
        $to_time                           = $validated['to_time'];
        $max_patient                       = $validated['max_patient'];
        $validated                         = Arr::except($validated, ['schedule_day', 'from_time', 'to_time', 'max_patient', 'branch', 'department']);

        if ($request->hasFile("image")) {
            $validated['image'] = $this->imageValidate($request, "image", null);
        }

        try {
            $doctor = Doctor::create($validated);

            if (count($schedule_days) > 0) {
                $days_schedule = [];

                foreach ($schedule_days as $key => $days) {

                    if (is_array($days)) {
                        foreach ($days as $day) {
                            $days_schedule[] = [
                                'doctor_id'  => $doctor->id,
                                'day'        => $day,
                                'from_time'  => $from_time[$key][0],
                                'to_time'    => $to_time[$key][0],
                                'max_client' => $max_patient[$key][0],
                                'created_at' => now(),
                            ];
                        }
                    } else {
                        $days_schedule[] = [
                            'doctor_id'  => $doctor->id,
                            'day'        => $days,
                            'from_time'  => $from_time[$key][0],
                            'to_time'    => $to_time[$key][0],
                            'max_client' => $max_patient[$key][0],
                            'created_at' => now(),
                        ];
                    }
                }
                DoctorHasSchedule::insert($days_schedule);
            }
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return redirect()->route('hospitals.doctor.index')->with(['success' => ["Doctor Created Successfully!"]]);
    }

    /**
     * Method for show the edit  list page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function edit($slug)
    {
        $page_title             = "Doctor Edit";
        $doctor                 = Doctor::where('slug', $slug)->first();
        $branch                 = Branch::auth()->with('departments')->orderByDesc("id")->paginate(10);
        $language               = Language::get();
        $doctor_has_schedule    = DoctorHasSchedule::where('doctor_id', $doctor->id)->get();
        return view('hospital.sections.doctor.edit', compact(
            'page_title',
            'doctor',
            'branch',
            'language',
            'doctor_has_schedule'
        ));
    }

    /**
     * Method for update manager
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request, $slug)
    {
        $doctor = Doctor::where('slug', $slug)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'branch_id'      => 'required|integer',
            'departments_id' => 'required|integer',
            'title'          => 'required|string',
            'qualification'  => 'required|string',
            'specialty'      => 'required|string',
            'designation'    => 'required|string',
            'contact'        => 'required|string',
            'floor_number'   => 'required|integer',
            'room_number'    => 'required|integer',
            'address'        => 'required|string',
            'fees'           => 'required|string',
            'off_days'       => 'required|array',
            'language'       => 'required|array',
            'image'          => 'nullable|mimes:png,jpg,jpeg,webp,svg',

            'schedule_days'   => 'required|array',
            'schedule_days.*' => 'required|string',

            'from_time'       => 'required|array',
            'from_time.*.*'   => 'required|string|date_format:H:i',
            'to_time'         => 'required|array',
            'to_time.*.*'     => 'required|string|date_format:H:i|after:from_time',

            'max_patient'     => 'required|array',
            'max_patient.*'   => 'required|integer',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated = $validator->validated();

        if ($request->hasFile('image')) {
            $validated['image'] = $this->imageValidate($request, "image", null);
        }
        $validated['off_days']             = implode(',', $request->input('off_days'));
        $validated['language']             = implode(',', $request->input('language'));


        $validated['branch_id']            = $validated['branch_id'];
        $validated['department_id']        = $validated['departments_id'];

        try {
            $doctor->update($validated);
            $schedule_days      = $validated['schedule_days'];
            $from_time          = $validated['from_time'];
            $to_time            = $validated['to_time'];
            $max_patient        = $validated['max_patient'];


            $existingSchedules       = DoctorHasSchedule::where('doctor_id', $doctor->id)->get();
            $existingScheduleIds     = $existingSchedules->pluck('day')->toArray();
            $requestScheduleIds      = collect($schedule_days)->map(fn($id) => (int)$id)->toArray();


            $schedulesToDelete = array_diff($existingScheduleIds, $requestScheduleIds);

            if (!empty($schedulesToDelete)) {
                DoctorHasSchedule::where('doctor_id', $doctor->id)
                    ->whereIn('day', $schedulesToDelete)
                    ->delete();
            }


            foreach ($schedule_days as $key => $day) {
                $scheduleData = [
                    'doctor_id'   => $doctor->id,
                    'day'         => $day,
                    'from_time'   => $from_time[$key],
                    'to_time'     => $to_time[$key],
                    'max_client'  => $max_patient[$key],
                    'updated_at'  => now(),
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
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return redirect()->route('hospitals.doctor.index')->with(['success' => ['Doctor Updated Successfully!']]);
    }

    /**
     * Method for delete
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function delete(Request $request)
    {
        $request->validate([
            'target' => 'required|numeric',
        ]);

        $doctor = Doctor::find($request->target);

        if (!$doctor) {
            return back()->with(['error' => ['Doctor not found!']]);
        }

        try {
            if ($doctor->image) {
                $imagePath = public_path('frontend/images/doctor/' . $doctor->image);
                if (file_exists($imagePath)) {
                    unlink($imagePath);
                }
            }

            $doctor->delete();

            return back()->with(['success' => ['Doctor Deleted Successfully!']]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
    }


    /**
     * Function for update  status
     * @param  \Illuminate\Http\Request  $request
     */
    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target'       => "required|string|max:100",
            'status'            => "required|boolean",
        ]);

        if ($validator->stopOnFirstFailure()->fails()) {
            $error = ['error' => $validator->errors()];
            return back()->with(['error'    =>  ['Something went wrong. Please try again!']]);
        }

        $validated = $validator->safe()->all();
        $id = $validated['data_target'];

        $doctor = Doctor::where('id', $id)->first();
        if (!$doctor) {
            $error = ['error' => [__('Doctor not found!')]];
            return Response::error($error, null, 404);
        }

        try {
            $doctor->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Doctor status updated successfully!')]];
        return Response::success($success, null, 200);
    }

    /**
     * Method for image validate
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function imageValidate($request, $input_name, $old_image = null)
    {
        if ($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name), [
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request, $input_name);
            $upload = upload_files_from_path_dynamic($image, 'doctor', $old_image);
            return $upload;
        }
        return false;
    }
}
