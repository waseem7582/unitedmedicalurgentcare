@php

    $app_local = get_default_language_code() ?? 'en';
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::BANNER_SECTION);
    $banner = App\Models\Admin\SiteSections::getData($slug)->first();

@endphp

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Banner Section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<div class="banner-section bg-overlay-banner bg_img"
    data-background="{{ isset($banner->value->image) ? get_image($banner->value->image, 'site-section') : asset('path/to/default/image.jpg') }}">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 col-lg-10">
                <div class="banner-content">
                    <h1 class="title">
                        {{ $banner->value->language->$app_local->heading ?? ($banner->value->language->$default->heading ?? '') }}
                    </h1>
                    <p>{{ $banner->value->language->$app_local->sub_heading ?? '' }}</p>
                    <div class="banner-btn">
                        <a href="{{ setRoute('frontend.find.doctor') }}"
                            class="btn--base btn">{{ $banner->value->language->$app_local->left_button ?? ($banner->value->language->$default->left_button ?? '') }}</a>
                        <a href="{{ setRoute('hospitals.login') }}"
                            class="btn--base btn">{{ $banner->value->language->$app_local->right_button ??
                                ($banner->value->language->$default->right_button ?? '') }}
                            <i class="fab fa-telegram-plane"></i></a>
                    </div>
                    <div class="banner-element">

                        <img src="{{ isset($banner->value->secondary_image) ? get_image($banner->value->secondary_image, 'site-section') : asset('path/to/default/image.jpg') }}"}}">
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Banner Section
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="banner-bottom-section">
    <div class="container">
        <div class="banner-bottom-area">
            <div class="row justify-content-center mb-20-none">

                @foreach ($banner->value->items ?? [] as $item)
                    <div class="col-lg-4 col-md-6 mb-20">
                        <div class="banner-bottom-wedget">
                            <div class="icon">
                                <img src="{{ get_image($item->image, 'site-section') }}" alt="location">
                            </div>
                            <div class="wedget-deatils">
                                <h3 class="title">
                                    {{ $item->language->$app_local->title ?? ($item->language->$default->title ?? '') }}
                                </h3>
                                <p>{{ $item->language->$app_local->description ?? ($item->language->$default->description ?? '') }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</section>
