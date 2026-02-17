<template>
    <Master>
        <section class="section dashboard">
          <div class="row">
    
                <!-- Top Selling -->
                <div class="col-12">
                  <div class="card top-selling overflow-auto">
    
                    <div class="filter">
                    <!--                       <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                          <h6>Filter</h6>
                        </li>
    
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                      </ul> -->
                    </div>
    
                    <div class="card-body pb-0">
                      <h5 class="card-title">Farm Worker Tasks <span>| Farm activities assigned </span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addWorkerTask()"
                                >
                                  Add Farm Work Task
                                </a>
                          </div>
                          <div class="col-auto d-flex justify-content-end">
                          <div class="btn-group" role="group">
                              <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-add-line"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                     <a @click="navigateTo('/clients' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Clients</a>
                                    <a @click="navigateTo('/savings' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Savings</a>
                                    <a @click="navigateTo('/loans' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Loans</a>
                                </div>
                              </div>
                            </div>
                        </div>   
            
                      </p>
    
                      <table id="WorkerTasksTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Worker Name</th>
                            <th scope="col">Venture Name</th>
                            <th scope="col">Task</th>
                            <th scope="col">Work Date</th>
                            <th scope="col">Amount Paid</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <!-- Spinner shown while data is initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="7" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                        <tbody v-else>
                          <tr v-for="workertask in workertasks" :key="workertask.id">
                            <td>{{workertask.worker.name}}</td>
                            <td>{{workertask.venture.name ?? "N/A"}}</td>
                            <td>{{workertask.task ?? "N/A"}}</td>
                            <td>{{workertask.work_date ?? "N/A"}}</td>
                            <td>{{workertask.amount_paid ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewWorkerTask(workertask)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editWorkerTask(workertask)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteWorkerTask(workertask.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View workertask Modal -->
              <div class="modal fade" id="viewworkertaskModal" tabindex="-1" aria-labelledby="viewworkertaskModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View workertask Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedWorkerTask">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedWorkerTask.worker_id">
                          <strong>Worker Name:</strong> <br> {{ selectedWorkerTask.worker.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedWorkerTask.venture_id">
                          <strong>Venture Name:</strong> <br> {{ selectedWorkerTask.venture.venture_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedWorkerTask.task">
                          <strong>Task:</strong> <br> {{ selectedWorkerTask.task }}
                        </div>

                        <div class="col-md-6" v-if="selectedWorkerTask.work_date">
                          <strong>Work Date:</strong> <br> {{ selectedWorkerTask.work_date }}
                        </div>

                        <div class="col-md-6" v-if="selectedWorkerTask.amount_paid">
                          <strong>Amount Paid:</strong> <br> {{ selectedWorkerTask.amount_paid }}
                        </div>                        

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add workertask Modal -->
                <div class="modal fade" id="AddWorkerTaskModal" tabindex="-1" aria-labelledby="AddWorkerTaskModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddWorkerTaskModalLabel">Add Farm Worker Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- First & Last Name -->
                          <div class="col-md-12">
                            <label class="form-label">Farm Worker*</label>
                            <select class="form-select" id="venture" v-model="data.worker" required>
                              <option value="">Select Venture</option>
                              <option v-for="worker in farmworkers" :key="worker.id" :value="worker.id">
                                {{ worker.name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture" v-model="data.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Task -->
                          <div class="col-md-6">
                            <label class="form-label">Task</label>
                            <input type="text" class="form-control" v-model="data.task">
                          </div>

                          <!-- Work Date -->
                          <div class="col-md-6">
                            <label class="form-label">Work Date</label>
                            <input type="text" class="form-control" v-model="data.work_date">
                          </div>
                          
                          <!-- Task -->
                          <div class="col-md-6">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" class="form-control" v-model="data.amount_paid">
                          </div>                          



                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submit" style="background: darkgreen; border-color: darkgreen;">
                          Save
                        </button>
                      </div>

                    </div>
                  </div>
                </div>


                <!-- EDIT WorkerTask MODAL -->
                <div class="modal fade" id="editWorkerTaskModal" tabindex="-1" aria-labelledby="editWorkerTaskModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Worker Task</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- First & Last Name -->
                          <div class="col-md-12">
                            <label class="form-label">Farm Worker*</label>
                            <select class="form-select" id="venture" v-model="form.worker" required>
                              <option value="">Select Venture</option>
                              <option v-for="worker in farmworkers" :key="worker.id" :value="worker.id">
                                {{ worker.name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture" v-model="form.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Task -->
                          <div class="col-md-6">
                            <label class="form-label">Task</label>
                            <input type="text" class="form-control" v-model="form.task">
                          </div>

                          <!-- Work Date -->
                          <div class="col-md-6">
                            <label class="form-label">Work Date</label>
                            <input type="text" class="form-control" v-model="form.work_date">
                          </div>
                          
                          <!-- Task -->
                          <div class="col-md-6">
                            <label class="form-label">Amount Paid</label>
                            <input type="number" class="form-control" v-model="form.amount_paid">
                          </div>                          



                        </form>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submitChanges" style="background: darkgreen; border-color: darkgreen;">
                          Save Changes
                        </button>
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
    import Swal from 'sweetalert2';
    import "jquery/dist/jquery.min.js";
    import "datatables.net-dt/js/dataTables.dataTables";
    import "datatables.net-dt/css/jquery.dataTables.min.css";
    import DefaultProfile from '@/assets/img/default-profile.png'
    import $ from "jquery";
    
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    
    window.toast = toast;
    
    export default {
      data() {
        return {
            workertasks: [],
            selectedWorkerTask: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD workertask
            id: "",
            worker_id: "",
            venture_id: "",
            task: "",
            work_date: "",
            amount_paid: ""
            },

            form: {        // EDIT workertask
            id: "",
            worker_id: "",
            venture_id: "",
            task: "",
            work_date: "",
            amount_paid: ""
            }
        }
      },      
      methods: {                
        viewWorkerTask(workertask)
        {
          console.log(this.selectedWorkerTask)
          this.selectedWorkerTask = workertask;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewWorkerTaskModal'));
          modal.show();
        },
        editWorkerTask(item) {
        this.form = {
            id: workertask.id,
            worker_id: workertask.worker_id,
            email: workertask.email,
            phone: workertask.phone,
            gender: workertask.gender
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editWorkerTaskModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.name) {
            document.getElementById('name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/workertasks/${this.form.id}`, this.form);

            toast.fire('Success!', 'workertask updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editWorkerTaskModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm worker task',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addWorkerTask()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddWorkerTaskModal'));
          modal.show();
        },
        async submit() {
            if (this.validateForm()) {

                // Start submitting process
                this.submitting = true;
                
                try {
                    // Simulate asynchronous submission process (you would replace this with your actual submission logic)
                    await this.submitForm();

                    // Submission successful
                    this.submitted = true;
                } catch (error) {
                    // Handle submission error
                    console.error("Submission error:", error);
                } finally {
                    // End submitting process
                    this.submitting = false;
                }
            }
        },
        validateForm() {
        let isValid = true;

        if (!this.data.name) {
            document.getElementById('name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/worker-tasks', this.data);

            toast.fire('Success!', 'Farm worker task added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddWorkerTaskModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              worker_id: "",
              venture_id: "",
              task: "",
              work_date: "",
              amount_paid: ""
            };

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Something went wrong',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },
        navigateTo(location){
            this.$router.push(location)
        },
        deleteWorkerTask(id){
                Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#006400',
                  cancelButtonColor: '#FFA500',
                  confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                  if (result.isConfirmed) { 
                  //send request to the server
                  axios.delete('/api/worker-tasks/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm worker task has been deleted.',
                    'success'
                  )
                  this.loadLists();
                  }).catch(() => {
                    Swal.fire(
                    'Failed!',
                    'There was something wrong.',
                    'warning'
                  )
                  }); 
                  }else if(result.isDenied) {
                    console.log('cancelled')
                  }
                                   
                })
        },
        loadLists() {
          this.initializing = true; // Start spinner
          axios.get('/api/worker-tasks')
            .then((response) => {
              this.workertasks = response.data.farmworkertasks;
              this.farmventures = response.data.farmventures;
              console.log(response)

              setTimeout(() => {
                $("#WorkerTasksTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching worker tasks list:', error);
            })
            .finally(() => {
              this.initializing = false; // Stop spinner
            });
        },
      },
      components : {
          Master,
      },
      mounted(){
        this.loadLists();
        // this.user = localStorage.getItem('user');
        // this.user = JSON.parse(this.user);
        // this.userId = this.user.id;
        // this.currentUser = JSON.parse(localStorage.getItem('user')) || {};
        // this.current_user_id = this.currentUser.id;
        // this.current_user = this.currentUser.first_name + " " + this.currentUser.last_name;

      }
    }
    </script>
    
    
    