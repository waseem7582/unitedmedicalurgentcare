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
        'active' => __('User Care'),
    ])
@endsection

@section('content')
    <div class="custom-card mt-15">
        <div class="card-header">
            <h6 class="title">{{ __('Hospital Overview') }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form">
                <div class="row align-items-center mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        <div class="user-action-btn-area">
                            <div class="user-action-btn">
                                <div class="user-action-btn">
                                    @include('admin.components.button.custom', [
                                        'type' => 'button',
                                        'class' => 'wallet-balance-update-btn bg--danger one',
                                        'text' => __('Add/Subtract Balance'),
                                        'icon' => 'las la-wallet me-1',
                                        'permission' => 'admin.users.wallet.balance.update',
                                    ])
                                </div>
                            </div>
                            <div class="user-action-btn">
                                @include('admin.components.link.custom', [
                                    'href' => setRoute('admin.hospitals.login.logs', $users->username),
                                    'class' => 'bg--base two',
                                    'icon' => 'las la-sign-in-alt me-1',
                                    'text' => __('Login Logs'),
                                    'permission' => 'admin.hospitals.login.logs',
                                ])
                            </div>
                            <div class="user-action-btn">
                                @include('admin.components.link.custom', [
                                    'href' => '#email-send',
                                    'class' => 'bg--base three modal-btn',
                                    'icon' => 'las la-mail-bulk me-1',
                                    'text' => __('Send Email'),
                                    'permission' => 'admin.hospitals.send.mail',
                                ])
                            </div>
                            <div class="user-action-btn">
                                @include('admin.components.link.custom', [
                                    'class' => 'bg--base four login-as-member',
                                    'icon' => 'las la-user-check me-1',
                                    'text' => __('Login as Hospital'),
                                    'permission' => 'admin.hospitals.login.as.member',
                                ])
                            </div>
                            <div class="user-action-btn">
                                @include('admin.components.link.custom', [
                                    'href' => setRoute('admin.hospitals.mail.logs', $users->username),
                                    'class' => 'bg--base five',
                                    'icon' => 'las la-history me-1',
                                    'text' => __('Email Logs'),
                                    'permission' => 'admin.hospitals.mail.logs',
                                ])
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <div class="user-profile-thumb">
                            <img src="{{ $users->userImage }}" alt="user">
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <ul class="user-profile-list">
                            <li class="bg--base one">{{ __('Full Name') }}: <span>{{ $users->fullname }}</span></li>
                            <li class="bg--info two">{{ __('username') }}: <span>{{ '@' . $users->username }}</span></li>
                            <li class="bg--success three">{{ __('Email') }}: <span>{{ $users->email }}</span></li>
                            <li class="bg--warning four">{{ __('Status') }}: <span>{{ $users->stringStatus->value }}</span>
                            </li>
                            <li class="bg--danger five">{{ __('Last Login:') }} <span>{{ $users->lastLogin }}</span></li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="custom-card mt-15">
        <div class="card-header">
            <h6 class="title">{{ __('Information of User') }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form" method="POST"
                action="{{ setRoute('admin.hospitals.details.update', $users->username) }}">
                @csrf
                <div class="row mb-10-none">
                    <div class="col-xl-6 col-lg-6 form-group">
                        @include('admin.components.form.input', [
                            'label' => __('Hospital Name') . '*',
                            'name' => 'hospital_name',
                            'value' => old('hospital_name', $users->hospital_name),
                            'attribute' => 'required',
                        ])
                    </div>
                    <div class="col-xl-6 col-lg-6 form-group">
                        <label>{{ __('Country') }}</label>
                        <select name="country" class="form--control select2-auto-tokenize country-select"
                            data-placeholder="Select Country"
                            data-old="{{ old('country', $users->address->country ?? '') }}"></select>
                    </div>
                    <div class="col-xl-6 col-lg-6 form-group">
                        <label>{{ __('Phone Number') }}</label>
                        <div class="input-group">
                            <div class="input-group-text phone-code">+{{ $users->mobile_code }}</div>
                            <input class="phone-code" type="hidden" name="mobile_code"
                                value="{{ $users->mobile_code }}" />
                            <input type="text" class="form--control" placeholder="{{ __('Write Here...') }}"
                                name="mobile" value="{{ old('mobile', $users->mobile) }}">
                        </div>
                        @error('mobile')
                            <span class="invalid-feedback d-block" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                    <div class="col-xl-6 col-lg-6 form-group">
                        @include('admin.components.form.input', [
                            'label' => __('State'),
                            'name' => 'state',
                            'placeholder' => __('Write Here...'),
                            'value' => old('state', $users->address->state ?? ''),
                        ])
                    </div>

                    <div class="col-xl-6 col-lg-6 form-group">
                        @include('admin.components.form.input', [
                            'label' => __('City'),
                            'name' => 'city',
                            'placeholder' => __('Write Here...'),
                            'value' => old('city', $users->address->city ?? ''),
                        ])
                    </div>
                    <div class="col-xl-6 col-lg-6 form-group">
                        @include('admin.components.form.input', [
                            'label' => __('Zip/Postal'),
                            'name' => 'zip_code',
                            'placeholder' => __('Write Here...'),
                            'value' => old('zip_code', $users->address->zip ?? ''),
                        ])
                    </div>
                    <div class="col-xl-12 col-lg-12 form-group">
                        @include('admin.components.form.input', [
                            'label' => __('Address'),
                            'name' => 'address',
                            'value' => old('address', $users->address->address ?? ''),
                            'placeholder' => __('Write Here...'),
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                        @include('admin.components.form.switcher', [
                            'label' => __('User Status'),
                            'value' => old('status', $users->status),
                            'name' => 'status',
                            'options' => [__('Active') => 1, __('Banned') => 0],
                            'permission' => 'admin.users.details.update',
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                        @include('admin.components.form.switcher', [
                            'label' => __('Email Verification'),
                            'value' => old('email_verified', $users->email_verified),
                            'name' => 'email_verified',
                            'options' => [__('verified') => 1, __('unverified') => 0],
                            'permission' => 'admin.users.details.update',
                        ])
                    </div>
                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                        @include('admin.components.form.switcher', [
                            'label' => __('2FA Verification'),
                            'value' => old('two_factor_verified', $users->two_factor_verified),
                            'name' => 'two_factor_verified',
                            'options' => [__('verified') => 1, __('unverified') => 0],
                            'permission' => 'admin.users.details.update',
                        ])
                    </div>

                    <div class="col-xl-3 col-lg-3 col-md-6 col-sm-6 form-group">
                        @include('admin.components.form.switcher', [
                            'label' => __('KYC Verification'),
                            'value' => old('kyc_verified', $users->kyc_verified),
                            'name' => 'kyc_verified',
                            'options' => [__('verified') => 1, __('unverified') => 0],
                            'permission' => 'admin.users.details.update',
                        ])
                    </div>

                    <div class="col-xl-12 col-lg-12 form-group mt-4">
                        @include('admin.components.button.form-btn', [
                            'text' => 'Update',
                            'permission' => 'admin.users.details.update',
                            'class' => 'w-100 btn-loading',
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Send Email Modal --}}
    @include('admin.components.modals.send-mail-hospital', compact('users'))

    {{-- Hospital Balance Update Modal --}}
    @if (admin_permission_by_name('admin.hospital.wallet.balance.update'))
        <div id="wallet-balance-update-modal" class="mfp-hide large">
            <div class="modal-data">
                <div class="modal-header px-0">
                    <h5 class="modal-title">{{ __('Add/Subtract Balance') }}</h5>
                </div>
                <div class="modal-form-data">
                    <form class="modal-form" method="POST"
                        action="{{ setRoute('admin.hospitals.wallet.balance.update', $users->username) }}"
                        enctype="multipart/form-data">
                        @csrf
                        <div class="row mb-10-none">
                            <div class="col-xl-12 col-lg-12 form-group">
                                <label for="balance">{{ __('Type') }}<span>*</span></label>
                                <select name="type" id="balance" class="form--control nice-select">
                                    <option disabled selected value=" ">{{ __('Select Type') }}</option>
                                    <option value="add">{{ __('Balance Add') }}</option>
                                    <option value="subtract">{{ __('Balance Subtract') }}</option>
                                </select>
                                @error('type')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                <label for="wallet">{{ __('Hospital Wallet') }}<span>*</span></label>
                                <select name="wallet" id="wallet" class="form--control select2-auto-tokenize">
                                    <option disabled selected value="">{{ __('Select Hospital Wallet') }}</option>
                                    @foreach ($users->wallets()->get() ?? [] as $item)
                                        <option value="{{ $item->id }}">{{ $item->currency->code }}</option>
                                    @endforeach
                                </select>
                                @error('wallet')
                                    <span class="invalid-feedback d-block" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input', [
                                    'label' => __('Amount'),
                                    'label_after' => '<span>*</span>',
                                    'type' => 'text',
                                    'name' => 'amount',
                                    'attribute' => 'step="any"',
                                    'value' => old('amount'),
                                    'placeholder' => __('Write Here..'),
                                    'class' => 'number-input',
                                ])
                            </div>
                            <div class="col-xl-12 col-lg-12 form-group">
                                @include('admin.components.form.input', [
                                    'label' => __('Remark'),
                                    'label_after' => '<span>*</span>',
                                    'name' => 'remark',
                                    'value' => old('remark'),
                                    'placeholder' => __('Write Here..'),
                                ])
                            </div>
                            <div
                                class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                                <button type="button" class="btn btn--danger modal-close">{{ __('Close') }}</button>
                                <button type="submit" class="btn btn--base">{{ __('Action') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

@endsection

@push('script')
    <script>
        getAllCountries("{{ setRoute('global.countries') }}");
        $(document).ready(function() {

            openModalWhenError("email-send", "#email-send");

            $("select[name=country]").change(function() {
                var phoneCode = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCode);
            });

            setTimeout(() => {
                var phoneCodeOnload = $("select[name=country] :selected").attr("data-mobile-code");
                placePhoneCode(phoneCodeOnload);
            }, 400);

            countrySelect(".country-select", $(".country-select").siblings(".select2"));
            stateSelect(".state-select", $(".state-select").siblings(".select2"));


            $(".login-as-member").click(function() {
                var action = "{{ setRoute('admin.hospitals.login.as.member', $users->username) }}";
                var target = "{{ $users->username }}";
                postFormAndSubmit(action, target);
            });
        })

        $(".wallet-balance-update-btn").click(function() {
            openModalBySelector("#wallet-balance-update-modal");
        });


        openModalWhenError("wallet-balance-update-modal", "#wallet-balance-update-modal");
    </script>
@endpush
