@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Health Package List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Health Package Edit'),
    ])
@endsection

@section('content')
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Edit Hospital Health Package') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.health-package.update',$package->uuid) }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        @include('admin.components.form.input', [
                            'label' => __('Package name') . '*',
                            'name' => 'name',
                            'placeholder' => __('Write Name') . '...',
                            'value' => old('name', $package->name),
                        ])
                    </div>
                    <div class="name-input-fild">
                        @include('admin.components.form.input', [
                            'label' => __('Package title') . '*',
                            'name' => 'title',
                            'placeholder' => __('Write Title') . '...',
                            'value' => old('title', $package->title),
                        ])
                    </div>
                    <div class="name-input-fild">
                        @include('admin.components.form.textarea', [
                            'label' => __('Package description') . '*',
                            'name' => 'description',
                            'placeholder' => __('Write Title') . '...',
                            'value' => old('title', $package->description),
                        ])
                    </div>

                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            @include('admin.components.form.input', [
                                'label' => __('Regular Price') . '*',
                                'name' => 'regular_price',
                                'placeholder' => __('Regular Price') . '...',
                                'value' => old('regular_price',get_amount($package->regular_price)),
                            ])

                            <div class="currency">
                                <p>{{ get_default_currency_code() }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            @include('admin.components.form.input', [
                                'label' => __('Offer Price') . '*',
                                'name' => 'offer_price',
                                'placeholder' => __('Offer Price') . '...',
                                'value' => old('offer_price',get_amount($package->offer_price)),
                            ])

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
                <button type="submit" class="btn-done btn--base">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')
@endpush
