@extends('layouts.branch')

@section('page_title', 'Submit a Digital Request Online')

@section('content')
<section class="section-dark jarallax">
    <img src="{{asset('templates/marketing_site/images/background/1.webp')}}" class="jarallax-img" alt="">

    <div class="container relative z-2">
        <div class="spacer-single xs-hide"></div>

        <div class="row g-4 justify-content-center align-items-center">

            <div class="col-lg-6">
                <div class="spacer-double sm-hide"></div>

                <div class="relative">

                    <!-- SUCCESS BOX -->
                    <div id="success_message_col"
                         class="success text-light p-40 h-100"
                         style="display:none;">
                        <h3>Request Received Successfully 🎉</h3>

                        <div id="success_extra"></div>

                        <p class="mt-3">
                            We’ve received your request. Our team will respond via WhatsApp or email shortly.
                        </p>

                        <a class="btn-main mt-3" href="{{ route('submit.job') }}">
                            Submit Another Request
                        </a>
                    </div>

                    <!-- FORM -->
                    <form id="booking_form"
                          class="relative z1000 bg-light rounded-1 p-40"
                          method="POST"
                          action="{{ route('cyber.requests.store') }}">

                        @csrf

                        <div class="row g-3">

                            <div class="col-lg-12">
                                <h2 class="mb-3">
                                    <i class="fa fa-desktop id-color me-2"></i>
                                    Submit a Request
                                </h2>

                                <p>
                                    Can’t visit our cyber? No problem. Submit your task online and we’ll handle it fast and professionally.
                                </p>
                            </div>

                            <!-- SERVICE -->
                            <div class="col-lg-12">
                                <select name="service" class="form-control" required>
                                    <option value="" disabled selected>Select Service You Need</option>
                                    <option>Printing / Scanning / Photocopy</option>
                                    <option>CV Writing / Typing</option>
                                    <option>KUCCPS Application</option>
                                    <option>KRA PIN / Returns Filing</option>
                                    <option>HELB Application</option>
                                    <option>Online Job Application</option>
                                    <option>Document Formatting</option>
                                    <option>Website / App Development</option>
                                    <option>Website / Tech Support</option>
                                    <option>Other Digital Task</option>
                                </select>
                            </div>

                            <!-- MESSAGE -->
                            <div class="col-lg-12">
                                <textarea name="message"
                                          class="form-control"
                                          placeholder="Describe your request..."
                                          required></textarea>
                            </div>

                            <!-- DELIVERY -->
                            <div class="col-lg-6">
                                <select name="delivery_method" class="form-control" required>
                                    <option value="" disabled selected>Delivery Method</option>
                                    <option>WhatsApp</option>
                                    <option>Email</option>
                                    <option>Download Link</option>
                                    <option>Physical Pickup</option>
                                </select>
                            </div>

                            <!-- URGENCY -->
                            <div class="col-lg-6">
                                <select name="urgency" class="form-control">
                                    <option value="" disabled selected>Turnaround Time</option>
                                    <option>Normal (24–48 hrs)</option>
                                    <option>Urgent (Same Day)</option>
                                    <option>Express (2–4 hrs)</option>
                                </select>
                            </div>

                            <!-- CONTACT -->
                            <div class="col-lg-4">
                                <input type="text" name="name" class="form-control" placeholder="Full Name" required>
                            </div>

                            <div class="col-lg-4">
                                <input type="email" name="email" class="form-control" placeholder="Email" required>
                            </div>

                            <div class="col-lg-4">
                                <input type="text" name="phone" class="form-control" placeholder="WhatsApp Number" required>
                            </div>

                            <!-- SUBMIT -->
                            <div class="col-lg-12">
                                <input type="submit" id="submit_btn"
                                       value="Submit Request"
                                       class="btn-main w-100">
                            </div>

                        </div>
                    </form>

                </div>
            </div>

        </div>
    </div>
</section>
@endsection


@push('scripts')
<script>
document.addEventListener("DOMContentLoaded", function () {

    const form = document.getElementById('booking_form');
    const button = document.getElementById('submit_btn');
    const successBox = document.getElementById('success_message_col');
    const successExtra = document.getElementById('success_extra');

    form.addEventListener('submit', async function (e) {
        e.preventDefault();

        let formData = new FormData(form);

        button.value = "Submitting...";
        button.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: "POST",
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            const data = await response.json();

            if (!response.ok) {
                throw data;
            }

            // hide form
            form.style.display = "none";

            // show success
            successBox.style.display = "block";

            successExtra.innerHTML = `
                <p><strong>Request ID:</strong> #${data.request_id ?? 'N/A'}</p>
                <p>We will contact you shortly via WhatsApp or Email.</p>

                <a class="btn-line mt-3" target="_blank"
                   href="https://wa.me/254112514440">
                   Chat on WhatsApp
                </a>
            `;

        } catch (err) {
            console.error(err);

            button.value = "Submit Request";
            button.disabled = false;

            alert("Something went wrong. Please try again.");
        }
    });

});
</script>
@endpush