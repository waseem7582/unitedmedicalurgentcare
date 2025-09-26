@php
    $defualt_lang = get_default_language_code() ?? 'en';
@endphp

@push('css')
@endpush

@section('content')
    @extends('frontend.layouts.master')

    <!-- hospital account -->

    <section class="register-section hospital-account bg-overlay-account bg_img"
        data-background="{{ asset('frontend/images/banner/account-bg.webp') }}">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="register-form">
                        <div class="login-form">
                            <div class="login-header-top">
                                <h3 class="title">{{ __('Login as Hospital') }}</h3>
                            </div>
                            <div class="register-header-top d-none">
                                <h3 class="title">{{ __('Join as Hospital') }}</h3>
                            </div>
                            <div class="form-hader d-flex justify-content-between">
                                <div class="login-button" @if($basic_settings->hospital_registration == false) style="width: 100%;" @endif>
                                    <button class="btn login-btn active w-100">{{ __('Login') }}</button>
                                </div>
                                @if ($basic_settings->hospital_registration)
                                    <div class="register-button">
                                        <button class="btn register-btn">{{ __('Registration') }}</button>
                                    </div>
                                @endif
                            </div>

                            <form class="account-form" id="login" method="POST"
                                action="{{ setRoute('hospitals.login.submit') }}">
                                @csrf
                                <div class="login-information pt-30">
                                    <div class="row mb-10-none">
                                        <div class="col-lg-12 form-group mb-10">
                                            <label>{{ __('Enter Email') }}</label>
                                            <input type="email" class="form-control form--control" name="credentials"
                                                placeholder="{{ __('Enter Email') }}...">
                                        </div>
                                        <div class="col-lg-12 form-group show_hide_password mb-10">
                                            <label>{{ __('Enter Password') }}</label>
                                            <input type="password" class="form-control form--control" name="password"
                                                placeholder="{{ __('Enter Password') }}...">
                                            <a href="#0" class="show-pass"><i class="fa fa-eye-slash"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <div class="forgot-item text-end">
                                                <label><a href="{{ setRoute('hospitals.password.forgot') }}"
                                                        class="text--base">{{ __('Forgot Password?') }}</a></label>
                                            </div>
                                        </div>
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit" class="btn--base w-100">{{ __('Login Now') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <form id="register" class="d-none" method="POST"
                                action="{{ setRoute('hospitals.register.submit') }}">
                                @csrf
                                <div class="personal-account pt-30 select-account" data-select-target="1">
                                    <div class="row">
                                        <div class="col-lg-12 form-group">
                                            <label>{{ __('Hospital Name') }}</label>
                                            <input type="text" class="form-control form--control" name="hospital_name"
                                                placeholder="{{ __('Hospital Name') }}">
                                        </div>
                                        <div class="col-lg-12 form-group">
                                            <label>{{ __('Email Address') }}</label>
                                            <input type="email" class="form-control form--control" name="email"
                                                placeholder="{{ __('Email') }}">
                                        </div>
                                        <div class="col-lg-12 form-group show_hide_password-2">
                                            <label>{{ __('Enter Password') }}</label>
                                            <input type="password" class="form-control form--control" name="password"
                                                placeholder="{{ __('Password') }}">
                                            <a href="#0" class="show-pass"><i class="fa fa-eye-slash"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                        @php
                                            $agree_policy = DB::table('basic_settings')->first();
                                        @endphp

                                        @if ($agree_policy->hospital_agree_policy == true)
                                            <div class="col-lg-12 form-group">
                                                <div class="custom-check-group">
                                                    <input type="checkbox" id="level-1" name="agree">
                                                    <label for="level-1"
                                                        class="mb-0">{{ __('I have read agreed with the') }} <a
                                                            href="{{ url('useful-link/terms-condition') }}"
                                                            class="text--base">{{ __('Terms & Conditions') }}</a></label>
                                                </div>
                                            </div>
                                        @endif
                                        <div class="col-lg-12 form-group text-center">
                                            <button type="submit"
                                                class="btn--base w-100">{{ __('Register Now') }}</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('script')
@endpush
