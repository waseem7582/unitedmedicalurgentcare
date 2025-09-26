<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Hospital\HealthPackage;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Str;

class HealthPackageController extends Controller
{
    public function index()
    {
        $packages = HealthPackage::auth()->get();

        return Response::success([__('Health packages retrieved successfully.')], [
            'currency symbol'  => get_default_currency_symbol(),
            'currency code'    => get_default_currency_code(),
            'packages' => $packages
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'title'             => 'required|string',
            'description'       => 'required|string',
            'regular_price'     => 'required|numeric|min:0',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
        ], [
            'offer_price.lte' => 'The offer price must be less than or equal to the regular price.'
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

        // Check for existing package
        if (HealthPackage::auth()
            ->where('name', $validated['name'])
            ->exists()
        ) {
            return Response::error(
                ['name' => 'Health Package already exists'],
                null,
                409 // Conflict
            );
        }

        DB::beginTransaction();

        try {
            $package = HealthPackage::create([
                'name'          => $validated['name'],
                'title'         => $validated['title'],
                'description'   => $validated['description'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
                'hospital_id'   => $validated['hospital_id'],
                'uuid'          => Str::uuid(),
                'created_at'    => now(),
                'updated_at'    => now()
            ]);

            DB::commit();


            return Response::success([__('Health Package created successfully.')], [
                'package' => $package
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Something went wrong! Please try again',
                ['error_details' => config('app.debug') ? $e->getMessage() : null],
                500 // Internal Server Error
            );
        }
    }

    public function update(Request $request)
    {
        $package = HealthPackage::where('uuid', $request->uuid)->first();

        if (!$package) {
            return Response::error(
                'Health Package not found',
                null,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'title'             => 'required|string',
            'description'       => 'required|string',
            'regular_price'     => 'required|numeric|min:0',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
        ], [
            'offer_price.lte' => 'The offer price must be less than or equal to the regular price.'
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

        // Check for duplicate package name (excluding current package)
        if (HealthPackage::auth()
            ->where('name', $validated['name'])
            ->where('uuid', '!=', $request->uuid)
            ->exists()
        ) {
            return Response::error(
                ['name' => 'Health Package name already exists'],
                null,
                409
            );
        }

        DB::beginTransaction();

        try {
            $package->update([
                'name'          => $validated['name'],
                'title'         => $validated['title'],
                'description'   => $validated['description'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
                'updated_at'    => now()
            ]);

            DB::commit();


            return Response::success([__('Health Package updated successfully.')], [
                'package' => $package->fresh()
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
            'package_id' => 'required|numeric|exists:health_packages,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422 // Unprocessable Entity
            );
        }

        $package = HealthPackage::find($request->package_id);

        DB::beginTransaction();

        try {
            $package->delete();

            DB::commit();



            return Response::success([__('Health package deleted successfully.')], [

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

    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target' => 'required|string|exists:health_packages,id',
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
        $package   = HealthPackage::find($validated['data_target']);

        if (!$package) {
            return Response::error(
                'package not found',
                null,
                404
            );
        }

        DB::beginTransaction();

        try {
            $package->update([
                'status' => $validated['status'], // Toggle the status
                'updated_at' => now()
            ]);

            DB::commit();



            return Response::success([__('Packager status updated successfully.')], [

                    'id' => $package->id,
                    'status' => $package->status

            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update package status',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
