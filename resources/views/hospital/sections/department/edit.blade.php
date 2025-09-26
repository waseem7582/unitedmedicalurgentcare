@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Department List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Department Edit'),
    ])
@endsection

@section('content')
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Update Hospital Department') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.department.update', $department->uuid) }}"
                    method="POST">
                    @csrf
                    <div class="name-input-fild">
                        @include('admin.components.form.input', [
                            'label' => __('Name') . '*',
                            'name' => 'name',
                            'placeholder' => __('Write Name') . '...',
                            'value' => old('name', $department->name),
                        ])
                    </div>
                    <div class="name-input-fild">
                        @include('admin.components.form.textarea', [
                            'label' => __('Description') . '(optional)',
                            'name' => 'description',
                            'value' => old('description', $department->description),
                        ])
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
