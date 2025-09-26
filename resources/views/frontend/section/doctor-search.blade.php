<div class="banner-flotting-section pt-5">
    <div class="container">
        <div class="banner-flotting-item">
            <form class="banner-flotting-item-form" action="{{ setRoute('frontend.doctor.search') }}" method="GET">
                @csrf
                <div class="flotting-item-inputdata">
                    <div class="form-group">
                        <select class="form--control select2-basic" name="hospital" id="hospitalSelect">
                            <option disabled selected>{{ __('Select Hospital') }}</option>
                            @foreach ($hospital as $item)
                                <option value="{{ $item->id }}" data-branches="{{ json_encode($item->branch) }}">
                                    {{ $item->hospital_name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <select class="form--control select2-basic" name="branch" id="branchSelect">
                            <option disabled selected>{{ __('Select Branch') }}</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <select class="form--control select2-basic" name="department" id="departmentSelect">
                            <option disabled selected>{{ __('Select Department') }}</option>
                        </select>
                    </div>

                    <div class="form-group dr-name">
                        <input type="text" class="form--control" name="name" value="{{ @$nameString }}"
                            placeholder="{{ __('Doctor Name') }}" spellcheck="false" data-ms-editor="true">
                        <i class="fas fa-user"></i>
                    </div>
                </div>
                <div class="form-group pt-3">
                    <button type="submit" class="btn--base search-btn w-100"><i class="fas fa-search me-1"></i>
                        {{ __('Search') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@push('script')

    {{-- need to review the code --}}
    <script>
        $(document).ready(function() {
            $('#hospitalSelect').on('change', function() {
                var selectedHospital = $(this).find(':selected');
                var branches = selectedHospital.data('branches') || [];


                $('#branchSelect').html('<option disabled selected>Select Branch</option>');
                $('#departmentSelect').html('<option disabled selected>Select Department</option>');


                if (branches.length > 0) {
                    $.each(branches, function(index, branch) {
                        var departmentsData = branch.departments ? branch.departments : [];

                        var option = $('<option>', {
                            value: branch.id,
                            text: branch.name
                        }).data('departments', departmentsData);

                        $('#branchSelect').append(option);
                    });
                }
            });

            $('#branchSelect').on('change', function() {
                var selectedBranch = $(this).find(':selected');
                var departments = selectedBranch.data('departments') || [];


                $('#departmentSelect').html('<option disabled selected>Select Department</option>');


                if (departments.length > 0) {
                    $.each(departments, function(index, department) {
                        $('#departmentSelect').append(
                            $('<option>', {
                                value: department.id,
                                text: department.name
                            })
                        );
                    });
                }
            });
        });
    </script>
@endpush
