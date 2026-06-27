@extends('layouts.branch')

@section('page-title', 'About Us')
@section('content')

<!-- Main Content Begin -->
<section data-bgimage="url({{ asset('templates/marketing_site/images/background/6.webp') }}) top">
    <div class="container">
        <div class="row g-4 gx-5 align-items-center">

            <div class="col-lg-6">
                <div class="ms-lg-4">
                    <div class="subtitle s2 mb-3 wow fadeInUp">About Us</div>

                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Your Trusted Cyber Café & Digital Services Hub
                    </h2>

                    <p class="wow fadeInUp" data-wow-delay=".4s">
                        We provide fast, reliable, and affordable cyber services including printing, scanning,
                        photocopying, internet access, CV writing, online applications, and general computer services.
                        Our goal is to help students, job seekers, and businesses complete their digital tasks quickly
                        and efficiently in a supportive environment.
                    </p>

                    <a class="btn-main fx-slide mb10 mb-3 wow fadeInUp" data-wow-delay=".6s"
                       href="{{ url('/contact') }}">
                        <span>Visit or Contact Us</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="relative">
                    <div class="p-4 mt-3 bg-white text-dark abs abs-centered rounded-1 text-center z-2 wow fadeIn">
                        <h1 class="fs-72 mb-1">{{ $yearsLabel }}</h1>
                        <div class="fs-16 lh-1-5">Years of Service</div>
                    </div>

                    <div class="row g-4">
                        <div class="col-lg-6 wow fadeInRight">
                            <div class="relative rounded-1 overflow-hidden">
                                <img src="{{ asset('templates/marketing_site/images/misc/p3.webp') }}"
                                     class="w-100"
                                     alt="Cyber Café Workspace">
                            </div>
                        </div>

                        <div class="col-lg-6 wow fadeInRight" data-wow-delay=".3s">
                            <div class="relative rounded-1 overflow-hidden">
                                <img src="{{ asset('templates/marketing_site/images/misc/p4.webp') }}"
                                     class="w-100"
                                     alt="Printing Services Area">
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
        <span class="fs-32 fw-500">Printing Services</span>
        <span class="mx-3 fs-32 op-3">/</span>

        <span class="fs-32 fw-500">Scanning & Photocopy</span>
        <span class="mx-3 fs-32 op-3">/</span>

        <span class="fs-32 fw-500">Online Applications</span>
        <span class="mx-3 fs-32 op-3">/</span>

        <span class="fs-32 fw-500">CV Writing</span>
        <span class="mx-3 fs-32 op-3">/</span>

        <span class="fs-32 fw-500">Internet Access</span>
        <span class="mx-3 fs-32 op-3">/</span>

        <span class="fs-32 fw-500">Basic Computer Training</span>
        <span class="mx-3 fs-32 op-3">/</span>
    </div>
</div>

<section class="bg-light">
    <div class="container">
        <div class="row gy-4 gx-5">

            <div class="col-lg-12">
                <div class="text-center">
                    <div class="subtitle s2 mb-3 wow fadeInUp">What We Do</div>
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Complete Digital Support for Everyday Needs
                    </h2>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="relative">

                    <div class="bg-color text-light text-center rounded-1 abs w-200px p-4 m-4 bottom-0 z-3 overflow-hidden wow zoomIn">
                        <h2 class="mb-0">99%</h2>
                        <p class="lh-1-5 fs-14 mb-0">Customer satisfaction rate</p>
                    </div>

                    <div class="rounded-1 w-90 overflow-hidden wow zoomIn">
                        <img src="{{ asset('templates/marketing_site/images/misc/l4.webp') }}"
                             class="w-100 wow scaleIn"
                             alt="Cyber Café Services">
                    </div>

                    <div class="rounded-1 w-50 abs mb-min-50 end-0 bottom-0 z-2 overflow-hidden shadow-soft wow zoomIn">
                        <img src="{{ asset('templates/marketing_site/images/misc/s4.webp') }}"
                             class="w-100 wow scaleIn"
                             alt="Printing Station">
                    </div>

                </div>
            </div>

            <div class="col-lg-6">

                <div class="relative mb-4 wow fadeInUp" data-wow-delay=".2s">
                    <img src="{{ asset('templates/marketing_site/images/icons-dark/padlock.png') }}"
                         class="absolute w-100px p-3 mb-3 z-2"
                         alt="Fast Service Icon">
                    <div class="ps-100">
                        <h4>Fast Document Services</h4>
                        <p>Quick printing, scanning, and photocopy services for all your personal and business needs.</p>
                    </div>
                </div>

                <div class="relative mb-4 wow fadeInUp" data-wow-delay=".4s">
                    <img src="{{ asset('templates/marketing_site/images/icons-dark/cloud.png') }}"
                         class="absolute w-100px p-3 mb-3 z-2"
                         alt="Internet Access Icon">
                    <div class="ps-100">
                        <h4>Reliable Internet Access</h4>
                        <p>Stable browsing and online application support for government, jobs, and school services.</p>
                    </div>
                </div>

                <div class="relative wow fadeInUp" data-wow-delay=".6s">
                    <img src="{{ asset('templates/marketing_site/images/icons-dark/quality.png') }}"
                         class="absolute w-100px p-3 mb-3 z-2"
                         alt="Support Icon">
                    <div class="ps-100">
                        <h4>Customer Assistance</h4>
                        <p>We help you complete online forms, CV writing, and general digital paperwork with ease.</p>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>

<section class="section-dark text-light bg-dark-gradient no-top no-bottom overflow-hidden">
    <div class="container-fluid position-relative half-fluid">
        <div class="container">
            <div class="row">

                <div class="col-lg-6 position-lg-absolute right-half h-100">
                    <div class="triangle-bottomright-dark"></div>
                    <div class="image" data-bgimage="url({{ asset('templates/marketing_site/images/misc/s4.webp') }}) center"></div>
                </div>

                <div class="col-lg-6">
                    <div class="me-lg-3">
                        <div class="py-5 my-5 me-lg-3">

                            <div class="subtitle s2 mb-3 wow fadeInUp">Why Choose Us</div>

                            <h2 class="wow fadeInUp" data-wow-delay=".2s">
                                Affordable, Fast & Convenient Cyber Services
                            </h2>

                            <p class="wow fadeInUp" data-wow-delay=".4s">
                                We are committed to providing dependable cyber services that save you time and effort.
                                Whether you need documents processed, internet access, or CV assistance, we ensure
                                quality service at affordable local rates.
                            </p>

                            <ul class="ul-check fw-600 mb-4 wow fadeInUp" data-wow-delay=".6s">
                                <li>Fast printing and document handling</li>
                                <li>Affordable internet access</li>
                                <li>Help with online applications and forms</li>
                                <li>Friendly and reliable customer support</li>
                            </ul>

                            <a class="btn-main wow fadeInUp" data-wow-delay=".9s"
                               href="{{ url('/contact') }}">
                                Visit Us Today
                            </a>

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
                    <h2 class="wow fadeInUp" data-wow-delay=".2s">
                        Dedicated to Serving Your Digital Needs
                    </h2>
                </div>
            </div>

            <div class="col-lg-3">
                <div class="rounded-1 overflow-hidden">
                    <img src="{{ asset('templates/marketing_site/images/team/9.webp') }}"
                         class="w-100"
                         alt="Cyber Café Staff">

                    <div class="bg-light p-4 overflow-hidden text-center">
                        <h4 class="mb-0">Mark Mosobo</h4>
                        <p class="mb-2">Cyber Services Operator</p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<section class="section-dark bg-color text-light pt-60 pb-50 relative overflow-hidden">

    <div class="w-30 abs abs-middle end-0 me-5 op-1">
        <img src="{{ asset('templates/marketing_site/images/logo-big-white.webp') }}" class="w-100 wow scaleIn" alt="">
    </div>

    <div class="container">
        <div class="row g-4">
            <div class="col-md-10">
                <h3 class="mb-0 fs-32 wow fadeInRight">
                    Need Fast Printing, Internet or CV Help?
                </h3>
            </div>

            <div class="col-md-2">
                <a class="btn-main fx-slide btn-line wow fadeInLeft"
                   href="{{ url('/submit-job') }}">
                    <span>Start For Free</span>
                </a>
            </div>
        </div>
    </div>

</section>

@endsection