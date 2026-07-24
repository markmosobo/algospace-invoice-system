<!DOCTYPE html>
<html>

<head>

<meta charset="utf-8">

<style>

@page {
    size: A4;
    margin:15px;
}


body {

    font-family: DejaVu Serif, serif;
    text-align:center;
    color:#333;

}


/* OUTER FRAME */

.outer {

    border:5px solid #064420;

    padding:8px;

    min-height:950px;

    box-sizing:border-box;

}



/* INNER FRAME */

.inner {

    border:2px solid #C9A227;

    padding:25px;

    min-height:900px;

    box-sizing:border-box;

    position:relative;

    overflow:hidden;

}



/* WATERMARK */

.watermark {

    position:absolute;

    width:350px;

    top:190px;

    left:120px;

    opacity:0.04;

}



/* LOGO */

.logo img {

    width:100px;

}


.logo {

    margin-bottom:5px;

}



/* COMPANY */

.company {

    font-size:26px;

    font-weight:bold;

    letter-spacing:2px;

    color:#064420;

}


.tagline {

    font-size:13px;

    color:#666;

    margin-top:3px;

}



/* GOLD LINE */

.line {

    width:80%;

    border-top:1px solid #C9A227;

    margin:12px auto;

}



/* TITLE */

h1 {

    font-size:34px;

    letter-spacing:3px;

    line-height:1.2;

    color:#064420;

    margin:10px 0;

}



/* TEXT */

.subtitle {

    font-size:16px;

}


.description {

    font-size:15px;

}



/* NAME */

.name {

    font-size:34px;

    font-weight:bold;

    color:#111;

    margin:18px 0 8px;

}



.name-line {

    width:55%;

    border-bottom:1px solid #C9A227;

    margin:auto;

}



/* COURSE */

.course {

    font-size:26px;

    font-style:italic;

    color:#064420;

    margin:12px 0;

}



/* DETAILS */

.details {

    margin-top:18px;

    font-size:14px;

}


.details strong {

    color:#064420;

}



/* VERIFICATION */

.verify {

    margin-top:10px;

    font-size:12px;

    color:#666;

}



/* ISSUE */

.issue {

    margin-top:10px;

    font-size:14px;

}



/* SIGNATURE AREA */

.signature-area {

    margin-top:25px;

}


.signature-table {

    width:100%;

}


.signature-table td {

    vertical-align:bottom;

}


.signature-image {

    width:100px;

}


.seal-image {

    width:90px;

}


.small {

    font-size:12px;

}



/* PREVENT BREAK */

.signature-area,
.signature-table {

    page-break-inside:avoid;

}

</style>


</head>


<body>


<div class="outer">


<div class="inner">


<img class="watermark"
src="{{ public_path('images/algospace-logo.png') }}">



<div class="logo">

<img src="{{ public_path('images/algospace-logo.png') }}">

</div>



<div class="company">

ALGOSPACE CYBERTECH

</div>



<div class="tagline">

Digital & Tech Solutions

</div>



<div class="line"></div>



<h1>
CERTIFICATE<br>
OF COMPLETION
</h1>





<p class="subtitle">

This certificate is proudly awarded to

</p>





<div class="name">

{{ strtoupper($enrollment->customer->name) }}

</div>



<div class="name-line"></div>





<p class="description">

for successfully completing the professional course

</p>





<div class="course">

{{ $enrollment->service->name }}

</div>





<p class="description">

conducted by AlgoSpace CyberTech

</p>





<div class="details">

<strong>

Certificate Number

</strong>

<br>

{{ $certificate->certificate_no }}

</div>





<div class="verify">

Verify this certificate at:

<br>

algospacecyber.co.ke/verify

</div>





<div class="issue">

Issued on:

<strong>

{{ \Carbon\Carbon::parse($certificate->issued_date)->format('d F Y') }}

</strong>

</div>







<div class="signature-area">


<table class="signature-table">


<tr>


<td align="left">


<img

src="{{ public_path('images/certificates/director-signature.png') }}"

class="signature-image">


<br>


____________________


<br>


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






<td align="right">


<img

src="{{ public_path('images/certificates/algospace-seal.png') }}"

class="seal-image">


<br>


<span class="small">

Official Seal

</span>


</td>



</tr>


</table>


</div>






</div>


</div>


</body>


</html>