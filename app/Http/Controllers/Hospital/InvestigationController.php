<?php

namespace App\Http\Controllers\Hospital;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use App\Http\Helpers\Response;
use App\Models\Hospital\Investigation;
use App\Models\Hospital\InvestigationCategory;
use App\Models\Hospital\InvestigationHasCategory;

class InvestigationController extends Controller
{
    /**
     * Method for show the setup page
     * return view
     */
    public function index()
    {
        $page_title             = "Setup Investigation";
        $investigation          = Investigation::auth()->with('investigationCategory')->orderByDesc('id')->paginate(10);

        return view('hospital.sections.investigation.index', compact(
            'page_title',
            'investigation'
        ));
    }

    /**
     * Method for show create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title             = "Investigation Add";
        $investigation_cat      = InvestigationCategory::get();
        return view('hospital.sections.investigation.create', compact(
            'page_title',
            'investigation_cat',
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
            'name'              => 'required|string',
            'regular_price'     => 'required',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
            'categories'        => 'required|array',
            'categories.*'      => 'required|string|exists:investigation_categories,id',
        ],[
            'offer_price.lte' => 'The offer price must be less than or equal to the regular price.'
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated                       = $validator->validate();
        $validated['slug']               = Str::slug($validated['name']);
        $validated['hospital_id']        = auth()->user()->id;

        if (Investigation::auth()->where('name', $validated['name'])
        ->exists()) {
            throw ValidationException::withMessages([
                'name' => 'Investigation already exists',
            ]);
        }

        DB::beginTransaction();

        try {
            $investigation = Investigation::create([
                'name'          => $validated['name'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
                'hospital_id'   => $validated['hospital_id'],
                'uuid'          => Str::uuid(),
                'created_at'    => now()
            ]);

            foreach ($validated['categories'] as $categoryId) {
                InvestigationHasCategory::create([
                    'investigation_id'             => $investigation->id,
                    'investigation_category_id'    => $categoryId
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.investigation.index')->with(['success' => [__("Investigation Created Successfully!")]]);
    }

    /**
     * Method for show the edit  list page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function edit($uuid)
    {
        $page_title             = "Investigation Edit";
        $investigation          = Investigation::where('uuid', $uuid)->with('investigationCategory')->first();
        $investigation_cat      = InvestigationCategory::get();

        return view('hospital.sections.investigation.edit', compact(
            'page_title',
            'investigation',
            'investigation_cat'
        ));
    }


    /**
     * Method for update
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request, $uuid)
    {
        $investigation = Investigation::where('uuid', $uuid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'regular_price'     => 'required',
            'offer_price'       => 'nullable|numeric|min:0|lte:regular_price',
            'categories'        => 'required|array',
            'categories.*'      => 'required|string|exists:investigation_categories,id'],[
                'offer_price.lte' => 'The offer price must be less than or equal to the regular price.'
            
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated          = $validator->validate();
        $validated['slug']  = Str::slug($validated['name']);

        DB::beginTransaction();
        try {

            $investigation->update([
                 'name'         => $validated['name'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
            ]);

            InvestigationHasCategory::where('investigation_id', $investigation->id)->delete();

            foreach ($validated['categories'] as $categoryId) {
                InvestigationHasCategory::create([
                    'investigation_id'                   => $investigation->id,
                    'investigation_category_id'          => $categoryId,
                ]);
            }

            DB::commit();
        } catch (Exception $e) {
            DB::rollBack();
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.investigation.index')->with(['success' => [__("Investigation Updated Successfully!")]]);
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

        $branch = Investigation::find($request->target);

        try {
            $branch->delete();
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Investigation Deleted Successfully!']]);
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

        $investigation = Investigation::where('id', $id)->first();

        if (!$investigation) {
            $error = ['error' => [__('Investigation not found!')]];
            return Response::error($error, null, 404);
        }

        try {
            $investigation->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Investigation status updated successfully!')]];
        return Response::success($success, null, 200);
    }
}
