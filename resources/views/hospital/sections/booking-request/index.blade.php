@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Booking Request'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
    ])
@endsection

@section('content')
    <div class="table-area mt-10">
        <div class="table-wrapper">
            <div class="my-salon parlour-list-area">
                <div class="table-responsive">
                    <table class="table table-striped custom-table">
                        <thead>
                            <tr>

                                <th>{{ __('Patient Name') }}</th>
                                <th>{{ __('Mobile') }}</th>
                                <th>{{ __('Email') }}</th>
                                <th>{{ __('Visit Type') }}</th>
                                <th>{{ __('Schedule Date') }}</th>
                                <th>{{ __('Payment Type') }}</th>
                                <th>{{ __('Status') }}</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($booking_data ?? [] as $index => $item)

                                <tr data-item="{{ json_encode($item) }}" class="text-light">

                                    <td>
                                        {{ $item->booking_data->data->name ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item->booking_data->data->number ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item->booking_data->data->email ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item->booking_data->data->visit_type ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item->date ?? '' }}
                                    </td>
                                    <td>
                                        {{ $item->type ?? '' }}
                                    </td>
                                    <td>
                                        <span
                                        class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('hospitals.booking.request.details', ['booking_id' => $item->uuid]) }}"
                                                class="btn btn--base edit-modal-button">
                                                <i class="las la-eye"></i>
                                            </a>

                                        </div>
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
        </div>
        {{ get_paginate($booking_data) }}
    </div>
@endsection
@push('script')
@endpush
