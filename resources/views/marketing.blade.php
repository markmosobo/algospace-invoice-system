<!DOCTYPE html>
<html lang="en">
<head>
    <title>AlgoSpace Cyber - Digital & Tech Services</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AlgoSpaceCyber offers professional cyber services.">
    <meta name="keywords" content="cyber, software development, antivirus, data protection">
    <meta name="author" content="AlgoSpace">

    <!-- Favicon -->
    <!-- <link rel="icon" href="{{ asset('templates/marketing_site/images/icon.webp') }}" sizes="16x16"> -->
    <link rel="icon" href="/algospace-favicon.png" type="image/x-icon">

    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">
    <!-- CSS Files -->
    <link href="{{ asset('templates/marketing_site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/swiper.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/datepicker.css') }}" rel="stylesheet">

    <!-- Color Scheme -->
    <link href="{{ asset('templates/marketing_site/css/colors/scheme-1.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/custom-swiper-1.css') }}" rel="stylesheet">
    <!-- Font override -->
    <style>
        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'Nunito', sans-serif;
        }
    </style>
</head>

<body>

<div id="wrapper">

    <div class="float-text show-on-scroll">
        <span><a href="#">Scroll to top</a></span>
    </div>

    <div class="scrollbar-v show-on-scroll"></div>

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
    <!-- header end -->

    <div class="no-bottom no-top" id="content">
        <div id="top"></div>

    <!-- HERO -->
    <section class="jarallax text-light section-dark" data-video-src="mp4:video/1.mp4">
        <div class="sw-overlay op-6"></div>
        <div class="gradient-edge-bottom"></div>

        <div class="container relative z-3">
            <div class="row g-4 justify-content-center">
                <div class="col-lg-12 text-center"> 
                    <div class="spacer-double"></div>

                    <div class="subtitle text-light s2 mb-3 wow fadeInUp" data-wow-delay=".0s">
                        Reliable Cyber & Digital Services
                    </div>

                    <h1 class="fs-120 fs-xs-10vw wow fadeInUp">
                        AlgoSpace Cyber
                    </h1>

                    <p class="lead mb-0 col-lg-6 offset-lg-3 wow fadeInUp" data-wow-delay=".2s">
                        Your one-stop cyber for fast internet, printing, scanning, typing, online applications,
                        government services, and everyday digital support — quick, affordable, and reliable.
                    </p>

                    <div class="spacer-single"></div>

                    <a class="btn-main fx-slide wow fadeIn" data-wow-delay=".6s" href="#">
                        <span>Get Started</span>
                    </a>
                </div>

                <div class="spacer-single"></div>

                <div class="row g-4">

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay=".2s">
                        <img src="{{ asset('templates/marketing_site/images/badge/1.webp') }}" class="w-100 px-4" alt="">
                    </div>

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay=".4s">
                        <img src="{{ asset('templates/marketing_site/images/badge/2.webp') }}" class="w-100 px-4" alt="">
                    </div>

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay=".6s">
                        <img src="{{ asset('templates/marketing_site/images/badge/3.webp') }}" class="w-100 px-4" alt="">
                    </div>

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay=".8s">
                        <img src="{{ asset('templates/marketing_site/images/badge/4.webp') }}" class="w-100 px-4" alt="">
                    </div>

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay="1.2s">
                        <img src="{{ asset('templates/marketing_site/images/badge/5.webp') }}" class="w-100 px-4" alt="">
                    </div>

                    <div class="col-lg-2 col-sm-4 col-6 wow fadeInRight" data-wow-delay="1.4s">
                        <img src="{{ asset('templates/marketing_site/images/badge/6.webp') }}" class="w-100 px-4" alt="">
                    </div>

                </div>
            </div>
        </div>
    </section>

    <!-- REMOTE SERVICES / ONLINE ORDERS -->
    <section class="section-dark bg-dark-2 text-light">
        <div class="container">

            <div class="row g-4 align-items-center">

                <!-- TEXT SIDE -->
                <div class="col-lg-6">

                    <div class="subtitle wow fadeInUp">
                        Serving You Anywhere
                    </div>

                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Not Near Our Cyber? We Help You Online
                    </h2>

                    <p class="lead wow fadeInUp" data-wow-delay=".4s">
                        You don’t need to visit our physical cyber to get help.
                        We assist clients remotely with document typing, CV writing,
                        online applications, website setup, system support, and more.
                    </p>

                    <ul class="wow fadeInUp" data-wow-delay=".5s">
                        <li>✔ WhatsApp & Email support</li>
                        <li>✔ M-Pesa payment supported</li>
                        <li>✔ Fast turnaround time</li>
                        <li>✔ Clients served across Kenya</li>
                    </ul>

                    <div class="spacer-single"></div>

                    <a href="https://wa.me/254112514440"
                    target="_blank"
                    class="btn-main fx-slide me-3 wow fadeInUp"
                    data-wow-delay=".6s">
                        <span>Request Help on WhatsApp</span>
                    </a>

                    <a href="{{ route('submit.job') }}"
                    class="btn-main fx-slide btn-line wow fadeInUp"
                    data-wow-delay=".7s">
                        <span>Submit a Job Online</span>
                    </a>

                </div>

                <!-- IMAGE SIDE -->
                <div class="col-lg-6">

                    <div class="relative wow fadeInUp" data-wow-delay=".3s">

                        <img src="{{ asset('templates/marketing_site/images/misc/remote-work.webp') }}"
                            class="w-100 rounded-1"
                            alt="Remote Cyber Services">

                    </div>

                </div>

            </div>

        </div>
    </section>

    <!-- HOW IT WORKS -->
    <section class="section-dark bg-dark section-pad text-light">
        <div class="container">

            <div class="text-center mb-5">
                <div class="subtitle wow fadeInUp">Simple Process</div>

                <h2 class="wow fadeInUp" data-wow-delay=".2s">
                    How It Works
                </h2>

                <p class="lead wow fadeInUp" data-wow-delay=".4s">
                    Getting help from AlgoSpace Cyber is simple. Whether online or in person,
                    we follow a clear process to get your work done fast and correctly.
                </p>
            </div>

            <div class="row g-4">

                <!-- STEP 1 -->
                <div class="col-lg-3 col-md-6">
                    <div class="bg-dark-gradient rounded-2 p-30 h-100 wow fadeInUp" data-wow-delay=".1s">
                        <span class="fs-32 fw-bold text-white">01</span>
                        <h4 class="mt-3">Submit Your Request</h4>
                        <p>Use WhatsApp or the online form to tell us what you need.</p>
                    </div>
                </div>

                <!-- STEP 2 -->
                <div class="col-lg-3 col-md-6">
                    <div class="bg-dark-gradient rounded-2 p-30 h-100 wow fadeInUp" data-wow-delay=".3s">
                        <span class="fs-32 fw-bold text-white">02</span>
                        <h4 class="mt-3">Get a Quote</h4>
                        <p>We review your request and send a clear price estimate.</p>
                    </div>
                </div>

                <!-- STEP 3 -->
                <div class="col-lg-3 col-md-6">
                    <div class="bg-dark-gradient rounded-2 p-30 h-100 wow fadeInUp" data-wow-delay=".5s">
                        <span class="fs-32 fw-bold text-white">03</span>
                        <h4 class="mt-3">Make Payment</h4>
                        <p>Pay securely via M-Pesa to begin processing.</p>
                    </div>
                </div>

                <!-- STEP 4 -->
                <div class="col-lg-3 col-md-6">
                    <div class="bg-dark-gradient rounded-2 p-30 h-100 wow fadeInUp" data-wow-delay=".7s">
                        <span class="fs-32 fw-bold text-white">04</span>
                        <h4 class="mt-3">Get Your Work Done</h4>
                        <p>We complete your task and deliver it digitally or physically.</p>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- SERVICES -->
    <section class="section-dark bg-dark text-light">
        <div class="container">
            <div class="text-center mb-5">
                <div class="subtitle wow fadeInUp">
                    What We Do
                </div>
                <h2 class="wow fadeInUp">
                    Everyday Cyber & Digital Services
                </h2>
            </div>

            <div class="row g-4">
                @php
                    $services = [
                        ['cyber-security', 'Secure Internet Browsing'],
                        ['encryption', 'Printing, Scanning & Photocopy'],
                        ['fingerprint', 'Online Applications & ID Services'],
                        ['monitoring', 'Typing & Document Formatting'],
                        ['cloud-storage', 'Email, Uploads & Downloads'],
                        ['settings', 'General Cyber Assistance']
                    ];
                @endphp

                @foreach($services as $service)
                <div class="col-lg-4 col-md-6">
                    <div class="bg-dark-gradient rounded-1 p-40 h-100">
                        <img 
                            src="{{ asset("templates/marketing_site/images/icons-white/$service[0].png") }}"
                            class="w-90px mb-3 bg-color p-3 rounded-1"
                            alt="{{ $service[1] }}"
                        >
                        <h4>{{ $service[1] }}</h4>
                        <p>
                            Fast, reliable service with on-site assistance to help you complete your tasks with ease.
                        </p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <section class="section-dark bg-dark-2">
        <div class="container">
            <div class="row g-4 justify-content-center mb-2">
                <div class="col-lg-12 text-light">
                    <div class="text-center">
                        <div class="subtitle wow fadeInUp">
                            Why Choose Us
                        </div> 
                        <h2 class="wow fadeInUp" data-wow-delay=".2s">
                            A Cyber You Can Rely On
                        </h2>
                        <p class="lead wow fadeInUp" data-wow-delay=".4s">
                            We focus on speed, reliability, and practical support.
                            Whether you’re printing documents, applying online, or browsing,
                            we make sure your work gets done smoothly and without stress.
                        </p>
                    </div>
                </div>                                       
            </div>

            <div class="row g-4">

                <!-- Item 01 -->
                <div class="col-lg-6 wow fadeInRight" data-wow-delay=".0s">
                    <a href="#" class="hover overflow-hidden rounded-1 bg-dark text-light d-block">
                        <div class="row g-0 align-items-center">
                            <div class="col-sm-6">
                                <div class="p-40">
                                    <h3>Fast & Stable Internet</h3>
                                    <p class="mb-0">
                                        Enjoy smooth browsing, quick downloads, and uninterrupted online applications
                                        without delays or system hangs.
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="relative overflow-hidden">
                                    <h3 class="abs text-white fs-32 lh-1 p-4 top-0 start-0 z-3">01</h3>

                                    <img src="{{ asset('templates/marketing_site/images/misc/geralt-data.webp') }}"
                                        class="w-100 hover-scale-1-2"
                                        alt="Fast Internet">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Item 02 -->
                <div class="col-lg-6 wow fadeInRight" data-wow-delay=".2s">
                    <a href="#" class="hover overflow-hidden rounded-1 bg-dark text-light d-block">
                        <div class="row g-0 align-items-center">
                            <div class="col-sm-6">
                                <div class="p-40">
                                    <h3>Assisted Digital Services</h3>
                                    <p class="mb-0">
                                        Not sure where to click or what to upload?
                                        We guide you through forms, applications, and documents step by step.
                                    </p>
                                </div>
                            </div>

                            <div class="col-sm-6">
                                <div class="relative overflow-hidden">
                                    <h3 class="abs text-white fs-32 lh-1 p-4 top-0 start-0 z-3">02</h3>

                                    <img src="{{ asset('templates/marketing_site/images/misc/aristal-ai.webp') }}"
                                        class="w-100 hover-scale-1-2"
                                        alt="Assisted Services">
                                </div>
                            </div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </section>

    <section class="py-90">
        <div class="container">

            <div class="row g-4">

                <!-- ITEM 1 -->
                <div class="col-md-3 col-sm-6">
                    <div class="de_count text-center wow fadeInRight" data-wow-delay=".0s">

                        <i class="p-3 circle bg-color text-light fs-34 d-inline-block mb-3 icofont-briefcase-2"></i>

                        <h3 class="fs-48 fw-bold mb-1 lh-1">
                            <span class="timer" data-to="800" data-speed="3000">0</span>+
                        </h3>

                        <p class="mb-0 opacity-75">
                            Documents Processed
                        </p>

                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="col-md-3 col-sm-6">
                    <div class="de_count text-center wow fadeInRight" data-wow-delay=".2s">

                        <i class="p-3 circle bg-color text-light fs-34 d-inline-block mb-3 icofont-thumbs-up"></i>

                        <h3 class="fs-48 fw-bold mb-1 lh-1">
                            <span class="timer" data-to="200" data-speed="3000">0</span>+
                        </h3>

                        <p class="mb-0 opacity-75">
                            Customers Assisted
                        </p>

                    </div>
                </div>

                <!-- ITEM 3 -->
                <div class="col-md-3 col-sm-6">
                    <div class="de_count text-center wow fadeInRight" data-wow-delay=".4s">

                        <i class="p-3 circle bg-color text-light fs-34 d-inline-block mb-3 icofont-users-alt-3"></i>

                        <h3 class="fs-48 fw-bold mb-1 lh-1">
                            <span class="timer" data-to="50" data-speed="3000">0</span>+
                        </h3>

                        <p class="mb-0 opacity-75">
                            Cyber & IT Solutions
                        </p>

                    </div>
                </div>

                <!-- ITEM 4 -->
                <div class="col-md-3 col-sm-6">
                    <div class="de_count text-center wow fadeInRight" data-wow-delay=".6s">

                        <i class="p-3 circle bg-color text-light fs-34 d-inline-block mb-3 icofont-badge"></i>

                        <h3 class="fs-48 fw-bold mb-1 lh-1">
                            <span class="timer" data-to="3" data-speed="3000">0</span>+
                        </h3>

                        <p class="mb-0 opacity-75">
                            Months Serving the Community
                        </p>

                    </div>
                </div>

            </div>

        </div>
    </section>

    <section class="bg-light pt-50 pb-0 overflow-hidden">
        <div class="container">
            <div class="row g-4 gx-5 align-items-center">
                
                <!-- CONTENT -->
                <div class="col-lg-6">
                    <div class="subtitle s2 mb-3 wow fadeInUp" data-wow-delay=".0s">
                        More Than a Cyber Café
                    </div>

                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Everyday Cyber Services + Advanced Digital Solutions
                    </h2>

                    <p class="col-lg-10 wow fadeInUp" data-wow-delay=".4s">
                        At AlgoSpace Cyber, we handle everyday digital tasks like printing, typing, scanning,
                        and online applications — while also offering advanced IT services including software
                        installation, system setup, website development, and mobile app solutions.
                    </p>

                    <p class="col-lg-10 wow fadeInUp" data-wow-delay=".5s">
                        Whether you're a student, job seeker, or business owner, we help you complete tasks faster,
                        solve technical problems, and build digital solutions that support your growth.
                    </p>

                    <a class="btn-main mb10 mb-3 wow fadeInUp" data-wow-delay=".6s" href="contact.php.html">
                        <span>Talk to Us</span>
                    </a>

                    <div class="border-bottom my-3"></div>

                    <!-- GOOGLE RATING (KEPT EXACT IDEA, ONLY TWEAKED WORDING LIGHTLY) -->
                    <div class="d-lg-flex align-items-center wow fadeInUp" data-wow-delay=".7s">
                        <div class="me-3">Google Rating</div>

                        <div class="de-flex justify-content-start align-items-center">
                            <div class="me-3">5.0</div>

                            <div class="d-flex fs-14 d-rating me-3">
                                <i class="fa fa-solid fa-star m-1"></i>
                                <i class="fa fa-solid fa-star m-1"></i>
                                <i class="fa fa-solid fa-star m-1"></i>
                                <i class="fa fa-solid fa-star m-1"></i>
                                <i class="fa fa-solid fa-star m-1"></i>
                            </div>
                        </div>

                        <div class="me-3">Based on Reviews</div>
                    </div>

                    <div class="spacer-double"></div>
                </div>

                <!-- IMAGE (UNCHANGED) -->
                <div class="col-lg-6">
                    <div class="relative">
                        <img src="{{ asset('templates/marketing_site/images/misc/hood.webp') }}"
                            class="w-100 wow fadeInUp"
                            data-wow-delay=".3s"
                            alt="AlgoSpace Cyber Services">
                    </div>                        
                </div>

            </div>
        </div>
    </section>

    <section class="section-dark bg-dark text-light">
        <div class="container">
            
            <div class="row g-4 justify-content-center mb-2">
                <div class="col-lg-12">
                    <div class="text-center">
                        <div class="subtitle wow fadeInUp">
                            Real Work We Handle
                        </div> 
                        <h2 class="wow fadeInUp" data-wow-delay=".2s">
                            Practical Digital Solutions That We Deliver Daily
                        </h2>
                        <p class="lead wow fadeInUp" data-wow-delay=".4s">
                            At AlgoSpace Cyber, we don’t just talk about systems — we help real people complete real tasks.
                            From school applications and business setups to website creation and software installation,
                            we turn everyday problems into working solutions.
                        </p>
                    </div>
                </div>                                       
            </div>

            <div class="row g-4">

                <!-- ITEM 1 -->
                <div class="col-lg-4 col-sm-6">
                    <div class="hover rounded-1 overflow-hidden relative text-light text-center wow fadeInRight" data-wow-delay=".0s">
                        <img src="{{ asset('templates/marketing_site/images/projects/branded-shirt.webp') }}" class="hover-scale-1-1 w-100" alt="">
                        
                        <div class="abs w-100 px-4 hover-op-1 z-4 hover-mt-40 abs-centered">
                            <div class="mb-3">
                                Helping individuals, startups, and small businesses build a strong digital identity — from professional email setup and online profiles to business branding essentials, document presentation, and digital presence setup that makes you look credible both online and offline.
                            </div>
                            <a class="btn-line" href="#">Learn More</a>
                        </div>

                        <div class="abs bg-color z-2 top-0 w-100 h-100 hover-op-1"></div>

                        <div class="abs z-2 bottom-0 mb-3 w-100 text-center hover-op-0">
                            <h4 class="fs-20 mb-3">Digital Branding & Identity Setup Services</h4>
                        </div>

                        <div class="gradient-edge-bottom abs w-100 h-60 bottom-0"></div>
                    </div>
                </div>

                <!-- ITEM 2 -->
                <div class="col-lg-4 col-sm-6">
                    <div class="hover rounded-1 overflow-hidden relative text-light text-center wow fadeInRight" data-wow-delay=".3s">
                        <img src="{{ asset('templates/marketing_site/images/projects/github.webp') }}" class="hover-scale-1-1 w-100" alt="">
                        
                        <div class="abs w-100 px-4 hover-op-1 z-4 hover-mt-40 abs-centered">
                            <div class="mb-3">
                                We design, fix, and deploy websites for businesses — from simple landing pages to full business websites and online stores.
                            </div>
                            <a class="btn-line" href="#">Learn More</a>
                        </div>

                        <div class="abs bg-color z-2 top-0 w-100 h-100 hover-op-1"></div>

                        <div class="abs z-2 bottom-0 mb-3 w-100 text-center hover-op-0">
                            <h4 class="fs-20 mb-3">Website Design & Business Setup</h4>
                        </div>

                        <div class="gradient-edge-bottom abs w-100 h-60 bottom-0"></div>
                    </div>
                </div>

                <!-- ITEM 3 -->
                <div class="col-lg-4 col-sm-6">
                    <div class="hover rounded-1 overflow-hidden relative text-light text-center wow fadeInRight" data-wow-delay=".6s">
                        <img src="{{ asset('templates/marketing_site/images/projects/learn.webp') }}" class="hover-scale-1-1 w-100" alt="">
                        
                        <div class="abs w-100 px-4 hover-op-1 z-4 hover-mt-40 abs-centered">
                            <div class="mb-3">
                                We support businesses and individuals with computer setup, software installation, troubleshooting, and everyday IT problem solving.
                            </div>
                            <a class="btn-line" href="#">Learn More</a>
                        </div>

                        <div class="abs bg-color z-2 top-0 w-100 h-100 hover-op-1"></div>

                        <div class="abs z-2 bottom-0 mb-3 w-100 text-center hover-op-0">
                            <h4 class="fs-20 mb-3">Computer & Software Support</h4>
                        </div>

                        <div class="gradient-edge-bottom abs w-100 h-60 bottom-0"></div>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <section>
        <div class="container">
            <div class="row g-4 mb-2">
                <div class="col-lg-12 text-center">
                    <div class="subtitle wow fadeInUp mb-3">
                        What Our Clients Say
                    </div>
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Real Feedback From Our Cyber Customers
                    </h2>
                    <p class="lead wow fadeInUp" data-wow-delay=".4s">
                        From students and job seekers to small business owners, we help people handle
                        everyday digital tasks, online applications, printing, and even website projects.
                    </p>
                </div>
            </div>
        </div>

        <div class="container-fluid">
            <div class="row">
                <div class="owl-carousel owl-theme wow fadeInUp four-cols-center-dots">

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/silas.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">S.C Ndiema</div>
                                        <small>Mentor</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “The services here are excellent, and the knowledge and expertise go beyond the required standards.
                                 I truly enjoy the experience every time I visit.”
                            </p>
                        </div>
                    </div>

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/no-profile.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Suhaima A.</div>
                                        <small>Student</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “AlgoSpace Cyber developed a very excellent and user-friendly system for my school project. The system is reliable and truly helpful. Even from Mombasa, I found it easy to use and effective regardless of location”
                            </p>
                        </div>
                    </div>                    

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/jakes.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Jakes O.</div>
                                        <small>Entrepreneur</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “The staff was fast, friendly, and very helpful with my printing/scanning needs.
                                 The computers are fast, and the environment is clean. Highly recommend!”
                            </p>
                        </div>
                    </div>

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/derrick.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Derrick S.</div>
                                        <small>Client</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “The best service I have ever received while paying with paybill”
                            </p>
                        </div>
                    </div>                    

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/himram.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Himram N.</div>
                                        <small>First Client</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “I’ve seen objectives being achieved here.
                                 There’s a strong sense of self-dependence in operations.
                                  It reflects a place that learns, adjusts, and makes improvements where needed”
                            </p>
                        </div>
                    </div>

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/jacinta.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Jacinta M.</div>
                                        <small>Waitress</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “The services offered are excellent, with a great customer rapport.
                                 Fast service and internet make the experience even better”
                            </p>
                        </div>
                    </div>

                    <div class="item">
                        <div class="bg-light rounded-1 p-30">
                            <div class="d-flex justify-content-between mb-3">
                                <div class="d-flex align-items-center">
                                    <img class="w-40px circle me-3" src="{{ asset('templates/marketing_site/images/testimonial/riziki.webp') }}">
                                    <div class="mt-2">
                                        <div class="text-dark fw-bold lh-1">Riziki H.</div>
                                        <small>April Properties</small>
                                    </div>
                                </div>
                                <img src="{{ asset('templates/marketing_site/images/misc/google-icon.svg') }}" class="w-30px">
                            </div>  

                            <div class="de-rating-ext mb-2">
                                <span class="d-stars">
                                    <i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i><i class="fa fa-star"></i>
                                </span>
                                <span class="ms-2 text-white">5.0</span>
                            </div>

                            <p>
                                “The system has been very helpful in our office—fast, user-friendly, and reliable for our daily operations.
                                 We’ve found AlgoSpace Cyber very reliable in service delivery.”
                            </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section> 
    
    <section aria-label="section" class="section-dark p-0">
        <div class="container-fluid">
            <div class="row">
                <div class="col-lg-12">

                    <a class="d-block hover popup-youtube" href="https://www.youtube.com/watch?v=fQJ-uWAXaZ8">

                        <div class="relative overflow-hidden">

                            <!-- CENTER PLAY BUTTON -->
                            <div class="absolute start-0 w-100 abs-middle fs-36 text-white text-center z-2">
                                <div class="player circle wow scaleIn">
                                    <span></span>
                                </div>
                            </div> 

                            <div class="absolute w-100 h-100 top-0 bg-dark hover-op-05"></div>

                            <!-- IMAGE -->
                            <img src="{{ asset('templates/marketing_site/images/background/developer.webp') }}"
                                class="w-100 hover-scale-1-1"
                                alt="AlgoSpace Cyber Services">

                            <!-- OVERLAY TEXT (OPTIONAL IMPROVEMENT) -->
                            <div class="absolute w-100 text-center text-light z-3" style="bottom: 20%;">
                                <h3 class="mb-1">See How We Help You Get Things Done</h3>
                                <p class="mb-0 opacity-75">
                                    Printing • Online Applications • Website Setup • Computer Support
                                </p>
                            </div>

                        </div>

                    </a>

                </div>
            </div>
        </div>
    </section> 
    
    <!-- <section>
    <div class="container">
        <div class="row g-4 mb-2">
            <div class="col-lg-12 text-center">
                <div class="subtitle wow fadeInUp mb-3">
                    Tips & Updates
                </div>
                <h2 class="wow fadeInUp" data-wow-delay=".2s">
                    Simple Digital Guides for Everyday Use
                </h2>
                <p class="lead wow fadeInUp" data-wow-delay=".4s">
                    Practical tips on printing, online applications, CV writing, computer basics,
                    and small business digital setup — explained in a simple way.
                </p>
            </div>
        </div>

        <div class="row g-4">

            <div class="col-lg-4">
                <a href="#" class="d-block hover relative rounded-20 overflow-hidden text-light">

                    <img src="{{ asset('templates/marketing_site/images/news/s1.webp') }}" class="w-100 hover-scale-1-1" alt="">

                    <div class="absolute start-0 bottom-0 p-40 z-2">
                        <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-3">
                            Cyber Tips
                        </div>

                        <h4>
                            How to Print and Format Documents Correctly at a Cyber Café
                        </h4>

                        <div class="relative">
                            <img src="{{ asset('templates/marketing_site/images/testimonial/1.webp') }}" class="w-20px me-2 circle" alt="">
                            <div class="d-inline fs-14 me-3">AlgoSpace Team</div>
                            <div class="d-inline fs-14">
                                <i class="icofont-ui-calendar id-color me-2"></i>Updated 2026
                            </div>
                        </div>
                    </div>

                    <div class="gradient-edge-bottom h-70"></div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="#" class="d-block hover relative rounded-20 overflow-hidden text-light">

                    <img src="{{ asset('templates/marketing_site/images/news/s2.webp') }}" class="w-100 hover-scale-1-1" alt="">

                    <div class="absolute start-0 bottom-0 p-40 z-2">
                        <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-3">
                            Digital Help
                        </div>

                        <h4>
                            Step-by-Step Guide to KUCCPS, KRA & HELB Applications
                        </h4>

                        <div class="relative">
                            <img src="{{ asset('templates/marketing_site/images/testimonial/2.webp') }}" class="w-20px me-2 circle" alt="">
                            <div class="d-inline fs-14 me-3">Support Desk</div>
                            <div class="d-inline fs-14">
                                <i class="icofont-ui-calendar id-color me-2"></i>2026 Guide
                            </div>
                        </div>
                    </div>

                    <div class="gradient-edge-bottom h-70"></div>
                </a>
            </div>

            <div class="col-lg-4">
                <a href="#" class="d-block hover relative rounded-20 overflow-hidden text-light">

                    <img src="{{ asset('templates/marketing_site/images/news/s3.webp') }}" class="w-100 hover-scale-1-1" alt="">

                    <div class="absolute start-0 bottom-0 p-40 z-2">
                        <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-3">
                            Business Setup
                        </div>

                        <h4>
                            How Small Businesses Can Start With a Simple Website
                        </h4>

                        <div class="relative">
                            <img src="{{ asset('templates/marketing_site/images/testimonial/3.webp') }}" class="w-20px me-2 circle" alt="">
                            <div class="d-inline fs-14 me-3">Dev Team</div>
                            <div class="d-inline fs-14">
                                <i class="icofont-ui-calendar id-color me-2"></i>2026
                            </div>
                        </div>
                    </div>

                    <div class="gradient-edge-bottom h-70"></div>
                </a>
            </div>

        </div>
    </div>
    </section>    -->

    <section class="section-dark bg-color text-light pt-60 pb-50 relative overflow-hidden">
        
        <div class="w-30 abs abs-middle end-0 me-5 op-1">

            <img src="{{ asset('templates/marketing_site/images/logo-big-white.webp') }}"
                class="w-100 wow scaleIn"
                alt="AlgoSpace Cyber">

        </div>

        <div class="container">
            <div class="row g-4 align-items-center">

                <div class="col-md-10">
                    <h3 class="mb-0 fs-32 wow fadeInRight">
                        Need Help With Computer Skills, Applications, or Digital Tasks?
                    </h3>

                    <p class="mb-0 opacity-75 wow fadeInRight" data-wow-delay=".2s">
                        We don’t just serve you — we also guide you. From CV writing, KUCCPS & KRA applications,
                        to basic computer training and website setup, we help you understand and get it done.
                    </p>
                </div>

                <div class="col-md-2 d-flex flex-column gap-2">

                    <a class="btn-main fx-slide btn-line wow fadeInLeft" href="appointment.php.html">
                        <span>Get Help Now</span>
                    </a>

                    <a class="btn-main fx-slide wow fadeInLeft" data-wow-delay=".2s" href="/training">
                        <span>Join Training</span>
                    </a>

                </div>

            </div>
        </div>

    </section>  
    </div>

    <!-- footer begin -->
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
    <!-- footer end -->      

</div>

<!-- =========================
     JS FILES (STRICT ORDER)
========================= -->


<script src="{{ asset('templates/marketing_site/js/plugins.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/designesia.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/custom-marquee.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/swiper.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/custom-swiper-1.js') }}"></script>
</body>
</html>