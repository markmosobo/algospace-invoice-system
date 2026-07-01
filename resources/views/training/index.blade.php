@extends('layouts.branch')

@section('page-title', 'All Courses')

@section('content')
<section>
    <div class="container">
        <div class="row g-4">

<div class="row g-4">

@forelse ($courses as $course)
    <div class="col-lg-4 wow fadeInRight" data-wow-delay=".0s">
        <div class="d-block hover relative rounded-20 overflow-hidden text-light">

            {{-- TEMP image (repeats for now) --}}
            <img
                src="{{ asset('templates/marketing_site/images/news/s1.webp') }}"
                class="w-100 hover-scale-1-1"
                alt="{{ $course->name }}"
            >

            <div class="absolute start-0 bottom-0 p-40 z-2">
                <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-3">
                    {{ ucfirst($course->tier) }} Course
                </div>

                <h4>{{ $course->name }}</h4>

                <div class="relative fs-14">
                    <span class="me-3">
                        💰 KES {{ number_format($course->price) }}
                    </span>
                    <span>
                        📅 Saturdays
                    </span>
                </div>
            </div>

            <div class="gradient-edge-bottom h-70"></div>
        </div>
    </div>
@empty
    <div class="col-12 text-center">
        <p>No courses available at the moment.</p>
    </div>
@endforelse

</div>

            <!-- pagination begin -->
@if ($courses->hasPages())
<div class="col-lg-12 pt-4 text-center">
    <div class="d-inline-block">
        {{ $courses->links('pagination::bootstrap-5') }}
    </div>
</div>
@endif
            <!-- pagination end -->

        </div>
    </div>
</section>
@endsection

