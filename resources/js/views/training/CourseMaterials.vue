<template>

<Master>

<section class="section dashboard">


<div class="card">

<div class="card-body">


<h5 class="card-title">

{{course.name}}

<span>| Course Materials</span>

</h5>



<div class="d-flex gap-2 mb-3">


<button
class="btn btn-success btn-sm"
@click="openModal"
>

<i class="bi bi-plus me-1"></i>
Add Material

</button>



<button
class="btn btn-primary btn-sm"
@click="generateHandbook"
>

<i class="bi bi-file-earmark-pdf me-1"></i>
Generate Handbook

</button>

<button
class="btn btn-dark btn-sm"
@click="downloadPackage"
>

<i class="bi bi-file-zip"></i>

Download Course Package

</button>

</div>

<!-- MATERIALS -->


<div
v-if="materials.length==0"
class="alert alert-secondary"
>

No course materials added.

</div>





<div
class="row"
>


<div
class="col-md-6 mb-3"
v-for="material in materials"
:key="material.id"
>



<div class="card shadow-sm h-100">


<div class="card-body">


<div class="d-flex">


<div class="me-3">

<i
:class="icon(material.type)"
class="fs-2"
>
</i>

</div>



<div class="flex-grow-1">


<h6>

{{material.title}}

</h6>



<div>


<span class="badge bg-primary me-1">

{{material.type}}

</span>



<span class="badge bg-secondary">

{{material.source}}

</span>


</div>





<p class="text-muted mt-2">

{{material.description}}

</p>





<small
v-if="material.session"
class="text-success"
>

Session:
{{material.session.title}}

</small>




</div>


</div>




<hr>




<div class="text-end">


<a
v-if="material.file"
:href="'/storage/'+material.file"
target="_blank"
class="btn btn-sm btn-outline-primary me-2"
>

<i class="bi bi-eye"></i>
Open

</a>



<a
v-if="material.url"
:href="material.url"
target="_blank"
class="btn btn-sm btn-outline-primary me-2"
>

<i class="bi bi-globe"></i>
Visit

</a>



<button
class="btn btn-sm btn-outline-success me-2"
@click="edit(material)"
>

Edit

</button>



<button
class="btn btn-sm btn-outline-danger"
@click="remove(material)"
>

Delete

</button>


</div>



</div>


</div>



</div>


</div>







</div>


</div>





<!-- MODAL -->


<div
class="modal fade"
id="materialModal"
tabindex="-1"
>


<div class="modal-dialog">


<div class="modal-content">



<div class="modal-header">


<h5 class="modal-title">

{{form.id?'Edit':'Add'}} Material

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
Description
</label>


<textarea
class="form-control mb-3"
rows="3"
v-model="form.description"
></textarea>





<label class="form-label">
Material Type
</label>


<select
class="form-select mb-3"
v-model="form.type"
>


<option value="note">
Note
</option>


<option value="ebook">
E-book
</option>


<option value="exercise">
Exercise
</option>


<option value="assignment">
Assignment
</option>


<option value="template">
Template
</option>


<option value="presentation">
Presentation
</option>


<option value="video">
Video
</option>


<option value="website">
Website
</option>


<option value="software">
Software
</option>


</select>





<label class="form-label">
Source
</label>


<select
class="form-select mb-3"
v-model="form.source"
>


<option value="upload">
Upload
</option>


<option value="external">
External Link
</option>


<option value="library">
Library
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
General Course Material
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






<div v-if="form.source=='upload'">


<label class="form-label">

File

</label>


<input
type="file"
class="form-control"
@change="selectFile"
/>


</div>




<div v-if="form.source=='external'">


<label>
URL
</label>


<input
class="form-control"
v-model="form.url"
/>


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

materials:[],

sessions:[],

file:null,


form:{

id:null,

title:"",

description:"",

type:"note",

source:"upload",

course_session_id:null,

url:""

}


}


},



methods:{
async downloadPackage(){

    let id=this.$route.params.id;

    try {

        let response = await axios.get(
            `/api/services/${id}/package`,
            {
                responseType:'blob'
            }
        );


        let blob = new Blob(
            [response.data],
            {
                type:'application/zip'
            }
        );


        let url = window.URL.createObjectURL(blob);


        let link=document.createElement('a');

        link.href=url;

        link.download="course-package.zip";

        document.body.appendChild(link);

        link.click();

        document.body.removeChild(link);


    } catch(error){

        console.log(error);

        alert("Failed downloading course package");

    }


},

async generateHandbook(){

    let id = this.$route.params.id;

    try {

        let response = await axios.get(
            `/api/services/${id}/handbook/pdf`,
            {
                responseType:'blob'
            }
        );


        let file = new Blob(
            [response.data],
            {
                type:'application/pdf'
            }
        );


        let url = window.URL.createObjectURL(file);


        window.open(url,'_blank');


    } catch(error){

        console.log(error);

        alert("Failed generating handbook");

    }

},

async load(){


let id=this.$route.params.id;



let course =
await axios.get(
`/api/courses/${id}`
);


this.course=course.data.data;





let materials =
await axios.get(
`/api/services/${id}/materials`
);


this.materials =
materials.data.data || [];





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
document.getElementById("materialModal")
).show();


},





selectFile(e){

this.file=e.target.files[0];

},





async save(){


let id=this.$route.params.id;


let data=new FormData();



Object.keys(this.form)
.forEach(key=>{

data.append(
key,
this.form[key] ?? ""
);

});



if(this.file){

data.append(
'file',
this.file
);

}





if(this.form.id){


data.append('_method','PUT');


await axios.post(
`/api/course-materials/${this.form.id}`,
data
);



}else{


await axios.post(
`/api/services/${id}/materials`,
data
);


}




bootstrap.Modal
.getInstance(
document.getElementById("materialModal")
)
.hide();



this.load();


},






edit(material){


this.form={...material};

this.file=null;


new bootstrap.Modal(
document.getElementById("materialModal")
).show();


},






async remove(material){


if(confirm("Delete material?")){


await axios.delete(

`/api/course-materials/${material.id}`

);


this.load();


}


},






icon(type){


let icons={


note:'bi bi-file-earmark-text text-primary',

ebook:'bi bi-book text-success',

exercise:'bi bi-pencil-square text-warning',

assignment:'bi bi-journal-check text-danger',

template:'bi bi-file-earmark-richtext',

presentation:'bi bi-easel',

video:'bi bi-play-circle',

website:'bi bi-globe',

software:'bi bi-download',

other:'bi bi-folder'


};


return icons[type] || icons.other;


},






reset(){


this.form={

id:null,

title:"",

description:"",

type:"note",

source:"upload",

course_session_id:null,

url:""

};


this.file=null;


}



},



mounted(){

this.load();

}



}

</script>