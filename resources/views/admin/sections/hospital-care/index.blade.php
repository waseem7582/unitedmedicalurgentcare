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
        'active' => __('Hospital Care'),
    ])
@endsection

@section('content')
    <div class="table-area">
        <div class="table-wrapper">
            <div class="table-header">
                <h5 class="title">{{ __("All Hospitals") }}</h5>
                <div class="table-btn-area">
                    @include('admin.components.search-input',[
                        'name'  => 'hospital_search',
                    ])
                        @include('admin.components.link.add-default',[
                        'text'          => __("Add Hospital"),
                        'href'          => setRoute('admin.hospitals.create'),
                        'permission'    => 'admin.hospitals.create'
                    ])
                </div>
            </div>
            <div class="table-responsive">
                @include('admin.components.data-table.hospital-table',compact('users'))
            </div>
        </div>
        {{ get_paginate($users) }}
    </div>
@endsection

@push('script')
    <script>
        itemSearch($("input[name=hospital_search]"),$(".user-search-table"),"{{ setRoute('admin.hospitals.search') }}");
    </script>
@endpush
