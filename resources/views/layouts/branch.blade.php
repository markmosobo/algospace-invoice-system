<!DOCTYPE html>
<html lang="en">
<head>
    <title>@yield('title', 'AlgoSpace Cyber - Digital & Tech Services')</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'AlgoSpaceCyber offers professional cyber and digital services.')">
    <meta name="keywords" content="cyber services, printing, online applications, IT support">
    <meta name="author" content="AlgoSpace">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Favicon -->
    <link rel="icon" href="{{ asset('algospace-favicon.png') }}" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('templates/marketing_site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/swiper.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/datepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/colors/scheme-1.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/custom-swiper-1.css') }}" rel="stylesheet">

    <style>
        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body>
<div id="wrapper">

    <!-- PRELOADER -->
    <div id="de-loader"></div>

    <!-- HEADER -->
    <header class="">

        <!-- TOPBAR -->
        <div id="topbar">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12">

                        <div class="d-flex justify-content-between xs-hide">

                            <div class="d-flex">
                                <div class="topbar-widget">
                                    <a href="#">
                                        <img src="{{ asset('templates/marketing_site/images/svg-white/bell.svg') }}" alt="">
                                        Visit 5 Times, Get Rewarded — Ask for Your Loyalty Card
                                    </a>
                                </div>
                            </div>

                            <div class="d-flex">
                                <div class="topbar-widget me-5">
                                    <a href="tel:+254112514440">
                                        <img src="{{ asset('templates/marketing_site/images/svg-white/phone.svg') }}" alt="">
                                        Call us: +254 112 514 440
                                    </a>
                                </div>

                                <div class="topbar-widget">
                                    <a href="mailto:support@algospace.co.ke">
                                        <img src="{{ asset('templates/marketing_site/images/svg-white/envelope.svg') }}" alt="">
                                        Message us: support@algospace.co.ke
                                    </a>
                                </div>
                            </div>

                        </div>

                    </div>
                </div>
            </div>
        </div>
        <!-- END TOPBAR -->


        <!-- MAIN HEADER -->
        <div class="container">
            <div class="row">
                <div class="col-md-12">

                    <div class="de-flex sm-pt10">

                        <!-- LOGO -->
                        <div class="de-flex-col">
                            <div id="logo">
                                <a href="{{ url('/') }}">
                                    <img class="logo-main" src="{{ asset('templates/marketing_site/images/logo-cropped.webp') }}" alt="Logo">
                                    <img class="logo-mobile" src="{{ asset('templates/marketing_site/images/logo-cropped.webp') }}" alt="Logo">
                                </a>
                            </div>
                        </div>

                        <!-- NAVIGATION MENU (FULL DROPDOWNS PRESERVED) -->
                        <div class="de-flex-col header-col-mid">

                            <ul id="mainmenu">

                                <li>
                                    <a class="menu-item" href="{{ url('/') }}">Home</a>
                                </li>

                                <li>
                                    <a class="menu-item" href="#">Services</a>
                                    <ul>
                                        <li><a href="#">Printing & Copying</a></li>
                                        <li><a href="#">Typing & Documents</a></li>
                                        <li><a href="#">Online Applications</a></li>
                                        <li><a href="#">Internet & Computer Use</a></li>
                                        <li><a href="#">Design & Branding</a></li>
                                        <li><a href="#">IT Support & Repairs</a></li>
                                        <li><a href="#">Web & App Development</a></li>
                                        <li><a href="#">Pricing</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a class="menu-item" href="#">About</a>
                                    <ul>
                                        <li><a href="#">About AlgoSpace</a></li>
                                        <li><a href="#">What We Offer</a></li>
                                        <li><a href="#">Why Choose Us</a></li>
                                        <li><a href="#">Our Setup</a></li>
                                        <li><a href="#">Contact Us</a></li>
                                    </ul>
                                </li>

                                <li>
                                    <a class="menu-item" href="#">Our Work</a>
                                    <ul>
                                        <li><a href="#">Web Projects</a></li>
                                        <li><a href="#">App Projects</a></li>
                                        <li><a href="#">Design Work</a></li>
                                        <li><a href="#">Client Jobs</a></li>
                                        <li><a href="#">Project Details</a></li>
                                    </ul>
                                </li>

                                <li><a class="menu-item" href="#">Guides</a></li>

                                <li><a class="menu-item" href="#">Get in Touch</a></li>

                            </ul>

                        </div>

                        <!-- CTA BUTTONS -->
                        <div class="de-flex-col">

                            <div class="menu_side_area">

                                <a href="{{ url('/login') }}" class="btn-main fx-slide btn-line me-2">
                                    <span>Client Portal</span>
                                </a>

                                <a href="#" class="btn-main fx-slide">
                                    <span>See Our Prices</span>
                                </a>

                                <span id="menu-btn"></span>

                            </div>

                        </div>

                    </div>

                </div>
            </div>
        </div>

    </header>

        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
            <section id="subheader" class="section-dark bg-dark text-light relative jarallax">
                <div class="gradient-edge-top"></div>
                <img src="{{ asset('templates/marketing_site/images/background/5.webp') }}" class="jarallax-img" alt="Contact">
                <div class="container relative z-2">
                    <div class="row gy-4 gx-5 align-items-center">
                        <div class="spacer-double sm-hide"></div>

                        <div class="col-lg-6">
                            <h1 class="mb-0 wow fadeInUp" data-wow-delay=".2s">
                                Contact                </h1>

                            <ul class="crumb wow fadeInUp">
                                <li><a href="index.php.html">Home</a></li>
                                <li class="active">Contact</li>
                            </ul>   
                        </div>

                        <div class="col-lg-6 text-lg-end sm-hide">
                            <h3>
                                "Prevention is cheaper than a breach"                </h3>
                        </div>
                    </div>
                </div>
            </section>    


    <!-- MAIN CONTENT -->
    <main>
        @yield('content')

        {{-- Default About Content (used if no section override) --}}
        @hasSection('content')
        @else
        <section class="py-90">
            <div class="container">
                <h2>Defending Your Digital World</h2>
                <p>
                    AlgoSpace Cyber helps individuals and businesses handle digital tasks
                    such as printing, online applications, IT support, and website development
                    with simplicity and reliability.
                </p>
            </div>
        </section>
        @endif
    </main>

    <!-- FOOTER -->
    <footer class="section-dark">
    <div class="container">
        <div class="row gx-5">
        <div class="col-lg-4 col-sm-6">

            <!-- FIXED IMAGE PATH -->
            <img src="{{ asset('templates/marketing_site/images/logo-cropped.webp') }}" class="logo-footer" alt="">

            <div class="spacer-20"></div>
            <p>
                At AlgoSpace Cyber, we’re the place people turn to when digital tasks need to get done without stress or confusion. 
                Whether it’s printing documents, writing and formatting CVs, submitting KUCCPS, KRA or HELB applications, or handling basic computer and website support, we focus on making technology simple and accessible for everyone.
            </p>
            <div class="social-icons mb-sm-30">
            <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
            <a href="#"><i class="fa-brands fa-x-twitter"></i></a>
            <a href="#"><i class="fa-brands fa-whatsapp"></i></a>
            <a href="#"><i class="fa-brands fa-instagram"></i></a>
            <a href="#"><i class="fa-brands fa-youtube"></i></a>
            </div>
        </div>

        <div class="col-lg-4 col-sm-12 order-lg-1 order-sm-2">
            <div class="row">

                <!-- COMPANY / NAVIGATION -->
                <div class="col-lg-5">
                    <div class="widget">
                        <h5>Quick Links</h5>
                        <ul>
                            <li><a href="#">Home</a></li>
                            <li><a href="#">Services</a></li>
                            <li><a href="#">About Us</a></li>                        
                            <li><a href="#">Pricing</a></li>
                            <li><a href="#">Contact</a></li>
                        </ul>
                    </div>
                </div>

                <!-- REAL SERVICES (ALGOSPACE CYBER) -->
                <div class="col-lg-7">
                    <div class="widget">
                        <h5>What We Do</h5>
                        <ul>
                            <li><a href="#">Printing & Photocopying</a></li>
                            <li><a href="#">CV Writing & Typing</a></li>
                            <li><a href="#">Online Applications (KUCCPS, KRA, HELB)</a></li>
                            <li><a href="#">Computer & Software Installation</a></li>
                            <li><a href="#">Website Development</a></li>
                            <li><a href="#">Internet & Cyber Services</a></li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-4 col-sm-6 order-lg-2 order-sm-1">
            <div class="widget">
            <h5>Contact Us</h5>

            <div class="fw-bold text-white">
                <i class="icofont-location-pin me-2 id-color"></i>Location
            </div>
            Villa Nova Building, Shop 1, along Kapsokwony–Kaptama Road, Kapsokwony

            <div class="spacer-20"></div>

            <div class="fw-bold text-white">
                <i class="icofont-phone me-2 id-color"></i>Call Us
            </div>
            +254 112 514 440

            <div class="spacer-20"></div>

            <div class="fw-bold text-white">
                <i class="icofont-envelope me-2 id-color"></i>Email Us
            </div>
            support@algospacecyber.co.ke
            </div>
        </div>
        </div>
    </div>

    <div class="subfooter">
        <div class="container">
        <div class="row">
            <div class="col-md-12">
            <div class="de-flex">
            <div class="de-flex-col">
                &copy; {{ date('Y') }} AlgoSpace Cyber. All Rights Reserved.
            </div>
                <ul class="menu-simple">
                <li><a href="#">Terms &amp; Conditions</a></li>
                <li><a href="#">Privacy Policy</a></li>
                </ul>
            </div>
            </div>
        </div>
        </div>
    </div>
    </footer>

</div>

<!-- JS -->
<script src="{{ asset('templates/marketing_site/js/plugins.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/designesia.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/swiper.js') }}"></script>
@stack('scripts')
</body>
</html>