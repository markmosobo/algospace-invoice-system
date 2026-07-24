<template>
<Master>

<section class="section dashboard">

<div class="card">

<div class="card-body">

<h5 class="card-title">
Enrollments
<span>| Student Management</span>
</h5>

<div class="table-responsive">

<table
id="EnrollmentTable"
class="table table-hover align-middle"
>

<thead>

<tr>

<th>Student</th>

<th>Course</th>

<th>Tier</th>

<th>Status</th>

<th>Progress</th>

<th>Enrolled</th>

<th width="260">
Actions
</th>

</tr>

</thead>

<tbody>

<tr v-if="loading">

<td
colspan="7"
class="text-center py-5"
>

<div class="spinner-border text-success"></div>

</td>

</tr>

<tr
v-for="e in enrollments"
:key="e.id"
>

<td>

<strong>
{{ e.customer?.name }}
</strong>

<br>

<small class="text-muted">
{{ e.customer?.phone }}
</small>

</td>

<td>

{{ e.service?.name }}

</td>

<td>

<span class="badge bg-info">

{{ e.service?.tier || 'Basic' }}

</span>

</td>

<td>

<span
class="badge"
:class="badgeClass(e.status)"
>

{{ e.status }}

</span>

</td>

<td style="width:180px;">

<div class="progress">

<div
class="progress-bar"
role="progressbar"
:style="{
width:(e.progress_percent || 0)+'%'
}"
>

{{ e.progress_percent || 0 }}%

</div>

</div>

</td>

<td>

{{ formatDate(e.enrolled_at) }}

</td>

<td>

<div class="btn-group">

<router-link

class="btn btn-outline-primary btn-sm"

:to="{
name:'EnrollmentProfile',
params:{
id:e.id
}
}"

>

<i class="bi bi-person"></i>

Profile

</router-link>



<button

class="btn btn-outline-warning btn-sm"

@click="certificate(e)"

:disabled="e.progress_percent < 100"

>

<i class="bi bi-award"></i>

Certificate

</button>

</div>

</td>

</tr>

</tbody>

</table>

</div>

</div>

</div>

</section>

</Master>
</template>

<script>
import Master from "@/components/Master.vue";
import axios from "axios";
import Swal from "sweetalert2";
import $ from "jquery";
import "datatables.net-dt";

export default{

components:{
Master
},

data(){

return{

loading:true,

enrollments:[]

};

},

methods:{

async loadData(){

this.loading=true;

const res=await axios.get("/api/enrollments");

this.enrollments=res.data.enrollments || [];

this.loading=false;

setTimeout(()=>{

if($.fn.DataTable.isDataTable("#EnrollmentTable")){

$("#EnrollmentTable").DataTable().destroy();

}

$("#EnrollmentTable").DataTable();

},300);

},

badgeClass(status){

switch(status){

case "active":
return "bg-success";

case "completed":
return "bg-primary";

case "pending":
return "bg-warning";

default:
return "bg-secondary";

}

},

formatDate(date){

if(!date) return "-";

const d=new Date(date);

return d.toLocaleDateString();

},

certificate(enrollment){

    axios({
        url:`/api/enrollments/${enrollment.id}/certificate`,
        method:'GET',
        responseType:'blob'
    })
    .then(response=>{

        const file = new Blob(
            [response.data],
            {
                type:'application/pdf'
            }
        );


        const fileURL =
            window.URL.createObjectURL(file);


        window.open(fileURL, '_blank');

    })
    .catch(error=>{

        console.log(error);

        Swal.fire({
            icon:'error',
            title:'Certificate failed',
            text:'Unable to generate certificate'
        });

    });

}

},

mounted(){

this.loadData();

}

};
</script>

<style scoped>

.progress{
height:18px;
}

.progress-bar{
font-size:11px;
}

.btn-group .btn{
white-space:nowrap;
}

</style>