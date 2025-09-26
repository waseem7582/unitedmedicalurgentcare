@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::DOWNLOAD_APP_SECTION);
    $download_app = App\Models\Admin\SiteSections::getData($slug)->first();
@endphp
<!-- app section -->
<section class="app-section ptb-80">
    <div class="container">
        <div class="section-tag">
            <span><i class="las la-heart"></i>
                {{ $download_app->value->language->$app_local->section_title ?? ($download_app->value->language->$default->section_title ?? '') }}</span>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">
                            {{ $download_app->value->language->$app_local->heading ?? ($download_app->value->language->$default->heading ?? '') }}
                        </h2>
                        <p>{{ $download_app->value->language->$app_local->sub_heading ?? ($download_app->value->language->$default->sub_heading ?? '') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-5 pb-30">
                <div class="app-btn-wrapper">
                    @foreach ($download_app->value->items ?? [] as $item)
                        <a href="{{ $item->link }}" class="app-btn">
                            <div class="app-icon">
                                <i class="{{ $item->icon ?? '' }}"></i>
                            </div>
                            <div class="content">
                                <span>{{ $item->language->$app_local->item_title ?? ($item->language->$default->item_title ?? '') }}</span>
                                <h5 class="title">
                                    {{ $item->language->$app_local->item_heading ?? ($item->language->$default->item_heading ?? '') }}
                                </h5>
                            </div>
                            <div class="icon">
                                <img src="{{ get_image($item->image, 'site-section') }}" alt="element">
                            </div>
                            <div class="app-qr">
                                <img src="{{ get_image($item->image, 'site-section') }}" alt="element">
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <div class="col-lg-7">
                <div class="app-img">
                    <img src="{{ get_image($download_app?->value?->image, 'site-section') }}" alt="img">
                </div>
            </div>
        </div>
    </div>
</section>
