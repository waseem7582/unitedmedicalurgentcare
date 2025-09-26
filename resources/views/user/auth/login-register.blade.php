@php
    $app_local = get_default_language_code();
    $default = App\Constants\LanguageConst::NOT_REMOVABLE;
    $slug = Illuminate\Support\Str::slug(App\Constants\SiteSectionConst::LOGIN_SECTION);
    $login = App\Models\Admin\SiteSections::getData($slug)->first();
@endphp
@extends('frontend.layouts.master')

@push('css')
@endpush

@section('content')

    <section class="register-section bg-overlay-account bg_img" data-background="{{ asset('frontend/images/banner/account-bg.webp') }}">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-6 col-lg-7 col-md-9">
                    <div class="register-form">
                        <div class="login-form">
                            <div class="login-header-top">
                                <h3 class="title">{{ __('Log in and Stay Connected') }}</h3>
                            </div>
                            <div class="register-header-top d-none">
                                <h3 class="title">{{ __('Register for an Account Today') }}</h3>
                            </div>
                            <div class="form-hader d-flex justify-content-between">
                                <div class="login-button">
                                    <button class="btn login-btn active">{{ __('Login') }}</button>
                                </div>
                                <div class="register-button">
                                    <button class="btn register-btn">{{ __("Registration") }}</button>
                                </div>
                            </div>
                            <form class="account-form" id="login" method="POST"
                            action="{{ setRoute('user.login.submit') }}">
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
                                                <label><a href="{{ setRoute('user.password.forgot') }}"
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
                            action="{{ setRoute('user.register.submit') }}">
                            @csrf
                                <div class="personal-account pt-30 select-account" data-select-target="1">
                                    <div class="row">
                                        <div class="col-lg-6 col-md-6 form-group">
                                            <label>{{ __('First Name') }}</label>
                                            <input type="text" class="form-control form--control" name="firstname"
                                            placeholder="{{ __('First Name') }}">
                                        </div>
                                        <div class="col-lg-6 col-md-6 form-group">
                                            <label>{{ __('Last Name') }}</label>
                                            <input type="text" class="form-control form--control" name="lastname"
                                            placeholder="{{ __('Last Name') }}">
                                        </div>
                                        <div class="col-lg-6 form-group">
                                            <label>{{ __('Email Address') }}</label>
                                            <input type="email" class="form-control form--control" name="email"
                                            placeholder="{{ __('Email') }}">
                                        </div>
                                        <div class="col-lg-6 form-group show_hide_password-2">
                                            <label>{{ __('Password') }}</label>
                                            <input type="password" class="form-control form--control" name="password"
                                            placeholder="{{ __('Password') }}">
                                            <a href="#0" class="show-pass-2"><i class="fa fa-eye-slash"
                                                    aria-hidden="true"></i></a>
                                        </div>
                                        @php
                                        $agree_policy = DB::table('basic_settings')->first();
                                    @endphp

                                    @if ($agree_policy->agree_policy == true)
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
