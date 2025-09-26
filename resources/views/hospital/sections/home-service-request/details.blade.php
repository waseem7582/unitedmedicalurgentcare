@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Home Service Request'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Service Request Details'),
    ])
@endsection

@section('content')
    <div class="booking-request-details">
        <div class="preview-list-wrapper">
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-user"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Name') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">

                    <span>{{ $booking_data->booking_data->data->name }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-envelope"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Email') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span>{{ $booking_data->booking_data->data->email }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-mobile"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Mobile') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span>{{ $booking_data->booking_data->data->number }}</span>
                </div>
            </div>

        <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-eye"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Shift') }}</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ $booking_data->booking_data->data->shift }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-stethoscope"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Hospital Name') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ $booking_data->hospital->hospital_name }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-money-bill-wave"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Fess') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ get_amount($booking_data->price) }} {{ get_default_currency_code() }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-wallet"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Payment Method') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ $booking_data->payment_method }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-spinner"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Status') }}</span>
                        </div>
                    </div>
                </div>
                @if ($booking_data->status == global_const()::STATUS_PENDING)
                    <div class="preview-list-right text-center">
                        <form action="{{ route('hospitals.booking.request.update.service.booking.request', $booking_data->uuid) }}"
                            method="POST">
                            @csrf
                            @method('PUT')
                            <button value="4" name="status" type="submit" data-bs-toggle="modal"
                                data-bs-target="#request_button"
                                class="btn btn-cancel request_button">{{ __('Cancel') }}</button>
                            <button value="1" name="status" type="submit"
                                class="btn btn-accept">{{ __('Complete') }}</button>
                        </form>

                    </div>
                @else
                    <div class="preview-list-right text-center">
                        <span
                            class="{{ $booking_data->stringStatus->class }}">{{ __($booking_data->stringStatus->value) }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection



@push('script')

@endpush
