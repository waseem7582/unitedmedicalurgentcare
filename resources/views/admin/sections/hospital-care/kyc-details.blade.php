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
    <div class="custom-card">
        <div class="card-header">
            <h6 class="title">{{ __('Edit KYC') }}</h6>
        </div>
        <div class="card-body">
            <form class="card-form">
                <div class="row align-items-center mb-10-none">
                    <div class="col-xl-4 col-lg-4 form-group">
                        <ul class="user-profile-list-three">
                            <li class="bg--base one">{{ __('Full Name') }}: <span>{{ $users->fullname }}</span></li>
                            <li class="bg--info two">{{ __('username') }}: <span>{{ '@' . $users->username }}</span></li>
                            <li class="bg--success three">{{ __('Email') }}: <span>{{ $users->email }}</span></li>
                            <li class="bg--warning four">{{ __('Status') }}: <span>{{ $users->stringStatus->value }}</span>
                            </li>
                            <li class="bg--danger five">{{ __('Last Login') }}: <span>{{ $users->lastLogin }}</span></li>
                        </ul>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <div class="user-profile-thumb">
                            <img src="{{ $users->userImage }}" alt="user">
                        </div>
                    </div>
                    <div class="col-xl-4 col-lg-4 form-group">
                        <ul class="user-profile-list">
                            <li class="bg--danger one">{{ __('State') }}:
                                <span>{{ $users->address->state ?? '-' }}</span>
                            </li>
                            <li class="bg--warning two">{{ __('Phone Number') }}: <span>{{ $users->full_mobile }}</span>
                            </li>
                            <li class="bg--success three">{{ __('Zip/Postal') }}:
                                <span>{{ $users->address->zip ?? '-' }}</span>
                            </li>
                            <li class="bg--info four">{{ __('City') }}: <span>{{ $users->address->city ?? '-' }}</span>
                            </li>
                            <li class="bg--base five">{{ __('Country') }}:
                                <span>{{ $users->address->country ?? '-' }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </form>
        </div>
    </div>
    <div class="custom-card mt-15">
        <div class="card-header">
            <h6 class="title">{{ __('Information of Logs') }}</h6>
            <span class="{{ $users->kycStringStatus->class }}">{{ $users->kycStringStatus->value }}</span>
            @include('admin.components.link.custom', [
                'href' => setRoute('admin.hospitals.details', $users->username),
                'text' => 'Profile',
                'class' => 'btn btn--base',
                'permission' => 'admin.hospitals.details',
            ])
        </div>
        <div class="card-body">
            @if ($users->kyc != null && $users->kyc->data != null)
                <ul class="product-sales-info">

                    @php
                        $kycData = $users->kyc->data;
                    @endphp
                    @foreach ($kycData ?? [] as $item)
                        @if ($item->type == 'file')
                            @php
                                $file_link = get_file_link('kyc-files', $item->value);
                            @endphp
                            <li>
                                <span class="kyc-title">{{ $item->label }}:</span>
                                @if ($file_link == false)
                                    <span>{{ __('File not found!') }}</span>
                                    @continue
                                @endif

                                @if (its_image($item->value))
                                    <span class="product-sales-thumb">
                                        <a class="img-popup" data-rel="lightcase:myCollection" href="{{ $file_link }}">
                                            <img src="{{ $file_link }}" alt="{{ $item->label }}">
                                        </a>
                                    </span>
                                @else
                                    <span class="text--danger">
                                        @php
                                            $file_info = get_file_basename_ext_from_link($file_link);
                                        @endphp
                                        <a href="{{ setRoute('file.download', ['kyc-files', $item->value]) }}">
                                            {{ Str::substr($file_info->base_name ?? '', 0, 20) . '...' . $file_info->extension ?? '' }}
                                        </a>
                                    </span>
                                @endif
                            </li>
                        @else
                            <li>
                                <span class="kyc-title">{{ $item->label }}:</span>
                                <span>{{ $item->value }}</span>
                            </li>
                        @endif
                    @endforeach
                </ul>
                <div class="product-sales-btn">
                    @if ($users->kyc_verified != global_const()::VERIFIED)
                        @include('admin.components.button.custom', [
                            'type' => 'button',
                            'class' => 'approve-btn w-100',
                            'text' => 'Approve',
                            'permission' => 'admin.hospitals.kyc.approve',
                        ])
                    @endif

                    @if ($users->kyc_verified != global_const()::REJECTED)
                        @include('admin.components.button.custom', [
                            'type' => 'button',
                            'class' => 'bg--danger reject-btn w-100',
                            'text' => 'Reject',
                            'permission' => 'admin.hospitals.kyc.reject',
                        ])
                    @endif
                </div>
            @else
                <div class="alert alert-primary">{{ __('KYC Information not submitted yet') }}</div>
            @endif
        </div>
    </div>

    {{-- KYC Reject Modal --}}
    <div id="reject-modal" class="mfp-hide large">
        <div class="modal-data">
            <div class="modal-header px-0">
                <h5 class="modal-title">{{ __('Rejejct KYC ') }} {{ '@' . $users->username }}</h5>
            </div>
            <div class="modal-form-data">
                <form class="modal-form" method="POST" action="{{ setRoute('admin.hospitals.kyc.reject', $users->username) }}"
                    enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="target" value="{{ $users->username }}">
                    <div class="row mb-10-none">
                        <div class="col-xl-12 col-lg-12 form-group">
                            @include('admin.components.form.textarea', [
                                'label' => __('Explain Rejection Reason') . '*',
                                'name' => 'reason',
                                'value' => old('reason'),
                            ])
                        </div>

                        <div class="col-xl-12 col-lg-12 form-group d-flex align-items-center justify-content-between mt-4">
                            <button type="button" class="btn btn--danger modal-close">{{ __('Cancel') }}</button>
                            <button type="submit" class="btn btn--base">{{ __('Submit') }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('script')
    <script>
        $(".reject-btn").click(function() {

            openModalBySelector($("#reject-modal"))
        });
        $(".approve-btn").click(function() {
            var actionRoute = "{{ setRoute('admin.hospitals.kyc.approve', $users->username) }}";
            var target = "{{ $users->username }}";
            var message = `Are you sure to approve {{ '@' . $users->username }} KYC information.`;
            openDeleteModal(actionRoute, target, message, "Approve", "POST");
        });

        function openDeleteModal(URL, target, message, actionBtnText = "{{ __('Remove') }}", method = "DELETE") {
            if (URL == "" || target == "") {
                return false;
            }

            if (message == "") {
                message = "{{ __('Are you sure to delete ?') }}";
            }
            var method = `<input type="hidden" name="_method" value="${method}">`;
            openModalByContent({
                    content: `<div class="card modal-alert border-0">
                        <div class="card-body">
                            <form method="POST" action="${URL}">
                                <input type="hidden" name="_token" value="${laravelCsrf()}">
                                ${method}
                                <div class="head mb-3">
                                    ${message}
                                    <input type="hidden" name="target" value="${target}">
                                </div>
                                <div class="foot d-flex align-items-center justify-content-between">
                                    <button type="button" class="modal-close btn btn--info">{{ __('Close') }}</button>
                                    <button type="submit" class="alert-submit-btn btn btn--danger btn-loading">${actionBtnText}</button>
                                </div>
                            </form>
                        </div>
                    </div>`,
                },

            );
        }

        function openModalByContent(data = {
            content: "",
            animation: "mfp-move-horizontal",
            size: "medium",
        }) {
            $.magnificPopup.open({
                removalDelay: 500,
                items: {
                    src: `<div class="white-popup mfp-with-anim ${data.size ?? "medium"}">${data.content}</div>`, // can be a HTML string, jQuery object, or CSS selector
                },
                callbacks: {
                    beforeOpen: function() {
                        this.st.mainClass = data.animation ?? "mfp-move-horizontal";
                    },
                    open: function() {
                        var modalCloseBtn = this.contentContainer.find(".modal-close");
                        $(modalCloseBtn).click(function() {
                            $.magnificPopup.close();
                        });
                    },
                },
                midClick: true,
            });
        }
    </script>
@endpush
