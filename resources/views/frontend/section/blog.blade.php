
@php
    $app_local      = get_default_language_code();
    $default        = App\Constants\LanguageConst::NOT_REMOVABLE;
    $slug           = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::BLOG_SECTION);
    $blog           = App\Models\Admin\SiteSections::getData($slug)->first();
@endphp
<!-- blog section -->
<section class="blog-section ptb-60">
    <div class="container">
        <div class="section-tag pb-20">
            <span><i class="las la-heart"></i> {{ $blog->value->language->$app_local->title ?? $blog->value->language->$default->title ?? "" }}</span>
            <div class="row">
                <div class="col-xl-10 col-lg-12">
                    <div class="section-title">
                        <h2 class="title">{{ $blog->value->language->$app_local->heading ?? $blog->value->language->$default->heading ?? "" }}</h2>
                    </div>
                </div>
            </div>
        </div>
        @foreach ($blogs ?? [] as $item)
        <div class="blog-area pt-40">
            <div class="blog-item mb-30">
                <div class="row">
                    <div class="col-lg-4 col-md-4">
                        <div class="blog-img">
                            <img src="{{ get_image($item->data->image ?? '','site-section') }}" alt="img">
                        </div>
                    </div>
                    <div class="col-lg-8 col-md-8">
                        <div class="blog-content">
                            <h3 class="title">{{ Str::words($item->data->language->$app_local->title ?? $item->data->language->$default->title ?? "","5","...") }}</h3>
                            <p>{!! Str::words($item->data->language->$app_local->description ?? $item->data->language->$default->description ?? '','10','...') !!}</p>
                            <div class="blog-btn">
                                <a href="{{ setRoute('frontend.blog.details',$item->slug) }}" class="btn--base btn">{{ __("Blog Details") }}</a>
                                <div class="blog-date">
                                    <i class="las la-history"></i>
                                    <p>{{ $blog?->created_at?->format('d-M-Y') ?? ""}}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
        {{ get_paginate($blogs) }}
    </div>
</section>