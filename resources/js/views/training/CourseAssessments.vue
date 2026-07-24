<template>

<Master>

<section class="section dashboard">


<div class="card">


<div class="card-body">


<h5 class="card-title">

{{course.name}}

<span>| Course Assessments</span>

</h5>




<div class="mb-3">


<button

class="btn btn-success btn-sm"

@click="openModal"

>

<i class="bi bi-plus"></i>

Add Assessment

</button>


</div>





<div
v-if="assessments.length===0"
class="alert alert-secondary"
>

No assessments created yet.

</div>





<div class="table-responsive">


<table class="table table-bordered table-hover">


<thead>

<tr>

<th>
Title
</th>

<th>
Type
</th>

<th>
Session
</th>

<th>
Marks
</th>

<th>
File
</th>

<th>
Status
</th>

<th>
Actions
</th>

</tr>

</thead>



<tbody>


<tr
v-for="assessment in assessments"
:key="assessment.id"
>


<td>


<strong>

{{assessment.title}}

</strong>


<br>


<small class="text-muted">

{{assessment.description}}

</small>


</td>



<td>


<span class="badge bg-primary">

{{assessment.assessment_type}}

</span>


</td>





<td>

{{assessment.session?.title || 'General'}}

</td>





<td>

{{assessment.max_marks}}

</td>





<td>


<a

v-if="assessment.attachment"

:href="'/storage/'+assessment.attachment"

target="_blank"

class="btn btn-outline-primary btn-sm"

>

<i class="bi bi-download"></i>

File

</a>


<span v-else>

-

</span>


</td>





<td>


<span

class="badge"

:class="assessment.is_active ? 'bg-success':'bg-danger'"

>

{{assessment.is_active?'Active':'Disabled'}}

</span>


</td>






<td>


<router-link


class="btn btn-primary btn-sm me-1"


:to="{

name:'AssessmentGradebook',

params:{
id:assessment.id
}

}"


>


<i class="bi bi-pencil-square"></i>

Grade


</router-link>






<button

class="btn btn-outline-success btn-sm me-1"

@click="edit(assessment)"

>

Edit

</button>







<button

class="btn btn-outline-danger btn-sm"

@click="remove(assessment)"

>

Delete

</button>



</td>



</tr>


</tbody>



</table>


</div>





</div>

</div>








<!-- MODAL -->


<div

class="modal fade"

id="assessmentModal"

tabindex="-1"

>


<div class="modal-dialog">


<div class="modal-content">



<div class="modal-header">


<h5 class="modal-title">

{{form.id?'Edit':'Add'}} Assessment

</h5>


<button

class="btn-close"

data-bs-dismiss="modal"

></button>


</div>







<div class="modal-body">





<label class="form-label">

Title

</label>


<input

class="form-control mb-3"

v-model="form.title"

/>






<label class="form-label">

Assessment Type

</label>


<select

class="form-select mb-3"

v-model="form.assessment_type"

>


<option value="practical">

Practical

</option>


<option value="homework">

Homework

</option>


<option value="quiz">

Quiz

</option>


<option value="assignment">

Assignment

</option>


<option value="exam">

Exam

</option>


<option value="project">

Project

</option>


<option value="other">

Other

</option>


</select>








<label class="form-label">

Session

</label>


<select

class="form-select mb-3"

v-model="form.course_session_id"

>


<option :value="null">

General Assessment

</option>



<option

v-for="s in sessions"

:key="s.id"

:value="s.id"

>

Session {{s.session_number}}

-

{{s.title}}

</option>


</select>








<label class="form-label">

Description

</label>


<textarea

class="form-control mb-3"

rows="3"

v-model="form.description"

></textarea>








<label class="form-label">

Instructions

</label>


<textarea

class="form-control mb-3"

rows="4"

v-model="form.instructions"

></textarea>







<label class="form-label">

Assessment File

</label>



<input

type="file"

class="form-control mb-3"

accept=".pdf,.doc,.docx"

@change="selectFile"

/>





<div
v-if="form.attachment"
class="mb-3"
>


<a

:href="'/storage/'+form.attachment"

target="_blank"

class="btn btn-outline-primary btn-sm"

>

<i class="bi bi-file-earmark"></i>

View Existing File

</a>


</div>









<div class="row">


<div class="col">


<label>

Maximum Marks

</label>


<input

type="number"

class="form-control"

v-model="form.max_marks"

/>


</div>






<div class="col">


<label>

Pass Mark

</label>


<input

type="number"

class="form-control"

v-model="form.pass_mark"

/>


</div>


</div>





</div>







<div class="modal-footer">


<button

class="btn btn-secondary"

data-bs-dismiss="modal"

>

Cancel

</button>




<button

class="btn btn-success"

@click="save"

>

Save

</button>



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


course:{},

assessments:[],

sessions:[],


file:null,



form:{


id:null,

title:"",

assessment_type:"practical",

course_session_id:null,

description:"",

instructions:"",

max_marks:100,

pass_mark:null,

attachment:null


}



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






let assessments =
await axios.get(
`/api/services/${id}/assessments`
);



this.assessments =
assessments.data.data || [];






let sessions =
await axios.get(
`/api/services/${id}/sessions`
);



this.sessions =
sessions.data.data || [];



},








openModal(){


this.reset();


new bootstrap.Modal(

document.getElementById(
'assessmentModal'
)

).show();


},








edit(assessment){


this.form={

...assessment

};


this.file=null;



new bootstrap.Modal(

document.getElementById(
'assessmentModal'
)

).show();



},







selectFile(e){


this.file =
e.target.files[0];


},








async save(){



let service_id =
this.$route.params.id;




let data =
new FormData();




data.append(
'service_id',
service_id
);



data.append(
'title',
this.form.title
);



data.append(
'assessment_type',
this.form.assessment_type
);



data.append(
'course_session_id',
this.form.course_session_id ?? ''
);



data.append(
'description',
this.form.description ?? ''
);



data.append(
'instructions',
this.form.instructions ?? ''
);



data.append(
'max_marks',
this.form.max_marks
);



data.append(
'pass_mark',
this.form.pass_mark ?? ''
);






let assessmentId=this.form.id;






if(assessmentId){


data.append(
'_method',
'PUT'
);



await axios.post(

`/api/course-assessments/${assessmentId}`,

data

);



}else{


let response =
await axios.post(

'/api/course-assessments',

data

);



assessmentId =
response.data.data.id;



}







if(this.file){


let upload =
new FormData();


upload.append(
'file',
this.file
);




await axios.post(

`/api/course-assessments/${assessmentId}/attachment`,

upload

);



}






bootstrap.Modal

.getInstance(

document.getElementById(
'assessmentModal'
)

)

.hide();




this.file=null;


this.load();



},










async remove(assessment){



if(confirm(
"Delete assessment?"
)){



await axios.delete(

`/api/course-assessments/${assessment.id}`

);



this.load();


}



},







reset(){


this.form={


id:null,

title:"",

assessment_type:"practical",

course_session_id:null,

description:"",

instructions:"",

max_marks:100,

pass_mark:null,

attachment:null


};


this.file=null;


}



},





mounted(){

this.load();

}



}


</script>