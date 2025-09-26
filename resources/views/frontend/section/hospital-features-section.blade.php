@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::VENDOR_FEATURES_SECTION);
    $features = App\Models\Admin\SiteSections::getData($slug)->first();

  

@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                            Start Hospital Feature
                        ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Hospital Feature
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

<section class="vendor-feature ptb-80">
    <div class="container">
        <div class="section-tag pb-30">
            <span><i class="las la-heart"></i>
                {{ $features->value->language->$app_local->section_title ?? ($features->value->language->$default->section_title ?? '') }}</span>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">
                            {{ $features->value->language->$app_local->heading ?? ($features->value->language->$default->heading ?? '') }}
                        </h2>
                        <p> {{ $features->value->language->$app_local->description ?? ($features->value->language->$default->description ?? '') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>
        @php
            $items = $features->value->items ?? [];
            $item_data = (array) $items;

            $converted_data = count($item_data) > 0 ? array_chunk($item_data, ceil(count($item_data) / 2)) : [[], []];

            $part1 = $converted_data[0] ?? [];
            $part2 = $converted_data[1] ?? [];

            // Initialize a counter for continuous numbering
            $counter = 1;
        @endphp

        <div class="row mb-20-none">
            <div class="col-lg-6 mb-20">
                @foreach ($part1 ?? [] as $item)
                <div class="feature-content">
                    <h3 class="heading text--base">{{ $counter++ }}. {{ $item->language->$app_local->title ?? '' }}</h3>
                    <ul class="feature-listing">
                        @foreach ($item->detailsItem as $data)
                            <li> {{ $data->language->$app_local->details ?? ($item->language->$default->details ?? '') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
            <div class="col-lg-6 mb-20">
                @foreach ($part2 ?? [] as $item)
                <div class="feature-content">
                    <h3 class="heading text--base">{{ $counter++ }}. {{ $item->language->$app_local->title ?? '' }}</h3>
                    <ul class="feature-listing">
                        @foreach ($item->detailsItem as $data)
                            <li> {{ $data->language->$app_local->details ?? ($item->language->$default->details ?? '') }}
                            </li>
                        @endforeach
                    </ul>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</section>