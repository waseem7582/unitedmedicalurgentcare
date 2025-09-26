<!--  how its work -->
@php

    $app_local = get_default_language_code() ?? 'en';
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::HOW_ITS_WORK_SECTION);
    $how_its_work = App\Models\Admin\SiteSections::getData($slug)->first();

@endphp


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        How to work section
 ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="how-work-section ptb-80">
    <div class="container">
        <div class="section-tag pb-20">
            <span><i class="las la-heart"></i>
                {{ $how_its_work->value->language->$app_local->section_title ?? ($how_its_work->value->language->$default->section_title ?? '') }}
            </span>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">
                            {{ $how_its_work->value->language->$app_local->heading ?? ($how_its_work->value->language->$default->heading ?? '') }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
        @php
            $items = $how_its_work->value->items ?? [];
            $item_data = (array) $items;

            $converted_data = count($item_data) > 0 ? array_chunk($item_data, ceil(count($item_data) / 2)) : [[], []];

            $part1 = $converted_data[0] ?? [];
            $part2 = $converted_data[1] ?? [];

        @endphp

    <div class="how-work-area">
        <div class="row mb-20-none">
            <div class="col-lg-6 mb-20">
                <div class="steps-content">
                    <div class="step-listing">
                        <div class="row mb-20-none">
                            @foreach ($part1 ?? [] as $key => $item)
                            <div class="col-lg-12 mb-20">
                                <div class="content">
                                    <span>Step {{ $loop->iteration }}</span>
                                    <h4 class="title">{{ $item->language->$app_local->title ?? ($item->language->$default->title ?? '') }}</h4>
                                    <p>{{ $item->language->$app_local->description ?? ($item->language->$default->description ?? '') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6 mb-20">
                <div class="steps-content">
                    <div class="step-listing">
                        <div class="row mb-20-none">
                            @foreach ($part2 ?? [] as $key => $item)
                            <div class="col-lg-12 mb-20">
                                <div class="content">
                                    <span>Step {{ $loop->iteration }}</span>
                                    <h4 class="title">{{ $item->language->$app_local->title ?? ($item->language->$default->title ?? '') }}</h4>
                                    <p>{{ $item->language->$app_local->description ?? ($item->language->$default->description ?? '') }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    </div>
</section>
