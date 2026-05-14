@extends('layouts.branch')

@section('title', 'Submit a Job - AlgoSpace Cyber')

@section('content')

<!-- HERO (SMALLER THAN HOMEPAGE) -->
<section class="section-dark bg-dark text-light pt-120 pb-80">
    <div class="container text-center">

        <div class="subtitle wow fadeInUp">
            Quick & Simple
        </div>

        <h1 class="wow fadeInUp" data-wow-delay=".2s">
            Submit Your Job Request
        </h1>

        <p class="lead col-lg-7 offset-lg-2 wow fadeInUp" data-wow-delay=".4s">
            Tell us what you need done — printing, CV writing, online applications, typing, or digital support.
            We’ll respond quickly with a quote and timeline.
        </p>

    </div>
</section>

<!-- MAIN FORM SECTION -->
<section class="section-dark bg-dark-2 text-light">
    <div class="container">

        <div class="row g-5">

            <!-- FORM -->
            <div class="col-lg-7">

                <div class="bg-dark-gradient rounded-2 p-40 wow fadeInUp">

                    <h3 class="mb-4">Job Details</h3>

                    <form action="{{ url('/jobs/submit') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- NAME -->
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <!-- PHONE -->
                        <div class="mb-3">
                            <label class="form-label">Phone Number (WhatsApp Preferred)</label>
                            <input type="text" name="phone" class="form-control" required>
                        </div>

                        <!-- EMAIL -->
                        <div class="mb-3">
                            <label class="form-label">Email (Optional)</label>
                            <input type="email" name="email" class="form-control">
                        </div>

                        <!-- SERVICE TYPE -->
                        <div class="mb-3">
                            <label class="form-label">Service Needed</label>
                            <select name="service_type" class="form-control" required>
                                <option value="">Select Service</option>
                                <option>Printing / Photocopy</option>
                                <option>Typing / Formatting</option>
                                <option>CV Writing</option>
                                <option>Online Application (KUCCPS / KRA / HELB)</option>
                                <option>Website / IT Support</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <!-- DESCRIPTION -->
                        <div class="mb-3">
                            <label class="form-label">Describe Your Request</label>
                            <textarea name="description" rows="5" class="form-control" placeholder="Explain what you need done..." required></textarea>
                        </div>

                        <!-- FILE UPLOAD -->
                        <div class="mb-4">
                            <label class="form-label">Attach File (Optional)</label>
                            <input type="file" name="file" class="form-control">
                            <small class="opacity-75">PDF, Images or Documents</small>
                        </div>

                        <!-- SUBMIT -->
                        <button type="submit" class="btn-main fx-slide w-100">
                            <span>Submit Job Request</span>
                        </button>

                    </form>

                </div>

            </div>

            <!-- SIDE INFO -->
            <div class="col-lg-5">

                <!-- INFO CARD -->
                <div class="bg-dark-gradient rounded-2 p-40 mb-4 wow fadeInUp">

                    <h4>What Happens Next?</h4>

                    <ul class="mt-3">
                        <li>✔ We review your request</li>
                        <li>✔ Send you a quote via WhatsApp</li>
                        <li>✔ You confirm payment (M-Pesa)</li>
                        <li>✔ We start working immediately</li>
                    </ul>

                </div>

                <!-- QUICK CONTACT -->
                <div class="bg-dark-gradient rounded-2 p-40 wow fadeInUp" data-wow-delay=".2s">

                    <h4>Need Faster Help?</h4>

                    <p class="mb-3">
                        Skip the form and message us directly on WhatsApp.
                    </p>

                    <a href="https://wa.me/254112514440"
                       target="_blank"
                       class="btn-main fx-slide w-100 mb-2">
                        <span>Chat on WhatsApp</span>
                    </a>

                    <a href="tel:+254112514440"
                       class="btn-main btn-line fx-slide w-100">
                        <span>Call Us</span>
                    </a>

                </div>

            </div>

        </div>

    </div>
</section>

<!-- TRUST SECTION -->
<section class="section-dark bg-dark text-light">
    <div class="container text-center">

        <h2 class="wow fadeInUp">
            Trusted Digital Support
        </h2>

        <p class="lead wow fadeInUp" data-wow-delay=".2s">
            We handle everyday cyber tasks for students, job seekers, and businesses across Kenya.
        </p>

    </div>
</section>

@endsection