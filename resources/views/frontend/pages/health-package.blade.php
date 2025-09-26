@extends('frontend.layouts.master')

@section('content')
    <section class="find-parlour-section">
        @include('frontend.section.package-search')
    </section>
    <section class="investigation-item-area ptb-80">
        <div class="container">
            <div class="row mb-20-none justify-content-center">
                @foreach ($package as $item)
                    <div class="col-lg-3 col-md-3 col-sm-6 mb-20">
                        <div class="investigation-item">
                            <h3 class="title">{{ $item->name }}</h3>

                                @if ($item->offer_price)
                                    <span class="price text--base"><b>{{ get_amount($item->offer_price) }}
                                            {{ get_default_currency_code() }}</b></span>
                                    <span class="price-cut">{{ get_amount($item->regular_price) }}
                                        {{ get_default_currency_code() }}</span>
                                @else
                                    <span class="price text--base"><b>{{ get_amount($item->regular_price) }}
                                            {{ get_default_currency_code() }}</b></span>
                                @endif

                            <p>{{ $item->title }}</p>
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
