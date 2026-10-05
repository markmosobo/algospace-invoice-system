<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Certificate Verification | AlgoSpace CyberTech
    </title>

    <style>

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: #f4f7f5;
            color: #333;
        }

        .page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 30px 15px;
        }

        .card {
            width: 100%;
            max-width: 650px;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
            overflow: hidden;
        }

        .header {
            background: #064420;
            color: white;
            text-align: center;
            padding: 30px 20px;
        }

        .logo {
            width: 75px;
            margin-bottom: 10px;
        }

        .company {
            font-size: 23px;
            font-weight: bold;
            letter-spacing: 1.5px;
        }

        .tagline {
            font-size: 12px;
            color: #d9e7df;
            margin-top: 5px;
        }

        .content {
            padding: 35px;
        }

        .verified {
            text-align: center;
            margin-bottom: 30px;
        }

        .verified-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #e7f5ec;
            color: #064420;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            font-weight: bold;
        }

        .verified h1 {
            margin: 0;
            color: #064420;
            font-size: 26px;
        }

        .verified p {
            margin-top: 8px;
            color: #666;
            font-size: 14px;
        }

        .details {
            border-top: 1px solid #eee;
            border-bottom: 1px solid #eee;
            margin-top: 25px;
        }

        .detail {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            padding: 15px 0;
            border-bottom: 1px solid #eee;
        }

        .detail:last-child {
            border-bottom: none;
        }

        .label {
            color: #777;
            font-size: 14px;
        }

        .value {
            font-weight: bold;
            color: #222;
            text-align: right;
        }

        .certificate-number {
            color: #064420;
            letter-spacing: 1px;
        }

        .footer {
            text-align: center;
            padding: 20px;
            background: #fafafa;
            color: #777;
            font-size: 12px;
        }

        .invalid {
            text-align: center;
        }

        .invalid-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 15px;
            border-radius: 50%;
            background: #fce8e8;
            color: #b42318;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 30px;
            font-weight: bold;
        }

        .invalid h1 {
            color: #b42318;
            margin-bottom: 10px;
        }

        .invalid p {
            color: #666;
            line-height: 1.6;
        }

        @media (max-width: 600px) {

            .content {
                padding: 25px 20px;
            }

            .detail {
                flex-direction: column;
                gap: 5px;
            }

            .value {
                text-align: left;
            }

            .company {
                font-size: 19px;
            }

        }

    </style>

</head>

<body>

<div class="page">

    <div class="card">

        {{-- HEADER --}}

        <div class="header">

            <img
                src="{{ asset('images/algospace-logo.png') }}"
                class="logo"
                alt="AlgoSpace CyberTech"
            >

            <div class="company">
                ALGOSPACE CYBERTECH
            </div>

            <div class="tagline">
                Digital & Tech Solutions
            </div>

        </div>


        {{-- VALID CERTIFICATE --}}

        @if($certificate)

            <div class="content">

                <div class="verified">

                    <div class="verified-icon">
                        ✓
                    </div>

                    <h1>
                        Certificate Verified
                    </h1>

                    <p>
                        This certificate is authentic and was issued
                        by AlgoSpace CyberTech.
                    </p>

                </div>


                <div class="details">


                    {{-- STUDENT --}}

                    <div class="detail">

                        <div class="label">
                            Student
                        </div>

                        <div class="value">
                            {{ $certificate->enrollment->customer->name }}
                        </div>

                    </div>


                    {{-- COURSE --}}

                    <div class="detail">

                        <div class="label">
                            Course Completed
                        </div>

                        <div class="value">
                            {{ $certificate->enrollment->service->name }}
                        </div>

                    </div>


                    {{-- CERTIFICATE NUMBER --}}

                    <div class="detail">

                        <div class="label">
                            Certificate Number
                        </div>

                        <div class="value certificate-number">

                            {{ $certificate->certificate_no }}

                        </div>

                    </div>


                    {{-- GRADE --}}

                    <div class="detail">

                        <div class="label">
                            Result
                        </div>

                        <div class="value">

                            {{ $certificate->grade }}

                            @if($certificate->percentage !== null)
                                ({{ $certificate->percentage }}%)
                            @endif

                        </div>

                    </div>


                    {{-- ISSUE DATE --}}

                    <div class="detail">

                        <div class="label">
                            Date Issued
                        </div>

                        <div class="value">

                            {{ \Carbon\Carbon::parse(
                                $certificate->issued_date
                            )->format('d F Y') }}

                        </div>

                    </div>


                    {{-- ISSUED BY --}}

                    <div class="detail">

                        <div class="label">
                            Issued By
                        </div>

                        <div class="value">

                            {{ $certificate->issued_by ?? 'AlgoSpace CyberTech' }}

                        </div>

                    </div>

                </div>

            </div>


        {{-- INVALID CERTIFICATE --}}

        @else

            <div class="content">

                <div class="invalid">

                    <div class="invalid-icon">
                        ×
                    </div>

                    <h1>
                        Certificate Not Found
                    </h1>

                    <p>
                        We could not find a certificate matching
                        the verification number provided.
                    </p>

                    <p>
                        Please check the certificate number and try again.
                    </p>

                </div>

            </div>

        @endif


        {{-- FOOTER --}}

        <div class="footer">

            &copy; {{ date('Y') }}
            AlgoSpace CyberTech.
            All rights reserved.

        </div>

    </div>

</div>

</body>

</html>