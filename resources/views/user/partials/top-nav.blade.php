<nav class="navbar-wrapper">
    <div class="dashboard-title-part">
        <div class="left">
            <div class="icon">
                <button class="sidebar-menu-bar">
                    <i class="fas fa-exchange-alt"></i>
                </button>
            </div>
            @yield('breadcrumb')
        </div>
        <div class="right">
            @php
                $current_url   = URL::current();
            @endphp
            @if ($current_url == setRoute('user.my.booking.index'))
                <form class="header-search-wrapper">
                    <div class="position-relative">
                        <input class="form-control" name="search_text" type="text" placeholder="{{ __("Ex: Booking Id/ Payment Type") }}" aria-label="Search">
                        <span class="las la-search"></span>
                    </div>
                </form>
            @endif
            <div class="header-notification-wrapper">
                <button class="notification-icon">
                    <i class="las la-bell"></i>
                </button>
                <div class="notification-wrapper">
                    <div class="notification-header">
                        <h5 class="title">{{ __("Notification") }}</h5>
                    </div>
                    <ul class="notification-list">
                        @forelse (get_user_notifications() ?? [] as $item)
                            <li>
                                <div class="thumb">
                                    <img src="{{ auth()->user()->userImage }}" alt="user">
                                </div>
                                <div class="content">
                                    <div class="title-area">
                                        <h5 class="title">{{ __("Booking") }}</h5>
                                        <span class="time">{{ @$item->created_at->diffForHumans() }}</span>
                                    </div>
                                    <span class="sub-title">{{ __(@$item->message->title) ." " . __("Doctor:") . "(" . @$item->message->doctor . ")" . "," . " " . __("Date:") . " " . @$item->message->date . "," . " " . __("Time:") . " " . @$item->message->from_time ."-" . @$item->message->to_time . ","  ." " . __(@$item->message->success) }}</span>
                                </div>
                            </li>
                        @empty
                            <p>{{ __("No Notification Found!") }}</p>
                        @endforelse
                    </ul>
                </div>
            </div>
            <div class="header-user-wrapper">
                <div class="header-user-thumb">
                    <a href="{{ setRoute('user.profile.index') }}"><img src="{{ auth()->user()->userImage }}" alt="client"></a>
                </div>
            </div>
        </div>
    </div>
</nav>
