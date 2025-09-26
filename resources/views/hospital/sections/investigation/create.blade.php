@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Investigation List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Investigation Add'),
    ])
@endsection

@section('content')
    <!-- Modal Add -->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Add Hospital Investigation') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.investigation.store') }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        <label>{{ __('Name') }} <span class="text--base">*</span></label>
                        <input type="text" name="name" class="form--control" placeholder="{{ __('Name') }}">
                    </div>
                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            <label>{{ __('Regular Price') }} <span class="text--base">*</span></label>
                            <input type="text" name="regular_price" class="form--control"
                                placeholder={{ __('Enter Price') }}>
                            <div class="currency">
                                <p>{{ get_default_currency_code() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            <label>{{ __('Offer Price') . ' (' . __('optional') . ')' }} <span class="text--base"></span></label>
                            <input type="text" name="offer_price" class="form--control"
                                placeholder={{ __('Offer Price') }}>
                            <div class="currency">
                                <p>{{ get_default_currency_code() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            <label>{{ __('Category') }} <span class="text--base">*</span></label>
                            @foreach ($investigation_cat as $option)
                                <div class="col-lg-4 col-md-4 col-sm-6 d-flex mb-3">
                                    <div class="box-checkbox" style="flex: 0;">
                                        <input type="checkbox" class="form-check-input" name="categories[]"
                                            value="{{ $option->id }}" id="day-{{ $loop->index }}">
                                    </div>
                                    <div class="box-name mt-1">
                                        <label class="form-check-label ms-1" for="day-{{ $loop->index }}">
                                            {{ ucfirst($option->name) }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
            </div>
            <div class="modal-footer justify-content-between border-0">
                <button type="button" class="btn-cancel" onclick="window.history.back();">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" class="btn-done btn--base">{{ __('Save') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')

@endpush
