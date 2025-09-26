@extends('user.layouts.master')

@section('breadcrumb')
    @include('user.components.breadcrumb', [
        'breadcrumbs' => [
            [
                'name' => __('Dashboard'),
                'url' => setRoute('user.dashboard'),
            ],
        ],
    ])
@endsection

@section('content')
    <div class="dashboard-card-area pt-3">
        <div class="row mb-20-none">
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img"
                    data-background="{{ asset('frontend/images/element/card-bg.webp') }}"
                    style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Transactions') }}</span>
                        <h4 class="sub-title text--base">{{ $total_transactions ?? 0 }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-dollar-sign"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img"
                    data-background="{{ asset('frontend/images/element/card-bg.webp') }}"
                    style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Online Transactions') }}</span>
                        <h4 class="sub-title text--base">{{ $total_online_transactions ?? 0 }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="menu-icon las la-history"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img"
                    data-background="{{ asset('frontend/images/element/card-bg.webp') }}"
                    style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Cash Payments') }}</span>
                        <h4 class="sub-title text--base">{{ $total_cash_transactions ?? 0 }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img"
                    data-background="{{ asset('frontend/images/element/card-bg.webp') }}"
                    style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Transactions Amount') }}</span>
                        <h4 class="sub-title text--base">
                            {{ get_amount($total_transactions_amount, get_default_currency_code()) ?? 0 }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
            <div class="col-xxl-3 col-xl-4 col-lg-4 col-md-4 col-sm-6 mb-20">
                <div class="dasboard-card-item bg-overlay  bg_img"
                    data-background="{{ asset('frontend/images/element/card-bg.webp') }}"
                    style="background-image: url('{{ asset('frontend/images/element/card-bg.webp') }}');">
                    <div class="card-title">
                        <span class="title">{{ __('Total Service Booking') }}</span>
                        <h4 class="sub-title text--base">
                            {{ $total_service_booking ?? 0 }}</h4>
                    </div>
                    <div class="card-icon">
                        <i class="las la-cloud-upload-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="chart-container pt-5">
        <div class="chart-main" id="chart" class="chart"
            data-chart_one_data="{{ json_encode($data['chart_one_data']) }}"
            data-month_day="{{ json_encode($data['month_day']) }}">
        </div>
    </div>
    <div class="booking-history pt-60">
        <div class="title-header pb-20">
            <h3 class="title">{{ __('Recent Bookings') }}</h3>
        </div>
        <div class="dashboard-list-wrapper">
            @forelse ($booking_data ?? [] as $item)
                <div class="dashboard-list-item-wrapper show">
                    <div class="dashboard-list-item sent">
                        <div class="dashboard-list-left">
                            <div class="dashboard-list-user-wrapper">
                                <div class="dashboard-list-user-icon">
                                    <img src="{{ get_image($item->doctor->image, 'doctor') }}" alt="user">
                                </div>
                                <div class="dashboard-list-user-content">
                                    <h4 class="title">{{ $item->doctor->name }}</h4>
                                    <span
                                        class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="dashboard-list-right">
                            <h4 class="main-money text--base"> {{ \Carbon\Carbon::parse($item->date)->format('jS M Y') }}
                            </h4>
                        </div>
                    </div>
                    <div class="preview-list-wrapper">
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-user"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Name') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $item->user->username }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-envelope"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Email') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $item->user->email }}</span>
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-history"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Schedule Date') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span>{{ $item->date }}</span>

                            </div>

                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-half"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Charge') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ getAmount($item->total_charge) }}
                                </span">
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-battery-full"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Total') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ getAmount($item->payable_price) }}
                                </span">
                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-smoking"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Status') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right text-center">

                                <span
                                    class="{{ $item->stringStatus->class }}">{{ __($item->stringStatus->value) }}</span>

                            </div>
                        </div>
                        <div class="preview-list-item">
                            <div class="preview-list-left">
                                <div class="preview-list-user-wrapper">
                                    <div class="preview-list-user-icon">
                                        <i class="las la-clock"></i>
                                    </div>
                                    <div class="preview-list-user-content">
                                        <span>{{ __('Schedule') }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="preview-list-right">
                                <span">{{ $item->schedule->from_time . ' - ' . $item->schedule->to_time }}
                                </span">
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <td colspan="7">
                    <div style="margin-top: 37.5px" class="alert alert-primary w-100 text-center">
                        {{ __('No Record Found!') }}
                    </div>
                </td>
            @endforelse
        </div>
    </div>
@endsection
@push('script')
    <script>
        const currentYear = new Date().getFullYear();
        var chart1 = $('#chart');
        var chart_one_data = chart1.data('chart_one_data');
        var month_day = chart1.data('month_day');





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
                color: "#FFFFFF",
                data: chart_one_data.complete_data
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
                categories: month_day,
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
