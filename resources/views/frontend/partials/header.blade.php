@php
    $menues = DB::table('setup_pages')->where('status', 1)->get();
    $language = App\Models\Admin\Language::where('status', 1)->first();
@endphp

<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    Start Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->
<header class="header-section position-relative">
    <div class="header">
        <div class="header-bottom-area">
            <div class="container custom-container">
                <div class="header-menu-content">
                    <nav class="navbar navbar-expand-xl p-0">
                        <a class="site-logo site-title" href="{{ setroute('frontend.index') }}"><img
                            src="{{ get_logo($basic_settings) }}" alt="site-logo"></a>
                        <button class="navbar-toggler ms-auto" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent"
                            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="fas fa-bars"></span>
                        </button>
                        <div class="collapse navbar-collapse" id="navbarSupportedContent">
                            <ul class="navbar-nav main-menu ms-auto">
                                @foreach ($menues as $item)
                                @php
                                    $title = $item->title ?? '';
                                @endphp
                                <li><a href="{{ url($item->url) }}"
                                        class=" @if ($current_url == url($item->url)) active @endif ">{{ __($title) }}
                                    </a></li>
                            @endforeach
                            </ul>
                            <div class="language-select">
                                @php
                                    $session_lan = session('local') ?? get_default_language_code();

                                @endphp
                                <select class="form--control langSel nice-select" name="lang_switcher">
                                    @foreach ($__languages as $item)
                                        <option value="{{ $item->code }}"
                                            @if ($session_lan == $item->code) selected @endif>{{ __($item->name) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="header-action">
                                <div class="header-action">
                                    @if (Auth::guard('hospital')->check())
                                        <a class="btn--base"
                                            href="{{ setRoute('hospitals.dashboard') }}">{{ __('Dashboard') }}</a>
                                    @elseif (Auth::guard('web')->check())
                                        <a class="btn--base"
                                            href="{{ setRoute('user.dashboard') }}">{{ __('Dashboard') }}</a>
                                    @else
                                        <a href="{{ setRoute('user.login') }}" class="btn--base">{{ __('Login Now') }}</a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
            </div>
        </div>
    </div>
</header>


<!--~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~
    End Header
~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~~-->


@push('script')
<script>
    $("select[name=lang_switcher]").change(function(){
        var selected_value = $(this).val();
        var submitForm = `<form action="{{ setRoute('frontend.languages.switch') }}" id="local_submit" method="POST"> @csrf <input type="hidden" name="target" value="${$(this).val()}" ></form>`;
        $("body").append(submitForm);
        $("#local_submit").submit();
    });

</script>
@endpush

