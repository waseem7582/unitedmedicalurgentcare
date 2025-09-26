@extends('frontend.layouts.master')

@section('content')
    <section class="find-parlour-section">
        @include('frontend.section.branch-search')
    </section>
    <section class="branch-item-area ptb-80">
        <div class="container">
            <div class="row mb-20-none justify-content-center">
                @foreach ($branch as $item)
                <div class="col-xl-4 col-lg-4 col-md-6 col-sm-6 mb-50">
                    <div class="branch-item">
                        <div class="icon-wrapper">
                            <div class="icon-area">
                                <i class="las la-hospital"></i>
                            </div>
                        </div>
                        <h4>{{ $item->name }}</h4>
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
