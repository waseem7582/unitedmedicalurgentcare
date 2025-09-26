@php
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;
    $app_local = get_default_language_code();

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::STATISTICS);
    $statistics = App\Models\Admin\SiteSections::getData($slug)->first();
@endphp
<!-- statistics-section -->
<div class="statistics-section ptb-80">
    <div class="container">
        <div class="section-tag pb-20">
            <span><i class="las la-heart"></i>
                {{ $statistics->value->language->$app_local->title ?? ($statistics->value->language->$default->title ?? '') }}</span>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">
                            {{ $statistics->value->language->$app_local->heading ?? ($statistics->value->language->$default->heading ?? '') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center pt-40 mb-20-none">
            @foreach ($statistics->value->items ?? [] as $key => $value)
                <div class="col-lg-3 col-md-6 col-sm-6 mb-20">
                    <div class="overview-details-area">
                        <div class="counter">
                            <div class="odo-area d-flex">
                                @php
                                    $about_statistic = numeric_unit_converter(
                                        $value->language->$app_local->item_counter_value ?? '',
                                    );
                                @endphp
                                <h5 class="odo-title odometer"
                                    data-odometer-final="{{ $value->language->$app_local->item_counter_value }}">
                                </h5>
                                <h3 class="title">{{ $about_statistic->unit }}</h3>
                            </div>
                        </div>
                        <div class="overview-details">
                            <h4 class="title">
                                {{ $value->language->$app_local->item_title ?? ($value->language->$default->item_title ?? '') }}
                            </h4>
                            <p>{{ $value->language->$app_local->item_description ?? ($value->language->$default->item_description ?? '') }}
                            </p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>
