@extends('hospital.layouts.master')

@push('css')

@endpush

@section('breadcrumb')
    @include('hospital.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => route('hospitals.dashboard'),
        ]
    ], 'active' => __("Add Tickets")])
@endsection

@section('content')
    <div class="row mb-20-none">
        <div class="col-xl-12 col-lg-12 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __("Add New Ticket") }}</h4>
                </div>
                <div class="card-body">
                    <form class="card-form" action="{{ route('hospitals.support.ticket.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="row">
                            <input type="text" name="name" value="{{ $hospital->username }}" hidden>
                            <input type="text" name="email" value="{{ $hospital->email }}" hidden>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input',[
                                    'label'         => __("Subject")."*",
                                    'name'          => "subject",
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.textarea',[
                                    'label'         => __("Message")."*",
                                    'name'          => "desc",
                                ])
                            </div>

                            <div class="col-xl-12 col-lg-12 form-group">
                                <label>{{ __("Attachments (Optional)") }}</label>
                                <input type="file" class="" name="attachment[]" id="attachment" multiple>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12">
                            <button type="submit" class="btn--base w-100">{{ __("Add New") }}</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>

    </script>
@endpush
