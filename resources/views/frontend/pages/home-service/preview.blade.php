@extends('frontend.layouts.master')
@php
    $default_currency_code = get_default_currency_code();
@endphp

@push('css')
@endpush

@section('content')
    <section class="appointment-preview ptb-80">
        <div class="container">
            <form action="{{ setRoute('frontend.home.service.booking.confirm', $booking->uuid) }}" method="POST">
                @csrf
                <div class="row justify-content-center mb-30-none">
                    <input type="hidden" id="selected-currency" name="gateway_currency">
                    <input type="hidden" value="{{ $booking->data->price }}" name="amount">
                    <div class="col-xl-8 col-lg-8 col-md-12 mb-30">
                        <div class="booking-area">
                            <div class="content pt-0">
                                <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i>
                                    {{ __('Appointment Preview') }}</h3>
                                <div class="list-wrapper">
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('Hospital NAME') }}:</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $hospital->hospital_name ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('SCHEDULE') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->date ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('Shift') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->shift ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('Time') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->time ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('Patient Name') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->name ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('PATIENT Age') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->age ?? '' }} {{ $booking->data->age_type ?? ''  }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('PATIENT Gender') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->gender ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('MOBILE') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->number ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('EMAIL') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->email ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('FEES') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->price ?? '' }} {{ get_default_currency_code() }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('Services') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ implode(', ', $investigations->pluck('name')->toArray()) }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="btn-area mt-30 ">
                                    <button type="submit" class="btn--base w-100">Confirm Appointment <i
                                            class="fas fa-check-circle ms-1"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </section>
@endsection
@push('script')
@endpush
