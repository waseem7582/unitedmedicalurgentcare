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
use App\Models\Hospital\HealthPackage;

class HealthPackageController extends Controller
{
    /**
     * Method for show the setup  page
     * return view
     */
    public function index()
    {
        $page_title             = __('Setup Health Package');
        $package                = HealthPackage::auth()->orderByDesc('id')->paginate(10);

        return view('hospital.sections.health-package.index', compact(
            'page_title',
            'package'
        ));
    }

    /**
     * Method for show create page
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function create()
    {
        $page_title             = __('Health Package Add');
        return view('hospital.sections.health-package.create', compact(
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
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated = $validator->validate();
        $validated['slug']               = Str::slug($validated['name']);
        $validated['hospital_id']        = auth()->user()->id;

        if (HealthPackage::auth()->where('name', $validated['name'])
            ->exists()
        ) {
            throw ValidationException::withMessages([
                'name' => 'Health Package already exists',
            ]);
        }

        try {
            HealthPackage::create([
                'name'          => $validated['name'],
                'title'         => $validated['title'],
                'description'   => $validated['description'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
                'hospital_id'   => $validated['hospital_id'],
                'uuid'          => Str::uuid(),
                'created_at'    => now()
            ]);
        } catch (Exception $e) {

            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.health-package.index')->with(['success' => [__("Health Package Created Successfully!")]]);
    }

    /**
     * Method for show the edit  list page
     * @param $slug
     * @param \Illuminate\Http\Request $request
     */
    public function edit($uuid)
    {
        $page_title             = __("Investigation Edit");
        $package                = HealthPackage::where('uuid', $uuid)->first();

        return view('hospital.sections.health-package.edit', compact(
            'page_title',
            'package',
        ));
    }


    /**
     * Method for update
     * @param string
     * @param \Illuminate\Http\Request $request
     */
    public function update(Request $request, $uuid)
    {
        $package = HealthPackage::where('uuid', $uuid)->firstOrFail();

        $validator = Validator::make($request->all(), [
            'name'              => 'required|string',
            'title'             => 'required|string',
            'description'       => 'required|string',
            'regular_price'     => 'required',
            'offer_price'       => 'required'],[
                'offer_price.lte' => 'The offer price must be less than or equal to the regular price.'
            
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput($request->all());
        }

        $validated          = $validator->validate();
        $validated['slug']  = Str::slug($validated['name']);

        try {

            $package->update([
                'name'          => $validated['name'],
                'title'         => $validated['title'],
                'description'   => $validated['description'],
                'slug'          => $validated['slug'],
                'regular_price' => $validated['regular_price'],
                'offer_price'   => $validated['offer_price'],
            ]);
        } catch (Exception $e) {

            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return redirect()->route('hospitals.health-package.index')->with(['success' => [__("Package Updated Successfully!")]]);
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
        $package = HealthPackage::find($request->target);

        try {
            $package->delete();
        } catch (Exception $e) {
            return back()->with(['error' => [__('Something went wrong! Please try again.')]]);
        }
        return back()->with(['success' => [__('Package Deleted Successfully!')]]);
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
            return back()->with(['error'    =>  [__('Something went wrong. Please try again!')]]);
        }

        $validated   = $validator->safe()->all();
        $id          = $validated['data_target'];

        $package = HealthPackage::where('id', $id)->first();

        if (!$package) {
            $error = ['error' => [__('Package not found!')]];
            return Response::error($error, null, 404);
        }

        try {
            $package->update([
                'status' => ($validated['status'] == true) ? false : true,
            ]);
        } catch (Exception $e) {
            $error = ['error' => [__('Something went wrong!. Please try again.')]];
            return Response::error($error, null, 500);
        }

        $success = ['success' => [__('Package status updated successfully!')]];
        return Response::success($success, null, 200);
    }
}
