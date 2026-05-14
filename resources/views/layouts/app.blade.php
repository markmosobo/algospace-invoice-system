<!DOCTYPE html>
<html lang="en">

<head>
    <title>@yield('title', 'AlgoSpace Cyber')</title>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Digital Cyber Services')">

    <link rel="icon" href="/algospace-favicon.png" type="image/x-icon">

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@300;400;600;700;800&display=swap" rel="stylesheet">

    <!-- CSS -->
    <link href="{{ asset('templates/marketing_site/css/bootstrap.min.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/plugins.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/swiper.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/style.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/datepicker.css') }}" rel="stylesheet">
    <link href="{{ asset('templates/marketing_site/css/colors/scheme-1.css') }}" rel="stylesheet">

    @stack('styles')
</head>

<body>

<div id="wrapper">

    {{-- HEADER --}}
    @include('partials.header')

    {{-- PAGE CONTENT --}}
    <div id="content">
        @yield('content')
    </div>

    {{-- FOOTER --}}
    @include('partials.footer')

</div>

<!-- JS -->
<script src="{{ asset('templates/marketing_site/js/plugins.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/designesia.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/custom-marquee.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/swiper.js') }}"></script>
<script src="{{ asset('templates/marketing_site/js/custom-swiper-1.js') }}"></script>

@stack('scripts')

</body>
</html>