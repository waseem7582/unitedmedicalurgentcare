@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Branch List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Branch Add'),
    ])
@endsection

@section('content')
    <!-- Modal Add Branch-->
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Add Hospital branch') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.branch.store') }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        <label>{{ __('Branch Name') }} <span class="text--base">*</span></label>
                        <input type="text" name="name" class="form--control" placeholder="{{ __('Branch Name') }}">
                    </div>
                    <div class="name-input-fild" data-select2-id="select2-data-12-dvm4">
                        <label>{{ __('Select Department') }}<span class="text--base">*</span></label>
                        <select name="departments[]" class="form--control select2-auto-tokenize select2-hidden-accessible"
                            placeholder="Add Department" multiple required>
                            @foreach ($department as $department)
                                <option value="{{ $department->id }}">{{ $department->name }}</option>
                            @endforeach
                        </select>
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
    <script>
        $(document).ready(function() {
            $('.select2-auto-tokenize').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });
    </script>
@endpush
