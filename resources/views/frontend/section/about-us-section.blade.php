@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;
    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::ABOUT_US_SECTION);
    $about = App\Models\Admin\SiteSections::getData($slug)->first();
 

@endphp

  <!-- about section -->
    <section class="about-section pt-60">
        <div class="container">
            <div class="section-tag">
                <span><i class="las la-heart"></i>
                    {{ $about->value->language->$app_local->title ?? ($about->value->language->$default->title ?? '') }}</span>
            </div>
            <div class="row mb-30-none">
                <div class="col-lg-6 mb-30">
                    <div class="about-content">
                        <h2 class="title">
                            {{ $about->value->language->$app_local->heading ?? ($about->value->language->$default->heading ?? '') }}
                        </h2>
                        <p class="sub-title">
                            {{ $about->value->language->$app_local->sub_heading ?? ($about->value->language->$default->sub_heading ?? '') }}
                        </p>
                        <div class="about-website">
                            <ul class="about-list">
                                @foreach ($about->value->items ?? [] as $key => $item)
                                    <li>
                                        <i class="las la-arrow-right"></i>
                                        {{ $item->language->$app_local->title ?? ($item->language->$default->title ?? '') }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mb-30">
                    <div class="about-img">
                        <img src="{{ isset($about->value->image) ? get_image($about->value->image, 'site-section') : asset('path/to/default/image.jpg') }}"
                            alt="img">
                    </div>
                </div>
            </div>
        </div>
    </section>