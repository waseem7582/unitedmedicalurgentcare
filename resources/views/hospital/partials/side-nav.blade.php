<div class="sidebar">
    <div class="sidebar-inner">
        <div class="sidebar-menu-wrapper">
            <div class="sidebar-logo">
                <a href="{{ setRoute('frontend.index') }}" class="sidebar-main-logo">
                    <img src="{{ get_logo_hospital($basic_settings) }}" data-white_img="{{ get_logo_hospital($basic_settings, 'dark') }}"
                        data-dark_img="{{ get_logo_hospital($basic_settings) }}" alt="logo">
                </a>
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.dashboard') }}">
                        <i class="menu-icon las la-tachometer-alt"></i>
                        <span class="menu-title">{{ __('Dashboard') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.booking.request.index') }}">
                        <i class="menu-icon las la-calendar-check"></i>
                        <span class="menu-title">{{ __('Booking Request') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.booking.request.home.service') }}">
                        <i class="menu-icon las las la-ambulance"></i>
                        <span class="menu-title">{{ __('Home Service Request') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.withdraw.money.index') }}">
                        <i class="menu-icon las la-hand-holding-usd"></i>
                        <span class="menu-title">{{ __('Withdraw Money') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.department.index') }}">
                        <i class="menu-icon las la-building"></i>
                        <span class="menu-title">{{ __('Hospital Department') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.branch.index') }}">
                        <i class="menu-icon las la-code-branch"></i>
                        <span class="menu-title">{{ __('Hospital Branch') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('hospitals.doctor.index') }}">
                        <i class="menu-icon las la-user-md"></i>
                        <span class="menu-title">{{ __('Manage Doctor') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('hospitals.investigation.index') }}">
                        <i class="menu-icon las la-vials"></i>
                        <span class="menu-title">{{ __('Investigation') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('hospitals.health-package.index') }}">
                        <i class="menu-icon las la-notes-medical"></i>
                        <span class="menu-title">{{ __('Health Package') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ route('hospitals.authorize.kyc') }}">
                        <i class="menu-icon las la-id-card"></i>
                        <span class="menu-title">{{ __('KYC Verification') }}</span>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="{{ setRoute('hospitals.security.google.2fa') }}">
                        <i class="menu-icon las la-shield-alt"></i>
                        <span class="menu-title">{{ __('2FA') }}</span>
                    </a>
                </li>

                <li class="sidebar-menu-item">
                    <a href="javascript:void(0)" class="logout-btn-hospital">
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
                <h4 class="title">{{ __('Help Center') }}?</h4>
                <p>{{ __('How can we help you?') }}</p>
                <div class="sidebar-doc-btn">
                    <a href="{{ route('hospitals.support.ticket.index') }}"
                        class="btn--base w-100">{{ __('Get Support') }}</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        $(".logout-btn-hospital").click(function() {
            var actionRoute = "{{ setRoute('hospitals.logout') }}";
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
            function openAlertModal(URL, target, message, actionBtnText = "{{ __('Remove') }}", method =
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
                          <button type="submit" class="alert-submit-btn btn--danger btn-loading btn-for-modal">${actionBtnText}</button>
                      </div>
                  </form>
              </div>
          </div>`,
                    },

                );
            }
        });
    </script>
@endpush
