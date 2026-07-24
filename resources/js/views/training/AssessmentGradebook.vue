<template>

<Master>

<section class="section dashboard">


<div class="card">

<div class="card-body">


<h5 class="card-title">

{{assessment.title}}

<span>
| Gradebook
</span>

</h5>



<div class="alert alert-info">

Maximum Marks:
<strong>
{{assessment.max_marks}}
</strong>

<br>

Pass Mark:
<strong>
{{assessment.pass_mark || 50}}
%</strong>

</div>





<div class="table-responsive">


<table class="table table-bordered">


<thead>

<tr>

<th>
Student
</th>

<th>
Score
</th>

<th>
Percentage
</th>

<th>
Grade
</th>

<th>
Remarks
</th>

<th>
Action
</th>

</tr>


</thead>



<tbody>


<tr
v-for="student in students"
:key="student.id"
>



<td>

{{student.customer.name}}

</td>




<td width="120">


<input

type="number"

class="form-control"

v-model="student.score"

:max="assessment.max_marks"

>

</td>




<td>

{{percentage(student)}}

%

</td>





<td>


<span

class="badge"

:class="gradeClass(student)"

>

{{grade(student)}}

</span>


</td>





<td>


<textarea

class="form-control"

rows="2"

v-model="student.remarks"

></textarea>


</td>






<td>


<button

class="btn btn-success btn-sm"

@click="save(student)"

>

<i class="bi bi-save"></i>

Save

</button>


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


const toast = Swal.mixin({

toast:true,
position:"top-end",
timer:2500,
showConfirmButton:false

});



export default{


components:{
Master
},




data(){


return{


assessment:{},


students:[]


}


},




methods:{





async load(){


let id=this.$route.params.id;



let response =
await axios.get(

`/api/course-assessments/${id}/gradebook`

);



this.assessment =
response.data.assessment;



this.students =
response.data.students;



},







percentage(student){


if(!student.score)

return 0;



return (

(student.score /
this.assessment.max_marks)

*100

).toFixed(2);



},







grade(student){


let percent =
this.percentage(student);



if(percent >=80)

return "Distinction";



if(percent >=70)

return "Credit";



if(percent >=50)

return "Pass";



return "Needs Improvement";


},







gradeClass(student){


let g=this.grade(student);



return {


"bg-success":
g==="Distinction",


"bg-primary":
g==="Credit",


"bg-warning":
g==="Pass",


"bg-danger":
g==="Needs Improvement"


}


},







async save(student){



let payload={


course_assessment_id:
this.assessment.id,


enrollment_id:
student.id,


score:
student.score,


percentage:
this.percentage(student),


grade:
this.grade(student),


remarks:
student.remarks


};





try{


if(student.assessment_id){


await axios.put(

`/api/student-assessments/${student.assessment_id}`,

payload

);


}

else{


let res =
await axios.post(

'/api/student-assessments',

payload

);



student.assessment_id =
res.data.data.id;


}





toast.fire({

icon:"success",

title:"Saved"

});



}



catch(error){


console.log(error.response);



toast.fire({

icon:"error",

title:"Failed"

});


}



}






},





mounted(){

this.load();

}



}


</script>