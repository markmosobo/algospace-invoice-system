<template>

<Master>

<section class="section dashboard">

<div class="row">

<div class="col-12">

<div class="card">

<div class="card-body">


<h5 class="card-title">

{{ course.name }}

<span>| Course Outline</span>

</h5>



<div class="mb-3">

<button
class="btn btn-success btn-sm"
@click="saveOutline"
>

<i class="bi bi-save"></i>
Save Outline

</button>


</div>




<!-- MAIN OUTLINE -->

<div class="mb-3">

<label class="form-label">
Course Overview
</label>


<textarea

class="form-control"

rows="4"

v-model="outline.overview"

placeholder="Describe the course..."

></textarea>


</div>





<div class="mb-3">

<label class="form-label">

Certificate Information

</label>


<textarea

class="form-control"

rows="3"

v-model="outline.certificate_information"

placeholder="Certificate requirements..."

></textarea>


</div>





<div class="mb-3">

<label class="form-label">

Notes

</label>


<textarea

class="form-control"

rows="3"

v-model="outline.notes"

placeholder="Additional notes..."

></textarea>


</div>





<hr>


<div class="d-flex justify-content-between align-items-center">


<h5>
Outline Sections
</h5>


<button

class="btn btn-outline-primary btn-sm"

@click="addItem"

>

<i class="bi bi-plus"></i>

Add Section

</button>


</div>





<!-- ITEMS -->

<div

v-for="(item,index) in outline.items"

:key="index"

class="card mt-3 border"


>


<div class="card-body">



<div class="row">


<div class="col-md-3">


<label class="form-label">
Section
</label>


<select

class="form-select"

v-model="item.section"

>


<option value="objective">
Objective
</option>


<option value="outcome">
Learning Outcome
</option>


<option value="requirement">
Requirement
</option>


<option value="assessment">
Assessment
</option>


</select>



</div>





<div class="col-md-9">


<label class="form-label">
Title
</label>


<input

type="text"

class="form-control"

v-model="item.title"

placeholder="Section title"

/>


</div>


</div>






<div class="mt-3">


<label class="form-label">

Description

</label>


<textarea

class="form-control"

rows="3"

v-model="item.description"

placeholder="Explain this section..."

></textarea>


</div>






<div class="text-end mt-3">


<button

class="btn btn-sm btn-danger"

@click="removeItem(index)"

>

<i class="bi bi-trash"></i>

Remove


</button>


</div>




</div>


</div>






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
import Swal from "sweetalert2";


const toast = Swal.mixin({

toast:true,

position:"top-end",

timer:3000,

showConfirmButton:false

});



export default {


components:{
Master
},



data(){


return{


course:{},



outline:{


id:null,


overview:"",


certificate_information:"",


notes:"",


items:[]

}



}


},






methods:{





async load(){


try{


let id=this.$route.params.id;



// Load course

let course =
await axios.get(
`/api/courses/${id}`
);



this.course =
course.data.data;




// Load outline

let outline =
await axios.get(
`/api/services/${id}/outline`
);



if(outline.data.data){

this.outline = {

    ...outline.data.data,

    items: outline.data.data.items || []

};

}



}

catch(error){


console.log(error);



}



},







addItem(){

console.log("Adding item");

console.log(this.outline.items);


this.outline.items.push({

section:"objective",

title:"",

description:"",

sort_order:this.outline.items.length + 1

});


console.log(this.outline.items);


},







removeItem(index){


this.outline.items.splice(
index,
1
);



},







async saveOutline(){



let id=this.$route.params.id;




try{



let response;



if(this.outline.id){



response =
await axios.put(


`/api/course-outlines/${this.outline.id}`,

this.outline


);



}

else{



response =
await axios.post(


`/api/services/${id}/outline`,

this.outline


);



}



this.outline =
response.data.data;



toast.fire({

icon:"success",

title:"Course outline saved"

});



}



catch(error){



console.log(error.response);



toast.fire({

icon:"error",

title:"Failed to save outline"

});



}



}





},





mounted(){


this.load();


}



}


</script>





<style scoped>

textarea{

resize:none;

}

</style>