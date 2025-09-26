@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Branch Edit'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Branch Edit'),
    ])
@endsection

@section('content')
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body">
                <h4 class="title text-center">{{ __('Edit Hospital branch') }}</h4>
                <form class="card-form" action="{{ setRoute('hospitals.branch.update',$branch->uuid) }}" method="POST">
                    @csrf
                    <div class="name-input-fild">
                        @include('admin.components.form.input', [
                            'label' => __('Name') . '*',
                            'name' => 'name',
                            'placeholder' => __('Write Name') . '...',
                            'value' => old('name', $branch->name),
                        ])
                    </div>
                    <div class="name-input-fild"><br>
                        <label>{{ __('Select Department') }}<span class="text--base">*</span></label>
                        <select name="departments[]" class="form--control select2-auto-tokenize select2-hidden-accessible"
                            placeholder="Add Department" multiple required>
                            @foreach ($department as $department)
                                <option value="{{ $department->id }}"
                                    {{ in_array($department->id, $branch->departments->pluck('id')->toArray()) ? 'selected' : '' }}>
                                    {{ $department->name }}
                                </option>
                            @endforeach
                        </select>
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
    <script>
        $(document).ready(function() {
            $('.select2-auto-tokenize').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });
    </script>
@endpush
