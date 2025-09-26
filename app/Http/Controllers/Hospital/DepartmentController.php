<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use App\Models\Hospital\Branch;
use App\Models\Hospital\Departments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Str;
use App\Http\Helpers\Response;


class DepartmentController extends Controller
{
    /**
     * Method for show the setup department page
     * return view
     */
    public function index()
    {
        $page_title          = __("Setup department");

        $department          = Departments::auth()->orderBy('id')->paginate(10);
        return view('hospital.sections.department.index', compact(
            'page_title',
            'department',
        ));
    }

    /**
     * Method for show create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title          = __('Department Add');
        return view('hospital.sections.department.create', compact(
            'page_title',
        ));
    }

    /**
     * Method for store
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {

        $validator             = Validator::make($request->all(), [
            'name'             => 'required|string',
            'description'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated                     = $validator->validate();
        $validated['slug']             = Str::slug($validated['name']);
        $validated['hospital_id']      = auth()->user()->id;
        $validated['uuid']             = Str::uuid();

        if (Departments::auth()->where('name', $validated['name'])
        ->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Department already exists',
            ]);
        }

        try {
            Departments::create($validated);
        } catch (Exception $e) {
            return back()->with(['error'  => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.department.index')->with(['success' => [__("Department Created Successfully!")]]);
    }

    /**
     * Method for show the edit  list page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function edit($uuid)
    {
        $page_title             = __('Department Edit');
        $department             = Departments::auth()->where('uuid', $uuid)->first();

        return view('hospital.sections.department.edit', compact(
            'page_title',
            'department'
        ));
    }

    /**
     * Method for update manager
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request ,$uuid)
    {

        $department = Departments::where('uuid', $uuid)->first();

        $validator  = Validator::make($request->all(), [
            'name'             => 'required|string',
            'description'      => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated = $validator->validate();

        try {
            $department->update([
                'name'         => $validated['name'],
                'description'  => $validated['description'],
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }
        return redirect()->route('hospitals.department.index')->with(['success' => ["Department Updated Successfully!"]]);
    }

    /**
     * Method for delete
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function delete(Request $request)
    {
        $request->validate([
            'target'    => 'required|numeric|',
        ]);
        $department = Departments::find($request->target);

        try {
            $department->delete();
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Department Deleted Successfully!']]);
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
        $id        = $validated['data_target'];

        $department = Departments::where('id', $id)->first();

        if (!$department) {
            $error = ['error' => [__('Department not found!')]];
            return Response::error($error, null, 404);
        }

        try {
            $department->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Department status updated successfully!')]];
        return Response::success($success, null, 200);
    }
}
