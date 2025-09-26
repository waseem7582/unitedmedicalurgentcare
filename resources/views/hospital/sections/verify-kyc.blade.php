@php
    $hospital_id = auth()->user()->id;
    $kyc_data = \App\Models\Hospital\HospitalKycData::where('hospital_id', $hospital_id)->first();
@endphp
@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('KYC Verification'),
    ])
@endsection


@section('content')
    <div class="row mb-20-none">
        <div class="col-xl-12 col-lg-12 mb-20">
            <div class="custom-card mt-10">
                <div class="dashboard-header-wrapper">
                    <h4 class="title">{{ __('KYC Verification') }}</h4>
                </div>


                @if (auth()->user()->kyc_verified == global_const()::APPROVED)
                    <div class="row justify-content-center mb-20-none">
                        <div class="col-xl-8 col-lg-10 mb-20">
                            <div class="kyc-preview mt-10">
                                <div class="kyc-title">
                                    <i class="las la-exclamation-circle"></i>
                                    <h3 class="title">{{ __('KYC-Status') }}</h3>
                                </div>
                                <div class="kyc-preview-area">
                                    <p>Status:
                                        <span>{{ __('Verified') }}</span>
                                    </p>

                                    <div class="card-body">
                                        @if ($users->kyc != null && $users->kyc->data != null)
                                            @php
                                                $kycData = $users->kyc->data;
                                            @endphp
                                            @foreach ($kycData ?? [] as $item)

                                                @if ($item->type == 'file')
                                                    @php
                                                        $file_link = get_file_link('kyc-files', $item->value);
                                                    @endphp
                                                    <div class="submit-img">
                                                        <div class="row mb-20-none">
                                                            @if ($file_link == false)
                                                                <span>{{ __('File not found!') }}</span>
                                                                @continue
                                                            @endif

                                                            @if (its_image($item->value))

                                                            <div class="col-lg-6 col-md-6 col-sm-12 mb-40">
                                                                <label>{{ $item->label }}</label>
                                                                <div class="kyc-img">
                                                                    <img src="{{ $file_link }}"
                                                                    alt="{{ $item->label }}">
                                                                </div>
                                                            </div>
                                                            @else
                                                                <span class="text--danger">
                                                                    @php
                                                                        $file_info = get_file_basename_ext_from_link(
                                                                            $file_link,
                                                                        );
                                                                    @endphp
                                                                    <a
                                                                        href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                                                        {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . ($file_info->extension ?? '') }}
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="submit-img" >
                                                        <span class="kyc-title">{{ $item->label }}:</span>
                                                        <span>{{ $item->value }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(auth()->user()->kyc_verified == global_const()::PENDING)
                    <div class="row justify-content-center mb-20-none">
                        <div class="col-xl-8 col-lg-10 mb-20">
                            <div class="kyc-preview mt-10">
                                <div class="kyc-title">
                                    <i class="las la-exclamation-circle"></i>
                                    <h3 class="title">{{ __('KYC-Status') }}</h3>
                                </div>
                                <div class="kyc-preview-area">
                                    <p>{{ __('Status') }}:
                                        <span>{{ __('Pending') }}</span>
                                    </p>
                                    <div class="card-body">
                                        @if ($users->kyc != null && $users->kyc->data != null)
                                            @php
                                                $kycData = $users->kyc->data;
                                            @endphp
                                            @foreach ($kycData ?? [] as $item)

                                                @if ($item->type == 'file')
                                                    @php
                                                        $file_link = get_file_link('kyc-files', $item->value);
                                                    @endphp
                                                    <div class="submit-img">
                                                        <div class="row mb-20-none">
                                                            @if ($file_link == false)
                                                                <span>{{ __('File not found!') }}</span>
                                                                @continue
                                                            @endif

                                                            @if (its_image($item->value))

                                                            <div class="col-lg-6 col-md-6 col-sm-12 mb-40">
                                                                <label>{{ $item->label }}</label>
                                                                <div class="kyc-img">
                                                                    <img src="{{ $file_link }}"
                                                                    alt="{{ $item->label }}">
                                                                </div>
                                                            </div>
                                                            @else
                                                                <span class="text--danger">
                                                                    @php
                                                                        $file_info = get_file_basename_ext_from_link(
                                                                            $file_link,
                                                                        );
                                                                    @endphp
                                                                    <a
                                                                        href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                                                        {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . ($file_info->extension ?? '') }}
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="submit-img" >
                                                        <span class="kyc-title">{{ $item->label }}:</span>
                                                        <span>{{ $item->value }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                @elseif(auth()->user()->kyc_verified == global_const()::REJECTED)
                    <div class="row justify-content-center mb-20-none">
                        <div class="col-xl-8 col-lg-10 mb-20">
                            <div class="kyc-preview mt-10">
                                <div class="kyc-title">
                                    <i class="las la-exclamation-circle"></i>
                                    <h3 class="title">{{ __('KYC-Status') }}</h3>
                                </div>
                                <div class="kyc-preview-area">
                                    <p>{{ __('Status') }}:
                                        <span class="text-danger">{{ __('Rejected') }}</span>
                                    </p>
                                    <p>{{ __('Reject Reason') }}:
                                        <span>{{ $users->kyc->reject_reason }} </span>
                                    </p>
                                    <div class="card-body">
                                        @if ($users->kyc != null && $users->kyc->data != null)
                                            @php
                                                $kycData = $users->kyc->data;
                                            @endphp
                                            @foreach ($kycData ?? [] as $item)

                                                @if ($item->type == 'file')
                                                    @php
                                                        $file_link = get_file_link('kyc-files', $item->value);
                                                    @endphp
                                                    <div class="submit-img">
                                                        <div class="row mb-20-none">
                                                            @if ($file_link == false)
                                                                <span>{{ __('File not found!') }}</span>
                                                                @continue
                                                            @endif

                                                            @if (its_image($item->value))

                                                            <div class="col-lg-6 col-md-6 col-sm-12 mb-40">
                                                                <label>{{ $item->label }}</label>
                                                                <div class="kyc-img">
                                                                    <img src="{{ $file_link }}"
                                                                    alt="{{ $item->label }}">
                                                                </div>
                                                            </div>
                                                            @else
                                                                <span class="text--danger">
                                                                    @php
                                                                        $file_info = get_file_basename_ext_from_link(
                                                                            $file_link,
                                                                        );
                                                                    @endphp
                                                                    <a
                                                                        href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                                                        {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . ($file_info->extension ?? '') }}
                                                                    </a>
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                @else
                                                    <div class="submit-img" >
                                                        <span class="kyc-title">{{ $item->label }}:</span>
                                                        <span>{{ $item->value }}</span>
                                                    </div>
                                                @endif
                                            @endforeach
                                        @endif
                                    </div>

                                </div>

                                <div class="kyc-area">
                                    <div class="card-body">
                                        <form action="{{ setRoute('hospitals.authorize.kyc.submit') }}" class="account-form"
                                            method="POST" enctype="multipart/form-data">
                                            @csrf
                                            <div class="row">
                                                @include('hospital.components.generate-kyc-fields', [
                                                    'fields' => $kyc_fields,
                                                ])
                                            </div>
                                            <div class="col-xl-12 col-lg-12 pt-5">
                                                <button type="submit" class="btn--base w-100">{{ __('Resubmit') }}</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="kyc-area">
                        <div class="card-body">
                            <form action="{{ setRoute('hospitals.authorize.kyc.submit') }}" class="account-form"
                                method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="row">
                                    @include('hospital.components.generate-kyc-fields', [
                                        'fields' => $kyc_fields,
                                    ])
                                </div>
                                <div class="col-xl-12 col-lg-12 pt-5">
                                    <button type="submit" class="btn--base w-100">{{ __('Verify') }}</button>
                                </div>
                            </form>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
