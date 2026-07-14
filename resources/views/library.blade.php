@extends('layouts.branch')

@section('page-title', 'Community Library')

@section('content')
<section>
    <div class="container">

        {{-- HERO INTRO --}}
        <div class="row mb-5 align-items-center">
            <div class="col-lg-8">
                <h2 class="mb-3">Cyber Community Library</h2>
                <p class="lead">
                    A shared community library providing access to books for learning,
                    imagination, and personal growth — both physical and digital.
                </p>
                <p>
                    Hosted within our cyber space, the library exists to support students,
                    readers, and book lovers in the community by making books more accessible
                    locally and online.
                </p>
            </div>
        </div>

        {{-- IMAGES --}}
        <div class="row g-4 gx-5 mb-5">
            <div class="col-lg-6">
                <div class="relative">
                    <img src="{{ asset('templates/marketing_site/images/misc/books.webp') }}"
                         class="w-100 rounded-1 wow scaleOut"
                         data-wow-delay=".0s"
                         alt="Community Library Space">
                </div>
            </div>
            <div class="col-lg-6">
                <div class="relative">
                    <img src="{{ asset('templates/marketing_site/images/misc/library.webp') }}"
                         class="w-100 rounded-1 wow scaleOut"
                         data-wow-delay=".2s"
                         alt="Reading and Study Area">
                </div>
            </div>
        </div>

        {{-- ABOUT --}}
        <div class="row mb-5">
            <div class="col-lg-3">
                <h3>About the Library</h3>
            </div>
            <div class="col-lg-9">
                <p>
                    The Cyber Community Library was created to encourage a reading culture
                    and make books more accessible to the surrounding community.
                    Our collection includes educational books, fiction, non-fiction,
                    reference materials, and general interest reading.
                </p>
                <p>
                    Whether you want to study, explore new ideas, enjoy a novel,
                    or access digital books online, the library offers a welcoming
                    and affordable space for readers of all ages.
                </p>
            </div>
        </div>

        {{-- CHALLENGES & SOLUTIONS --}}
        <div class="row g-5 mb-5">
            <div class="col-lg-6 wow fadeInLeft">
                <div class="p-40 bg-dark-2 text-light rounded-1 h-100">
                    <h3 class="mb-4">Challenges in Accessing Books</h3>
                    <ol class="ol-style-1">
                        <li>Limited access to libraries within the local community.</li>
                        <li>High cost of purchasing books individually.</li>
                        <li>Few shared spaces dedicated to reading and learning.</li>
                        <li>Difficulty accessing books digitally.</li>
                        <li>Lack of a community-centered reading environment.</li>
                    </ol>
                </div>
            </div>

            <div class="col-lg-6 wow fadeInRight">
                <div class="p-40 bg-color text-light rounded-1 h-100">
                    <h3 class="mb-4">What the Library Provides</h3>
                    <ol class="ol-style-1 c2">
                        <li>A growing collection of physical books available on-site.</li>
                        <li>Access to e-books for online readers.</li>
                        <li>A quiet and welcoming reading space.</li>
                        <li>Affordable and community-friendly access.</li>
                        <li>Opportunities for group reading and discussions.</li>
                    </ol>
                </div>
            </div>
        </div>

        {{-- ACCESS OPTIONS --}}
        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="p-30 bg-dark-2 text-light rounded-1 h-100">
                    <h4 class="mb-3">📚 Physical Books</h4>
                    <p>
                        Visit the library to browse and read physical books.
                        Borrowing options are available for registered members.
                    </p>
                    <a class="btn-main fx-slide btn-line wow fadeInLeft"
                    href="{{ url('/physical-books') }}">
                        <span>View Physical Books</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-30 bg-dark-2 text-light rounded-1 h-100">
                    <h4 class="mb-3">💻 E-Books</h4>
                    <p>
                        Explore our digital library and read e-books online
                        using your phone, tablet, or computer.
                    </p>
                    <a class="btn-main fx-slide btn-line wow fadeInLeft"
                    href="{{ url('/e-books') }}">
                        <span>Browse E-Books</span>
                    </a>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="p-30 bg-dark-2 text-light rounded-1 h-100">
                    <h4 class="mb-3">👥 Community Space</h4>
                    <p>
                        A shared environment for readers, learners,
                        and book enthusiasts to connect and grow together.
                    </p>
                    <a class="btn-main fx-slide btn-line wow fadeInLeft"
                    href="{{ url('/community-space') }}">
                        <span>Join the Community</span>
                    </a>
                </div>
            </div>
        </div>

        {{-- HOW IT WORKS --}}
        <div class="row mb-5">
            <div class="col-lg-3">
                <h3>How It Works</h3>
            </div>
            <div class="col-lg-9">
                <ul class="ul-style-2">
                    <li>Browse available physical or digital books.</li>
                    <li>Read on-site, borrow a book, or access e-books online.</li>
                    <li>Register as a member for extended access.</li>
                    <li>Participate in reading sessions and community activities.</li>
                </ul>
            </div>
        </div>

        {{-- CTA --}}
        <div class="row">
            <div class="col-lg-12">
                <div class="p-40 bg-dark-2 text-light rounded-1 text-center">
                    <h3 class="mb-3">A Library for Everyone</h3>
                    <p class="mb-4">
                        The Cyber Community Library is a shared resource —
                        supporting education, imagination, and lifelong learning.
                    </p>
                    <a class="btn-main fx-slide btn-line wow fadeInLeft"
                    href="{{ url('/physical-books') }}">
                        <span>Explore the Library</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection