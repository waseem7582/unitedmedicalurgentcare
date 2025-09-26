@extends('user.layouts.master')

@push('css')
@endpush
@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Find Doctor'),
                'url' => setRoute('user.profile.index'),
            ],
        ],
    ])
@endsection
@section('content')
    <section class="find-parlour-section">
        @include('frontend.section.doctor-search')
    </section>

    <section class="doctor-list-section pt-60 pb-80">
        <div class="container">
            <div class="row justify-content-center mb-20-none">
                @forelse ($doctor ?? [] as $item)
                    <div class="col-lg-6 col-md-10 mb-20">
                        <div class="doctor-card">
                            <div class="doctor-thumb">
                                <img src="{{ get_image($item?->image, 'doctor') }}" alt="doctor-img">
                            </div>
                            <div class="doctor-details">
                                <h4 class="title"><i class="fas la-user"></i> {{ $item->name ?? '' }}</h4>
                                <p> {{ $item->title ?? '' }}</p>
                                <strong>{{ $item->qualification ?? '' }}</strong>
                                <p> { {{ $item->specialty ?? '' }} }</p>
                                <div class="booking-btn">
                                    <a href="{{ setRoute('frontend.doctor.booking.index', $item->slug) }}"
                                        class="btn--base btn">{{ __('Book Now') }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-xl-8">
                        <div class="alert alert-primary alert-section-bg text-center">
                            {{ __('No Record Found!') }}
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>
@endsection
@push('script')
@endpush
