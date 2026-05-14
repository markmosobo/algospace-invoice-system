@extends('layouts.branch')

@section('page_title', 'Submit a Job Online')


@section('content')
<section class="section-dark jarallax">
    <img src="{{asset('templates/marketing_site/images/background/1.webp')}}" class="jarallax-img" alt="">
    <div class="container relative z-2">
        <div class="spacer-single xs-hide"></div>
        <div class="row g-4 justify-content-center align-items-center">

            <div class="col-lg-6">
                <div class="spacer-double sm-hide"></div>
                <div class="relative">
                    <div id="success_message_col" class='success text-light p-40 h-100'>
                        <h3>Thank You For Reaching Out</h3>
                        <p>We've received your request and a cybersecurity expert will contact you shortly. Click the button below to submit another request.</p>
                        <a class="btn-main" href="appointment.php.html">Submit Again</a>
                    </div>

                    <form name="bookingForm" id="booking_form" class="relative z1000 bg-light rounded-1 p-40" method="post" action="action-booking.php">

                        <div class="row g-3">
                            <div class="col-lg-12">
                                <h2 class="mb-3"><i class="fa fa-shield id-color me-2"></i> Start For Free</h2>
                                <p>Concerned about cyber threats? Book your free consultation today and get expert advice on how to protect your systems, data, and reputation.</p>
                                <div class="relative">
                                    <select name="service" id="service" class="form-control">
                                        <option disabled="" selected="" value="">Select Cybersecurity Service</option>
                                        <option value="Network Vulnerability Assessment">Network Vulnerability Assessment</option>
                                        <option value="Penetration Testing">Penetration Testing</option>
                                        <option value="Managed Security Services">Managed Security Services</option>
                                        <option value="Incident Response Planning">Incident Response Planning</option>
                                        <option value="Security Awareness Training">Security Awareness Training</option>
                                        <option value="Compliance & Risk Assessment">Compliance & Risk Assessment</option>
                                    </select>

                                    <i class="absolute top-0 end-0 id-color pt-3 pe-3 icofont-simple-down"></i>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div id="date" class="relative input-group date" data-date-format="mm-dd-yyyy">
                                    <i class="absolute top-0 end-0 id-color pt-3 pe-3 icofont-calendar"></i>
                                    <input class="form-control" value="Select Date" name="date" type="text">
                                    <span class="input-group-addon"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>

                            <div class="col-lg-6">
                                <div class="relative">
                                    <select name="time" id="time" class="form-control">
                                        <option disabled="" selected="" value="">Select Time</option>
                                        <option value="10:00">10:00</option>
                                        <option value="11:00">11:00</option>
                                        <option value="12:00">12:00</option>
                                        <option value="13:00">13:00</option>
                                        <option value="14:00">14:00</option>
                                        <option value="15:00">15:00</option>
                                        <option value="16:00">16:00</option>
                                        <option value="17:00">17:00</option>
                                        <option value="18:00">18:00</option>
                                        <option value="19:00">19:00</option>
                                        <option value="20:00">20:00</option>
                                    </select>
                                    <i class="absolute top-0 end-0 id-color pt-3 pe-3 icofont-simple-down"></i>
                                </div>
                            </div>

                            <div class="col-lg-4">
                                <input type="text" name="name" id="name" placeholder="Name" class="form-control" required="">
                            </div>

                            <div class="col-lg-4">
                                <input type="text" name="email" id="email" placeholder="Email" class="form-control" required="">
                            </div>

                            <div class="col-lg-4">
                                <input type="text" name="phone" id="phone" placeholder="Phone" class="form-control" required="">
                            </div>

                            <div class="col-lg-12">
                                <textarea name="message" id="message" class="form-control" placeholder="Tell us about your security concerns or needs..."></textarea>
                            </div>

                            <div class="col-lg-12">
                                <div id='submit'>
                                    <input type='submit' id='send_message' value='Send Appointment' class="btn-main">
                                </div>
                            </div>
                        </div>

                        <div id="error_message" class='error'>
                            Sorry there was an error sending your form.
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="sw-overlay"></div>
</section>
@endsection