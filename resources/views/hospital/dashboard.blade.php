@extends('hospital.layouts.master')

@section('breadcrumb')
    @include('hospital.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('hospitals.dashboard'),
            ],
        ],
    ])
@endsection

@section('content')
    <div class="dashboard-card-area pt-3">
        <div class="row mb-20-none">
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Hospital Wallets') }}</span>
                        <h4 class="sub-title text--base">{{ get_amount($hospital_wallet->balance ?? '0') }}
                            <span>{{ get_default_currency_code() }}</span>
                        </h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Hospital Offline Wallets') }}</span>
                        <h4 class="sub-title text--base">{{ get_amount($hospital_offline_wallet->balance ?? '0') }}
                            <span>{{ get_default_currency_code() }}</span>
                        </h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                </div>
            </div>

            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Branch') }}</span>
                        <h4 class="sub-title text--base">{{ $total_branch }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Doctors') }}</span>
                        <h4 class="sub-title text--base">{{ $total_doctor }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Departments') }}</span>
                        <h4 class="sub-title text--base">{{ $total_departments }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Service Booking') }}</span>
                        <h4 class="sub-title text--base">{{ $total_service_booking }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img" data-background="{{ asset('frontend/images/element/card-bg.webp') }}"  style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Service') }}</span>
                        <h4 class="sub-title text--base">{{ $total_service  }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-container pt-5">
        <div class="chart-main" id="chart" class="chart" data-chart_one_data="{{ json_encode($data['chart_one_data']) }}"
            data-month_day="{{ json_encode($data['month_day']) }}">
        </div>
    </div>
@endsection

@push('script')
    <script>
        // Retrieve the chart container and data
        const currentYear = new Date().getFullYear();
        var chart1 = $('#chart');
        var chart_one_data = chart1.data('chart_one_data'); // Get the data passed to the chart
        var month_day = chart1.data('month_day'); // Get the month_day data

        var formattedDates = month_day.map(date => {
            let d = new Date(date);
            return d.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'short',
                day: '2-digit'
            }).replace(',', '');
        });


        var options = {
            series: [{
                name: 'Completed Transactions',
                color: "#637DFE",
                data: chart_one_data.complete_data // Use the correct key
            }],
            chart: {
                type: 'bar',
                height: 350,
                stacked: true,
                toolbar: {
                    show: false
                },
                zoom: {
                    enabled: true
                }
            },
            plotOptions: {
                bar: {
                    horizontal: false,
                    borderRadius: 10
                },
            },
            xaxis: {

                type: 'categories', // Use categories instead of datetime
                categories: formattedDates,
            },
            legend: {
                position: 'bottom',
                offsetX: 40
            },
            title: {
                text: `Monthly Transactions, ${currentYear}`,
                floating: true,
                offsetY: 330,
                align: 'center',
                style: {
                    color: '#FFFFFF'
                }
            },
            fill: {
                opacity: 1
            }
        };

        // Render the chart
        var chart = new ApexCharts(document.querySelector("#chart"), options);
        chart.render();
    </script>
@endpush
