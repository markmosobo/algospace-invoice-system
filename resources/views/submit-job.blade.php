<!DOCTYPE html>
<html lang="en">

<head>
    <title>AlgoSpace Cyber - Digital & Tech Services</title>

    <!-- Meta -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="AlgoSpaceCyber offers professional cybersecurity services.">
    <meta name="keywords" content="cyber security, firewall, antivirus, data protection">
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

        <!-- preloader begin -->
        <div id="de-loader"></div>
        <!-- preloader end -->

        <!-- header begin -->
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

        <!-- content begin -->
        <div class="no-bottom no-top" id="content">
            <div id="top"></div>
<section id="subheader" class="section-dark bg-dark text-light relative jarallax">
    <div class="gradient-edge-top"></div>
    <img src="images/background/1.webp" class="jarallax-img" alt="About Us">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">
            <div class="spacer-double sm-hide"></div>

            <div class="col-lg-6">
                <h1 class="mb-0 wow fadeInUp" data-wow-delay=".2s">
                    About Us                </h1>

                <ul class="crumb wow fadeInUp">
                    <li><a href="index.php.html">Home</a></li>
                    <li class="active">About Us</li>
                </ul>   
            </div>

            <div class="col-lg-6 text-lg-end sm-hide">
                <h3>
                    "Prevention is cheaper than a breach"                </h3>
            </div>
        </div>
    </div>
</section>

<!-- Main Content Begin -->
<section data-bgimage="url(images/background/6.webp) top">
    <div class="container">
        <div class="row g-4 gx-5 align-items-center">
            
            <div class="col-lg-6">
                <div class="ms-lg-4">
                    <div class="subtitle s2 mb-3 wow fadeInUp" data-wow-delay=".0s">About Us</div>
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">Defending Your Digital World, 24/7</h2>

                    <p class="wow fadeInUp" data-wow-delay=".4s">
                        For over 15 years, we’ve been safeguarding organizations from evolving cyber threats. 
                        Our team of experts provides end-to-end security solutions — from proactive threat detection 
                        and vulnerability assessments to rapid incident response — ensuring your data, systems, and 
                        reputation remain fully protected in an ever-changing digital landscape.
                    </p>

                    <a class="btn-main fx-slide mb10 mb-3 wow fadeInUp" data-wow-delay=".6s" href="contact.php.html"><span>Start For Free</span></a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="relative">
                    <div class="p-4 mt-3 bg-white text-dark abs abs-centered rounded-1 text-center z-2 wow fadeIn">
                        <h1 class="fs-72 mb-1">15</h1>
                        <div class="fs-16 lh-1-5">Years of Experience</div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6 wow fadeInRight">
                            <div class="relative rounded-1 overflow-hidden">
                                <img src="images/misc/p1.webp" class="w-100" alt="Cybersecurity Operations Center">
                            </div>
                        </div>
                        <div class="col-lg-6 wow fadeInRight" data-wow-delay=".3s">
                            <div class="relative rounded-1 overflow-hidden">
                                <img src="images/misc/p2.webp" class="w-100" alt="Network Security Infrastructure">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        </div>
        
    </div>
</section>

<div class="bg-color text-light d-flex py-4 lh-1 overflow-hidden">
  <div class="de-marquee-list-2 wow fadeIn" data-wow-duration="2s">
      <span class="fs-32 fw-500">Network Security</span>
      <span class="mx-3 fs-32 op-3">/</span>
      <span class="fs-32 fw-500">Endpoint Protection</span>
      <span class="mx-3 fs-32 op-3">/</span>
      <span class="fs-32 fw-500">Threat Intelligence</span>
      <span class="mx-3 fs-32 op-3">/</span>
      <span class="fs-32 fw-500">Penetration Testing</span>
      <span class="mx-3 fs-32 op-3">/</span>
      <span class="fs-32 fw-500">Security Audits</span>
      <span class="mx-3 fs-32 op-3">/</span>
      <span class="fs-32 fw-500">Incident Response</span>
      <span class="mx-3 fs-32 op-3">/</span>
  </div>

</div>

<section class="bg-light">
    <div class="container">
        <div class="row gy-4 gx-5">
            <div class="col-lg-12">
                   <div class="text-center">
                       <div class="subtitle s2 mb-3 wow fadeInUp" data-wow-delay=".0s">Cybersecurity Experts</div>
                       <h2 class="wow fadeInUp" data-wow-delay=".2s">Comprehensive Cybersecurity Solutions for Modern Threats</h2>
                   </div>
               </div>

               <div class="col-lg-6">
                   <div class="relative">
                       <div class="bg-color text-light text-center rounded-1 abs w-200px p-4 m-4 bottom-0 z-3 overflow-hidden wow zoomIn">
                           <h2 class="mb-0">99.9%</h2>
                           <p class="lh-1-5 fs-14 mb-0">Threat detection and prevention rate</p>
                       </div>
                       <div class="rounded-1 w-90 overflow-hidden wow zoomIn">
                           <img src="images/misc/l1.webp" class="w-100 wow scaleIn" alt="Cyber Defense Dashboard">
                       </div>
                       <div class="rounded-1 w-50 abs mb-min-50 end-0 bottom-0 z-2 overflow-hidden shadow-soft wow zoomIn" data-wow-delay=".2s">
                           <img src="images/misc/s1.webp" class="w-100 wow scaleIn" data-wow-delay=".2s" alt="Security Monitoring Interface">
                       </div>
                   </div>
               </div>
            <div class="col-lg-6">
                <div class="relative mb-4 wow fadeInUp" data-wow-delay=".2s">
                    <img src="images/icons-dark/padlock.png" class="absolute w-100px p-3 mb-3 z-2" alt="Vulnerability Assessment Icon">
                    <div class="ps-100">
                        <h4>Vulnerability Assessment</h4>
                        <p>Identify weaknesses before attackers do, reduce risk exposure, and strengthen your overall security posture.</p>
                    </div>
                </div>

                <div class="relative mb-4 wow fadeInUp" data-wow-delay=".4s">
                    <img src="images/icons-dark/cloud.png" class="absolute w-100px p-3 mb-3 z-2" alt="Safety Icon">
                    <div class="ps-100">
                        <h4>Data Protection</h4>
                        <p>Safeguard sensitive data against breaches and leaks with strong encryption, access control, and secure storage.</p>
                    </div>
                </div>

                <div class="relative wow fadeInUp" data-wow-delay=".6s">
                    <img src="images/icons-dark/quality.png" class="absolute w-100px p-3 mb-3 z-2" alt="Reputation Icon">
                    <div class="ps-100">
                        <h4>Brand Reputation</h4>
                        <p>Maintain customer trust and brand integrity by preventing cyber incidents and demonstrating strong data responsibility.</p>
                    </div>
                </div>
            </div>

            
        </div>

        <div class="spacer-single sm-hide"></div>
    </div>
</section>

<section class="section-dark text-light bg-dark-gradient no-top no-bottom overflow-hidden">
    <div class="container-fluid position-relative half-fluid">
        <div class="container">
            <div class="row">
                <!-- Image -->
                <div class="col-lg-6 position-lg-absolute right-half h-100">
                    <div class="triangle-bottomright-dark"></div>
                    <div class="image" data-bgimage="url(images/misc/s4.webp) center"></div>
                </div>
                <!-- Text -->
                <div class="col-lg-6">
                    <div class="me-lg-3">
                        <div class="py-5 my-5 me-lg-3">
                            <div class="subtitle s2 mb-3 wow fadeInUp" data-wow-delay=".0s">Maximum Protection</div>
                            <h2 class="wow fadeInUp" data-wow-delay=".2s">Cybersecurity Solutions for Complete Digital Safety</h2>
                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                Safeguard your business with our comprehensive cybersecurity services. 
                                We protect your data, networks, and infrastructure using advanced threat detection, 
                                proactive defense strategies, and rapid incident response — ensuring peace of mind 
                                in an increasingly hostile digital world.
                            </p>

                            <ul class="ul-check fw-600 mb-4 wow fadeInUp" data-wow-delay=".6s">
                                <li>24/7 threat monitoring and response</li>
                                <li>Advanced protection against malware and ransomware</li>
                                <li>Customized security solutions for your business</li>
                                <li>Compliance-ready and future-proof defense strategies</li>
                            </ul>

                            <a class="btn-main wow fadeInUp" data-wow-delay=".9s" href="contact.php.html">Request a Security Assessment</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section>
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-12">
                <div class="text-center">
                    <div class="subtitle wow fadeInUp">Our Team</div> 
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">Complete Cyber Defense Against Modern Attacks</h2>
                </div>
            </div>
            
            <div class="col-lg-3">
                <div class="rounded-1 overflow-hidden">
                    <img src="images/team/1.webp" class="w-100" alt="Cybersecurity Expert">
                    <div class="bg-light p-4 overflow-hidden text-center">
                        <h4 class="mb-0">John Smith</h4>
                        <p class="mb-2">Chief Security Officer</p>
                        <div class="social-icons">
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-linkedin"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-x-twitter"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="rounded-1 overflow-hidden">
                    <img src="images/team/2.webp" class="w-100" alt="Threat Analyst">
                    <div class="bg-light p-4 overflow-hidden text-center">
                        <h4 class="mb-0">Sarah Johnson</h4>
                        <p class="mb-2">Lead Threat Analyst</p>
                        <div class="social-icons">
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-linkedin"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-x-twitter"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="rounded-1 overflow-hidden">
                    <img src="images/team/3.webp" class="w-100" alt="Cloud Security Specialist">
                    <div class="bg-light p-4 overflow-hidden text-center">
                        <h4 class="mb-0">Thomas Bennett</h4>
                        <p class="mb-2">Cloud Security Specialist</p>
                        <div class="social-icons">
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-linkedin"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-x-twitter"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="rounded-1 overflow-hidden">
                    <img src="images/team/4.webp" class="w-100" alt="Incident Response Manager">
                    <div class="bg-light p-4 overflow-hidden text-center">
                        <h4 class="mb-0">Joshua Henry</h4>
                        <p class="mb-2">Incident Response Manager</p>
                        <div class="social-icons">
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-linkedin"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-x-twitter"></i></a>
                            <a href="#"><i class="bg-white id-color bg-hover-2 text-hover-white fa-brands fa-github"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>


<section class="section-dark bg-color text-light pt-60 pb-50 relative overflow-hidden">
    
    <div class="w-30 abs abs-middle end-0 me-5 op-1">
        <img src="images/logo-big-white.webp" class="w-100 wow scaleIn" alt="">
    </div>
    <div class="container">
        <div class="row g-4">
            <div class="col-md-10">
                <h3 class="mb-0 fs-32 wow fadeInRight">Need 24/7 Protection From Cyber Attacks?</h3>
            </div>
            <div class="col-md-2">                            
                <a class="btn-main fx-slide btn-line wow fadeInLeft" href="appointment.php.html"><span>Start For Free</span></a>
            </div>
        </div>
    </div>
</section>
<!-- Main Content End -->

        </div>
        <!-- content end -->

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

    <div id="buy-now" class="show-on-scroll">
        <a class="btn-buy" href="https://themeforest.net/item/cyberguard-cyber-security-services-php-template/60316831" target="_blank">
          Buy on <img src="demo/envato.svg" alt="">
        </a>
    </div>

    <!-- Javascript Files
    ================================================== -->
    <script src="{{ asset('templates/marketing_site/js/plugins.js') }}"></script>
    <script src="{{ asset('templates/marketing_site/js/designesia.js') }}"></script>
    <script src="{{ asset('templates/marketing_site/js/custom-marquee.js') }}"></script>
    <script src="{{ asset('templates/marketing_site/js/swiper.js') }}"></script>
    <script src="{{ asset('templates/marketing_site/js/custom-swiper-1.js') }}"></script>

        

    <!-- datepicker begin -->
    <script>
        $(function () {
          $("#date").datepicker({ 
                autoclose: true, 
                todayHighlight: true
          }).datepicker('update', new Date());
        });

    </script>
    <!-- datepicker close -->

  </body>
</html>
