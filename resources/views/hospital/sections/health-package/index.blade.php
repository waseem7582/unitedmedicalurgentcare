@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Health Package List'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header-title">
                <div class="table-name mb-10">
                    <h3 class="title">{{ __('Hospital Health Package List') }}</h3>
                </div>
                <div class="add-btn text-end mb-10">
                    <a href="{{ setRoute('hospitals.health-package.create') }}" class="btn--base"><i class="fas la-plus"></i>
                        {{ __('Add Health Package') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-striped custom-table">
                    <thead>
                        <tr>
                            <th>{{ __('Name') }}</th>
                            <th>{{ __('Price') }}</th>
                            <th>{{ __('Status') }}</th>
                            <th>{{ __('Date') }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($package ?? [] as $key => $item)
                            <tr data-item="{{ $item }}">

                                <td>{{ $item->name ?? '' }}</td>
                                <td>
                                    @if ($item->offer_price)
                                        <span class="price"><b>{{ get_amount($item->offer_price) }}
                                                {{ get_default_currency_code() }}</b></span>
                                        <del>{{ get_amount($item->regular_price) }}
                                            {{ get_default_currency_code() }}</del>
                                    @else
                                        <span class="price"><b>{{ get_amount($item->regular_price) }}
                                                {{ get_default_currency_code() }}</b></span>
                                    @endif
                                </td>
                                <td>
                                    @include('frontend.components.form.switcher', [
                                        'name' => 'status',
                                        'value' => $item->status,
                                        'options' => [__('Active') => 1, __('Freeze') => 0],
                                        'onload' => true,
                                        'data_target' => $item->id,
                                    ])
                                </td>
                                <td> {{ $item->created_at ?? '' }}
                                </td>
                                <td class="status-btn">
                                    <a href="{{ setRoute('hospitals.health-package.edit', $item->uuid) }}" class="btn-edit">
                                        <i class="las la-pen"></i>
                                    </a>
                                    <a class="btn-cancel delete-btn">
                                        <i class="las la-trash-alt"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <td colspan="7">
                                <div style="margin-top: 37.5px" class="alert alert-primary w-100 text-center">
                                    {{ __('No Record Found!') }}
                                </div>
                            </td>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        {{ get_paginate($package) }}
    </div>
@endsection

@push('script')
    <script>
        $('.delete-btn').click(function() {
            var oldData = JSON.parse($(this).closest('[data-item]').attr('data-item'));
            var actionRoute = "{{ setRoute('hospitals.health-package.delete') }}";
            var target = oldData.id;
            var message =
                `{{ __('Are you sure to') }} <strong>{{ __('delete') }}</strong> {{ __('this Health Package?') }}`;

            openDeleteModal(actionRoute, target, message);
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


        // Switcher
        switcherAjax("{{ route('hospitals.health-package.status.update') }}");
    </script>
@endpush
