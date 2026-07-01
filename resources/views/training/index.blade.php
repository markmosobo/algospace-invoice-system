@extends('layouts.branch')

@section('page-title')
@php
    $tierTitles = [
        'basic'      => 'Basic – Computer Fundamentals',
        'practical'  => 'Practical – Office & Cyber Skills',
        'refresher'  => 'Refresher – Skills Update',
        'coding'     => 'Coding – Programming & Logic',
    ];
@endphp

{{ $activeTier && isset($tierTitles[$activeTier])
    ? $tierTitles[$activeTier]
    : 'All Training Courses'
}}
@endsection
@section('content')
<section>
    <div class="container">
        <div class="row g-4">

        <div class="row g-4">

        @forelse ($courses as $course)
        <div class="col-lg-4">
            <a
                href="{{ route('training-courses.show', $course->id) }}"
                class="d-block hover relative rounded-20 overflow-hidden text-light"
            >

            <img
                src="{{ asset('templates/marketing_site/images/' . $course->tier_image) }}"
                class="w-100 hover-scale-1-1"
                alt="{{ $course->name }}"
            >

                <div class="absolute start-0 bottom-0 p-40 z-2">
                    <div class="bg-color rounded-1 p-0 px-2 d-inline-block mb-3">
                        {{ ucfirst($course->tier) }} Course
                    </div>

                    <h4>{{ $course->name }}</h4>
                    @php
                        $units = $course->duration_units;

                        if ($units == 0.5) {
                            $durationLabel = '½ Saturday';
                        } elseif ($units == 1) {
                            $durationLabel = '1 Saturday';
                        } else {
                            $durationLabel = $units . ' Saturdays';
                        }
                    @endphp

                    <div class="relative fs-14">
                        💰 KES {{ number_format($course->price) }} • 📅 {{ $durationLabel }}
                    </div>


                </div>
            </a>
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

