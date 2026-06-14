@extends('layouts.branch')

@section('page-title', $project->title)

@section('content')
<section>
    <div class="container">

        <div class="row g-4 gx-5">

            {{-- Cover Images --}}
            <div class="col-lg-6">
                <div class="relative">

                    <img src="{{ asset('storage/' . $project->cover_image ?? 'templates/marketing_site/images/misc/l2.webp') }}"
                        class="w-100 rounded-1 wow scaleOut"
                        alt="{{ $project->title }}">

                </div>
            </div>

            <div class="col-lg-6">
                <div class="relative">

                    {{-- SWIPER (replaces static image ONLY here) --}}
                    <div class="swiper projectSwiper rounded-1 overflow-hidden">

                        <div class="swiper-wrapper">

                            {{-- media images --}}
                            @forelse($project->media as $media)
                                <div class="swiper-slide">
                                    <img src="{{ asset('storage/' . $media->file_path) }}"
                                    class="w-100"
                                    alt="{{ $project->title }}">
                                </div>
                            @empty
                                <div class="swiper-slide">
                                    <img src="{{ asset('templates/marketing_site/images/misc/l3.webp') }}"
                                        class="w-100"
                                        alt="No media">
                                </div>
                            @endforelse

                        </div>

                        <div class="swiper-pagination"></div>
                        <div class="swiper-button-prev"></div>
                        <div class="swiper-button-next"></div>

                    </div>

                </div>
            </div>

            <div class="spacer-single"></div>

            {{-- Overview --}}
            <div class="col-lg-3">
                <h3>Project Overview</h3>
            </div>

            <div class="col-lg-9">
                <p>
                    {{ $project->description }}
                </p>
            </div>

            <div class="spacer-single"></div>

        </div>

        <div class="row g-5">

            {{-- LEFT: CHALLENGES --}}
            <div class="col-lg-6 wow fadeInLeft">
                <div class="p-40 bg-dark-2 text-light rounded-1 h-100 relative">

                    <h3 class="mb-4">Project Details</h3>

                    <ol class="ol-style-1">

                        <li><strong>Type:</strong> {{ ucfirst($project->type) }}</li>

                        <li><strong>Status:</strong> {{ ucfirst($project->status) }}</li>

                        <li><strong>Stage:</strong> {{ $project->current_stage ?? 'Not set' }}</li>

                        <li><strong>Progress:</strong> {{ $project->progress }}%</li>

                        <li><strong>Start Date:</strong> {{ $project->start_date ?? 'N/A' }}</li>

                        <li><strong>Due Date:</strong> {{ $project->due_date ?? 'N/A' }}</li>

                    </ol>

                </div>
            </div>

            {{-- RIGHT: SOLUTIONS (you can later make this dynamic too) --}}
            <div class="col-lg-6 wow fadeInRight">
                <div class="p-40 bg-color text-light rounded-1 h-100 relative">

                    <h3 class="mb-4">Highlights</h3>

                    <ol class="ol-style-1 c2">

                        <li>Project executed under {{ $project->board_type }} board.</li>
                        <li>Status currently marked as {{ $project->status }}.</li>
                        <li>Progress tracking at {{ $project->progress }}% completion.</li>
                        <li>Aligned with {{ $project->type }} category objectives.</li>

                    </ol>

                </div>
            </div>

        </div>

    </div>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>

<style>
    .projectSwiper img {
        width: 100%;
        height: 420px;
        object-fit: cover;
        border-radius: 10px;
    }

    @media (max-width: 768px) {
        .projectSwiper img {
            height: 300px;
        }
    }

    .swiper-button-next,
    .swiper-button-prev {
        color: #fff;
        background: rgba(0,0,0,0.4);
        width: 38px;
        height: 38px;
        border-radius: 50%;
    }

    .swiper-button-next:after,
    .swiper-button-prev:after {
        font-size: 14px;
    }

    .swiper-pagination-bullet {
        background: #fff;
        opacity: 0.6;
    }

    .swiper-pagination-bullet-active {
        opacity: 1;
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

<script>
    new Swiper('.projectSwiper', {
        loop: true,
        spaceBetween: 10,
        slidesPerView: 1,

        pagination: {
            el: '.swiper-pagination',
            clickable: true,
        },

        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },

        autoplay: {
            delay: 3500,
            disableOnInteraction: false,
        },
    });
</script>
</section>
@endsection