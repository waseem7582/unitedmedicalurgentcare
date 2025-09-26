@php

    $app_local = get_default_language_code() ?? 'en';
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;
    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::WHY_CHOICE_US_SECTION);
    $why_choice_us = App\Models\Admin\SiteSections::getData($slug)->first();


@endphp
<!-- Why Choice us -->
<section class="why-choice-us pt-80">
    <div class="container">
        <div class="row">
            <div class="col-xl-8 col-lg-10">
                <div class="how-its-work-title">
                    <h4 class="titte text--base pb-20">
                        {{ $why_choice_us->value->language->$app_local->title ?? ($why_choice_us->value->language->$default->title ?? '') }}
                    </h4>
                    <h2 class="titte">
                        {{ $why_choice_us->value->language->$app_local->heading ?? ($why_choice_us->value->language->$default->heading ?? '') }}<i
                            class="las la-arrow-right"></i></h2>
                    <p>{{ $why_choice_us->value->language->$app_local->sub_heading ?? ($why_choice_us->value->language->$default->sub_heading ?? '') }}
                    </p>
                </div>
            </div>
        </div>
        <div class="choice-us-area pt-40">
            <div class="row mb-20-none">
                @if (isset($why_choice_us->value->items))
                    @foreach ( $why_choice_us->value->items ?? [] as $key => $item)
                 
                        <div class="col-xl-4 col-lg-6 col-md-6 mb-20">
                            <div class="choice-item">
                                <div class="icon">
                                    <i class="{{ $item->icon ?? '' }}"></i>
                                </div>
                                <div class="choice-content">
                                    <h4 class="title">
                                        {{ $item->language->$app_local->item_title ?? ($item->language->$default->item_title ?? '') }}
                                    </h4>
                                    <p>{{ $item->language->$app_local->item_description ?? ($item->language->$default->item_description ?? '') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
</section>
