<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">

<style>
@page {
    size: A4;
    margin: 15px;
}

body {
    font-family: DejaVu Serif, serif;
    text-align: center;
    color: #333;
    margin: 0;
}

/* MAIN LAYOUT */
.outer {
    padding: 8px;
    min-height: 950px;
    box-sizing: border-box;
}

.inner {
    padding: 25px;
    min-height: 900px;
    box-sizing: border-box;
    position: relative;
    overflow: hidden;
}

/* REPEATING WATERMARK */
.watermark-pattern {
    position: absolute;
    top: 75px;
    left: 0;
    width: 100%;
    height: 820px;
    z-index: 0;
    overflow: hidden;
    pointer-events: none;
}

.watermark-row {
    width: 100%;
    height: 190px;
    text-align: center;
    white-space: nowrap;
}

.watermark-row img {
    width: 185px;
    opacity: 0.05;
    margin-left: 30px;
    margin-right: 30px;
}

/* KEEP CERTIFICATE CONTENT ABOVE WATERMARK */
.inner > :not(.watermark-pattern) {
    position: relative;
    z-index: 1;
}

/* HEADER LOGO */
.logo {
    margin-bottom: 8px;
}

.logo img {
    width: 200px;
}

/* COMPANY */
.company {
    font-size: 26px;
    font-weight: bold;
    letter-spacing: 2px;
    color: #064420;
}

.tagline {
    font-size: 13px;
    color: #666;
    margin-top: 4px;
}

/* GOLD DIVIDER */
.line {
    width: 65%;
    border-top: 1px solid #C9A227;
    margin: 20px auto;
}

/* CERTIFICATE TITLE */
h1 {
    font-size: 34px;
    letter-spacing: 3px;
    line-height: 1.2;
    color: #064420;
    margin: 20px 0 26px;
}

/* INTRODUCTION */
.subtitle {
    font-size: 16px;
    margin: 0 0 20px;
}

/* RECIPIENT NAME */
.name {
    font-size: 34px;
    font-weight: bold;
    color: #111;
    margin: 20px 0 10px;
}

.name-line {
    width: 55%;
    border-bottom: 1px solid #C9A227;
    margin: auto;
}

/* DESCRIPTION */
.description {
    font-size: 15px;
    margin: 20px 0;
}

/* COURSE NAME */
.course {
    font-size: 26px;
    font-style: italic;
    color: #064420;
    margin: 20px 0 24px;
}

/* CERTIFICATE DETAILS */
.details {
    margin-top: 25px;
    font-size: 14px;
}

.details strong {
    color: #064420;
}

/* FOOTER / SIGNATURE / SEAL / QR */
.signature-area {
    margin-top: 38px;
    page-break-inside: avoid;
}

/* SIGNATURE + QR + SEAL TABLE */
.footer-table {
    width: 100%;
    border-collapse: collapse;
    border: none;
    table-layout: fixed;
}

.footer-table tr {
    border: none;
}

.footer-table td {
    border: none;
    vertical-align: bottom;
}

.footer-signature {
    width: 40%;
    text-align: left;
    padding-left: 15px;
}

.footer-qr {
    width: 30%;
    text-align: center;
}

.footer-seal {
    width: 30%;
    text-align: right;
    padding-right: 15px;
}

/* HANDWRITTEN SIGNATURE SPACE */
.signature-space {
    height: 45px;
}

/* SIGNATURE LINE */
.signature-line {
    width: 180px;
    border-bottom: 1px solid #333;
    margin-bottom: 6px;
}

/* SEAL IMAGE */
.seal-image {
    width: 70px;
    height: 70px;
    margin-bottom: 5px;
}

/* QR SECTION */
.qr-section {
    text-align: center;
    margin-top: 0;
    page-break-inside: avoid;
}

/* QR CODE */
.qr-image {
    width: 75px;
    height: 75px;
    margin-bottom: 5px;
}

/* SMALL TEXT */
.small {
    font-size: 11px;
}

/* ISSUE DATE */
.issue {
    margin-top: 25px;
    font-size: 14px;
    text-align: center;
    page-break-inside: avoid;
}

.issue strong {
    color: #064420;
}

/* PREVENT PAGE BREAKS */
.signature-area,
.footer-table,
.qr-section,
.issue {
    page-break-inside: avoid;
}
</style>
</head>

<body>
<div class="outer">
    <div class="inner">

        <!-- REPEATING WATERMARK -->
        <div class="watermark-pattern">

            <div class="watermark-row">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
            </div>

            <div class="watermark-row">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
            </div>

            <div class="watermark-row">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
            </div>

            <div class="watermark-row">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
            </div>

            <div class="watermark-row">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
                <img src="{{ public_path('images/algospace-logo.png') }}" alt="">
            </div>

        </div>

        <!-- HEADER LOGO -->
        <div class="logo">
            <img
                src="{{ public_path('images/algospace-logo.png') }}"
                alt="AlgoSpace CyberTech"
            >
        </div>

        <!-- COMPANY NAME -->
        <div class="company">
            ALGOSPACE CYBERTECH
        </div>

        <!-- TAGLINE -->
        <div class="tagline">
            Digital & Tech Solutions
        </div>

        <!-- GOLD DIVIDER -->
        <div class="line"></div>

        <!-- CERTIFICATE TITLE -->
        <h1>
            CERTIFICATE<br>
            OF COMPLETION
        </h1>

        <!-- AWARDED TO -->
        <p class="subtitle">
            This certificate is proudly awarded to
        </p>

        <!-- STUDENT NAME -->
        <div class="name">
            {{ strtoupper($enrollment->customer->name) }}
        </div>

        <div class="name-line"></div>

        <!-- COURSE DESCRIPTION -->
        <p class="description">
            for successfully completing the professional course
        </p>

        <!-- COURSE NAME -->
        <div class="course">
            {{ $enrollment->service->name }}
        </div>

        <!-- ISSUER -->
        <p class="description">
            conducted by AlgoSpace CyberTech
        </p>

        <!-- CERTIFICATE NUMBER -->
        <div class="details">
            <strong>Certificate Number</strong>
            <br>
            {{ $certificate->certificate_no }}
        </div>

        <!-- SIGNATURE + QR CODE + OFFICIAL SEAL -->
        <div class="signature-area">
            <table class="footer-table">
                <tr>

                    <!-- SIGNATURE -->
                    <td class="footer-signature">
                        <div class="signature-space"></div>
                        <div class="signature-line"></div>

                        <span class="small">
                            <strong>
                                {{ $certificate->issued_by ?? 'Director' }}
                            </strong>
                            <br>
                            Director
                            <br>
                            AlgoSpace CyberTech
                        </span>
                    </td>

                    <!-- QR CODE -->
                    <td class="footer-qr">
                        <div class="qr-section">
                            <img
                                src="data:image/svg+xml;base64,{{ $qrCode }}"
                                class="qr-image"
                                width="75"
                                height="75"
                                alt="Certificate verification QR code"
                            >

                            <div class="small">
                                Scan to verify
                                <br>
                                certificate
                            </div>
                        </div>
                    </td>

                    <!-- OFFICIAL SEAL -->
                    <td class="footer-seal">
                        <img
                            src="{{ public_path('images/certificates/algospace-seal.png') }}"
                            class="seal-image"
                            width="75"
                            height="75"
                            alt="AlgoSpace CyberTech Official Seal"
                        >

                        <br>

                        <span class="small">
                            Official Seal
                        </span>
                    </td>

                </tr>
            </table>
        </div>

        <!-- ISSUE DATE -->
        <div class="issue">
            Issued on:
            <strong>
                {{ \Carbon\Carbon::parse($certificate->issued_date)->format('d F Y') }}
            </strong>
        </div>

    </div>
</div>
</body>
</html>