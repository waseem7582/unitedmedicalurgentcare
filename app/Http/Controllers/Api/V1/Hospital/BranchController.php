<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use App\Models\Hospital\Branch;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\BranchHasDepartment;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::auth()
            ->with('departments')
            ->get();

        return Response::success([__('Branches retrieved successfully.')], [
            'branches' => $branches
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'departments'    => 'required|array',
            'departments.*'  => 'required|string|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                $request->all(),
                422 // Unprocessable Entity
            );
        }

        $validated = $validator->validated();
        $validated['slug'] = Str::slug($validated['name']);
        $validated['hospital_id'] = auth()->user()->id;

        // Check for existing branch
        if (Branch::auth()->where('name', $validated['name'])->exists()) {
            return Response::error(
                ['name' => 'Branch already exists'],
                null,
                409 // Conflict
            );
        }

        DB::beginTransaction();

        try {
            $branch = Branch::create([
                'name'        => $validated['name'],
                'slug'        => $validated['slug'],
                'hospital_id' => $validated['hospital_id'],
                'uuid'        => Str::uuid()
            ]);


            $branchDepartments = [];
            foreach ($validated['departments'] as $departmentId) {
                $branchDepartments[] = [
                    'branch_id'     => $branch->id,
                    'department_id' => $departmentId,
                    'created_at'    => now(),
                    'updated_at'    => now()
                ];
            }


            BranchHasDepartment::insert($branchDepartments);

            DB::commit();


            return Response::success([__('Branch created successfully.')], [
                'branch' => $branch->load('departments')
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Something went wrong! Please try again',
                ['error_details' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    public function update(Request $request)
    {

        $branch = Branch::where('uuid', $request->uuid)->first();
        if (!$branch) {
            return Response::error(
                'Branch not found',
                null,
                404
            );
        }


        $validator = Validator::make($request->all(), [
            'name'           => 'required|string',
            'departments'    => 'required|array',
            'departments.*'  => 'required|string|exists:departments,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                $request->all(),
                422
            );
        }

        $validated = $validator->validated();
        $validated['slug'] = Str::slug($validated['name']);

        if (Branch::auth()
            ->where('name', $validated['name'])
            ->where('uuid', '!=', $request->uuid)
            ->exists()
        ) {
            return Response::error(
                ['name' => 'Branch name already exists'],
                null,
                409
            );
        }

        DB::beginTransaction();

        try {

            $branch->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
            ]);

            BranchHasDepartment::where('branch_id', $branch->id)->delete();

            $branchDepartments = [];
            foreach ($validated['departments'] as $departmentId) {
                $branchDepartments[] = [
                    'branch_id' => $branch->id,
                    'department_id' => $departmentId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }
            BranchHasDepartment::insert($branchDepartments);

            DB::commit();

            return Response::success([__('Branch updated successfully.')], [
                'branch' => $branch->load('departments')
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Something went wrong! Please try again',
                ['error_details' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_id' => 'required|numeric|exists:branches,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422
            );
        }

        $branch = Branch::find($request->branch_id);

        if (!$branch) {
            return Response::error(
                'Branch not found',
                null,
                404 // Not Found
            );
        }

        try {

            BranchHasDepartment::where('branch_id', $branch->id)->delete();

            $branch->delete();

            return Response::success([__('Branch deleted successfully.')], [

            ]);
        } catch (Exception $e) {
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
            'data_target' => 'required|string|exists:branches,id',
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
        $branch   = Branch::find($validated['data_target']);

        if (!$branch) {
            return Response::error(
                'departments not found',
                null,
                404
            );
        }

        DB::beginTransaction();

        try {
            $branch->update([
                'status' => $validated['status'], // Toggle the status
                'updated_at' => now()
            ]);

            DB::commit();


            return Response::success([__('Branch status updated successfully.')], [
                'branches' => [
                    'id' => $branch->id,
                    'status' => $branch->status
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update branch status',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
