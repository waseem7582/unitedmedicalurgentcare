<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Hospital\Branch;
use App\Models\Hospital\BranchHasDepartment;
use App\Models\Hospital\Departments;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Response;


class BranchController extends Controller
{
    /**
     * Method for show the setup Branch page
     * return view
     */
    public function index()
    {
        $page_title      = __("Setup Branch");
        $branch          = Branch::auth()->with('departments')->orderBy('id')->paginate(10);

        return view('hospital.sections.branch.index', compact(
            'page_title',
            'branch'
        ));
    }

    /**
     * Method for show create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title          =__("Branch Add");
        $department          = Departments::auth()->where('status', true)->get();
        return view('hospital.sections.branch.create', compact(
            'page_title',
            'department',
        ));
    }

    /**
     * Method for store Remittance Bank
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'departments'    => 'required|array',
            'departments.*'  => 'required|string|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated                       = $validator->validate();
        $validated['slug']               = Str::slug($validated['name']);
        $validated['hospital_id']        = auth()->user()->id;

        if (Branch::auth()->where('name', $validated['name'])
        ->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Branch already exists',
            ]);
        }

        DB::beginTransaction();

        try {
            $branch = Branch::create([
                'name'          => $validated['name'],
                'slug'          => $validated['slug'],
                'hospital_id'   => $validated['hospital_id'],
                'uuid'          => Str::uuid()
            ]);

            foreach ($validated['departments'] as $departmentId) {
                BranchHasDepartment::create([
                    'branch_id'     => $branch->id,
                    'department_id' => $departmentId
                ]);
            }
            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.branch.index')->with(['success' => [__("Branch Created Successfully!")]]);
    }

    /**
     * Method for show the edit  list page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function edit($uuid)
    {
        $page_title             = __("Branch Edit");
        $branch                 = Branch::auth()->where('uuid', $uuid)->with('departments')->first();
        $department             = Departments::auth()->where('status', true)->get();

        return view('hospital.sections.branch.edit', compact(
            'page_title',
            'branch',
            'department'
        ));
    }


    /**
     * Method for update
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request, $uuid)
    {
        $branch = Branch::where('uuid', $uuid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'departments'    => 'required|array',
            'departments.*'  => 'required|string|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated = $validator->validate();
        $validated['slug'] = Str::slug($validated['name']);

        DB::beginTransaction();

        try {

            $branch->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
            ]);

            BranchHasDepartment::where('branch_id', $branch->id)->delete();
            foreach ($validated['departments'] as $departmentId) {
                BranchHasDepartment::create([
                    'branch_id'     => $branch->id,
                    'department_id' => $departmentId,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.branch.index')->with(['success' => [__("Branch Updated Successfully!")]]);
    }



    /**
     * Method for delete
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function delete(Request $request)
    {
        $request->validate([
            'target'    => 'required|numeric',
        ]);

        $branch = Branch::find($request->target);

        try {
            $branch->delete();
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Branch Deleted Successfully!']]);
    }

    /**
     * Function for update admin status
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

        $validated  = $validator->safe()->all();
        $id         = $validated['data_target'];

        $branch = Branch::where('id', $id)->first();

        if (!$branch) {
            $error = ['error' => [__('branch not found!')]];
            return Response::error($error, null, 404);
        }

        try {
            $branch->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('branch status updated successfully!')]];
        return Response::success($success, null, 200);
    }
}
