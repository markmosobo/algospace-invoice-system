@extends('layouts.branch')

@section('page-title', $category)

@section('content')
<!-- Main Content Begin -->
@php
$categoryIcons = [

    // Document-heavy services
    'Printing & Copying' => 'cyber-security.png',
    'Typing & Documents' => 'encryption.png',

    // Online / government / forms
    'Online Applications' => 'fingerprint.png',

    // Usage / public access / browsing
    'Internet & Computer Use' => 'monitoring.png',

    // Data / storage / recovery tasks
    'Backup & Recovery' => 'cloud-storage.png',

    // System / admin / technical setup
    'Security Configuration' => 'settings.png',
];

$icon = $categoryIcons[$category] ?? 'cyber-security.png';
@endphp

<section data-bgimage="url({{ asset('templates/marketing_site/images/background/6.webp') }}) top">
    <div class="container">

        <div class="row g-4">

            @forelse($services as $service)

                <div class="col-lg-4 col-md-6 wow fadeInRight">
                    <div class="d-block relative bg-dark-gradient text-light rounded-1 p-40 pb-20 overflow-hidden">

                        <div class="relative z-2 wow scaleIn">

                            {{-- ICON --}}
                            <img src="{{ asset('templates/marketing_site/images/icons-white/' . $icon) }}"
                                class="w-90px mb-3 bg-color p-3 rounded-1"
                                alt="{{ $category }}">

                            {{-- NAME --}}
                            <h4>{{ $service->name }}</h4>

                            {{-- PRICE --}}
                            <div class="mb-2">
                                <span class="badge bg-dark text-light">
                                    KES {{ number_format($service->price) }} / {{ $service->unit }}
                                </span>
                            </div>

                            {{-- DESCRIPTION --}}
                            <p>
                                Professional {{ strtolower($category) }} service delivered fast and reliably.
                            </p>

                            {{-- CTA --}}
                            <a class="btn-main fx-slide bg-dark"
                            href="{{ url('/services/' . $service->id) }}">
                                <span>Learn More</span>
                            </a>

                        </div>

                        {{-- decorative background icon --}}
                        <img src="{{ asset('templates/marketing_site/images/icons-white/' . $icon) }}"
                             class="w-80 abs start-60 abs-middle op-1"
                             alt="">
                    </div>
                </div>

            @empty

                <div class="col-12 text-center text-light">
                    <p>No services found under <strong>{{ $category }}</strong>.</p>
                </div>

            @endforelse

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

<section class="bg-color text-light pt-60 pb-50 relative overflow-hidden">

    <div class="w-30 abs abs-middle end-0 me-5 op-1">
        <img src="{{ asset('templates/marketing_site/images/logo-big-white.webp') }}"
             class="w-100 wow scaleIn"
             alt="Cyber Services">
    </div>

    <div class="container">
        <div class="row g-4 align-items-center">

            <div class="col-md-9">
                <h3 class="mb-2 fs-32 wow fadeInRight">
                    Need Quick & Reliable Cyber Services?
                </h3>

                <p class="mb-0 wow fadeInRight">
                    Printing • Typing • Online Applications • KRA • HELB • eCitizen • Scanning & More
                </p>
            </div>

            <div class="col-md-3 text-md-end">
                <a class="btn-main fx-slide btn-line wow fadeInLeft"
                href="https://wa.me/254112514440?text=Hello%20I%20need%20cyber%20services" target="_blank" rel="noopener">
                    <span>Chat on WhatsApp</span>
                </a>
            </div>

        </div>
    </div>
</section>
<!-- Main Content End -->
@endsection