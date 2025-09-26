<!DOCTYPE html>
<html lang="{{ get_default_language_code() }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="csrf-token" content="{{ csrf_token() }}">
     <!-- favicon -->
    <link rel="shortcut icon" href="{{ get_hospital_fav($basic_settings) }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@100;200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @php
        $current_url = URL::current();
    @endphp
    @if ($current_url == setRoute('frontend.index'))
        <title>{{ __($basic_settings->hospital_site_name) ?? '' }} - {{ __($basic_settings->hospital_site_title) ?? '' }}</title>
    @else
        <title>{{ __($basic_settings->hospital_site_name) ?? '' }} - {{ __($page_title) ?? '' }}</title>
    @endif

    @include('partials.header-asset')

    @stack('css')
    @php
        $hospitalPrimaryColor = @$basic_settings->hospital_base_color ?? '#7A3DDD';
        $hospitalSecondaryColor = @$basic_settings->hospital_secondary_color ?? '#D860EC';
    @endphp

    <style>
        :root {
            --primary-color: {{ $hospitalPrimaryColor }};
            --secondary-color: {{ $hospitalSecondaryColor }};
        }
    </style>
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
        @include('hospital.partials.side-nav')

        <div class="main-wrapper">
            <div class="main-body-wrapper">
                @include('hospital.partials.top-nav')
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


    @stack('script')


    @php
        $errorName = '';
    @endphp
    @if ($errors->any())
        @php
            $error = (object) $errors;
            $msg = $error->default;
            $messageNames = $msg->keys();
            $errorName = $messageNames[0];
        @endphp
    @endif
    <script>
        var error = "{{ $errorName }}";
        if (
            error == 'firstname' ||
            error == 'agree' ||
            error == 'register_password' ||
            error == 'register_email' ||
            error == 'lastname'
        ) {
            $('.register-btn').addClass('active');
            $('#login').addClass('d-none');
            $('.login-btn').removeClass('active');
            $('#register').removeClass('d-none');
        }
    </script>

    <script>
        var fileHolderAfterLoad = {};
    </script>

    <script src="https://appdevs.cloud/cdn/fileholder/v1.0/js/fileholder-script.js" type="module"></script>
    <script type="module">
        import {
            fileHolderSettings
        } from "https://appdevs.cloud/cdn/fileholder/v1.0/js/fileholder-settings.js";
        import {
            previewFunctions
        } from "https://appdevs.cloud/cdn/fileholder/v1.0/js/fileholder-script.js";

        var inputFields = document.querySelector(".file-holder");
        fileHolderAfterLoad.previewReInit = function(inputFields) {
            previewFunctions.previewReInit(inputFields)
        };

        fileHolderSettings.urls.uploadUrl = "{{ setRoute('fileholder.upload') }}";
        fileHolderSettings.urls.removeUrl = "{{ setRoute('fileholder.remove') }}";
    </script>

    <script>
        function fileHolderPreviewReInit(selector) {
            var inputField = document.querySelector(selector);
            fileHolderAfterLoad.previewReInit(inputField);
        }
    </script>

</body>

</html>
