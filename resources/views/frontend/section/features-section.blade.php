<!-- Features -->
@php

    $app_local = get_default_language_code() ?? 'en';
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FEATURES_SECTION);
    $features = App\Models\Admin\SiteSections::getData($slug)->first();

@endphp

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Feature section
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="feature-section pt-80">
    <div class="container">
        <div class="section-tag pb-20">
            <span><i class="las la-heart"></i>
                {{ $features->value->language->$app_local->section_title ?? ($features->value->language->$default->section_title ?? '') }}</span>
                <h2 class="title">{{ $features->value->language->$app_local->heading ?? ($features->value->language->$default->heading ?? '') }}</h2>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">
                            {{ $features->value->language->$app_local->description_title ??
                                ($features->value->language->$default->description_title ?? '') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="feature-section-area bg-overlay-base bg_img" data-background="{{ asset('frontend/images/element/feature-bg.webp') }}">
            <div class="feature-list mb-20-none">
                @php
                    $step_key = 0;
                    $features = $features->value->items ?? [];
                @endphp
                @foreach ($features as $items)
                    @php
                        $step_key++;
                    @endphp
                    <div class="feature-item mb-20">
                        <div class="feature-sirial">
                            <label>{{ $items->language->$app_local->title }}</label>
                            <div class="sirial-number">{{ $step_key }}</div>
                        </div>
                        <div class="feature-details">
                            <div class="content">
                                <p>{{ $items->language->$app_local->details ??''}}</p>
                            </div>
                            <div class="icon">
                                <i class="las la-heart"></i>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
