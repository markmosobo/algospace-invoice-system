@extends('layouts.branch')

@section('page-title', $service->name)

@section('content')

@php
    // Category → icon mapping (using existing assets only)
    $categoryIcons = [
        'Printing & Copying' => 'monitoring.png',
        'Typing & Documents' => 'encryption.png',
        'Online Applications' => 'fingerprint.png',
        'Internet & Computer Use' => 'monitoring.png',
        'Security Configuration' => 'settings.png',
    ];

    $icon = $categoryIcons[$service->category] ?? 'cyber-security.png';
@endphp

<section>
    <div class="container">
        <div class="row g-4 gx-5">

            {{-- LEFT SIDEBAR --}}
            <div class="col-lg-3">
                <div class="me-lg-3">

                    <p class="text-muted small mb-2">
                        {{ $service->category }}
                    </p>

                    {{-- ACTIVE SERVICE --}}
                    <a class="bg-color text-light d-block p-3 px-4 rounded-10px mb-3 relative">
                        <h5 class="mb-0">{{ $service->name }}</h5>
                        <i class="icofont-long-arrow-right absolute abs-middle fs-24 end-20px"></i>
                    </a>

                    {{-- OTHER SERVICES --}}
                    @foreach($relatedServices as $item)
                        <a href="{{ url('/services/' . $item->id) }}"
                           class="bg-light d-block p-3 px-4 rounded-10px mb-3">
                            <h6 class="mb-0">{{ $item->name }}</h6>
                        </a>
                    @endforeach

                </div>
            </div>

            {{-- MAIN CONTENT --}}
            <div class="col-lg-9">

                {{-- HERO --}}
                <div class="row g-4 gx-5 align-items-center">
                    <div class="col-lg-5">
                        <div class="bg-dark-gradient p-4 rounded-1 text-center">
                            <img src="{{ asset('templates/marketing_site/images/icons-white/' . $icon) }}"
                                 class="w-120px mb-3"
                                 alt="{{ $service->category }}">
                        </div>
                    </div>

                    <div class="col-lg-7">
                        <h2>{{ $service->name }}</h2>

                        <p class="text-muted mb-3">
                            Category: <strong>{{ $service->category }}</strong>
                        </p>

                        {{-- PRICE BLOCK --}}
                        <div class="d-flex align-items-center gap-4 mb-4">
                            <div class="bg-light px-4 py-3 rounded-1">
                                <h4 class="mb-0 text-dark">
                                    KES {{ number_format($service->price, 2) }}
                                </h4>
                                <small class="text-muted">
                                    per {{ $service->unit }}
                                </small>
                            </div>

                            <div>
                                <span class="badge bg-success">
                                    {{ ucfirst($service->payment_type) }}
                                </span>

                                @if($service->is_bundle)
                                    <span class="badge bg-info">
                                        Bundle
                                    </span>
                                @endif
                            </div>
                        </div>

                        <p>
                            This <strong>{{ strtolower($service->category) }}</strong> service
                            is delivered professionally with clear pricing and reliable turnaround,
                            suitable for both individuals and businesses.
                        </p>
                    </div>
                </div>

                <div class="spacer-double"></div>

                {{-- FEATURES --}}
                <h2>What This Service Includes</h2>

                <div class="row g-4">

                    <div class="col-lg-6">
                        <div class="relative">
                            <i class="abs fs-40 p-4 border-1-black icon_check rounded-1 text-dark"></i>
                            <div class="ps-100 ms-4">
                                <h4>Clear Service Scope</h4>
                                <p>
                                    Charged per {{ $service->unit }} with transparent pricing.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="relative">
                            <i class="abs fs-40 p-4 border-1-black icon_check rounded-1 text-dark"></i>
                            <div class="ps-100 ms-4">
                                <h4>Professional Execution</h4>
                                <p>
                                    Handled by experienced cyber operators with attention to detail.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="relative">
                            <i class="abs fs-40 p-4 border-1-black icon_check rounded-1 text-dark"></i>
                            <div class="ps-100 ms-4">
                                <h4>Fast Turnaround</h4>
                                <p>
                                    Efficient service delivery without unnecessary delays.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-6">
                        <div class="relative">
                            <i class="abs fs-40 p-4 border-1-black icon_check rounded-1 text-dark"></i>
                            <div class="ps-100 ms-4">
                                <h4>No Hidden Charges</h4>
                                <p>
                                    What you see is what you pay — no surprise costs.
                                </p>
                            </div>
                        </div>
                    </div>

                </div>

                <div class="spacer-double"></div>

                {{-- CTA --}}
                <div class="bg-light p-4 rounded-1">
                    <h4>Need This Service?</h4>
                    <p class="mb-3">
                        Visit our cyber or contact us to get this service done quickly and professionally.
                    </p>

                    <a href="{{ url('/contact') }}" class="btn-main">
                        Get Started
                    </a>
                </div>

            </div>
        </div>
    </div>
</section>

@endsection