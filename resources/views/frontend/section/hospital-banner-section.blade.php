   @php
       $app_local = get_default_language_code();
       $default = App\Constants\LanguageConst::NOT_REMOVABLE;

       $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::VENDOR_BANNER_SECTION);
       $banner = App\Models\Admin\SiteSections::getData($slug)->first();

   @endphp
   <!-- Hospital Landing page -->
   <section class="vendor-banner bg-overlay-vendor bg_img vendor-bg"
       data-background="{{ isset($banner->value->image) ? get_image($banner->value->image, 'site-section') : asset('path/to/default/image.jpg') }}">
       <div class="container">
           <div class="row">
               <div class="col-xl-8 col-lg-10 col-md-12">
                   <div class="vendor-banner-content">
                       <div class="banner-title">
                           <h1 class="title">
                               {{ $banner->value->language->$app_local->heading ?? ($banner->value->language->$default->heading ?? '') }}
                           </h1>
                       </div>
                       <div class="banner-subtitle">
                           <p>{{ $banner->value->language->$app_local->sub_heading ?? '' }}</p>
                       </div>
                       <div class="banner-btn">
                           <a href="{{ setRoute('hospitals.login') }}"
                               class="btn--base">{{ $banner->value->language->$app_local->button ?? ($banner->value->language->$default->button ?? '') }}
                               <i class="las la-stethoscope"></i></a>
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </section>
