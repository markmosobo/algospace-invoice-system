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
                          action="{{ route('cyber.requests.store') }}"
                          enctype="multipart/form-data">

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
                                <select name="service_id"
                                        id="service_select"
                                        class="form-control select2"
                                        required>
                                    <option value="" disabled selected>Select Service You Need</option>

                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}"
                                                data-price="{{ $service->price }}"
                                                data-payment="{{ $service->payment_type }}">
                                            {{ $service->name }} (KES {{ number_format($service->price) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- PRICE DISPLAY -->
                            <div class="col-lg-12">
                                <div id="price_box"
                                     class="alert alert-info"
                                     style="display:none;">
                                    <strong>Amount:</strong>
                                    <span id="service_price"></span><br>

                                    <small id="payment_note"></small>
                                </div>
                            </div>

                            <!-- MESSAGE -->
                            <div class="col-lg-12">
                                <textarea name="message"
                                          class="form-control"
                                          placeholder="Describe your request..."
                                          required></textarea>
                            </div>

                            <!-- FILE UPLOAD -->
                            <div class="col-lg-12">
                                <label class="form-label text-dark mb-1">
                                    Attach Files (optional)
                                </label>

                                <input type="file"
                                       name="files[]"
                                       class="form-control"
                                       multiple
                                       accept=".jpg,.jpeg,.png,.pdf,.doc,.docx" />
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
                            <div class="row g-3">

                                <!-- NAME + EMAIL (SAME LINE) -->
                                <div class="col-lg-6">
                                    <input type="text"
                                        name="name"
                                        class="form-control"
                                        placeholder="Full Name"
                                        required>
                                </div>

                                <div class="col-lg-6">
                                    <input type="email"
                                        name="email"
                                        class="form-control"
                                        placeholder="Email"
                                        required>
                                </div>

                                <!-- PHONE (FULL WIDTH BELOW) -->
                                <div class="col-lg-12">
                                    <label class="form-label text-dark mb-1">Phone Number</label>

                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="fab fa-whatsapp text-success"></i>
                                            +254
                                        </span>

                                        <input type="text"
                                            id="phone_local"
                                            class="form-control"
                                            placeholder="712345678"
                                            maxlength="9"
                                            required>

                                        <input type="hidden" name="phone" id="phone_full">
                                    </div>

                                    <small class="text-muted">Enter 9-digit number only (e.g. 712345678)</small>
                                </div>

                            </div>

                            <!-- SUBMIT -->
                            <div class="col-lg-12">
                                <input type="submit"
                                       id="submit_btn"
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<style>
.select2-container .select2-selection--single {
    height: 48px;
    border-radius: 6px;
    border: 1px solid #ddd;
    background: #fff !important;
}

.select2-container--default .select2-selection--single .select2-selection__rendered {
    line-height: 46px;
    color: #333;
}

.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 46px;
}
</style>

<script>
$(document).ready(function () {

    // SELECT2
    $('#service_select').select2({
        placeholder: "Search or select a service",
        width: '100%'
    });

    // PRICE LOGIC
    $('#service_select').on('change', function () {

        const option = this.selectedOptions[0];

        const price = option.dataset.price;
        const paymentType = option.dataset.payment;

        $('#service_price').text(`KES ${Number(price).toLocaleString()}`);

        $('#payment_note').text(
            paymentType === 'prepay'
                ? 'Payment is required before processing this request.'
                : 'Payment can be made after service delivery.'
        );

        $('#price_box').show();
    });

    // PHONE AUTO BUILD (+254)
    $('#phone_local').on('input', function () {

        let val = $(this).val().replace(/\D/g, '');

        if (val.length > 9) {
            val = val.substring(0, 9);
        }

        $(this).val(val);

        $('#phone_full').val('254' + val);
    });

    // AJAX SUBMIT
    $('#booking_form').on('submit', function (e) {
        e.preventDefault();

        let form = this;
        let formData = new FormData(form);
        let button = $('#submit_btn');

        button.val("Submitting...");
        button.prop("disabled", true);

        $.ajax({
            url: form.action,
            method: "POST",
            data: formData,
            processData: false,
            contentType: false,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            },
            success: function (data) {

                $('#booking_form').hide();
                $('#success_message_col').show();

                $('#success_extra').html(`
                    <p><strong>Request ID:</strong> #${data.request_id ?? 'N/A'}</p>
                    <p>We will contact you shortly via WhatsApp or Email.</p>

                    <a class="btn-line mt-3" target="_blank"
                       href="https://wa.me/254112514440">
                       Chat on WhatsApp
                    </a>
                `);
            },
            error: function () {

                button.val("Submit Request");
                button.prop("disabled", false);

                alert("Something went wrong. Please try again.");
            }
        });
    });

});
</script>

<style>
.input-group-text {
    height: 48px;
    display: flex;
    align-items: center;
    border-radius: 6px 0 0 6px;
    background: #f8f9fa;
    border: 1px solid #ddd;
    border-right: 0;
    font-size: 14px;
}

.input-group .form-control {
    height: 48px;
    border-radius: 0 6px 6px 0;
    border: 1px solid #ddd;
    box-shadow: none;
}

.input-group .form-control:focus {
    box-shadow: none;
    border-color: #aaa;
}    
</style>

@endpush