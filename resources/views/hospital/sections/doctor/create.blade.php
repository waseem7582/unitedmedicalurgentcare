@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Doctor List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
        'active' => __('Doctor Add'),
    ])
@endsection

@section('content')
    <!-- Add doctor-->
    <form class="card-form" action="{{ setRoute('hospitals.doctor.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="add-new-doctor">
            <div class="row justify-content-center">
                <div class="col-xl-4 col-lg-4 form-group mb-5">
                    @include('admin.components.form.input-file', [
                        'label' => __('Image'),
                        'name' => 'image',
                        'class' => 'file-holder',
                        'old_files' => old('image'),
                        'attribute' => 'data-height=130',
                    ])
                </div>
            </div>
            <div class="add-doctor-details">

                <div class="row">
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Select Branch ') }}<span class="text--base">*</span></label>

                        <select id="branch" class="form--control select2-basic" name="branch_id">
                            <option>{{ __('Select Branch') }}</option>
                            @foreach ($branch as $item)
                                <option value="{{ $item->id }}" data-departments="{{ $item->departments }}"
                                    {{ old('branch') == $item->id ? 'selected' : '' }}>
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
                        <input type="text" name="name" class="form--control" placeholder="{{ __('Enter Name') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Title') }} <span class="text--base">*</span></label>
                        <input type="text" name="title" class="form--control" placeholder="{{ __('Enter Title') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Qualification') }}<span class="text--base">*</span></label>
                        <input type="text" name="qualification" class="form--control"
                            placeholder="{{ __('Enter Qualification') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Specialty') }}<span class="text--base">*</span></label>
                        <input type="text" name="specialty" class="form--control"
                            placeholder="{{ __('Write Specialty') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Language Spoken') }}<span class="text--base">*</span></label>
                        <select name="language[]" class="form--control select2-auto-tokenize select2-hidden-accessible"
                            placeholder="Add Language" multiple required>
                            @foreach ($language as $item)
                                <option value="{{ $item->name }}">{{ $item->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Designation') }}<span class="text--base">*</span></label>
                        <input type="text" name="designation" class="form--control"
                            placeholder="{{ __('Write Designation') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Contact') }}<span class="text--base">*</span></label>
                        <input type="text" name="contact" class="form--control" placeholder="{{ __('Write Contact') }}">
                    </div>

                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Off Days') }}<span class="text--base">*</span></label>
                        <select name="off_days[]" class="form--control select2-auto-tokenize" placeholder="Add Language"
                            multiple="multiple" required id="doctor_off_days">
                            @foreach (getWeekDays() as $day)
                                <option value="{{ $day['value'] }}">{{ $day['day_name'] }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Floor Number') }}<span class="text--base">*</span></label>
                        <input type="text" name="floor_number" class="form--control"
                            placeholder="{{ __('Write Floor Number') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Room Number') }}<span class="text--base">*</span></label>
                        <input type="text" name="room_number" class="form--control"
                            placeholder="{{ __('Write Room Number') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Address') }}<span class="text--base">*</span></label>
                        <input type="text" name="address" class="form--control" placeholder="{{ __('Write Address') }}">
                    </div>
                    <div class="col-xl-4 col-lg-6 col-sm-6 mb-20">
                        <label>{{ __('Doctor Fees') }}<span class="text--base">*</span></label>
                        <input type="number" name="fees" class="form--control" placeholder="{{ __('Write Fees') }}">
                    </div>
                </div>
                <div class="add-schedule mt-30">
                    <div class="schedule-add-btn">
                        <button type="button" class="btn--base mb-20 add-schedule-btn"><i class="fas fa-plus"></i>
                            {{ __('Add Schedule') }}</button>
                    </div>
                    <h4 class="title">{{ __('Schedule Booking') }}</h4>
                    <div class="results">
                        <div class="make-schedule mt-20">
                            <div class="schedule-data">
                                <div class="row mb-10-none">
                                    <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                                        <label>{{ __('Day') }}</label>
                                        <select name="schedule_days[0][]"
                                            class="form--control select2-auto-tokenize schedule_day" multiple="multiple"
                                            required>
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
        $(document).ready(function() {
            initScheduleRow($('.make-schedule').first());

            $(document).on('change', '#branch', function() {
                var selectedOption = $(this).find('option:selected');
                var departments = selectedOption.data('departments');
                var departmentDropdown = $('#department');
                departmentDropdown.empty();
                departmentDropdown.append(
                    '<option disabled selected>{{ __('Select Department') }}</option>');

                if (departments && departments.length > 0) {
                    departments.forEach(function(dept) {
                        departmentDropdown.append('<option value="' + dept.id + '">' + dept.name +
                            '</option>');
                    });
                }
            });

            $('.select2-auto-tokenize').select2({
                tags: true,
                tokenSeparators: [',']
            });

            $(document).on('click', '.row-cross-btn', function(e) {
                e.preventDefault();
                $(this).closest('.make-schedule').remove();
                updateDisabledDays(); // Update disabled options when a row is removed
            });

            let scheduleRowIndex = 1;
            $('.add-schedule-btn').click(function() {
                let newRow = `
            <div class="make-schedule mt-20">
                <div class="schedule-data">
                    <div class="row mb-10-none">
                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                            <label>{{ __('Day') }}</label>
                            <select name="schedule_days[${scheduleRowIndex}][]" class="form--control schedule_day" multiple="multiple" required>
                                @foreach (getWeekDays() as $day)
                                    <option value="{{ $day['value'] }}">{{ $day['day_name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                            <label>{{ __('From Time') }}</label>
                            <input type="time" name="from_time[${scheduleRowIndex}][]" class="form--control">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                            <label>{{ __('To Time') }}</label>
                            <input type="time" name="to_time[${scheduleRowIndex}][]" class="form--control">
                        </div>
                        <div class="col-lg-3 col-md-3 col-sm-6 mb-10">
                            <label>{{ __('Max Patient') }}</label>
                            <input type="number" name="max_patient[${scheduleRowIndex}][]" class="form--control" placeholder="{{ __('Write Here') }}">
                        </div>
                    </div>
                </div>
      <div class="schedule-remove">
    <i class="las la-trash-alt row-cross-btn text-danger" style="cursor: pointer;"></i>
</div>

        `;

                let $newRow = $(newRow);
                $('.results').append($newRow);
                initScheduleRow($newRow);
                scheduleRowIndex++;
            });

            $('#doctor_off_days').on('change', function() {
                updateDisabledDays();
            });

            function initScheduleRow($row) {
                $row.find('.schedule_day').select2({
                    tags: true,
                    tokenSeparators: [',']
                }).on('change', function() {
                    updateDisabledDays();
                });

                updateDisabledDays();
            }

            function updateDisabledDays() {
                let allSelectedDays = [];
                $('.schedule_day').each(function() {
                    const selected = $(this).val();
                    if (selected) {
                        allSelectedDays = allSelectedDays.concat(selected);
                    }
                });

                const offDays = $('#doctor_off_days').val() || [];

                $('.schedule_day').each(function() {
                    const $select = $(this);
                    const currentSelected = $select.val() || [];

                    $select.find('option').prop('disabled', false);

                    $select.find('option').each(function() {
                        const optionValue = $(this).val();
                        const isSelectedElsewhere = allSelectedDays.includes(optionValue) && !
                            currentSelected.includes(optionValue);
                        const isOffDay = offDays.includes(optionValue);

                        if (isSelectedElsewhere || isOffDay) {
                            $(this).prop('disabled', true);
                        }
                    });

                    $select.trigger('change.select2');
                });
            }
        });
    </script>
@endpush
