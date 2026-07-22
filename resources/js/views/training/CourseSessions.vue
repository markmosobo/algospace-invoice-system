<template>

<Master>

<section class="section dashboard">


<div class="card">

<div class="card-body">


<h5 class="card-title">

{{course.name}}

<span>| Course Sessions</span>

</h5>


<button 
class="btn btn-success btn-sm"
@click="openForm"
>

<i class="bi bi-plus"></i>
Add Session

</button>



<div
    v-for="s in sessions"
    :key="s.id"
    class="card mt-3 shadow-sm"
>
    <div class="card-body">

        <div class="d-flex justify-content-between align-items-start">

            <div>

                <h5 class="mb-1">
                    Session {{ s.session_number }} - {{ s.title }}
                </h5>

                <small class="text-muted">
                    {{ s.duration_hours }} Hours
                </small>

                <p class="mt-2">
                    {{ s.description }}
                </p>

            </div>

            <div>

                <button
                    class="btn btn-sm btn-primary me-2"
                    @click="edit(s)"
                >
                    Edit
                </button>

                <button
                    class="btn btn-sm btn-danger"
                    @click="remove(s)"
                >
                    Delete
                </button>

            </div>

        </div>

        <hr>

        <div class="d-flex justify-content-between mb-2">

            <strong>Topics</strong>

            <button
                class="btn btn-sm btn-outline-success"
                @click="addTopic(s)"
            >
                <i class="bi bi-plus"></i>

                Add Topic
            </button>

        </div>

        <div
            v-if="!s.topics || s.topics.length==0"
            class="text-muted"
        >
            No topics added.
        </div>

        <ul
            v-else
            class="list-group"
        >

            <li
                class="list-group-item d-flex justify-content-between align-items-center"
                v-for="topic in s.topics"
                :key="topic.id"
            >

                <div>

                    <strong>{{ topic.title }}</strong>

                    <br>

                    <small>
                        {{ topic.description }}
                    </small>

                </div>

                <div>

                    <button
                        class="btn btn-sm btn-outline-primary me-2"
                        @click="editTopic(topic,s)"
                    >
                        Edit
                    </button>

                    <button
                        class="btn btn-sm btn-outline-danger"
                        @click="deleteTopic(topic.id)"
                    >
                        Delete
                    </button>

                </div>

            </li>

        </ul>

    </div>

</div>



<hr>


<div v-if="showForm">


<h5>
{{form.id?'Edit':'New'}} Session
</h5>


<input
class="form-control mb-2"
placeholder="Session number"
v-model="form.session_number"
/>


<input
class="form-control mb-2"
placeholder="Title"
v-model="form.title"
/>


<input
class="form-control mb-2"
placeholder="Hours"
v-model="form.duration_hours"
/>


<textarea
class="form-control mb-2"
placeholder="Description"
v-model="form.description"
></textarea>



<button
class="btn btn-success"
@click="save"
>
Save
</button>


</div>


</div>

</div>

<div class="modal fade" id="topicModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">
                <h5 class="modal-title">
                    {{ topicForm.id ? 'Edit Topic' : 'Add Topic' }}
                </h5>

                <button
                    class="btn-close"
                    data-bs-dismiss="modal">
                </button>
            </div>

            <div class="modal-body">

                <input
                    class="form-control mb-3"
                    v-model="topicForm.title"
                    placeholder="Topic title"
                >

                <textarea
                    class="form-control"
                    rows="4"
                    v-model="topicForm.description"
                    placeholder="Description">
                </textarea>

            </div>

            <div class="modal-footer">

                <button
                    class="btn btn-secondary"
                    data-bs-dismiss="modal">
                    Cancel
                </button>

                <button
                    class="btn btn-success"
                    @click="saveTopic">
                    Save Topic
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

sessions:[],

showForm:false,

topicForm:{
    id:null,
    course_session_id:null,
    title:"",
    description:"",
    sort_order:1
},

form:{


id:null,

session_number:1,

title:"",

description:"",

duration_hours:4.5,

sort_order:1


}


}


},


methods:{
addTopic(session){

    this.topicForm = {
        id:null,
        course_session_id:session.id,
        title:"",
        description:"",
        sort_order:(session.topics?.length || 0) + 1
    };

    new bootstrap.Modal(
        document.getElementById("topicModal")
    ).show();

},
editTopic(topic, session){

    this.topicForm = {
        id: topic.id,
        course_session_id: session.id,
        title: topic.title,
        description: topic.description,
        sort_order: topic.sort_order
    };


    new bootstrap.Modal(
        document.getElementById("topicModal")
    ).show();

},
async saveTopic(){

    if(this.topicForm.id){

        await axios.put(
            `/api/course-session-topics/${this.topicForm.id}`,
            this.topicForm
        );

    }else{

        await axios.post(
            `/api/course-sessions/${this.topicForm.course_session_id}/topics`,
            this.topicForm
        );

    }

bootstrap.Modal.getInstance(
    document.getElementById("topicModal")
).hide();
    this.load();

},

async deleteTopic(id){

    await axios.delete(
        `/api/course-session-topics/${id}`
    );

    this.load();

},

async load(){


let id=this.$route.params.id;


let course=
await axios.get(`/api/courses/${id}`);


this.course=course.data.data;



let sessions=
await axios.get(`/api/services/${id}/sessions`);


this.sessions=sessions.data.data || [];


},



openForm(){

this.showForm=true;

},



edit(s){

this.form={...s};

this.showForm=true;

},



async save(){


let id=this.$route.params.id;



if(this.form.id){


await axios.put(
`/api/course-sessions/${this.form.id}`,
this.form
);


}else{


await axios.post(
`/api/services/${id}/sessions`,
this.form
);


}


this.showForm=false;

this.load();


},



async remove(s){


await axios.delete(
`/api/course-sessions/${s.id}`
);


this.load();


}


},


mounted(){

this.load();

}


}


</script>