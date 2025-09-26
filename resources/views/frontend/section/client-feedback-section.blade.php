@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;

    $feedBack_slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::CLIENT_FEEDBACK_SECTION);
    $feedBack = App\Models\Admin\SiteSections::getData($feedBack_slug)->first();

@endphp
  <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        Testimonial section
                    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    <section class="testimonial-section pb-80">
        <div class="container">
            <div class="section-tag pb-20">
                <span><i class="las la-heart"></i>
                    {{ $feedBack->value->language->$app_local->title ?? ($feedBack->value->language->$default->title ?? '') }}</span>
            </div>
            <div class="row mb-40-none">
                <div class="col-lg-6 col-md-12 mb-40">
                    <div class="customer-says">
                        <h3 class="title">
                            {{ $feedBack->value->language->$app_local->heading ?? ($feedBack->value->language->$default->heading ?? '') }}
                        </h3>
                        <p> {{ $feedBack->value->language->$app_local->sub_heading ?? ($feedBack->value->language->$default->sub_heading ?? '') }}
                        </p>

                        @php
                            $client_value = $feedBack->value->items ?? [];
                            $item_count = count((array) $client_value);
                            $client_image = array_slice((array) $client_value, -3, 3, true); // Get the latest 3 items
                        @endphp
                        <div class="client-img d-flex">
                            @foreach ($client_image ?? [] as $key => $item)
                                <div class="img-1">
                                    <img src="{{ get_image($item->image, 'site-section') ?? '' }}" alt="client">
                                </div>
                            @endforeach
                        </div>
                        <div class="comment pt-2">
                            <P><span class="text--base">{{ $item_count }} </span> {{ _('Customer Reviews') }}</P>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 mb-40">
                    <div class="testimonial-section">
                        <div class="testimonial-slider">
                            <div class="swiper-wrapper">
                                @php
                                    $client_comment = $feedBack->value->items ?? [];
                                @endphp
                                @foreach ($client_comment ?? [] as $key => $item)
                                    <div class="swiper-slide">
                                        <div class="testimonial-wrapper">
                                            <div class="testimonial-ratings">
                                                @for ($i = 0; $i < $item->star; $i++)
                                                    <i class="fas fa-star"></i>
                                                @endfor
                                            </div>
                                            <p>{{ $item->language->$app_local->comment ?? ($item->language->$default->comment ?? '') }}
                                            </p>
                                            <div class="client-details d-flex justify-content-between">
                                                <div class="client-img">
                                                    <img src={{ get_image($item->image, 'site-section') ?? '' }}
                                                        alt="client">
                                                </div>
                                                <div class="client-title text--base">
                                                    <h4 class="title text--base">
                                                        {{ $item->language->$app_local->name ?? ($item->language->$default->name ?? '') }}
                                                    </h4>
                                                    <P>{{ $item->language->$app_local->designation ?? ($item->language->$default->designation ?? '') }}
                                                    </P>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="swiper-pagination"></div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
                        End Testimonial section
                    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->