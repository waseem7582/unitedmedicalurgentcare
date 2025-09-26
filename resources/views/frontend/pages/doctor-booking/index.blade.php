@extends('frontend.layouts.master')

@push('css')
@endpush

@section('content')
    <section class="make-appointment ptb-60">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10">
                    <div class="appointment-area">
                        <h3 class="title"><i class="fas fa-info-circle text--base mb-20"></i> {{ __('Make Appointment') }}
                        </h3>
                        <div class="doctor-card">
                            <div class="doctor-thumb">
                                <img src="{{ get_image($doctor->image, 'doctor') }}" alt="doctor-img">
                            </div>
                            <div class="doctor-details">
                                <h4 class="title"><i class="fas la-user"></i>{{ $doctor->name }}</h4>
                                <p><strong>{{ $doctor->title }}</strong></p>
                                <p><strong>{{ $doctor->qualification }}</strong></p>
                                <p><strong>{ {{ $doctor->qualification }} }</strong></p>
                                <p><strong>{{ __('Address') }} :</strong> {{ $doctor->address }}</p>
                                <p><strong>{{ __('Fees') }} :</strong> {{ get_amount($doctor->fees) }}
                                    {{ get_default_currency_code() }}</p>
                            </div>
                        </div>
                        <form action="{{ route('frontend.doctor.booking.store') }}" class="doc-form mt-20" method="POST">
                            @csrf
                            <input type="text" name="doctor_id" value="{{ $doctor->id }}" hidden>
                            <input type="hidden" id="scheduleId" name="schedule_id" value="">
                            <div class="about-details pt-10">
                                <div class="shedule-title pt-4">
                                    <h4 class="title"><i class="fas fa-history text--base"> </i> {{ __('Make Schedule') }}
                                    </h4>
                                </div>
                                <div class="shedule-area">
                                    <div class="row mb-10-none">
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Enter Name') }}</label>
                                            <input type="text" name="name" class="form--control"
                                                placeholder={{ __('Enter Name') }}>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Enter Gender') }}</label>
                                            <select name="gender" class="nice-select nice-select-hight"
                                                style="display: none;">
                                                <option value="male">{{ __('Male') }}</option>
                                                <option value="female">{{ __('Female') }}</option>
                                                <option value="other">{{ __('Other') }}</option>
                                            </select>

                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10 age-select">
                                            <label>{{ __('Enter Age') }}</label>
                                            <input type="number" name="age" class="form--control" placeholder="Enter Age">
                                            <div class="age-type">
                                                <select name="age_type" class="nice-select" style="display: none;">
                                                    <option value="Year">{{ __('Year') }}</option>
                                                    <option value="Month">{{ __('Month') }}</option>
                                                    <option value="Days">{{ __('Days') }}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Mobile Number') }}</label>
                                            <input type="number" name="number" class="form--control"
                                                placeholder={{ __('Enter Number') }}>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Email Address') }}</label>
                                            <input type="email" name="email" class="form--control" value="{{ auth()->user()->email }}"
                                                placeholder={{ __('Enter Email') }} readonly>
                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Available Schedule Date') }}</label>
                                            <input type="text" name="date" id="datepicker">
                                            <span id="scheduleTime">{{ __('Select a date to see schedule') }}</span>
                                            <div id="availableSlots"></div>



                                        </div>
                                        <div class="col-lg-6 col-md-6 mb-10">
                                            <label>{{ __('Visit Type') }}</label>
                                            <select name="visit_type" class="nice-select nice-select-hight"
                                                style="display: none;">
                                                <option>{{ __('New') }}</option>
                                                <option>{{ __('Report') }}</option>
                                                <option>{{ __('Followup') }}</option>
                                            </select>
                                        </div>
                                        <div class="col-xl-12 col-lg-12 mb-10">
                                            <label>{{ __('Your Message') }} <small
                                                    class="text--warning">{{ __('optional') }}</small></label>
                                            <textarea name="message" class="form--control" placeholder="Write Here..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="appointment-footer pt-5">
                                    <div class="col-lg-12 form-group pt-3">
                                        <button type="submit" id="bookButton"
                                            class="btn--base small w-100">{{ __('Proceed Now') }}</button>
                                    </div>
                                </div>
                            </div>
                        </form>

                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
    <script>
        var offDays  = @json($doctor->off_days);
        var schedule = @json($doctor->schedules);
        var bookings = @json($doctor->booking);

        $(document).ready(function() {
            $("#datepicker").flatpickr({
                disable: [
                    function(date) {
                        return offDays.includes(date.getDay());
                    }
                ],
                dateFormat: "Y-m-d",
                onChange: function(selectedDates, dateStr, instance) {
                    if (selectedDates.length > 0) {
                        var selectedDate = selectedDates[0];
                        var selectedDay = selectedDate.getDay();


                        var year = selectedDate.getFullYear();
                        var month = String(selectedDate.getMonth() + 1).padStart(2, '0');
                        var day = String(selectedDate.getDate()).padStart(2, '0');
                        var formattedDate = `${year}-${month}-${day}`;

                        var selectedSchedule = schedule.find(entry => entry.day == selectedDay);

                        if (selectedSchedule) {
                            $("#scheduleTime").text("From: " + selectedSchedule.from_time + " - " +
                                "To: " + selectedSchedule.to_time);
                            $("#scheduleId").val(selectedSchedule.id);

                            var bookingsOnDate = bookings.filter(booking =>
                                booking.date === formattedDate &&
                                booking.schedule_id === selectedSchedule.id
                            );

                            var maxClients = selectedSchedule.max_client || 1;
                            var availableSlots = maxClients - bookingsOnDate.length;

                            var availabilityText = availableSlots > 0 ?
                                `${availableSlots} slot(s) available` : "No available slots";
                            $("#availableSlots").text(availabilityText);

                            if (availableSlots > 0) {
                                $("#bookButton").show();
                            } else {
                                $("#bookButton").hide();
                            }

                            $("#availableSlots").text(availabilityText);
                        } else {
                            $("#scheduleTime").text("No schedule available");
                            $("#scheduleId").val("");
                            $("#availableSlots").text("");

                        }
                    }
                }
            });
        });
    </script>
@endpush
