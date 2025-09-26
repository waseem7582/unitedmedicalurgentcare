@extends('frontend.layouts.master')
@php
    $default_currency_code = get_default_currency_code();
@endphp

@push('css')
@endpush

@section('content')
    <section class="appointment-preview ptb-80">
        <div class="container">
            <form action="{{ setRoute('frontend.doctor.booking.confirm', $booking->uuid) }}" method="POST">
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
                                            <p>{{ __('DOCTOR NAME') }}:</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->doctor->name ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('SPECIALITY') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->doctor->specialty ?? '' }}</p>
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
                                            <p>{{ __('Patient Name') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->name ?? '' }}</p>
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
                                            <p>{{ $booking->data->number ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('DOCTOR FEES') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->price ?? '' }} {{ get_default_currency_code() }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('FEES & CHARGES') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->total_charge ?? '' }}</p>
                                        </div>
                                    </div>
                                    <div class="preview-area">
                                        <div class="preview-item">
                                            <p>{{ __('PAYABLE AMOUNT') }} :</p>
                                        </div>
                                        <div class="preview-details">
                                            <p>{{ $booking->data->payable_price ?? '' }}</p>
                                        </div>
                                    </div>

                                </div>
                                <div class="payment-type-select">
                                    <h4 class="title"><i class="fas fa-spinner text--base"></i>
                                        {{ __('Select Payment Method') }}</h4>
                                    <div class="select-payment-option pt-10">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault1" checked="">
                                            <label class="form-check-label" for="flexRadioDefault1">
                                                {{ __('Cash Payment') }}
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="flexRadioDefault"
                                                id="flexRadioDefault2">
                                            <label class="form-check-label" for="flexRadioDefault2">
                                                {{ __('Online Payment') }}
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <input type="hidden" id="selected-payment-method" name="payment_method" value="cash">

                                <div class="payment-page-section" style="display: none;">
                                    <div class="payment-type pt-20">
                                        <div class="select-payment-area">
                                            <div class="radio-wrapper pt-2">
                                                @foreach ($payment_method as $item)
                                                    <div class="radio-item">
                                                        <input type="radio" id="level-{{ $item->id }}"
                                                            class="hide-input" id="payment-method-select"
                                                            data-currencies="{{ $item->currencies }}" name="radio-group">
                                                        <label for="level-{{ $item->id }}"> <img
                                                                src="{{ get_image($item->image, 'payment-gateways') }}"
                                                                alt="icon">
                                                            {{ $item->name }}</label>
                                                    </div>
                                                @endforeach

                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div id="currency-section" class="pt-4">
                                    <h4 class="title"><i class="fas fa-spinner text--base"></i>
                                        {{ __('Select Payment Currency') }}</h4>
                                    <select id="currency-dropdown" class="form-control select2-basic pt-5">
                                    </select>
                                </div>


                                <div class="btn-area mt-30 ">
                                    <button type="submit" class="btn--base w-100">{{ __('Confirm Appointment') }} <i
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
    <script>
        function updatePaymentSection() {
            const walletRadio = document.getElementById('flexRadioDefault1');
            const paymentSection = document.querySelector('.payment-page-section');
            const hiddenInput = document.getElementById('selected-payment-method');

            if (walletRadio.checked) {
                paymentSection.style.display = 'none';
                hiddenInput.value = 'cash'; // Store 'cash' in hidden input
            } else {
                paymentSection.style.display = 'block';
                hiddenInput.value = 'online'; // Store 'online' in hidden input
            }
        }

        $('#flexRadioDefault1').on('click', function() {
            $("#currency-section").hide();
        });


        // Add event listeners to the radio buttons
        document.querySelectorAll('input[name="flexRadioDefault"]').forEach(radio => {
            radio.addEventListener('change', updatePaymentSection);
        });

        // Initial call to set the correct display and hidden input on page load
        updatePaymentSection();


        $(document).ready(function() {
            $("#currency-section").hide();
            const $selectedGatewayCurrency = $('#selected-gateway-currency');
            const $hiddenInput = $('#selected-currency');

            $('.hide-input').change(function() {
                $("#currency-section").show();
                var selectedOption = $(this).find('option:selected');
                var currencyData = $(this).data('currencies');
                var html = '<option value="">Select Currency</option>';

                $.each(currencyData, function(index, item) {
                    html += '<option value="' + item.alias + '">' + item.name + '</option>';
                });

                $('#currency-dropdown').html(html);
            });

            $('#currency-dropdown').change(function() {
                var selectedAlias = $(this).val();
                $hiddenInput.val(selectedAlias);
            });
        });
    </script>
@endpush
