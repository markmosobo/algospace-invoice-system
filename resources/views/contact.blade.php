@extends('layouts.branch')

@section('page-title', 'Contact Us')

@section('content')
<section data-bgimage="url(images/background/6.webp) top">
    <div class="container">
        <div class="row g-4">

            <!-- INTRO -->
            <div class="col-lg-6">
                <h3 class="wow fadeInUp">Send Your Message</h3>
                <p class="mb-0">
                    Got a question, idea, or just need support? Drop us a message here.
                    Fill in your details and message below, and our team will get back to you as soon as possible.
                </p>
            </div>

            <div class="clearfix"></div>

            <!-- INFO CARD -->
            <div class="col-lg-6">

                <div class="rounded-1 bg-light overflow-hidden">
                    <div class="row g-2">

                        <div class="col-sm-6">
                            <div class="auto-height relative"
                                 data-bgimage="url({{ asset('templates/marketing_site/images/misc/s1.webp') }})">
                            </div>
                        </div>

                        <div class="col-sm-6 relative">
                            <div class="p-30">

                                <div class="fw-bold text-dark">
                                    <i class="icofont-location-pin me-2 id-color-2"></i>
                                    Office Location
                                </div>
                                Villa Nova Building, Shop 1, along Kapsokwony–Kaptama Road, Mt. Elgon

                                <div class="spacer-20"></div>

                                <div class="fw-bold text-dark">
                                    <i class="icofont-phone me-2 id-color-2"></i>
                                    Call Us Directly
                                </div>
                                +254 112 514 440

                                <div class="spacer-20"></div>

                                <div class="fw-bold text-dark">
                                    <i class="icofont-envelope me-2 id-color-2"></i>
                                    Send a Message
                                </div>
                                support@algospacecyber.co.ke

                            </div>
                        </div>

                    </div>
                </div>

            </div>

            <!-- FORM -->
            <div class="col-lg-6">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form id="contact_form" class="position-relative z1000">
                    @csrf

                    <div class="field-set">
                        <input type="text" name="name" id="name" class="form-control" placeholder="Your Name" required="">
                    </div>

                    <div class="field-set">
                        <input type="text" name="email" id="email" class="form-control" placeholder="Your Email" required="">
                    </div>

                    <div class="field-set">
                        <input type="text" name="phone" id="phone" class="form-control" placeholder="Your Phone" required="">
                    </div>

                    <div class="field-set mb20">
                        <textarea name="message" id="message" class="form-control" placeholder="Your Message" required=""></textarea>
                    </div>

                    <div id="submit" class="mt20">
                        <input type="submit" id="send_message" value="Send Message" class="btn-main">
                    </div>

                    <div id="form_alert" class="mt-3"></div>
                </form>

            </div>

            <!-- MAP SECTION (BOTTOM - HIGH CONVERSION POSITION) -->
            <div class="col-lg-12 mt-4">

                <div class="alert alert-info">
                    📍 Need directions? Use the map below to find us or open in Google Maps for navigation.
                </div>

                <div class="ratio ratio-16x9">
                    <iframe
                        src="https://www.google.com/maps?q=0.8441914,34.7166216&hl=en&z=16&output=embed"
                        style="border:0; border-radius:10px;"
                        allowfullscreen=""
                        loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade">
                    </iframe>
                </div>

                <div class="mt-3">
                    <a href="https://www.google.com/maps?q=0.8441914,34.7166216"
                       target="_blank"
                       class="btn btn-primary">
                        Open in Google Maps
                    </a>
                </div>

            </div>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    document.getElementById("contact_form").addEventListener("submit", function(e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let alertBox = document.getElementById("form_alert");

        alertBox.innerHTML = "Sending message...";

        fetch("{{ route('contact.store') }}", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                "Accept": "application/json"
            },
            body: formData
        })
        .then(async response => {
            let data = await response.json();

            if (!response.ok) throw data;

            return data;
        })
        .then(data => {
            alertBox.innerHTML = `<div class="alert alert-success">${data.message}</div>`;
            form.reset();
        })
        .catch(error => {
            let msg = "Something went wrong. Try again.";

            if (error?.errors) {
                msg = Object.values(error.errors).flat().join("<br>");
            } else if (error?.message) {
                msg = error.message;
            }

            alertBox.innerHTML = `<div class="alert alert-danger">${msg}</div>`;
        });

    });

});
</script>
@endpush