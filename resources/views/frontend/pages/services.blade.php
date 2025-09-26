@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;
    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::SERVICES_SECTION);
    $services = App\Models\Admin\SiteSections::getData($slug)->first();
@endphp
@extends('frontend.layouts.master')

@section('content')



 @include('frontend.section.banner-section')
    <!-- service -->
    <section class="service-section ptb-80">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <h4 class="title text--base pb-20"> {{ $services->value->language->$app_local->title ?? ($services->value->language->$default->title ?? '') }}</h4>
                    <h2 class="title">{{ $services->value->language->$app_local->heading ?? ($services->value->language->$default->heading ?? '') }}</h2>
                    <p>{{ $services->value->language->$app_local->sub_heading ?? ($services->value->language->$default->sub_heading ?? '') }}</p>
                </div>
            </div>
            <div class="row pt-40 mb-30-none">
                    @php
                        $service_items= $services->value->items ?? [];

                    @endphp
                @foreach ($service_items ?? [] as $key=>$item  )
                <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-6 mb-30">
                    <div class="service-area">
                        <div class="service-icon">
                            <img src="{{ get_image($item->image, 'site-section') ?? '' }}" alt="icon">
                        </div>
                        <div class="service-type">
                            <h3 class="title">{{ $item->language->$app_local->title ?? ($services->language->$default->title ?? '') }}</h3>
                            <p>{{ $item->language->$app_local->description ?? ($services->language->$default->description ?? '') }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection
