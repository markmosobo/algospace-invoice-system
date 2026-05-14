@extends('layouts.app')

@section('title', 'About Us - AlgoSpace Cyber')

@section('content')

<!-- Subheader -->
<section id="subheader" class="section-dark bg-dark text-light relative jarallax">
    <div class="container relative z-2">
        <div class="row gy-4 gx-5 align-items-center">

            <div class="col-lg-6">
                <h1>About Us</h1>

                <ul class="crumb">
                    <li><a href="/">Home</a></li>
                    <li class="active">About Us</li>
                </ul>
            </div>

            <div class="col-lg-6 text-lg-end sm-hide">
                <h3>"Prevention is cheaper than a breach"</h3>
            </div>

        </div>
    </div>
</section>

<!-- Page Content -->
<section class="py-5">
    <div class="container">
        <h2>Defending Your Digital World</h2>
        <p>
            For over 15 years, we’ve been safeguarding organizations from cyber threats...
        </p>
    </div>
</section>

@endsection