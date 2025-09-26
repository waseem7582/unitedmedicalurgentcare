<?php

namespace App\Http\Controllers\Admin;

use Exception;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Constants\GlobalConst;
use App\Models\Admin\Language;
use App\Constants\LanguageConst;
use App\Models\Admin\SiteSections;
use App\Constants\SiteSectionConst;
use App\Http\Controllers\Controller;
use App\Models\Admin\Blog;
use App\Models\Admin\BlogCategory;
use Illuminate\Http\RedirectResponse;
use App\Http\Helpers\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class SetupSectionsController extends Controller
{
    protected $languages;

    public function __construct()
    {
        $this->languages = Language::get();
    }

    /**
     * Register Sections with their slug
     * @param string $slug
     * @param string $type
     * @return string
     */
    public function section($slug, $type)
    {
        $sections = [
            'banner'    => [
                'view'          => "bannerView",
                'update'        => "bannerUpdate",
                'itemStore'     => "bannerItemStore",
                'itemUpdate'    => "bannerItemUpdate",
                'itemDelete'    => "bannerItemDelete"
            ],
            'project-feature'  => [
                'view'          => "projectFeatureView",
                'update'        => "projectFeatureUpdate",
                'itemStore'     => "projectFeatureItemStore",
                'itemUpdate'    => "projectFeatureItemUpdate",
                'itemDelete'    => "projectFeatureItemDelete",
            ],
            'features'  => [
                'view'          => "featuresView",
                'update'        => "featuresUpdate",
                'itemStore'     => "featuresItemStore",
                'itemUpdate'    => "featuresItemUpdate",
                'itemDelete'    => "featuresItemDelete",
            ],
            'how-it-work'       => [
                'view'          => "howItsWorkView",
                'update'        => "howItsWorkUpdate",
                'itemStore'     => "howItsWorkItemStore",
                'itemUpdate'    => "howItsWorkItemUpdate",
                'itemDelete'    => "howItsWorkItemDelete"
            ],
            'statistics'       => [
                'view'          => "statisticsView",
                'update'        => "statisticsUpdate",
                'itemStore'     => "statisticsItemStore",
                'itemUpdate'    => "statisticsItemUpdate",
                'itemDelete'    => "statisticsItemDelete"
            ],
            'download-app'      => [
                'view'          => "downloadAppView",
                'update'        => "downloadAppUpdate",
                'itemStore'     => "downloadAppItemStore",
                'itemUpdate'    => "downloadAppItemUpdate",
                'itemDelete'    => "downloadAppItemDelete"
            ],
            'about-us'  => [
                'view'          => "aboutUsView",
                'update'        => "aboutUsUpdate",
                'itemStore'     => "aboutUsItemStore",
                'itemUpdate'    => "aboutUsItemUpdate",
                'itemDelete'    => "aboutUsItemDelete"
            ],

            'faq'         => [
                'view'            => "faqView",
                'update'          => "faqUpdate",
                'itemStore'       => "faqItemStore",
                'itemUpdate'      => "faqItemUpdate",
                'itemDelete'      => "faqItemDelete",
            ],
            'contact'          => [
                'view'         => "contactView",
                'update'       => "contactUpdate",
            ],
            'blog'        => [
                'view'       => "blogView",
                'update'     => "blogUpdate",
            ],
            'clients-feedback' => [
                'view'          => "clientsFeedbackView",
                'update'        => "clientsFeedbackUpdate",
                'itemStore'     => "clientsFeedbackItemStore",
                'itemUpdate'    => "clientsFeedbackItemUpdate",
                'itemDelete'    => "clientsFeedbackItemDelete",
            ],
            'footer' => [
                'view'          => "footerView",
                'update'        => "footerUpdate",
            ],
            'hospital-banner'    => [
                'view'      => "hospitalBannerView",
                'update'    => "hospitalBannerUpdate",
            ],
            'hospital-features'  => [
                'view'          => "hospitalFeaturesView",
                'update'        => "hospitalFeaturesUpdate",
                'itemStore'     => "hospitalFeaturesItemStore",
                'itemUpdate'    => "hospitalFeaturesItemUpdate",
                'itemDelete'    => "hospitalFeaturesItemDelete",
            ],
            'hospital-requirements'  => [
                'view'          => "hospitalRequirementsView",
                'update'        => "hospitalRequirementsUpdate",
                'itemStore'     => "hospitalRequirementsItemStore",
                'itemUpdate'    => "hospitalRequirementsItemUpdate",
                'itemDelete'    => "hospitalRequirementsItemDelete",
                'item'          => "hospitalRequirementsItemDelete",
            ],
        ];

        if (!array_key_exists($slug, $sections)) abort(404);
        if (!isset($sections[$slug][$type])) abort(404);
        $next_step = $sections[$slug][$type];
        return $next_step;
    }

    /**
     * Method for getting specific step based on incoming request
     * @param string $slug
     * @return method
     */
    public function sectionView($slug)
    {
        $section = $this->section($slug, 'view');
        return $this->$section($slug);
    }

    /**
     * Method for distribute store method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemStore(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemStore');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemUpdate');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute delete method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionItemDelete(Request $request, $slug)
    {
        $section = $this->section($slug, 'itemDelete');
        return $this->$section($request, $slug);
    }

    /**
     * Method for distribute update method for any section by using slug
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     * @return method
     */
    public function sectionUpdate(Request $request, $slug)
    {
        $section = $this->section($slug, 'update');
        return $this->$section($request, $slug);
    }

    /**
     * Method for show banner section page
     * @param string $slug
     * @return view
     */
    public function bannerView($slug)
    {
        $page_title = "Banner Section";
        $section_slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.banner-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update banner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
            'left_button' => "required|string|max:50",
            'right_button' => "required|string|max:50",
        ];

        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        $data['secondary_image'] = $section->value->secondary_image ?? null;
        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        if ($request->hasFile("secondary_image")) {
            $data['secondary_image']      = $this->imageValidate($request, "secondary_image", $section->value->secondary_image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {


            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store banner item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function bannerItemStore(Request $request, $slug)
    {
        // Define basic validation fields
        $basic_field_name = [
            'title'       => "required|string|max:255",
            'description' => "required|string|max:500",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "banner-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;


        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $unique_id = uniqid();

        $validator  = Validator::make($request->all(), [
            'image' => 'nullable|file',
        ]);
        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput();

        $validated = $validator->validate();

        $section_data['items'][$unique_id]['image']        = "";

        if ($request->hasFile('image')) {

            if (!empty($section_data['items'][$unique_id]['image'])) {
                Storage::disk('site-section')->delete($section_data['items'][$unique_id]['image']);
            }

            $section_data['items'][$unique_id]['image']   = $request->file('image')->storeAs('', $request->file('image')->getClientOriginalName(), 'site-section');
        }


        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']       = $unique_id;

        $update_data['key']   = $slug;
        $update_data['value'] = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update banner item
     * @param string $slug
     * @return view
     */
    public function bannerItemUpdate(Request $request, $slug)
    {
        // Validate target field
        $request->validate([
            'target' => "required|string",
        ]);

        // Define validation rules for input fields
        $basic_field_name = [
            'title_edit'       => "required|string|max:255",
            'description_edit' => "required|string|max:500",
        ];

        // Get section by slug
        $slug = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if (!$section) {
            return back()->with(['error' => ['Section not found!']]);
        }

        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'][$request->target])) {
            return back()->with(['error' => ['Section item not found!']]);
        }

        // Validate language-wise data
        $language_wise_data = $this->contentValidate($request, $basic_field_name, "banner-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        // Update language content
        $section_values['items'][$request->target]['language'] = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        // Validate image upload
        $validator = Validator::make($request->all(), [
            'image' => 'nullable|file',
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput();

        if ($request->hasFile("image")) {
            // Delete old image if exists
            if (!empty($section_values['items'][$request->target]['image'])) {
                Storage::disk('site-section')->delete($section_values['items'][$request->target]['image']);
            }

            // Store new image
            $section_values['items'][$request->target]['image'] = $request->file("image")->storeAs('', $request->file("image")->getClientOriginalName(), 'site-section');
        }

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item updated successfully!']]);
    }

    /**
     * Method for delete banner item
     * @param string $slug
     * @return view
     */
    public function bannerItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target' => 'required|string',
        ]);

        $slug     = Str::slug(SiteSectionConst::BANNER_SECTION);
        $section  = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values  = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section Item not Found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }



    /**
     * Method for show about us projectFeatureView page
     * @param string $slug
     * @return view
     */
    public function projectFeatureView($slug)
    {
        $page_title = "Project Feature Section";
        $section_slug = Str::slug(SiteSectionConst::PROJECT_FEATURE);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.project-feature-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for store project feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function projectFeatureItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'item_description'       => 'required|string',
        ];


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "projectFeature-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug       = Str::slug(SiteSectionConst::PROJECT_FEATURE);
        $section    = SiteSections::where('key', $slug)->first();
        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $validator  = Validator::make($request->all(), [
            'icon'            => "required|string|max:100",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'projectFeature-add');
        $validated = $validator->validate();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']       = $unique_id;
        $section_data['items'][$unique_id]['icon']     = $validated['icon'];

        $update_data['key']   = $slug;
        $update_data['value'] = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again.']]);
        }
        return back()->with(['success' => ['Section item added successfully!']]);
    }


    /**
     * Method for update project feature item
     * @param string $slug
     * @return view
     */
    public function projectFeatureItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'           => 'required|string',
        ]);

        $basic_field_name      = [
            "item_description_edit"  => "required|string|max:100",
        ];

        $slug        = Str::slug(SiteSectionConst::PROJECT_FEATURE);
        $section     = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values["items"])) return back()->with(['error' => ['Section item not found']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "projectFeature-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $validator  = Validator::make($request->all(), [
            'icon_edit'            => "required|string|max:100",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'projectFeature-edit');
        $validated = $validator->validate();

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']     = $validated['icon_edit'];


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something Went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item updated successfully!']]);
    }
    /**
     * Method for delete project feature item
     * @param string $slug
     * @return view
     */
    public function projectFeatureItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target' => 'required|string',
        ]);

        $slug     = Str::slug(SiteSectionConst::PROJECT_FEATURE);
        $section  = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values  = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section Item not Found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }


    /**
     * Method for show features section page
     * @param string $slug
     * @return view
     */
    public function featuresView($slug)
    {
        $page_title = "Features Section";
        $section_slug = Str::slug(SiteSectionConst::FEATURES_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.features-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update features section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading'       => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::FEATURES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }


        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'title'           => "required|string|max:200",
            'details'         => "required|string|max:355",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "features-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FEATURES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;


        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",

        ]);


        $basic_field_name = [
            'title_edit'           => "required|string|max:255",
            'details_edit'         => "required|string|max:355",
        ];

        $slug = Str::slug(SiteSectionConst::FEATURES_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "features-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featuresItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::FEATURES_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }



    /**
     * Method for show how its work section page
     * @param string $slug
     * @return view
     */
    public function howItsWorkView($slug)
    {
        $page_title     = "How Its Work Section";
        $section_slug   = Str::slug(SiteSectionConst::HOW_ITS_WORK_SECTION);
        $data           = SiteSections::getData($section_slug)->first();
        $languages      = $this->languages;

        return view('admin.sections.setup-sections.how-its-work-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    /**
     * Method for update howItsWork section page
     * @param string $slug
     * @return view
     */
    public function howItsWorkUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title'       => 'required|string|max:100',
            'heading'             => 'required|string|max:100',
        ];

        $slug     = Str::slug(SiteSectionConst::HOW_ITS_WORK_SECTION);
        $section  = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data    = json_decode(json_encode($section->value), true);
        } else {
            $data    = [];
        }

        $data['language']     = $this->contentValidate($request, $basic_field_name);

        $update_data['key']   = $slug;
        $update_data['value'] = $data;


        try {
            SiteSections::updateOrCreate(["key" => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
    /**
     * Method for store howItsWork item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function howItsWorkItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'title'               => "required|string|max:255",
            'description'         => "required|string|max:255",
        ];


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "work-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::HOW_ITS_WORK_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;


        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update howItsWork item
     * @param string $slug
     * @return view
     */
    public function howItsWorkItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",

        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
            'description_edit'     => "required|string|max:255",

        ];

        $slug = Str::slug(SiteSectionConst::HOW_ITS_WORK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "work-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }
    /**
     * Method for delete howItsWork item
     * @param string $slug
     * @return view
     */
    public function howItsWorkItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target' => 'required|string',
        ]);

        $slug     = Str::slug(SiteSectionConst::HOW_ITS_WORK_SECTION);
        $section  = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values  = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section Item not Found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }


    /**
     * Method for show statistics section page
     * @param string $slug
     * @return view
     */
    public function statisticsView($slug)
    {
        $page_title     = "Statistics Section";
        $section_slug   = Str::slug(SiteSectionConst::STATISTICS);
        $data           = SiteSections::getData($section_slug)->first();
        $languages      = $this->languages;

        return view('admin.sections.setup-sections.statistics-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    /**
     * Method for update statistics section page
     * @param string $slug
     * @return view
     */
    public function statisticsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'title'       => 'required|string|max:100',
            'heading'     => 'required|string|max:100',
        ];

        $slug     = Str::slug(SiteSectionConst::STATISTICS);
        $section  = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data    = json_decode(json_encode($section->value), true);
        } else {
            $data    = [];
        }

        $data['language']     = $this->contentValidate($request, $basic_field_name);

        $update_data['key']   = $slug;
        $update_data['value'] = $data;


        try {
            SiteSections::updateOrCreate(["key" => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }
    /**
     * Method for store statistics item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function statisticsItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'item_counter_value'  => 'required|string',
            'item_title'                => 'required|string|max:100',
            'item_description'          => 'required|string',
        ];


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "statistics-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug       = Str::slug(SiteSectionConst::STATISTICS);
        $section    = SiteSections::where('key', $slug)->first();
        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']       = $unique_id;


        $update_data['key']   = $slug;
        $update_data['value'] = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again.']]);
        }
        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update statistics item
     * @param string $slug
     * @return view
     */
    public function statisticsItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'           => 'required|string',
        ]);

        $basic_field_name      = [
            "item_counter_value_edit"   => "required|string|max:100",
            "item_title_edit"            => "required|string|max:100",
            "item_description_edit"      => "required|string|max:100",
        ];

        $slug        = Str::slug(SiteSectionConst::STATISTICS);
        $section     = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values["items"])) return back()->with(['error' => ['Section item not found']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "statistics-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;



        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something Went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item updated successfully!']]);
    }
    /**
     * Method for delete statistics item
     * @param string $slug
     * @return view
     */
    public function statisticsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target' => 'required|string',
        ]);

        $slug     = Str::slug(SiteSectionConst::STATISTICS);
        $section  = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values  = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section Item not Found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }


    /**
     * Method for show download app section
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     */
    public function downloadAppView($slug)
    {
        $page_title     = "Download App Section";
        $section_slug   = Str::slug(SiteSectionConst::DOWNLOAD_APP_SECTION);
        $data           = SiteSections::getData($section_slug)->first();
        $languages      = $this->languages;

        return view('admin.sections.setup-sections.download-app-section', compact(
            'page_title',
            'data',
            'languages',
            'slug'
        ));
    }
    /**
     * Method for update download app section
     * @param string
     * @param \Illuminate\\Http\Request $request
     */

    public function downloadAppUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading'       => 'required|string|max:100',
            'sub_heading'   => 'required|string',
        ];

        $slug             = Str::slug(SiteSectionConst::DOWNLOAD_APP_SECTION);
        $section          = SiteSections::where("key", $slug)->first();

        if ($section      != null) {
            $data         = json_decode(json_encode($section->value), true);
        } else {
            $data         = [];
        }
        $validator  = Validator::make($request->all(), [
            'image'            => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);
        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput();

        $validated = $validator->validate();

        $data['image']    = $section->value->image ?? "";

        if ($request->hasFile("image")) {
            $data['image'] = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']     = $this->contentValidate($request, $basic_field_name);
        $update_data['key']   = $slug;
        $update_data['value'] = $data;
        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section updated successfully!']]);
    }
    /**
     * Method for store download app item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function downloadAppItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'item_title'    => "required|string|max:2555",
            'item_heading'  => "required|string|max:2555",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "download-app-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug    = Str::slug(SiteSectionConst::DOWNLOAD_APP_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $validator  = Validator::make($request->all(), [
            'icon'            => "required|string|max:100",
            'link'            => "required|url",
            'image'           => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'download-app-add');
        $validated = $validator->validate();

        $section_data['items'][$unique_id]['language']     = $language_wise_data;
        $section_data['items'][$unique_id]['id']           = $unique_id;
        $section_data['items'][$unique_id]['image']        = "";
        $section_data['items'][$unique_id]['link']         = $validated['link'];
        $section_data['items'][$unique_id]['icon']         = $validated['icon'];
        $section_data['items'][$unique_id]['created_at']   = now();
        if ($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image']    = $this->imageValidate($request, "image", $section->value->items->image ?? null);
        }

        $update_data['key']     = $slug;
        $update_data['value']   = $section_data;
        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }
    /**
     * Method for update download app item
     * @param string $slug
     * @return view
     */
    public function downloadAppItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'           => 'required|string',
        ]);

        $basic_field_name      = [
            'item_title_edit'       => "required|string|max:2555",
            'item_heading_edit'     => "required|string|max:2555",
            'icon_edit'             => "required|string|max:2555",
        ];



        $slug    = Str::slug(SiteSectionConst::DOWNLOAD_APP_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $request->merge(['old_image' => $section_values['items'][$request->target]['image'] ?? null]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "download-app-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);
        $validator      = Validator::make($request->all(), [
            'icon_edit'              => "required|string|max:100",
            'link'                   => "required|url",
            'image'                  => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'download-app-edit');
        $validated = $validator->validate();

        $section_values['items'][$request->target]['language']      = $language_wise_data;
        $section_values['items'][$request->target]['link']          = $validated['link'];
        $section_values['items'][$request->target]['icon']          = $validated['icon_edit'];

        if ($request->hasFile("image")) {
            $section_values['items'][$request->target]['image']    = $this->imageValidate($request, "image", $section_values['items'][$request->target]['image'] ?? null);
        }
        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }
    /**
     * Method for delete download app item
     * @param string $slug
     * @return view
     */
    public function downloadAppItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'     => 'required|string',
        ]);

        $slug         = Str::slug(SiteSectionConst::DOWNLOAD_APP_SECTION);
        $section      = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            $image_name = $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            $image_path = get_files_path('site-section') . '/' . $image_name;
            delete_file($image_path);
            $section->update([
                'value'    => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }



    /**
     * Method for show about us section page
     * @param string $slug
     * @return view
     */
    public function aboutUsView($slug)
    {
        $page_title = "About US Section";
        $section_slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.about-us-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update about section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'title'         => "required|string|max:100",
            'heading'       => "required|string|max:100",
            'sub_heading'   => "required|string",
        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }

        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'title'         => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "about-us-item-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id'] = $unique_id;


        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemUpdate(Request $request, $slug)
    {

        $request->validate([
            'target'        => "required|string",

        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",

        ];

        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "about-us-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete about item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function aboutUsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::ABOUT_US_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }


    /**
     * Mehtod for show faq section page
     * @param string $slug
     * @return view
     */
    public function faqView($slug)
    {
        $page_title   = "Faq Section";
        $section_slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $data         = SiteSections::getData($section_slug)->first();
        $languages    = $this->languages;

        return view('admin.sections.setup-sections.faq-section', compact(
            'page_title',
            'data',
            'languages',
            'slug'
        ));
    }
    /**
     * Mehtod for update faq section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function faqUpdate(Request $request, $slug)
    {

        $basic_field_name   = [
            'title'         => 'required|string|max:100',
            'heading'       => 'required|string|max:100',
        ];

        $slug           = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section        = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data       = json_decode(json_encode($section->value), true);
        } else {
            $data       = [];
        }


        $data['language']      = $this->contentValidate($request, $basic_field_name);
        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => 'Something went wrong! Please try again.']);
        }
        return back()->with(['success'  =>  ['Section updated successfully!']]);
    }
    /**
     * Mehtod for store faq item information
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     */
    public function faqItemStore(Request $request, $slug)
    {
        $basic_field_name  = [
            'question'     => "required|string|max:255",
            'answer'       => "required|string|max:500",

        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $default = get_default_language_code();
        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['status']   = 1;
        $section_data['items'][$unique_id]['id']       = $unique_id;

        $update_data['key']     = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again']]);
        }

        return back()->with(['success'   => ['Section item added successfully!']]);
    }
    /**
     * Mehtod for update faq item information
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     */
    public function faqItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'         => 'required|string',
        ]);

        $basic_field_name = [
            'question_edit'  => "required|string|max:255",
            'answer_edit'    => "required|string|max:500",
        ];

        $slug              = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section           = SiteSections::getData($slug)->first();

        if (!$section) return back()->with(['error' => ['Section Not Found!']]);
        $section_values    = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => ['Section Item Not Found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['[error' => ['Section Item is invalid']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "faq-edit");

        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, '_edit');
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;

        try {
            $section->update([
                'value'  => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success'   => ['Information updated successfully!']]);
    }
    /**
     * Mehtod for delete faq item information
     * @param string $slug
     * @return view
     */
    public function faqItemDelete(request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);

        $slug           = Str::slug(SiteSectionConst::FAQ_SECTION);
        $section        = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);

        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return $e->getMessage();
        }
        return back()->with(['success' => ['Section item deleted successfully!']]);
    }



    /**
     * Method for show contact section page
     * @param string $slug
     * @return view
     */
    public function contactView($slug)
    {
        $page_title      = "Contact Section";
        $section_slug    = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $data            = SiteSections::getData($section_slug)->first();
        $languages       = $this->languages;

        return view('admin.sections.setup-sections.contact-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }
    /**
     * Method for update contact section information
     * @param string $slug
     * @param \Illuminate\Http\Request $request
     */
    public function contactUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'title'        => "required|string|max:100",
            'description'  => "required|string",

        ];

        $slug       = Str::slug(SiteSectionConst::CONTACT_SECTION);
        $section    = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }
        $validated  = Validator::make($request->all(), [
            'phone'            => "required|string|max:100",
            'address'          => "required|string|max:100",
            'email'            => "required|email",
            'schedule'         => "nullable|array",
            'schedule.*'       => "nullable|string|max:255",
        ])->validate();;

        $schedules = [];
        foreach ($validated['schedule'] ?? [] as $key => $schedule) {
            $schedules[] = [
                'schedule'          => $validated['schedule'][$key] ?? "",

            ];
        }
        $data['schedules']  = $schedules;
        $data['language']   = $this->contentValidate($request, $basic_field_name);
        $data['phone']      = $validated['phone'];
        $data['address']    = $validated['address'];
        $data['email']      = $validated['email'];



        $update_data['key']    = $slug;
        $update_data['value']  = $data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }


    /**
     *  Method for show blog section page
     * @param string $slug
     * @return view
     */
    public function blogView($slug)
    {
        $page_title         = "Blog Section";
        $section_slug       = Str::slug(SiteSectionConst::BLOG_SECTION);
        $data               = SiteSections::getData($section_slug)->first();
        $languages          = $this->languages;
        $category           = BlogCategory::get();
        $active_category    = BlogCategory::where('status', true)->get();
        $blog               = Blog::orderByDesc("id")->get();
        $blog_active        = Blog::where('status', true)->get();
        $blog_deactive      = Blog::where('status', false)->get();


        return view('admin.sections.setup-sections.blog-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
            'category',
            'active_category',
            'blog',
            'blog_active',
            'blog_deactive',
        ));
    }
    /**
     * Mehtod for update Blog section page
     * @param string $slug
     * @return view
     */
    public function blogUpdate(Request $request, $slug)
    {

        $basic_field_name       = [
            'title'             => 'required|string|max:100',
            'heading'           => 'required|string|max:300',
        ];

        $slug     = Str::slug(SiteSectionConst::BLOG_SECTION);
        $section  = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data    = json_decode(json_encode($section->value), true);
        } else {
            $data    = [];
        }

        $data['language']     = $this->contentValidate($request, $basic_field_name);
        $update_data['key']   = $slug;
        $update_data['value'] = $data;


        try {
            SiteSections::updateOrCreate(["key" => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }





    /**
     * Method for show clients feedback section page
     * @param string $slug
     * @return view
     */
    public function clientsFeedbackView($slug)
    {
        $page_title = "Client Feedback Section";
        $section_slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.clients-feedback-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update clients feedback section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'title' => "required|string|max:100",
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }

        $section_data['language']  = $this->contentValidate($request, $basic_field_name);

        $update_data['key']    = $slug;
        $update_data['value']  = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store clients feedback item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'comment'    => "required|string|max:1000",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "client-feedback-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        // request data validate
        $validator = Validator::make($request->all(), [
            'name'              => "required|string|max:255",
            'designation'       => "required|string|max:500",
            'image'             => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
            'star'              => "required|integer|gt:0|lt:6"
        ]);
        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'client-feedback-add');
        $validated = $validator->validate();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']            = $unique_id;
        $section_data['items'][$unique_id]['image']         = "";
        $section_data['items'][$unique_id]['name']          = $validated['name'];
        $section_data['items'][$unique_id]['designation']   = $validated['designation'];
        $section_data['items'][$unique_id]['star']          = $validated['star'];

        if ($request->hasFile("image")) {
            $section_data['items'][$unique_id]['image'] = $this->imageValidate($request, "image", $section->value->items->image ?? null);
        }

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update clientsFeedbackItem item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemUpdate(Request $request, $slug)
    {
        $validator = Validator::make($request->all(), [
            'target'                => "required|string",
            'name_edit'             => "required|string|max:255",
            'designation_edit'      => "required|string|max:500",
            'star_edit'             => "required|integer|gt:0|lt:6",
            'image_edit'            => "nullable|image|mimes:jpg,png,svg,webp|max:10240",
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator->errors())->withInput()->with('modal', 'client-feedback-update');
        }

        $validated = $validator->validate();

        $basic_field_name = [
            'comment_edit'     => "required|string|max:1000",
        ];

        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "client-feedback-update");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language']          = $language_wise_data;
        $section_values['items'][$request->target]['name']              = $request->name_edit;
        $section_values['items'][$request->target]['designation']       = $request->designation_edit;
        $section_values['items'][$request->target]['star']              = $request->star_edit;

        $section_values['items'][$request->target]['image']     = $section_values['items'][$request->target]['image'] ?? "";
        if ($request->hasFile("image_edit")) {
            $section_values['items'][$request->target]['image'] = $this->imageValidate($request, "image_edit", $section_values['items'][$request->target]['image'] ?? null);
        }

        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete clientsFeedbackItem item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function clientsFeedbackItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::CLIENT_FEEDBACK_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            $image_link = get_files_path('site-section') . '/' . $section_values['items'][$request->target]['image'];
            unset($section_values['items'][$request->target]);
            delete_file($image_link);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }


    /**
     * Method for show footer section page
     * @param string $slug
     * @return view
     */
    public function footerView($slug)
    {
        $page_title = "Footer Section";
        $section_slug = Str::slug(SiteSectionConst::FOOTER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.footer-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update footer section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function footerUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'contact_desc'      => "required|string|max:1000",
        ];

        $data['contact']['language']   = $this->contentValidate($request, $basic_field_name);

        $validated = Validator::make($request->all(), [
            'icon'              => "required|array",
            'icon.*'            => "required|string|max:200",
            'link'              => "required|array",
            'link.*'            => "required|string|url|max:255",
        ])->validate();

        // generate input fields
        $social_links = [];
        foreach ($validated['icon'] as $key => $icon) {
            $social_links[] = [
                'icon'          => $icon,
                'link'          => $validated['link'][$key] ?? "",
            ];
        }

        $data['contact']['social_links']    = $social_links;

        $slug = Str::slug(SiteSectionConst::FOOTER_SECTION);

        try {
            SiteSections::updateOrCreate(['key' => $slug], [
                'key'   => $slug,
                'value' => $data,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }



    /**
     * Method for show hospitalBanner section page
     * @param string $slug
     * @return view
     */
    public function hospitalBannerView($slug)
    {
        $page_title = "Banner Section";
        $section_slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.hospital-banner-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update hospitalBanner section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalBannerUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'heading' => "required|string|max:100",
            'sub_heading' => "required|string|max:500",
            'button' => "required|string|max:50",

        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_BANNER_SECTION);
        $section = SiteSections::where("key", $slug)->first();
        $data['image'] = $section->value->image ?? null;
        if ($request->hasFile("image")) {
            $data['image']      = $this->imageValidate($request, "image", $section->value->image ?? null);
        }

        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }


    /**
     * Method for show hospitalFeatures section page
     * @param string $slug
     * @return view
     */
    public function hospitalFeaturesView($slug)
    {
        $page_title = "Features Section";
        $section_slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.hospital-features-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update hospitalFeatures section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalFeaturesUpdate(Request $request, $slug)
    {
        $basic_field_name = [
            'section_title' => "required|string|max:100",
            'heading'       => "required|string|max:100",
            'description'   => "required|string",

        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $data = json_decode(json_encode($section->value), true);
        } else {
            $data = [];
        }


        $data['language']  = $this->contentValidate($request, $basic_field_name);
        $update_data['value']  = $data;
        $update_data['key']    = $slug;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalFeaturesItemStore(Request $request, $slug)
    {

        $basic_field_name = [
            'title'               => "required|string|max:255",
        ];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "hospital-requirements-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;
        $slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
        $section = SiteSections::where("key", $slug)->first();

        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();
        $section_data['items'][$unique_id]['language']   = $language_wise_data;
        $section_data['items'][$unique_id]['id']         = $unique_id;

        $update_data['key'] = $slug;
        $update_data['value']   = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalFeaturesItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'        => "required|string",

        ]);

        $basic_field_name = [
            'title_edit'     => "required|string|max:255",
        ];

        $slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "features-item-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $section_values['items'][$request->target]['language'] = $language_wise_data;


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Information updated successfully!']]);
    }

    /**
     * Method for delete features item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalFeaturesItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }


    /**
     * Method for show hospitalRequirements section page
     * @param string $slug
     * @return view
     */
    public function hospitalRequirementsView($slug)
    {
        $page_title = "Hospital Requirements Section";
        $section_slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $data = SiteSections::getData($section_slug)->first();
        $languages = $this->languages;

        return view('admin.sections.setup-sections.hospital-requirements-section', compact(
            'page_title',
            'data',
            'languages',
            'slug',
        ));
    }

    /**
     * Method for update hospitalRequirements section information
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalRequirementsUpdate(Request $request, $slug)
    {

        $basic_field_name = [
            'title'       => 'required|string|max:100',
            'heading'     => 'required|string|max:100',
            'sub_heading'     => 'required|string',
        ];

        $slug     = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section  = SiteSections::where("key", $slug)->first();
        if ($section != null) {
            $data    = json_decode(json_encode($section->value), true);
        } else {
            $data    = [];
        }

        $data['language']     = $this->contentValidate($request, $basic_field_name);

        $update_data['key']   = $slug;
        $update_data['value'] = $data;


        try {
            SiteSections::updateOrCreate(["key" => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again']]);
        }

        return back()->with(['success' => ['Section updated successfully!']]);
    }

    /**
     * Method for store hospitalRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalRequirementsItemStore(Request $request, $slug)
    {
        $basic_field_name = [
            'item_title'             => 'required|string|max:100',
            'item_description'       => 'required|string',
        ];


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "whyChoiceUs-add");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $slug       = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section    = SiteSections::where('key', $slug)->first();
        if ($section != null) {
            $section_data = json_decode(json_encode($section->value), true);
        } else {
            $section_data = [];
        }
        $unique_id = uniqid();

        $validator  = Validator::make($request->all(), [
            'icon'            => "required|string|max:100",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'whyChoiceUs-add');
        $validated = $validator->validate();

        $section_data['items'][$unique_id]['language'] = $language_wise_data;
        $section_data['items'][$unique_id]['id']       = $unique_id;
        $section_data['items'][$unique_id]['icon']     = $validated['icon'];

        $update_data['key']   = $slug;
        $update_data['value'] = $section_data;

        try {
            SiteSections::updateOrCreate(['key' => $slug], $update_data);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went worng! Please try again.']]);
        }
        return back()->with(['success' => ['Section item added successfully!']]);
    }

    /**
     * Method for update hospitalRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalRequirementsItemUpdate(Request $request, $slug)
    {
        $request->validate([
            'target'           => 'required|string',
        ]);

        $basic_field_name      = [
            "item_title_edit"  => "required|string|max:100",
            "item_description_edit"  => "required|string",
        ];

        $slug        = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section     = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values["items"])) return back()->with(['error' => ['Section item not found']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "whyChoiceUs-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $language_wise_data = array_map(function ($language) {
            return replace_array_key($language, "_edit");
        }, $language_wise_data);

        $validator  = Validator::make($request->all(), [
            'icon_edit'            => "required|string|max:100",
        ]);

        if ($validator->fails()) return back()->withErrors($validator->errors())->withInput()->with('modal', 'whyChoiceUs-edit');
        $validated = $validator->validate();

        $section_values['items'][$request->target]['language'] = $language_wise_data;
        $section_values['items'][$request->target]['icon']     = $validated['icon_edit'];


        try {
            $section->update([
                'value' => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something Went wrong! Please try again.']]);
        }
        return back()->with(['success' => ['Section item updated successfully!']]);
    }

    /**
     * Method for delete hospitalRequirements item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function hospitalRequirementsItemDelete(Request $request, $slug)
    {
        $request->validate([
            'target'    => 'required|string',
        ]);
        $slug = Str::slug(SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
        $section = SiteSections::getData($slug)->first();
        if (!$section) return back()->with(['error' => ['Section not found!']]);
        $section_values = json_decode(json_encode($section->value), true);
        if (!isset($section_values['items'])) return back()->with(['error' => ['Section item not found!']]);
        if (!array_key_exists($request->target, $section_values['items'])) return back()->with(['error' => ['Section item is invalid!']]);

        try {
            unset($section_values['items'][$request->target]);
            $section->update([
                'value'     => $section_values,
            ]);
        } catch (Exception $e) {
            return back()->with(['error' => ['Something went wrong! Please try again.']]);
        }

        return back()->with(['success' => ['Section item delete successfully!']]);
    }

    /**
     * Method for show Feature section page
     * @param string $slug
     * @return view
     */
    public function featureDetailsView($id)
    {

        $languages      = $this->languages;
        $page_title     = "Feature Details Section";
        $result         = DB::table('site_sections')
            ->where('value->items->' . $id . '->id', $id)
            ->first();


        if ($result) {
            $value = json_decode($result->value, true);
            $key = $result->key;
            $item = $value['items'][$id] ?? null;
            if ($item) {

                return view('admin.sections.setup-sections.feature-details-section', compact('page_title', 'item', 'languages'));
            }
        }
    }

    /**
     * Method for store Feature Item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureDetailsItemStore(Request $request, $id)
    {

        try {

            $basic_field_name = ['details' => 'required|string|max:250'];


            $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-add");
            if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;


            $slug = Str::slug(SiteSectionConst::VENDOR_FEATURES_SECTION);
            $section = SiteSections::where("key", $slug)->first();


            $section_data = json_decode(json_encode($section->value), true);

            $unique_id = uniqid();
            $section_data['items'][$id]['detailsItem'][$unique_id]['language']  = $language_wise_data;
            $section_data['items'][$id]['detailsItem'][$unique_id]['id']        = $unique_id;


            $update_data = [
                'key' => $slug,
                'value' => $section_data
            ];


            SiteSections::updateOrCreate(['key' => $slug], $update_data);

            return back()->with(['success' => 'Section item added/updated successfully!']);
        } catch (Exception $e) {

            return back()->withErrors(['error' => 'An error occurred while processing your request.']);
        }
    }

    /**
     * Method for update Feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureDetailsItemUpdate(Request $request)
    {
        $request->validate([
            'main_id'    => 'required|string',
            'details_item_id' => 'required|string',
        ]);

        $main_id = $request->main_id;
        $details_item_id = $request->details_item_id;
        $result = DB::table('site_sections')
            ->where('value->items->' . $main_id . '->id', $main_id)
            ->first();
        $key = $result->key;
        $value = json_decode($result->value, true);
        $basic_field_name = ['details' => 'required|string|max:250'];


        $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;

        $basic_field_name = ['details' => 'required|string|max:250'];

        $language_wise_data = $this->contentValidate($request, $basic_field_name, "requirements-details-edit");
        if ($language_wise_data instanceof RedirectResponse) return $language_wise_data;


        if (isset($value['items'][$main_id]['detailsItem'][$details_item_id])) {
            try {

                $value['items'][$main_id]['detailsItem'][$details_item_id]['language'] = $language_wise_data;
                $section_data['items'][$main_id]['detailsItem'][$details_item_id]['id'] = $details_item_id;

                DB::table('site_sections')
                    ->where('key', $key)
                    ->update(['value' => json_encode($value)]);

                return back()->with(['success' => ['Section item deleted successfully!']]);
            } catch (Exception $e) {
                return back()->with(['error' => ['Something went wrong! Please try again.']]);
            }
        } else {
            return back()->with(['error' => ['Section not found!']]);
        }
    }


    /**
     * Method for delete Feature item
     * @param string $slug
     * @param \Illuminate\Http\Request  $request
     */
    public function featureDetailsItemDelete(Request $request, $id, $parentId)
    {

        $request->validate([
            'target' => 'required|string',
        ]);

        $result = DB::table('site_sections')
            ->where('value->items->' . $id . '->id', $id)
            ->first();

        if ($result) {
            $value = json_decode($result->value, true);
            $key = $result->key;



            if (isset($value['items'][$id]['detailsItem'][$parentId])) {
                try {

                    unset($value['items'][$id]['detailsItem'][$parentId]);

                    DB::table('site_sections')
                        ->where('key', $key)
                        ->update(['value' => json_encode($value)]);

                    return back()->with(['success' => ['Section item deleted successfully!']]);
                } catch (Exception $e) {
                    return back()->with(['error' => ['Something went wrong! Please try again.']]);
                }
            } else {
                return back()->with(['error' => ['Section not found!']]);
            }
        } else {
            return back()->with(['error' => ['Section not found!']]);
        }
    }

    /**
     * Method for get languages form record with little modification for using only this class
     * @return array $languages
     */
    public function languages()
    {
        $languages = Language::whereNot('code', LanguageConst::NOT_REMOVABLE)->select("code", "name")->get()->toArray();
        $languages[] = [
            'name'      => LanguageConst::NOT_REMOVABLE_CODE,
            'code'      => LanguageConst::NOT_REMOVABLE,
        ];
        return $languages;
    }

    /**
     * Method for validate request data and re-decorate language wise data
     * @param object $request
     * @param array $basic_field_name
     * @return array $language_wise_data
     */
    public function contentValidate($request, $basic_field_name, $modal = null)
    {
        $languages = $this->languages();

        $current_local = get_default_language_code();
        $validation_rules = [];
        $language_wise_data = [];
        foreach ($request->all() as $input_name => $input_value) {
            foreach ($languages as $language) {
                $input_name_check = explode("_", $input_name);
                $input_lang_code = array_shift($input_name_check);
                $input_name_check = implode("_", $input_name_check);
                if ($input_lang_code == $language['code']) {
                    if (array_key_exists($input_name_check, $basic_field_name)) {
                        $langCode = $language['code'];
                        if ($current_local == $langCode) {
                            $validation_rules[$input_name] = $basic_field_name[$input_name_check];
                        } else {
                            $validation_rules[$input_name] = str_replace("required", "nullable", $basic_field_name[$input_name_check]);
                        }
                        $language_wise_data[$langCode][$input_name_check] = $input_value;
                    }
                    break;
                }
            }
        }
        if ($modal == null) {
            $validated = Validator::make($request->all(), $validation_rules)->validate();
        } else {
            $validator = Validator::make($request->all(), $validation_rules);
            if ($validator->fails()) {
                return back()->withErrors($validator)->withInput()->with("modal", $modal);
            }
            $validated = $validator->validate();
        }

        return $language_wise_data;
    }

    /**
     * Method for validate request image if have
     * @param object $request
     * @param string $input_name
     * @param string $old_image
     * @return boolean|string $upload
     */
    public function imageValidate($request, $input_name, $old_image)
    {
        if ($request->hasFile($input_name)) {
            $image_validated = Validator::make($request->only($input_name), [
                $input_name         => "image|mimes:png,jpg,webp,jpeg,svg",
            ])->validate();

            $image = get_files_from_fileholder($request, $input_name);
            $upload = upload_files_from_path_dynamic($image, 'site-section', $old_image);
            return $upload;
        }

        return false;
    }
}
