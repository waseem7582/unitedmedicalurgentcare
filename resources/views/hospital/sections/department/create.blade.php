@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Department List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Department Add'),
    ])
@endsection

@section('content')
    <!-- Modal Add Department-->
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="title text-center">{{ __('Add Hospital Department') }}</h4>
                    <form class="card-form" action="{{ setRoute('hospitals.department.store') }}" method="POST">
                        @csrf
                        <div class="name-input-fild">
                            <label>{{ __('Department Name') }} <span class="text--base">*</span></label>
                            <input type="text" name="name" class="form--control" placeholder="{{ __('Department Name') }}">
                        </div>
                        <div class="name-input-fild">
                            @include('admin.components.form.textarea',[
                                    'label'         => __("Description").__("optional"),
                                    'name'          => "description",
                                    'placeholder'   =>__('Description')
                                ])
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
