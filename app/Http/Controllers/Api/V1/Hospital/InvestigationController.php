<?php

namespace App\Http\Controllers\Api\V1\Hospital;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Hospital\Investigation;
use App\Models\Hospital\InvestigationCategory;
use Illuminate\Support\Facades\Validator;
use App\Models\Hospital\InvestigationHasCategory;

class InvestigationController extends Controller
{
    public function index()
    {
        $investigations = Investigation::auth()
            ->with('investigationCategory')
            ->get();

        return Response::success([__('Investigations retrieved successfully')], [
            'currency symbol'  => get_default_currency_symbol(),
                'currency code'    => get_default_currency_code(),
                'investigations' => $investigations
        ]);
    }

    public function category()
    {
        $category = InvestigationCategory::get();

        return Response::success([__('Category retrieved successfully')], [
           'category' => $category
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'regular_price'     => 'required',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
            'categories'        => 'required|array',
            'categories.*'      => 'required|string|exists:investigation_categories,id'],[
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
        $validated['hospital_id'] = auth()->user()->id;

        if (Investigation::auth()->where('name', $validated['name'])->exists()) {
            return Response::error(
                ['name' => 'Investigation already exists'],
                null,
                409
            );
        }

        DB::beginTransaction();

        try {
            $investigation = Investigation::create([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price' => $validated['offer_price'],
                'hospital_id' => $validated['hospital_id'],
                'uuid' => Str::uuid(),
                'created_at' => now(),
                'updated_at' => now()
            ]);

            $categories = array_map(function ($categoryId) use ($investigation) {
                return [
                    'investigation_id' => $investigation->id,
                    'investigation_category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $validated['categories']);

            InvestigationHasCategory::insert($categories);

            DB::commit();

            return Response::success([__('Investigation created successfully')], [
                'investigation' => $investigation->load('investigationCategory')
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
        $investigation = Investigation::where('uuid', $request->uuid)->first();

        if (!$investigation) {
            return Response::error(
                'Investigation not found',
                null,
                404
            );
        }

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'regular_price'     => 'required',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
            'categories'        => 'required|array',
            'categories.*'      => 'required|string|exists:investigation_categories,id'],[
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

        // Check for duplicate investigation name (excluding current one)
        if (Investigation::auth()
            ->where('name', $validated['name'])
            ->where('uuid', '!=', $request->uuid)
            ->exists()
        ) {
            return Response::error(
                ['name' => 'Investigation name already exists'],
                null,
                409
            );
        }

        DB::beginTransaction();

        try {
            // Update investigation
            $investigation->update([
                'name' => $validated['name'],
                'slug' => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price' => $validated['offer_price'],
                'updated_at' => now()
            ]);

            // Sync categories (delete old and create new)
            InvestigationHasCategory::where('investigation_id', $investigation->id)->delete();

            $categories = array_map(function ($categoryId) use ($investigation) {
                return [
                    'investigation_id' => $investigation->id,
                    'investigation_category_id' => $categoryId,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }, $validated['categories']);

            InvestigationHasCategory::insert($categories);

            DB::commit();


            return Response::success([__('Investigation updated successfully')], [
                'investigation' => $investigation->load('investigationCategory')
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update investigation',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    public function delete(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'investigation_id' => 'required|numeric|exists:investigations,id',
        ]);

        if ($validator->fails()) {
            return Response::error(
                $validator->errors()->all(),
                null,
                422 // Unprocessable Entity
            );
        }

        $investigation = Investigation::find($request->investigation_id);

        DB::beginTransaction();

        try {
            InvestigationHasCategory::where('investigation_id', $investigation->id)->delete();

            $investigation->delete();

            DB::commit();

            return Response::success([__('Investigation deleted successfully')], [

            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to delete investigation',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }

    public function statusUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'data_target' => 'required|string|exists:investigations,id',
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
        $investigation   = Investigation::find($validated['data_target']);

        if (!$investigation) {
            return Response::error(
                'package not found',
                null,
                404
            );
        }

        DB::beginTransaction();

        try {
            $investigation->update([
                'status' => $validated['status'], // Toggle the status
                'updated_at' => now()
            ]);

            DB::commit();

            return Response::success([__('investigation status updated successfully')], [
                'investigation' => [
                    'id' => $investigation->id,
                    'status' => $investigation->status
                ]
            ]);
        } catch (Exception $e) {
            DB::rollBack();
            return Response::error(
                'Failed to update investigation status',
                ['error' => config('app.debug') ? $e->getMessage() : null],
                500
            );
        }
    }
}
