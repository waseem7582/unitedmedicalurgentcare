@extends('frontend.layouts.master')

@push('css')
@endpush

@section('content')
    <section class="home-service-section ptb-80">
        <div class="container">
            <form action="{{ route('frontend.home.service.confirm') }}" class="doc-form mt-20" method="POST">
                @csrf
                <div class="row mb-30-none">
                    <div class="col-lg-5 mb-30">
                        <div class="booking-area">
                            <div class="title-area mb-30">
                                <h4 class="title text-center">SERVICE <span
                                        class="text--base">{{ __('AT YOUR DOORSTEP') }}</span>
                                </h4>
                                <p>{{ __('No more waiting rooms, no more long commutes, and no more hassle. With our service, you can
                                                                                                                                                                                                                                                                        now book
                                                                                                                                                                                                                                                                        a doctor appointment and have a qualified healthcare professional visit you at your
                                                                                                                                                                                                                                                                        preferred
                                                                                                                                                                                                                                                                        location and time.') }}
                                </p>
                            </div>
                            <div class="content pt-0">
                                <div class="list-wrapper">
                                    <ul class="list">
                                        <li>{{ __('Address') }}: <span
                                                class="text--base">{{ $contact->value->address ?? '' }}</span>
                                        </li>
                                        <li>{{ __('Contact') }}: <span
                                                class="text--base text-lowercase">{{ $contact->value->email ?? '' }}</span>
                                        </li>
                                    </ul>
                                </div>
                                <div class="list-wrapper pt-20">
                                    <h4 class="title text--base"><i class="fas fa-history"></i> {{ __('Schedule') }}</h4>
                                    <div class="row">
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-sm-6 mb-10">
                                            <label>{{ __('Schedule Date') }}</label>
                                            <input type="date" name="date" class="form--control">
                                        </div>
                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-sm-6 mb-10">
                                            <label>{{ __('Start time') }}</label>
                                            <input type="time" name="time" class="form--control">
                                        </div>

                                        <div class="col-xxl-6 col-xl-12 col-lg-12 col-md-6 col-sm-6 mb-10">
                                            <label>{{ __('Select Shift') }}</label>
                                            <select name="shift" class="nice-select nice-select-hight"
                                                style="display: none;">
                                                <option value="morning">{{ __('Morning Shift') }}</option>
                                                <option value="evening">{{ __('Evening Shift') }}</option>
                                            </select>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-7 mb-30">
                        <div class="booking-area fixed">
                            <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i>
                                {{ __('Appointment Form') }}
                            </h3>
                            <div class="row mb-10-none">
                                <div class="col-lg-6 col-md-6 mb-10">
                                    <label>{{ __('Enter Name') }}</label>
                                    <input type="text" name="name" class="form--control"
                                        placeholder="{{ __('Enter Name') }}">
                                </div>
                                <div class="col-lg-6 col-md-6 mb-10">
                                    <label>{{ __('Enter Gender') }}</label>
                                    <select name="gender" class="nice-select nice-select-hight" style="display: none;">
                                        <option value="male">{{ __('Male') }}</option>
                                        <option value="female">{{ __('Female') }}</option>
                                        <option value="other">{{ __('Other') }}</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 col-md-6 mb-10 age-select">
                                    <label>{{ __('Enter Age') }}</label>
                                    <input type="number" name="age" class="form--control"
                                        placeholder="{{ __('Enter Age') }}">
                                    <div class="age-type">
                                        <select class="nice-select" name="age_type" style="display: none;">
                                            <option value="year">{{ __('Year') }}</option>
                                            <option value="month">{{ __('Month') }}</option>
                                            <option value="day">{{ __('Days') }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 mb-10">
                                    <label>{{ __('Mobile Number') }}</label>
                                    <input type="number" class="form--control" name="number"
                                        placeholder="{{ __('Enter Number') }}">
                                </div>
                                <div class="col-lg-12 col-md-12 mb-10">
                                    <label>{{ __('Email Address') }}</label>
                                    <input type="email" class="form--control" name="email"
                                        value="{{ auth()->user()->email }}" placeholder="{{ __('Enter Email') }}"
                                        readonly>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 form-group">
                                    <label for="address">{{ __('Address') }}</label>

                                    <input type="text" placeholder="{{ __('Enter Address') }}..." name="address"
                                        class="form--control  ">
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12 form-group">
                                    <label for="address">{{ __('Hospital') }}</label>
                                    <select name="hospital_id" id="hospital-select" class="nice-select">
                                        <option value="">{{ __('Select Hospital') }}</option>
                                        @foreach ($hospital ?? [] as $item)
                                            <option value="{{ $item->id }}">{{ $item->hospital_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div id="service_box" class="col-xl-12 col-lg-12 col-md-12 form-group">
                                    <div class="home-check-area custom-check-group">
                                        <div class="home-check-wrapper">
                                            <!-- Checkboxes will be dynamically inserted here -->
                                        </div>
                                    </div>
                                </div>
                                <span id="massage" class="text--danger pb-4">{{ __('No Home Service Available') }}</span>

                                <div class="col-xl-12 col-lg-12 form-group">
                                    <label for="messagespanclasstext--warningoptionalspan">{{ __('Message') }}<span
                                            class="text--warning">{{ __('Optional') }}</span></label>
                                    <textarea class="form--control" placeholder="{{ __('Write Here') }}..." name="message"></textarea>

                                </div>
                                <div class="col-lg-12 form-group">
                                    <button type="submit" class="btn--base small">{{ __('Submit') }} <i
                                            class="fas fa-paper-plane ms-1"></i></button>
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

        $(document).ready(function() {
            let default_cur = '{{ get_default_currency_code()}}';

            $('#service_box').hide();
            $('#massage').hide();
            // CSRF token setup for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#hospital-select').change(function() {
                var hospitalId = $(this).val();

                if (hospitalId) {
                    $.ajax({
                        url: "{{ route('frontend.home.service.get.home.service') }}",
                        type: "POST",
                        data: {
                            hospital_id: hospitalId
                        },
                        success: function(response) {

                            // Clear previous checkboxes
                            $('.home-check-wrapper').empty();

                            if (response.success && response.investigations.length > 0) {
                                // Add new checkboxes based on response
                                $.each(response.investigations, function(index, service) {

                                    var checkboxId = 'home_service_' + service.id;
                                    $('.home-check-wrapper').append(
                                        '<div class="home-check-area custom-check-group">' +
                                        '<input type="checkbox" name="investigations[]" value="' +
                                        service.id + '" id="' + checkboxId + '">' +
                                        '<label for="' + checkboxId + '">' +
                                        service.name + " " +
                                        (service.offer_price ? service.offer_price :
                                            service.regular_price) + ' ' +default_cur+
                                        '</label>' +
                                        '</div>'
                                    );

                                });
                                $('#service_box').show();
                                $('#massage').hide();
                            } else {
                                $('#service_box').hide();
                                $('#massage').show();
                            }
                        },
                        error: function(xhr) {
                            $('#service_box').hide();
                            alert('An error occurred. Please try again.');
                        }
                    });
                } else {
                    $('#service_box').hide();
                }
            });
        });
    </script>
@endpush
