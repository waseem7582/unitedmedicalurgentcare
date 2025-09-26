<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Hospital\Departments;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class DepartmentController extends Controller
{
    public function index()
    {
        $departments = Departments::auth()->get();

        return Response::success([__( 'Departments retrieved successfully')], [
            'departments' => $departments,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422
            );
        }

        $validated = $validator->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['hospital_id'] = auth()->user()->id;
        $validated['uuid'] = Str::uuid();

        if (Departments::auth()->where('name', $validated['name'])->exists()) {
            return Response::error(
                ['name' => 'Department already exists'],
                null,
                409 // Conflict status code
            );
        }

        try {
            $department = Departments::create($validated);

        return Response::success([__('Department created successfully')], [
          'department' => $department
        ]);

        } catch (Exception $e) {
            return Response::error(
                'Something went wrong! Please try again.',
                null,
                500
            );
        }
    }

    public function update(Request $request)
    {
        $department = Departments::where('uuid', $request->uuid)->first();

        if (!$department) {
            return Response::error(
                'Department not found',
                null,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'description' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422
            );
        }

        $validated = $validator->validated();

        // Check if another department with the same name already exists
        if (Departments::auth()
            ->where('name', $validated['name'])
            ->where('uuid', '!=', $request->uuid)
            ->exists()
        ) {
            return Response::error(
                ['name' => 'Department name already exists'],
                null,
                409
            );
        }

        try {
            $department->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'slug' => Str::slug($validated['name']), // Update slug as well
            ]);

        return Response::success([__(  'Department updated successfully')], [
           'department' => $department->fresh()
        ]);
        } catch (Exception $e) {
            return Response::error(
                'Something went wrong! Please try again',
                null,
                500
            );
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'department_id' => 'required|numeric|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422 // Unprocessable Entity
            );
        }

        $department = Departments::find($request->department_id);

        if (!$department) {
            return Response::error(
                'Department not found',
                null,
                404 // Not Found
            );
        }

        try {
            $department->delete();

        return Response::success([__( 'Department deleted successfully')], [

        ]);
        } catch (Exception $e) {
            return Response::error(
                'Something went wrong! Please try again',
                null,
                500
            );
        }
    }

    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target' => 'required|string|exists:departments,id',
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
        $departments   = Departments::find($validated['data_target']);

        if (!$departments) {
            return Response::error(
                'departments not found',
                null,
                404
            );
        }

        DB::beginTransaction();

        try {
            $departments->update([
                'status' => $validated['status'], // Toggle the status
                'updated_at' => now()
            ]);

            DB::commit();

        return Response::success([__(  'Department status updated successfully')], [
            'departments' => [
                'id' => $departments->id,
                'status' => $departments->status
            ]
        ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update department status',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
