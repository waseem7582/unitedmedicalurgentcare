<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ isset($page_title) ? __($page_title) : __('Dashboard') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">


    @include('partials.header-asset')
    @php
        $primaryColor = @$basic_settings->base_color ?? '#7A3DDD';
        $secondaryColor = @$basic_settings->secondary_color ?? '#D860EC';
    @endphp

    <style>
        :root {
            --primary-color: {{ $primaryColor }};
            --secondary-color: {{ $secondaryColor }};
        }
    </style>


    @stack('css')
</head>

<body class="{{ get_default_language_dir() }}">

    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start body overlay
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
    <div id="body-overlay" class="body-overlay"></div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End body overlay
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        Start Dashboard
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

        @include('user.partials.side-nav')

        <div class="main-wrapper">
            <div class="main-body-wrapper">
                @include('user.partials.top-nav')
                <div class="body-wrapper">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>
    <!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
        End Dashboard
    ~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->

    @stack('modal')


    @include('partials.footer-asset')
    @include('user.partials.push-notification')

    @stack('script')

    <script>


    $(".logout-btn").click(function() {

    var actionRoute = "{{ setRoute('user.logout') }}";
    var target = 1;
    var message = `{{ __('Are you sure to') }} <strong>{{ __('Logout') }}</strong>?`;

    openAlertModal(actionRoute, target, message, "{{ __('Logout') }}", "POST");
    /**
    * Function for open delete modal with method DELETE
    * @param {string} URL
    * @param {string} target
    * @param {string} message
    * @returns
    */
    function openAlertModal(URL, target, message, actionBtnText = "{{ __('Remove') }}",
    method =
                "DELETE") {
    if (URL == "" || target == "") {
    return false;
    }

    if (message == "") {
    message = "Are you sure to delete ?";
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
                    <button type="button" class="modal-close btn--base btn-for-modal">{{ __('Close') }}</button>
                    <button type="submit"
                        class="alert-submit-btn btn--danger btn-loading btn-for-modal">${actionBtnText}</button>
                </div>
            </form>
        </div>
    </div>`,
    },

    );
    }
    });
    </script>
    <script>
        function laravelCsrf() {
            return $("head meta[name=csrf-token]").attr("content");
        }
    </script>


    <script>
        var options = {
            series: [{
                name: 'series1',
                color: "#8358ff",
                data: [31, 50, 70, 81, 42, 109, 100]
            }, {
                name: 'series2',
                data: [11, 32, 95, 32, 34, 52, 41]
            }],
            chart: {
                height: 350,
                type: 'area',
                toolbar: {
                    show: false
                },
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth'
            },
            xaxis: {
                type: 'datetime',
                categories: ["2018-09-19T00:00:00.000Z", "2018-09-19T01:30:00.000Z", "2018-09-19T02:30:00.000Z",
                    "2018-09-19T03:30:00.000Z", "2018-09-19T04:30:00.000Z", "2018-09-19T05:30:00.000Z",
                    "2018-09-19T06:30:00.000Z"
                ]
            },
            tooltip: {
                x: {
                    format: 'dd/MM/yy HH:mm'
                },
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart1"), options);
        chart.render();

        var options = {
            series: [{
                data: [44, 55, 41, 64, 22, 43, 21],
                color: "#8358ff"
            }, {
                data: [53, 32, 33, 52, 13, 44, 32]
            }],
            chart: {
                type: 'bar',
                toolbar: {
                    show: false
                },
                height: 350
            },
            plotOptions: {
                bar: {
                    horizontal: true,
                    dataLabels: {
                        position: 'top',
                    },
                }
            },
            dataLabels: {
                enabled: true,
                offsetX: -6,
                style: {
                    fontSize: '12px',
                    colors: ['#fff']
                }
            },
            stroke: {
                show: true,
                width: 1,
                colors: ['#fff']
            },
            tooltip: {
                shared: true,
                intersect: false
            },
            xaxis: {
                categories: [2001, 2002, 2003, 2004, 2005, 2006, 2007],
            },
        };

        var chart = new ApexCharts(document.querySelector("#chart2"), options);
        chart.render();
    </script>


</body>

</html>
