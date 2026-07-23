<template>

<Master>

<section class="section dashboard">


<div class="card">

<div class="card-body">


<h5 class="card-title">

{{course.name}}

<span>| Students</span>

</h5>


<table class="table table-bordered table-hover">


<thead>

<tr>

<th>
Student
</th>

<th>
Status
</th>

<th>
Paid
</th>

<th>
Progress
</th>

<th>
Action
</th>

</tr>

</thead>


<tbody>


<tr
v-for="e in students"
:key="e.id"
>


<td>

{{e.customer.name}}

</td>



<td>

<span
class="badge"
:class="statusClass(e.status)"
>

{{e.status}}

</span>

</td>




<td>

KES {{Number(e.amount_paid).toLocaleString()}}

</td>




<td>


<div class="progress">

<div

class="progress-bar"

:style="{
width:e.progress_percent+'%'
}"

>

{{e.progress_percent}}%

</div>

</div>


</td>




<td>


<router-link

class="btn btn-primary btn-sm"

:to="`/enrollments/${e.id}`"

>

<i class="bi bi-eye"></i>

Manage

</router-link>


</td>



</tr>



<tr v-if="students.length==0">

<td
colspan="5"
class="text-center"
>

No enrolled students

</td>

</tr>


</tbody>


</table>


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

course:{},

students:[]

}

},


methods:{


async load(){


let id=this.$route.params.id;


let course =
await axios.get(
`/api/courses/${id}`
);


this.course =
course.data.data;



let students =
await axios.get(
`/api/services/${id}/enrollments`
);


this.students =
students.data.data || [];


},



statusClass(status){


return {

active:'bg-success',

pending:'bg-warning',

completed:'bg-primary',

dropped:'bg-danger'

}[status];


}



},



mounted(){

this.load();

}


}


</script>