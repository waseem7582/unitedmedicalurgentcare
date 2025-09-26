@extends('admin.layouts.master')

@push('css')
@endpush

@section('page-title')
    @include('admin.components.page-title', ['title' => __($page_title)])
@endsection

@section('breadcrumb')
    @include('admin.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('admin.dashboard'),
            ],
        ],
        'active' => __($page_title),
    ])
@endsection

@section('content')
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __($page_title) }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" method="POST" action="{{ setRoute('admin.support.ticket.store') }}"
                enctype="multipart/form-data">
                @csrf
                <div class="row mb-10-none">
                    <div class="input-fields">
                        <div class="row">
                            <input type="hidden" name="user_type">
                            <div class="col-xl-6 col-lg-6 form-group">
                                <label>{{ __('User Type') }}*</label>
                                <select name="user_type_select" class="form--control user-type">
                                    <option value="user">{{ __('User') }}</option>
                                    <option value="hospital">{{ __('Hospital') }}</option>
                                </select>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input', [
                                    'label' => __('Email'),
                                    'label_after' => '*',
                                    'placeholder' => __('Write Email') . '...',
                                    'name' => 'email',
                                    'value' => old('email'),
                                ])
                                <label class="exist text-start"></label>
                            </div>
                            {{-- New User Fields --}}
                            <div class="row new-user d-none">
                                <div class="col-xl-6 col-lg-6 form-group">
                                    @include('admin.components.form.input', [
                                        'label' => __('First Name') . '*',
                                        'name' => 'user_firstname',
                                        'placeholder' => __('Enter First Name'),
                                        'value' => old('user_firstname'),
                                    ])
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    @include('admin.components.form.input', [
                                        'label' => __('Last Name') . '*',
                                        'name' => 'user_lastname',
                                        'placeholder' => __('Enter Last Name'),
                                        'value' => old('user_lastname'),
                                    ])
                                </div>
                       
                                <div class="form-group w-50 pe-3">
                                    @include('admin.components.form.input', [
                                        'label' => __('State') . '*',
                                        'name' => 'user_state',
                                        'placeholder' => __('Enter State'),
                                        'value' => old('user_state'),
                                    ])
                                </div>
                                <div class="form-group w-50">
                                    @include('admin.components.form.input', [
                                        'label' => __('Address') . '*',
                                        'name' => 'user_address',
                                        'placeholder' => __('Enter Address'),
                                        'value' => old('user_address'),
                                    ])
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Password') }}*</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form--control place_random_password @error('user_password') is-invalid @enderror"
                                            placeholder="{{ __('Enter Password') }}" name="user_password">
                                        <button class="input-group-text rand_password_generator"
                                            type="button">{{ __('Generate') }}</button>
                                    </div>
                                    @error('user_password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            {{-- New Hospital Fields --}}
                            <div class="row new-hospital d-none">
                                <div class="col-xl-6 col-lg-6 form-group">
                                    @include('admin.components.form.input', [
                                        'label' => __('Hospital name') . '*',
                                        'name' => 'hospital_name',
                                        'placeholder' => __('Enter Hospital Name'),
                                        'value' => old('hospital_name'),
                                    ])
                                </div>
                             
                                <div class="form-group w-50 pe-3">
                                    <label>{{ __('Mobile') }}<span>*</span></label>
                                    <input type="text" class="form--control" placeholder="Enter Phone ..."
                                        name="hospital_mobile" value="{{ old('hospital_mobile') }}">
                                </div>
                                <div class="col-xl-6 col-lg-6 form-group">
                                    <label>{{ __('Password') }}*</label>
                                    <div class="input-group">
                                        <input type="text"
                                            class="form--control place_random_password @error('hospital_password') is-invalid @enderror"
                                            placeholder="{{ __('Enter Password') }}" name="hospital_password">
                                        <button class="input-group-text rand_password_generator"
                                            type="button">{{ __('Generate') }}</button>
                                    </div>
                                    @error('hospital_password')
                                        <span class="invalid-feedback d-block" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-xl-6 col-lg-6 form-group">
                                @include('admin.components.form.input', [
                                    'label' => __('Subject'),
                                    'label_after' => '*',
                                    'placeholder' => __('Write Subject') . '...',
                                    'name' => 'subject',
                                    'value' => old('subject'),
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.textarea', [
                                    'label' => __('Message'),
                                    'label_after' => '*',
                                    'placeholder' => __('Write Message') . '...',
                                    'name' => 'desc',
                                    'value' => old('desc'),
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                <label>{{ __('Attachments') }}</label>
                                <input type="file" name="attachment[]" multiple>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.button.form-btn', [
                            'class' => 'w-100 btn-loading',
                            'text' => 'Save',
                            'permission' => 'admin.support.ticket.store',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('script')
  <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function() {
            $("input[name=email], .user-type").on("keyup change", function() {
                runEmailCheck();
            });

            placeRandomPassword(".rand_password_generator", ".place_random_password");

            function placeRandomPassword(clickedButton, placeInput) {
                $(clickedButton).click(function() {
                    var generateRandomPassword = makeRandomString(10);
                    $(placeInput).val(generateRandomPassword);
                });
            }

            function runEmailCheck() {
                var email = $("input[name=email]").val();
                var userType = $("select[name=user_type_select]").val();

                var checkUserURL = userType === "hospital" ?
                    "{{ setRoute('admin.support.ticket.check.hospital') }}" :
                    "{{ setRoute('admin.support.ticket.check.user') }}";

                var userConst = "{{ support_ticket_const()::USER }}";
                var newUserConst = "{{ support_ticket_const()::NEWUSER }}";
                var hospitalConst = "{{ support_ticket_const()::HOSPITAL }}";
                var newHospitalConst = "{{ support_ticket_const()::NEWHOSPITAL }}";

                if (email == '' || email == null) {
                    $('.exist').text('');
                    return;
                }

                $.post(checkUserURL, {
                    email: email,
                    _token: "{{ csrf_token() }}"
                }, function(response) {
                    if (response.not_exists) {
                        $('.exist').removeClass('text--success').addClass('text--danger').text(response
                            .not_exists);

                        if (userType === "user") {
                            $('.new-user').removeClass('d-none');
                            $('.new-hospital').addClass('d-none');
                            $('input[name=user_type]').val(newUserConst);
                        } else {
                            $('.new-hospital').removeClass('d-none');
                            $('.new-user').addClass('d-none');
                            $('input[name=user_type]').val(newHospitalConst);
                        }
                        return;
                    }

                    if (response['data'] != null) {
                        $('.exist').removeClass('text--danger').addClass('text--success')
                            .text("Registered " + response.type + ".");
                        $('.new-user, .new-hospital').addClass('d-none');
                        $('input[name=user_type]').val(response.type);
                    }
                });
            }
        });
    </script>
@endpush
