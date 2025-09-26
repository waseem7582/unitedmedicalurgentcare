@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Doctor List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Doctor edit'),
    ])
@endsection

@section('content')
    <!-- edit doctor-->
    <form class="card-form" action="{{ setRoute('hospitals.doctor.update', $doctor->slug) }}" method="POST"
        enctype="multipart/form-data">
        @csrf
        <div class="add-new-doctor">
            <div class="row justify-content-center">
                <div class="col-xl-5 col-lg-6 col-sm-8">
                    <label>{{ __('Doctor Img') }}<span>*</span></label>
                    <div class="file-holder-wrapper">
                        @include('admin.components.form.input-file', [
                            'label' => __('Image'),
                            'name' => 'image',
                            'class' => 'file-holder',
                            'old_files_path' => files_asset_path('doctor'),
                            'old_files' => old('image', $doctor->image),
                        ])
                    </div>
                </div>
            </div>
            <div class="add-doctor-details">

                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Select Branch ') }}<span class="text--base">*</span></label>
                        <select id="branch" class="form--control select2-basic" name="branch_id">
                            <option disabled>{{ __('Select Branch') }}</option>
                            @foreach ($branch as $item)
                                <option value="{{ $item->id }}" data-departments="{{ $item->departments }}"
                                    {{ $doctor->branch_id == $item->id ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Select Departments') }} <span class="text--base">*</span></label>
                        <select id="department" name="departments_id" class="form--control select2-basic">
                            <option disabled selected>{{ __('Select Department') }}</option>
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Name') }} <span class="text--base">*</span></label>
                        <input type="text" name="name" class="form--control" placeholder="{{ __('Enter Name') }}"
                            value="{{ old('name', $doctor->name) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Title') }} <span class="text--base">*</span></label>
                        <input type="text" name="title" class="form--control" placeholder="{{ __('Enter Title') }}"
                            value="{{ old('title', $doctor->title) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Qualification') }}<span class="text--base">*</span></label>
                        <input type="text" name="qualification" class="form--control"
                            placeholder="{{ __('Enter Qualification') }}"
                            value="{{ old('qualification', $doctor->qualification) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Specialty') }}<span class="text--base">*</span></label>
                        <input type="text" name="specialty" class="form--control"
                            placeholder="{{ __('Write Specialty') }}" value="{{ old('specialty', $doctor->specialty) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">

                        <label>{{ __('Language Spoken') }}<span class="text--base">*</span></label>
                        @php
                            $selectedLanguages = explode(',', $doctor->language);
                        @endphp
                        <select name="language[]" class="form--control select2-auto-tokenize select2-hidden-accessible"
                            placeholder="Add Language" multiple required>
                            @foreach ($language as $item)
                                <option value="{{ $item->name }}"
                                    {{ in_array($item->name, $selectedLanguages) ? 'selected' : '' }}>
                                    {{ $item->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Designation') }}<span class="text--base">*</span></label>
                        <input type="text" name="designation" class="form--control"
                            placeholder="{{ __('Write Designation') }}"
                            value="{{ old('designation', $doctor->designation) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Contact') }}<span class="text--base">*</span></label>
                        <input type="text" name="contact" class="form--control" placeholder="{{ __('Write Contact') }}"
                            value="{{ old('contact', $doctor->contact) }}">
                    </div>

                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Off Days') }}<span class="text--base">*</span></label>
                        @php
                            $offdays = explode(',', $doctor->off_days);

                        @endphp
                        <select name="off_days[]" class="form--control select2-auto-tokenize" placeholder="Add Language"
                            multiple="multiple" required id="doctor_off_days">
                            @foreach (getWeekDays() as $day)
                                <option value="{{ $day['value'] }}"
                                    {{ in_array($day['value'], $offdays) ? 'selected' : '' }}>
                                    {{ $day['day_name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Floor Number') }}<span class="text--base">*</span></label>
                        <input type="text" name="floor_number" class="form--control"
                            placeholder="{{ __('Write Floor Number') }}"
                            value="{{ old('floor_number', $doctor->floor_number) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Room Number') }}<span class="text--base">*</span></label>
                        <input type="text" name="room_number" class="form--control"
                            placeholder="{{ __('Write Room Number') }}"
                            value="{{ old('room_number', $doctor->room_number) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Address') }}<span class="text--base">*</span></label>
                        <input type="text" name="address" class="form--control" placeholder="{{ __('Write Address') }}"
                            value="{{ old('name', $doctor->address) }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Fees') }}<span class="text--base">*</span></label>
                        <input type="text" name="fees" class="form--control" placeholder="{{ __('Write Fees') }}"
                            value="{{ old('name', $doctor->fees) }}">
                    </div>
                </div>
                <div class="add-schedule mt-30">
                    <div class="schedule-add-btn">
                        <button type="button" class="btn--base mb-20 add-schedule-btn"><i class="fas fa-plus"></i>
                            {{ __('Add Schedule') }}</button>
                    </div>
                    <h4 class="title">{{ __('Schedule Booking') }}</h4>
                    <div class="results">
                        @forelse ($doctor_has_schedule ?? [] as $item)
                            <div class="make-schedule mt-20">
                                <div class="schedule-data">
                                    <div class="row mb-10-none">
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>Day</label>
                                            <select name="schedule_days[]"
                                                class="form--control select2-auto-tokenize schedule_day" required>
                                                @foreach (getWeekDays() as $day)
                                                    <option value="{{ $day['value'] }}"
                                                        {{ $day['value'] == $item->day ? 'selected' : '' }}>
                                                        {{ $day['day_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('From Time') }}</label>
                                            <input type="time" name="from_time[]" class="form--control"
                                                value="{{ $item->from_time }}">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('To Time') }}</label>
                                            <input type="time" name="to_time[]" class="form--control"
                                                value="{{ $item->to_time }}">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('Max Patient') }}</label>
                                            <input type="number" name="max_patient[]" class="form--control"
                                                placeholder="{{ __('Write Here') }}" value="{{ $item->max_client }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="schedule-remove">
                                    <i class="las la-trash-alt row-cross-btn text-danger" style="cursor: pointer;"></i>
                                </div>
                            </div>
                        @empty
                            <div class="make-schedule mt-20">
                                <div class="schedule-data">
                                    <div class="row mb-10-none">
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>Day</label>
                                            <select name="schedule_days[0][]"
                                                class="form--control select2-auto-tokenize schedule_day"
                                                multiple="multiple" required>
                                                @foreach (getWeekDays() as $day)
                                                    <option value="{{ $day['value'] }}">{{ $day['day_name'] }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('From Time') }}</label>
                                            <input type="time" name="from_time[0][]" class="form--control">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('To Time') }}</label>
                                            <input type="time" name="to_time[0][]" class="form--control">
                                        </div>
                                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                            <label>{{ __('Max Patient') }}</label>
                                            <input type="number" name="max_patient[0][]" class="form--control"
                                                placeholder="{{ __('Write Here') }}">
                                        </div>
                                    </div>
                                </div>
                                <div class="schedule-remove">
                                    <i class="las la-trash-alt row-cross-btn text-danger" style="cursor: pointer;"></i>
                                </div>
                            </div>
                        @endforelse
                    </div>
                </div>
                <div class="add-btn mt-5">
                    <button type="submit" class="btn--base w-100">{{ __('Save Doctor') }}</button>
                </div>
            </div>
        </div>
    </form>
@endsection
@push('script')
    <script>
        $(document).on('change', '#branch', function() {
            var selectedOption = $(this).find('option:selected');
            var departments = selectedOption.data('departments');
            var departmentDropdown = $('#department');

            departmentDropdown.empty();
            departmentDropdown.append('<option disabled selected>{{ __('Select Department') }}</option>');

            if (departments && departments.length > 0) {
                departments.forEach(function(dept) {
                    departmentDropdown.append('<option value="' + dept.id + '">' + dept.name + '</option>');
                });
            }
        });

        // Initialize select2 for tokenized inputs
        $('.select2-auto-tokenize').select2({
            tags: true,
            tokenSeparators: [',']
        });

        // Function to handle branch change
        function handleBranchChange() {
            var selectedOption = $('#branch').find('option:selected');
            var departments = selectedOption.data('departments');


            var departmentDropdown = $('#department');

            var currentDepartmentId = @json($doctor->department_id); // Get current department ID
            console.log(currentDepartmentId);


            departmentDropdown.empty();
            departmentDropdown.append('<option disabled selected>{{ __('Select Department') }}</option>');

            if (departments && departments.length > 0) {
                departments.forEach(function(dept) {
                    // Check if this department is the one currently assigned to the doctor
                    var isSelected = (dept.id == currentDepartmentId);
                    departmentDropdown.append('<option value="' + dept.id + '"' +
                        (isSelected ? ' selected' : '') + '>' + dept.name + '</option>');
                });
            }
        }

        // Trigger branch change on page load
        handleBranchChange();

        // Bind the change event
        $(document).on('change', '#branch', handleBranchChange);

        $(document).ready(function() {
            $('.select2-auto-tokenize').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });

        $(document).on('click', '.row-cross-btn', function(e) {
            e.preventDefault();
            $(this).parent().parent().hide(300);
            setTimeout(timeOutFunc, 300, $(this));

            function timeOutFunc(element) {
                $(element).parent().parent().remove();
            }
        });

        function updateScheduleDays() {
            let selectedOffDays = $('#doctor_off_days').val();
            $('.schedule_day').each(function() {
                let scheduleDaySelect = $(this);
                scheduleDaySelect.find('option').each(function() {
                    let optionValue = $(this).val();
                    if (selectedOffDays.includes(optionValue)) {
                        $(this).prop('disabled', true);
                    } else {
                        $(this).prop('disabled', false);
                    }
                });
                scheduleDaySelect.trigger('change');
            });
        }

        $('#doctor_off_days').on('change', function() {
            updateScheduleDays();
        });

        function initializeNewScheduleRow(newRow) {
            let selectedOffDays = $('#doctor_off_days').val();
            let scheduleDaySelect = newRow.find('.schedule_day');
            scheduleDaySelect.find('option').each(function() {
                let optionValue = $(this).val();
                if (selectedOffDays.includes(optionValue)) {
                    $(this).prop('disabled', true);
                } else {
                    $(this).prop('disabled', false);
                }
            });
            scheduleDaySelect.select2({
                tags: true,
                tokenSeparators: [',']
            });
        }

        $(document).ready(function() {


            $('.add-schedule-btn').click(function() {
                let newRow = `
                    <div class="make-schedule mt-20">
                        <div class="schedule-data">
                            <div class="row mb-10-none">
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                    <label>Day</label>
                                    <select name="schedule_days[]" class="form--control select2-auto-tokenize schedule_day" required>
                                        @foreach (getWeekDays() as $day)
                                            <option value="{{ $day['value'] }}">{{ $day['day_name'] }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                    <label>{{ __('From Time') }}</label>
                                    <input type="time" name="from_time[]" class="form--control">
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                    <label>{{ __('To Time') }}</label>
                                    <input type="time" name="to_time[]" class="form--control">
                                </div>
                                <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                    <label>{{ __('Max Patient') }}</label>
                                    <input type="number" name="max_patient[]" class="form--control" placeholder="{{ __('Write Here') }}">
                                </div>
                            </div>
                        </div>
                         <div class="schedule-remove">
    <i class="las la-trash-alt row-cross-btn text-danger" style="cursor: pointer;"></i>
</div>
                    </div>
                `;

                let $newRow = $(newRow);
                $('.results').append($newRow);
                initializeNewScheduleRow($newRow);
            });

            $(document).on('click', '.row-cross-btn', function() {
                $(this).closest('.make-schedule').remove();
            });
        });

        updateScheduleDays();

        $(document).ready(function() {
            $('.select2-auto-tokenize').select2({
                tags: true,
                tokenSeparators: [',']
            });
        });
    </script>
@endpush
