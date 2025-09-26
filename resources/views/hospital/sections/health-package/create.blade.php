@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Health Package List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Health Package Add'),
    ])
@endsection

@section('content')
    <!-- Modal Add -->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Add Hospital Health Package') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.health-package.store') }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        <label>{{ __('Package Name') }} <span class="text--base">*</span></label>
                        <input type="text" name="name" class="form--control" placeholder="{{ __('Enter Name') }}">
                    </div>
                    <div class="name-input-fild">
                        <label>{{ __('Package Title') }} <span class="text--base">*</span></label>
                        <input type="text" name="title" class="form--control" placeholder="{{ __("Enter title") }}">
                    </div>
                    <div class="name-input-fild">
                        <label>{{ __('Package description') }} <span class="text--base">*</span></label>
                        <textarea name="description" id="" cols="30" rows="10"></textarea>
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
                                placeholder={{ __('Enter Price') }}>
                            <div class="currency">
                                <p>{{ get_default_currency_code() }}</p>
                            </div>
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
