<template>

<Master>

<section class="section dashboard">

<div class="card shadow-sm">

<div class="card-body">


<!-- HEADER -->

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h4 class="mb-1">

{{ enrollment.customer?.name }}

</h4>

<span class="text-muted">

{{ enrollment.service?.name }}

</span>

</div>


<span
class="badge bg-success"
v-if="enrollment.status=='active'"
>

Active

</span>


</div>





<!-- SUMMARY CARDS -->

<div class="row g-3 mb-4">


<div class="col-md-3">

<div class="card border">

<div class="card-body text-center">

<small class="text-muted">
Course Fee
</small>

<h5>

{{formatMoney(enrollment.service?.price)}}

</h5>

</div>

</div>

</div>




<div class="col-md-3">

<div class="card border">

<div class="card-body text-center">

<small class="text-muted">
Paid
</small>

<h5 class="text-success">

{{formatMoney(enrollment.amount_paid)}}

</h5>

</div>

</div>

</div>




<div class="col-md-3">

<div class="card border">

<div class="card-body text-center">

<small class="text-muted">
Balance
</small>

<h5 class="text-danger">

{{formatMoney(balance)}}

</h5>

</div>

</div>

</div>





<div class="col-md-3">

<div class="card border">

<div class="card-body text-center">

<small class="text-muted">
Progress
</small>

<h5>

{{enrollment.progress_percent}}%

</h5>

</div>

</div>

</div>


</div>





<!-- INFORMATION -->


<div class="row">



<div class="col-md-6">


<div class="card border">

<div class="card-body">


<h6 class="fw-bold">
Student Information
</h6>


<table class="table table-sm">


<tr>

<td>Name</td>

<td>
{{enrollment.customer?.name}}
</td>

</tr>


<tr>

<td>Phone</td>

<td>
{{enrollment.customer?.phone || '-'}}
</td>

</tr>


<tr>

<td>Email</td>

<td>
{{enrollment.customer?.email || '-'}}
</td>

</tr>


<tr>

<td>Enrolled</td>

<td>
{{formatDate(enrollment.enrolled_at)}}
</td>

</tr>


<tr>

<td>Status</td>

<td>

{{enrollment.status}}

</td>

</tr>


</table>


</div>

</div>


</div>






<div class="col-md-6">


<div class="card border">

<div class="card-body">


<h6 class="fw-bold">
Course Information
</h6>


<table class="table table-sm">


<tr>

<td>Course</td>

<td>
{{enrollment.service?.name}}
</td>

</tr>



<tr>

<td>Schedule</td>

<td>
{{enrollment.service?.schedule_type}}
</td>

</tr>



<tr>

<td>Duration</td>

<td>

{{enrollment.service?.duration_units}}
Sessions

</td>

</tr>




<tr>

<td>Session Hours</td>

<td>

{{enrollment.service?.session_hours}}

Hours

</td>

</tr>


<tr>

<td>Start Date</td>

<td>

{{formatDate(enrollment.starts_at)}}

</td>

</tr>


</table>


</div>

</div>


</div>



</div>





<!-- PROGRESS -->


<div class="card border mb-4">

<div class="card-body">


<h6 class="fw-bold">

Assessment Progress

</h6>



<div class="progress mb-3">


<div

class="progress-bar bg-success"

:style="{
width:
enrollment.progress_percent+'%'
}"

>

{{enrollment.progress_percent}}%

</div>


</div>



<table class="table table-bordered">


<thead>

<tr>

<th>
Session
</th>

<th>
Description
</th>

<th>
Duration
</th>

<th>
Completed
</th>

<th>
Date
</th>

</tr>

</thead>



<tbody>


<tr

v-for="s in enrollment.sessions"

:key="s.id"

>


<td>

{{s.session.title}}

</td>



<td>

{{s.session.description}}

</td>



<td>

{{s.session.duration_hours}} hrs

</td>



<td>


<input

type="checkbox"

:checked="s.completed"

@click="toggle(s)"

>


</td>



<td>

{{formatDateTime(s.completed_at)}}

</td>



</tr>


</tbody>


</table>



</div>

</div>







<!-- PAYMENTS -->


<div class="card border mb-4">

<div class="card-body">


<h6 class="fw-bold">

Payment Summary

</h6>



<div class="row">


<div class="col-md-4">

Invoice

<br>

<strong>

{{enrollment.invoice?.invoice_number}}

</strong>

</div>



<div class="col-md-4">

Invoice Total

<br>

<strong>

{{formatMoney(enrollment.invoice?.total_amount)}}

</strong>

</div>



<div class="col-md-4">

Invoice Status

<br>

<strong>

{{enrollment.invoice?.status}}

</strong>

</div>


</div>




<hr>



<table class="table">


<thead>

<tr>

<th>Date</th>

<th>Method</th>

<th>Amount</th>

</tr>

</thead>


<tbody>


<tr
v-for="p in payments"
:key="p.id"
>


<td>

{{formatDate(p.payment_date)}}

</td>


<td>

{{p.method}}

</td>


<td>

{{formatMoney(p.amount)}}

</td>


</tr>


</tbody>


</table>


</div>

</div>





<!-- TIMELINE -->


<div class="card border">


<div class="card-body">


<h6 class="fw-bold">

Activity Timeline

</h6>



<ul class="list-group">


<li class="list-group-item">


Enrolled

<br>

<small>

{{formatDateTime(enrollment.enrolled_at)}}

</small>


</li>



<li class="list-group-item">


Payment Received

<br>

<small>

{{formatDateTime(enrollment.paid_at)}}

</small>


</li>


</ul>


</div>

</div>



</div>

</div>


</section>


</Master>


</template>





<script>

import Master from "@/components/Master.vue";
import axios from "axios";


export default{


components:{
Master
},



data(){

return{

enrollment:{},

payments:[]

}

},




computed:{


balance(){

let total =
Number(this.enrollment.service?.price || 0);


let paid =
Number(this.enrollment.amount_paid || 0);


return total-paid;

}


},





methods:{



async load(){


let id=this.$route.params.id;



let e =
await axios.get(
`/api/enrollments/${id}`
);



this.enrollment =
e.data.data;




let p =
await axios.get(
`/api/enrollments/${id}/payments`
);



this.payments =
p.data.data;



},





async toggle(session){


await axios.put(

`/api/enrollments/${this.enrollment.id}/progress`,

{

session_id:
session.course_session_id,


completed:
!session.completed

}

);



this.load();


},





formatDate(date){


if(!date)
return '-';


return new Date(date)
.toLocaleDateString(
'en-KE',
{

day:'2-digit',

month:'short',

year:'numeric'

}

);


},




formatDateTime(date){


if(!date)
return '-';


return new Date(date)
.toLocaleString(
'en-KE',
{

day:'2-digit',

month:'short',

year:'numeric',

hour:'2-digit',

minute:'2-digit'

}

);


},





formatMoney(amount){


return new Intl.NumberFormat(
'en-KE',
{

style:'currency',

currency:'KES'

}

).format(amount || 0);


}



},




mounted(){

this.load();

}



}


</script>