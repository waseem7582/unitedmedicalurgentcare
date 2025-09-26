@extends('frontend.layouts.master')

@section('content')
    <section class="find-parlour-section">
        @include('frontend.section.investigation-search')
    </section>
    <section class="investigation-item-area ptb-80">
        <div class="container">
            <div class="row mb-20-none justify-content-center">
                @foreach ($investigation as $item)
                    <div class="col-lg-3 col-md-3 col-sm-6 mb-20">
                        <div class="investigation-item">
                            <h5 class="title text-uppercase">{{ $item->name }}</h5>
                            <span class="price">{{ __('Price') }}: {{get_amount($item->offer_price ?? '')  }}</span>
                            <span class="price-cut">{{ get_amount($item->regular_price ?? '')  }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        var message = '@json($message)';
        throwMessage('error', JSON.parse(message));
    </script>
@endpush
