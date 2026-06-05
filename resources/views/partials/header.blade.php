<header>

    <!-- TOPBAR -->
    <div id="topbar">
        <div class="container">
            <div class="d-flex justify-content-between xs-hide">

                <div class="topbar-widget">
                    <a href="#">
                        <img src="{{ asset('templates/marketing_site/images/svg-white/bell.svg') }}">
                        Fast Digital Cyber Services in Kenya
                    </a>
                </div>

                <div class="d-flex">
                    <div class="topbar-widget me-4">
                        <a href="tel:+254112514440">
                            <img src="{{ asset('templates/marketing_site/images/svg-white/phone.svg') }}">
                            +254 112 514 440
                        </a>
                    </div>

                    <div class="topbar-widget">
                        <a href="mailto:support@algospace.co.ke">
                            <img src="{{ asset('templates/marketing_site/images/svg-white/envelope.svg') }}">
                            support@algospace.co.ke
                        </a>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- MAIN NAV -->
    <div class="container">
        <div class="de-flex sm-pt10">

            <!-- LOGO -->
            <div id="logo">
                <a href="{{ url('/') }}">
                    <img class="logo-main" src="{{ asset('templates/marketing_site/images/logo-cropped.webp') }}">
                    <img class="logo-mobile" src="{{ asset('templates/marketing_site/images/logo-cropped.webp') }}">
                </a>
            </div>

            <!-- MENU -->
            <ul id="mainmenu">

                <li><a href="{{ url('/') }}">Home</a></li>

                <li><a href="#">Services</a></li>

                <li><a href="{{ url('/about') }}">About</a></li>

                <li><a href="#">Projects</a></li>

                <li><a href="#">Blog</a></li>

                <li><a href="{{ url('/contact') }}">Contact</a></li>

            </ul>

            <!-- CTA -->
            <div class="menu_side_area">

                <a href="{{ route('submit.job') }}" class="btn-main fx-slide btn-line">
                    <span>Submit Job</span>
                </a>

                <span id="menu-btn"></span>

            </div>

        </div>
    </div>

</header>