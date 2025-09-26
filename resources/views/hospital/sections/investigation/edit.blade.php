@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Investigation Edit'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Investigation Edit'),
    ])
@endsection

@section('content')
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Add Hospital Investigation') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.investigation.update',$investigation->uuid) }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        @include('admin.components.form.input', [
                            'label' => __('Name') . '*',
                            'name' => 'name',
                            'placeholder' => __('Write Name') . '...',
                            'value' => old('name', $investigation->name),
                        ])
                    </div>
                    <div class="col-12 mb-10">
                        <div class="price-input-fild">
                            @include('admin.components.form.input', [
                                'label' => __('Regular Price') . '*',
                                'name' => 'regular_price',
                                'placeholder' => __('Regular Price') . '...',
                                'value' => old('regular_price',get_amount($investigation->regular_price)),
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
                                'value' => old('offer_price', $investigation->offer_price !== null ? get_amount($investigation->offer_price) : ''),
                            ])

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
                                            value="{{ $option->id }}" id="day-{{ $loop->index }}"   {{ in_array($option->id, $investigation->investigationCategory->pluck('id')->toArray()) ? 'checked' : '' }}>
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
                <button type="submit" class="btn-done btn--base">{{ __('Update') }}</button>
                </form>
            </div>
        </div>
    </div>
@endsection


@push('script')

@endpush
