@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $faq_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::FAQ_SECTION);
    $faq = App\Models\Admin\SiteSections::getData($faq_slug)->first();

@endphp
<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        Start Faq
                    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<section class="faq-section pb-80">
    <div class="container">
        <div class="section-header-title">
            <div class="section-tag">
                <span><i class="las la-heart"></i>
                    {{ $faq->value->language->$app_local->title ?? ($faq->value->language->$default->title ?? '') }}</span>
            </div>
            <div class="faq-title pb-20">
                <h2 class="title">
                    {{ $faq->value->language->$app_local->heading ?? ($faq->value->language->$default->heading ?? '') }}
                </h2>
            </div>
        </div>
        <div class="row justify-content-center mb-20-none">
            @php
                $items = $faq->value->items ?? [];
                $item_data = (array) $items;
                $converted_data =
                    count($item_data) > 0 ? array_chunk($item_data, ceil(count($item_data) / 2)) : [[], []];
                $part1 = $converted_data[0];
                $part2 = $converted_data[1];
            @endphp
            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="faq-wrapper">
                    @foreach ($part1 ?? [] as $item)
                        @if ($item->status == 1)
                            <div class="faq-item">
                                <h3 class="faq-title"><span
                                        class="title">{{ $item->language->$app_local->question ?? ($item->language->$default->question ?? '') }}</span><span
                                        class="right-icon"></span></h3>
                                <div class="faq-content">
                                    <p>{{ $item->language->$app_local->answer ?? ($item->language->$default->answer ?? '') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
            <div class="col-xl-6 col-lg-6 mb-20">
                <div class="faq-wrapper pb-30">
                    @foreach ($part2 ?? [] as $item)
                        @if ($item->status == 1)
                            <div class="faq-item">
                                <h3 class="faq-title"><span class="title">
                                        {{ $item->language->$app_local->question ?? ($item->language->$default->question ?? '') }}</span><span
                                        class="right-icon"></span></h3>
                                <div class="faq-content">
                                    <p>{{ $item->language->$app_local->answer ?? ($item->language->$default->answer ?? '') }}
                                    </p>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</section>

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        End Faq
                    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
