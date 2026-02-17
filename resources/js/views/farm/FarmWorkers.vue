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
                      <h5 class="card-title">Farm Workers <span>| Farm workers, and casual labourers</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addFarmWorker()"
                                >
                                  Add Farm Worker
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
    
                      <table id="FarmWorkersTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Role</th>
                            <th scope="col">Phone</th>
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
                          <tr v-for="item in farmworkers" :key="item.id">
                            <td>{{item.name}}</td>
                            <td>{{item.role ?? "N/A"}}</td>
                            <td>{{item.phone ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewFarmWork(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editFarmWorker(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteFarmWorker(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Farm Worker Modal -->
              <div class="modal fade" id="viewFarmWorkerModal" tabindex="-1" aria-labelledby="viewFarmWorkerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Farm Worker Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedFarmWorker">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedFarmWorker.name">
                          <strong>Full Name:</strong> <br> {{ selectedFarmWorker.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmWorker.role">
                          <strong>Role:</strong> <br> {{ selectedFarmWorker.role }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmWorker.phone">
                          <strong>Phone:</strong> <br> {{ selectedFarmWorker.phone }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmWorker.daily_rate">
                          <strong>Daily Rate:</strong> <br> {{ selectedFarmWorker.daily_rate }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmWorker.status">
                          <strong>Current Status:</strong> <br> {{ selectedFarmWorker.status }}
                        </div>
                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Customer Modal -->
                <div class="modal fade" id="AddFarmWorker" tabindex="-1" aria-labelledby="AddFarmWorkerLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddFarmWorkerLabel">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- First & Last Name -->
                          <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name" class="form-control" v-model="data.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" id="role" class="form-control" v-model="data.role" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="phone" class="form-control" v-model="data.phone">
                          </div>

                          <!-- Status -->
                          <div class="col-md-6">
                            <label class="form-label">Daily Rate</label>
                            <input type="number" id="daily_rate" class="form-control" v-model="data.daily_rate">
                          </div>                          

                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="data.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="inactive">In-active</option>
                            </select>
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


                <!-- EDIT Farm Worker MODAL -->
                <div class="modal fade" id="EditFarmWorker" tabindex="-1" aria-labelledby="EditFarmWorkerLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Worker</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- First & Last Name -->
                          <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" id="role" class="form-control" v-model="data.role" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="phone" class="form-control" v-model="form.phone">
                          </div>

                          <!-- Status -->
                          <div class="col-md-6">
                            <label class="form-label">Daily Rate</label>
                            <input type="number" id="daily_rate" class="form-control" v-model="form.daily_rate">
                          </div>                          

                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="form.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="inactive">In-active</option>
                            </select>
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
            farmworkers: [],
            selectedFarmWorker: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD farm worker
            id: "",
            name: "",
            role: "",
            phone: "",
            daily_rate: "",
            status: ""
            },

            form: {        // EDIT farm worker
            id: "",
            name: "",
            role: "",
            phone: "",
            daily_rate: "",
            status: ""
            }
        }
      },      
      methods: {                
        viewFarmWork(item)
        {
          console.log(this.selectedFarmWorker)
          this.selectedFarmWorker = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewFarmWorkerModal'));
          modal.show();
        },
        editFarmWorker(item) {
        this.form = {
            id: item.id,
            name: item.name,
            role: item.role,
            phone: item.phone,
            daily_rate: item.daily_rate
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditFarmWorker')
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
            await axios.put(`/api/farm-workers/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm worker updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditFarmWorker')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm worker',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addFarmWorker()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddFarmWorker'));
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
            await axios.post('/api/farm-workers', this.data);

            toast.fire('Success!', 'Farm worker added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddFarmWorker')
            );
            modal.hide();

            // Reset form
            this.data = {
            id: "",
            name: "",
            role: "",
            phone: "",
            daily_rate: "",
            status: ""
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
        deleteFarmWorker(id){
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
                  axios.delete('/api/farm-workers/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm worker has been deleted.',
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
          axios.get('/api/farm-workers')
            .then((response) => {
              this.farmworkers = response.data;
              console.log(response)

              setTimeout(() => {
                $("#FarmWorkersTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching user list:', error);
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
    
    
    