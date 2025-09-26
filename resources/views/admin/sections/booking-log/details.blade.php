@extends('admin.layouts.master')

@push('css')

    <style>
        .fileholder {
            min-height: 374px !important;
        }

        .fileholder-files-view-wrp.accept-single-file .fileholder-single-file-view,.fileholder-files-view-wrp.fileholder-perview-single .fileholder-single-file-view{
            height: 330px !important;
        }
    </style>
@endpush

@section('page-title')
    @include('admin.components.page-title',['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb',['breadcrumbs' => [
        [
            'name'  => __("Dashboard"),
            'url'   => setRoute("admin.dashboard"),
        ],

    ], 'active' => __("Booking Details")])
@endsection

@section('content')
<div class="booking-details-log">
    <div class="row mb-30-none">
        <div class="col-lg-6 mb-30">
            <div class="transaction-area">
                <h4 class="title mb-0"><i class="fas fa-user text--base me-2"></i>{{ __("Doctor Information") }}</h4>
                <div class="content pt-0">
                    <div class="list-wrapper">
                        <ul class="list">
                            <li>{{ __("DoctorName") }}<span>{{ $data->doctor->name ?? '' }}</span></li>
                            <li>{{ __("Experience") }}<span>{{ $data->doctor->experience ?? '' }}</span></li>
                            <li>{{ __("Contact") }}<span>{{ $data->doctor->contact ?? '' }}</span></li>
                            <li>{{ __("Address") }}<span>{{ $data->doctor->address ?? '' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-30">
            <div class="transaction-area">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="title mb-0"><i class="fas fa-user text--base me-2"></i>{{ __("Service & Schedule Information") }}</h4>
                </div>
                <div class="content pt-0">
                    <div class="list-wrapper">
                        <ul class="list">
                            <li>{{ __("Date") }}<span>{{ $data->date ?? '' }}</span></li>
                            <li>{{ __("Time") }}<span>{{ $data->schedule->from_time ?? '' }} - {{ $data->schedule->to_time ?? '' }}</span></li>
                            <li>{{ __("Status") }}
                                <span class="{{ $data->stringStatus->class }}">{{ __($data->stringStatus->value) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-30">
            <div class="transaction-area">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="title mb-0"><i class="fas fa-user text--base me-2"></i>{{ __("Payment Information") }}</h4>
                </div>
                <div class="content pt-0">
                    <div class="list-wrapper">
                        <ul class="list">
                            <li>{{ __("Booking Number") }} <span>{{ $data->trx_id ?? ''  }}</span> </li>
                            <li>{{ __("Payment Method") }} <span>{{ $data->payment_method ?? ''  }}</span> </li>
                            <li>{{ __("Service Price") }} <span>{{ get_default_currency_symbol() }}{{ get_amount($data->price) }}</span> </li>
                            <li>{{ __("Fees & Charges") }} <span>{{ get_default_currency_symbol() }}{{ get_amount($data->total_charge) }}</span> </li>
                            <li>{{ __("Total Payable Price") }} <span>{{ get_default_currency_symbol() }}{{ get_amount($data->payable_price) }}</span> </li>
                            <li>{{ __("Remark") }} <span>{{ $data->remark ?? 'N/A' }}</span></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        {{-- <form action="{{ setRoute('admin.booking-log.status.update',$data->trx_id) }}" method="post">
            @csrf
            <div class="col-lg-12 mb-30">
                <div class="transaction-area">
                    <h4 class="title"><i class="fas fa-user text--base me-2"></i>{{ __("Progress of DoctorBookings") }}</h4>
                    <div class="content pt-0">
                        <div class="radio-area">
                            <div class="radio-wrapper">
                                <div class="radio-item">
                                    <input type="radio" id="level-2" value="{{ global_const()::STATUS_PENDING }}" @if($data->status == global_const()::STATUS_PENDING) checked @endif name="status">
                                    <label for="level-2">{{ __("Pending") }}</label>
                                </div>
                            </div>
                            <div class="radio-wrapper">
                                <div class="radio-item">
                                    <input type="radio" id="level-3" value="{{ global_const()::STATUS_SUCCESS }}" @if($data->status == global_const()::STATUS_SUCCESS) checked @endif name="status">
                                    <label for="level-3">{{ __("Confirm Payment") }}</label>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.button.form-btn',[
                                'class'         => "w-100 btn-loading",
                                'text'          => "Update",
                            ])
                        </div>
                    </div>
                </div>
            </div>
        </form> --}}

    </div>
</div>
@endsection
@push('script')

@endpush
