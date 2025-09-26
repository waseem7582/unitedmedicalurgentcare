<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-menu-wrapper">
            <div class="sidebar-logo">
                <a href="{{ setRoute('frontend.index') }}" class="sidebar-main-logo">
                    <img src="{{ get_logo($basic_settings) }}" data-white_img="{{ get_logo($basic_settings, 'dark') }}"
                        data-dark_img="{{ get_logo($basic_settings) }}" alt="logo">
                </a>
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.dashboard') }}">
                        <i class="menu-icon las la-home"></i>
                        <span class="menu-title">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.my.booking.index') }}">
                        <i class="menu-icon las la-user-clock"></i>
                        <span class="menu-title">{{ __('Doctor Bookings') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.find.doctor') }}">
                        <i class="menu-icon las la-user-md"></i>
                        <span class="menu-title">{{ __('Find Doctor') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.my.booking.service') }}">
                        <i class="menu-icon las la-briefcase-medical"></i>
                        <span class="menu-title">{{ __('Service Bookings') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('user.security.google.2fa') }}">
                        <i class="menu-icon las la-shield-alt"></i>
                        <span class="menu-title">{{ __('Google 2FA') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="logout-btn">
                        <i class="menu-icon las la-sign-out-alt"></i>
                        <span class="menu-title">{{ __('Logout') }}</span>
                    </a>
                </li>
            </ul>

        </div>
        <div class="sidebar-doc-box bg_img"
            data-background="{{ asset('frontend/images/element/sidebar-bg.webp') }}">
            <div class="sidebar-doc-icon">
                <i class="las la-headphones-alt"></i>
            </div>
            <div class="sidebar-doc-content">
                <h4 class="title">{{ __('Need Help') }}?</h4>
                <p>{{ __('How can we help you?') }}</p>
                <div class="sidebar-doc-btn">
                    <a href="{{ setRoute('user.support.ticket.index') }}"
                        class="btn--base w-100">{{ __('Get Support') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(".active-deactive-btn").click(function() {
            var actionRoute = "{{ setRoute('user.security.google.2fa.status.update') }}";
            var target = 1;
            var btnText = $(this).text();
            var message =
            `Are you sure to <strong>${btnText}</strong> 2 factor authentication (Powered by google)?`;
            openAlertModal(actionRoute, target, message, btnText, "POST");
        });
    </script>
@endpush
