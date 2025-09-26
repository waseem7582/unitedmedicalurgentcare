  @php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::VENDOR_REQUIREMENTS_SECTION);
    $requirements = App\Models\Admin\SiteSections::getData($slug)->first();

@endphp
  <!-- Hospital Requirements -->
    <section class="hospital-requirements pb-80">
        <div class="container">
            <div class="section-tag pb-30">
                <span><i class="las la-heart"></i>
                    {{ $requirements->value->language->$app_local->title ?? ($requirements->value->language->$default->title ?? '') }}</span>
                <div class="row">
                    <div class="col-xl-10 col-lg-12">
                        <div class="section-title">
                            <h2 class="title">
                                {{ $requirements->value->language->$app_local->heading ?? ($requirements->value->language->$default->heading ?? '') }}
                            </h2>
                            <p> {{ $requirements->value->language->$app_local->sub_heading ?? ($requirements->value->language->$default->sub_heading ?? '') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mb-30-none justify-content-center">
                @foreach ($requirements->value->items ?? [] as $key => $value)
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-30">
                        <div class="required-content-area">
                            <div class="icon">
                                <i class="{{ $value->icon ?? '' }}"></i>
                            </div>
                            <div class="required-content">
                                <h3 class="title">
                                    {{ $value->language->$app_local->item_title ?? $value->language->$default->item_title }}
                                </h3>
                                <p>{{ $value->language->$app_local->item_description ?? $value->language->$default->item_description }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>