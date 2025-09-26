<?php

namespace App\Http\Controllers\Api\V1\User;

use Exception;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Http\Helpers\Response;
use App\Models\Admin\Language;
use App\Models\Hospital\Branch;
use App\Models\Hospital\Doctor;
use App\Models\Admin\UsefulLink;
use App\Models\Admin\AppSettings;
use App\Http\Controllers\Controller;
use App\Models\Hospital\Departments;
use Illuminate\Support\Facades\Route;
use App\Models\Admin\AppOnboardScreens;
use App\Models\Hospital\Hospital;
use App\Models\Hospital\Investigation;
use App\Providers\Admin\CurrencyProvider;
use Illuminate\Support\Facades\Validator;
use App\Providers\Admin\BasicSettingsProvider;

class SettingController extends Controller
{
    protected $languages;
    public function __construct()
    {
        $this->languages = Language::get();
    }
    public function basicSettings()
    {
        $basic_settings = BasicSettingsProvider::get()->only(['id', 'site_name', 'base_color', 'secondary_color', 'hospital_base_color', 'hospital_secondary_color', 'site_title', 'timezone', 'site_logo', 'hospital_site_logo', 'hospital_site_logo_dark', 'site_logo_dark', 'site_fav', 'hospital_site_fav', 'site_fav_dark', 'hospital_site_fav_dark', 'hospital_registration', 'hospital_agree_policy', 'user_registration', 'agree_policy']);

        $languages = Language::select(['id', 'name', 'code', 'status'])->get();

        $app_settings = AppSettings::first();
        $onboard_screen_user = AppOnboardScreens::where('type',GlobalConst::USER)->orderByDesc('id')->where('status',1)->get()->map(function($data){
            return[
                'id' => $data->id,
                'title' => $data->title,
                'sub_title' => $data->sub_title,
                'image' => $data->image,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

        });
        $onboard_screen_hospital = AppOnboardScreens::where('type',GlobalConst::HOSPITAL)->orderByDesc('id')->where('status',1)->get()->map(function($data){
            return[
                'id' => $data->id,
                'title' => $data->title,
                'sub_title' => $data->sub_title,
                'image' => $data->image,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

        });

        $base_cur = CurrencyProvider::default()->first();
        $base_cur->makeHidden(['admin_id', 'country', 'name', 'created_at', 'updated_at', 'type', 'flag', 'sender', 'receiver', 'default', 'status', 'editData']);

        $app_image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];


        return Response::success([__("Basic settings fetch successfully!")], [
            'basic_settings'    => $basic_settings,
            'base_cur'          => $base_cur,
            'web_links'         => [
                'about-us'          => Route::has('frontend.about') ? route('frontend.about') : url('/'),
                'contact-us'        => Route::has('frontend.contact') ? route('frontend.contact') : url('/'),
                'privacy-policy'    => setRoute('frontend.link', UsefulLink::where('type', GlobalConst::USEFUL_LINK_PRIVACY_POLICY)->first()?->slug),
                'blog'              => Route::has('frontend.blog') ? route('frontend.blog') : url('/'),
                'hospital_login'      => Route::has('hospitals.login') ? route('hospitals.login') : url('/'),
            ],
            'languages'         => $languages,
            'splash_screen'     => $app_settings,
            'user_onboard_screens'   => $onboard_screen_user,
            'hospital_onboard_screens'   => $onboard_screen_hospital,
            'image_paths'       => [
                'base_path'         => url("/"),
                'path_location'     => files_asset_path_basename("image-assets"),
                'default_image'     => files_asset_path_basename("default"),
            ],
            'app_image_paths'   => $app_image_paths,
        ], 200);
    }

    public function splashScreen()
    {
        $user_splash = AppSettings::select('splash_screen_image as user_slash', 'version')->first();

        $hospital_splash = AppSettings::select('hospital_splash_screen_image as hospital_slash', 'hospital_version')->first();

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__('Splash screen data fetch successfully!')], [
            'user_splash' => $user_splash,
            'hospital_splash' => $hospital_splash,
            'image_paths'   => $image_paths,
        ], 200);
    }

    public function onboardScreens()
    {
        $onboard_screen_user = AppOnboardScreens::where('type',GlobalConst::USER)->orderByDesc('id')->where('status',1)->get()->map(function($data){
            return[
                'id' => $data->id,
                'title' => $data->title,
                'sub_title' => $data->sub_title,
                'image' => $data->image,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

        });
        $onboard_screen_hospital = AppOnboardScreens::where('type',GlobalConst::HOSPITAL)->orderByDesc('id')->where('status',1)->get()->map(function($data){
            return[
                'id' => $data->id,
                'title' => $data->title,
                'sub_title' => $data->sub_title,
                'image' => $data->image,
                'status' => $data->status,
                'created_at' => $data->created_at,
                'updated_at' => $data->updated_at,
            ];

        });

        $image_paths = [
            'base_url'          => url("/"),
            'path_location'     => files_asset_path_basename("app-images"),
            'default_image'     => files_asset_path_basename("default"),
        ];

        return Response::success([__('Onboard screen data fetch successfully!')], [
            'user_onboard_screens'   => $onboard_screen_user,
            'hospital_onboard_screen'   => $onboard_screen_hospital,
            'image_paths'       => $image_paths,
        ], 200);
    }

    public function getLanguages()
    {
        try {
            $api_languages = get_api_languages();
        } catch (Exception $e) {
            return Response::error([$e->getMessage()], [], 500);
        }

        return Response::success([__("Language data fetch successfully!")], [
            'languages' => $api_languages,
        ], 200);
    }

    public function searchDoctor(Request $request){
        $validator = Validator::make($request->all(), [
            'hospital'        => 'nullable',
            'branch'          => 'nullable',
            'department'      => 'nullable',
            'name'            => 'nullable',
        ]);
        if ($validator->fails()) {
            return Response::error($validator->errors()->all(),[]);
        }
     if ($request->hospital && $request->branch && $request->department && $request->name) {
            $doctor    = Doctor::where('hospital_id', $request->hospital)->where('branch_id', $request->branch)->where('department_id', $request->department)->where('name', 'like', '%' . $request->name . '%')->get();
        } else {
            $doctor    = Doctor::where('name', 'like', '%' . $request->name . '%')->get();
        }
        if ($doctor->isEmpty()) {
            return Response::error(['Doctor not found!'],[],404);
        }
        return Response::success(['Doctor Find Successfully!'],$doctor,200);
    }

    public function searchByData()
    {
        $department     = Departments::where('status',true)->get();
        $branch         = Branch::with('departments')->where('status',true)->get();
        $Hospital       = Hospital::where('status', true)->get();

        return Response::success('Search by Data fetched successfully.', [
            'department'         => $department,
            'branch'             => $branch,
            'Hospital'           => $Hospital,
        ]);
    }



}
