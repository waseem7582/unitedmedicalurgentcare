@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Booking Request'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Booking Details'),
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
                            <span>{{ __('MOBILE') }}:</span>
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
                            <span>{{ __('Visit Type') }}</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ $booking_data->booking_data->data->visit_type }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-stethoscope"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Doctor Name') }}:</span>
                        </div>
                    </div>
                </div>
                <div class="preview-list-right">
                    <span">{{ $booking_data->doctor->name }}</span>
                </div>
            </div>
            <div class="preview-list-item">
                <div class="preview-list-left">
                    <div class="preview-list-user-wrapper">
                        <div class="preview-list-user-icon">
                            <i class="las la-money-bill-wave"></i>
                        </div>
                        <div class="preview-list-user-content">
                            <span>{{ __('Doctor Fess') }}:</span>
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
                        <form action="{{ route('hospitals.booking.request.update.booking.request', $booking_data->uuid) }}"
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
@push('modal')
    <!--Modal -->
    <div class="modal fade" id="request_button" tabindex="-1" aria-labelledby="request_button" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    <h4 class="title text-center">{{ __('This is a Online Payment. Are You Sure ?') }} </h4>
                    <div class="row mb-10-none">
                        <p>{{ __('This payment has been received via an online payment platform. If you cancel this booking request, you must return the total amount along with charges.') }}
                        </p>
                    </div>
                </div>
                <div class="modal-footer justify-content-between border-0">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancel</button>
                    <form action="{{ route('hospitals.booking.request.update.booking.request', $booking_data->uuid) }}"
                        method="POST">
                        @csrf
                        @method('PUT')
                        <button value="4" name="status" type="submit" data-bs-toggle="modal"
                            data-bs-target="#request_button"
                            class="btn btn-accept">{{ __('Confirm Cancellation') }}</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endpush


@push('script')
    <script>
        var paymentType = @json($booking_data->type);
        var selectedButton = null;

        $(document).ready(function() {
            if (paymentType !== 'online') {
                $('.request_button').removeAttr('data-bs-toggle data-bs-target');
            }
        })

        $('.request_button').on('click', function(e) {
            if (paymentType == 'online') {
                e.preventDefault(); // Prevent form submission

            }
        });
    </script>
@endpush
