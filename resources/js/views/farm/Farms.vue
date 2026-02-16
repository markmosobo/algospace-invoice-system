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
                      <h5 class="card-title">Farms <span>| Arable land owned</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addFarm()"
                                >
                                  Add Farm
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
    
                      <table id="FarmsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Location</th>
                            <th scope="col">Size</th>
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
                          <tr v-for="item in farms" :key="item.id">
                            <td>{{item.name}}</td>
                            <td>{{item.location ?? "N/A"}}</td>
                            <td>{{item.size ?? "N/A"}} ha</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewFarm(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editFarm(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteFarm(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Farm Modal -->
              <div class="modal fade" id="viewFarmModal" tabindex="-1" aria-labelledby="viewFarmModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Farm Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedFarm">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedFarm.name">
                          <strong>Name:</strong> <br> {{ selectedFarm.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarm.location">
                          <strong>Location:</strong> <br> {{ selectedFarm.location }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarm.size">
                          <strong>Size:</strong> <br> {{ selectedFarm.size }} ha
                        </div>

                        <div class="col-md-6" v-if="selectedFarm.description">
                          <strong>Description:</strong> <br> {{ selectedFarm.description }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Farm Modal -->
                <div class="modal fade" id="addFarmModal" tabindex="-1" aria-labelledby="addFarmModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addFarmModalLabel">Add Farm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name" class="form-control" v-model="data.name" required>
                          </div>

                          <!-- Location -->
                          <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" id="location" class="form-control" v-model="data.location" required>
                          </div>

                          <!-- Size -->
                          <div class="col-md-6">
                            <label class="form-label">Size (in hectares)</label>
                            <input type="number" id="size" class="form-control" v-model="data.size">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea rows="4" id="description" class="form-control" v-model="data.description" />
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


                <!-- EDIT Farm MODAL -->
                <div class="modal fade" id="editFarmModal" tabindex="-1" aria-labelledby="editFarmModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Name -->
                          <div class="col-md-12">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name_edit" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Loaction -->
                          <div class="col-md-6">
                            <label class="form-label">Location</label>
                            <input type="text" id="location_edit" class="form-control" v-model="form.location" required>
                          </div>

                          <!-- Size -->
                          <div class="col-md-6">
                            <label class="form-label">Size (in hectares)</label>
                            <input type="number" class="form-control" v-model="form.size">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea rows="4" id="description_edit" class="form-control" v-model="form.description" />
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
            farms: [],
            selectedFarm: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD farm
              id: "",
              name: "",
              location: "",
              size: "",
              description: ""
            },

            form: {        // EDIT farm
              id: "",
              name: "",
              location: "",
              size: "",
              description: ""
            }
        }
      },      
      methods: {                
        viewFarm(item)
        {
          console.log(this.selectedFarm)
          this.selectedFarm = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewFarmModal'));
          modal.show();
        },
        editFarm(item) {
        this.form = {
            id: item.id,
            name: item.name,
            location: item.location,
            size: item.size,
            description: item.description
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editFarmModal')
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
            await axios.put(`/api/farms/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editFarmModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm details',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addFarm()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addFarmModal'));
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
            await axios.post('/api/farms', this.data);

            toast.fire('Success!', 'Farm added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addFarmModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              name: "",
              location: "",
              size: "",
              description: ""
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
        deleteFarm(id){
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
                  axios.delete('/api/farms/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm has been deleted.',
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
          axios.get('/api/farms')
            .then((response) => {
              this.farms = response.data;
              console.log(response)

              setTimeout(() => {
                $("#FarmsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching farms list:', error);
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
    
    
    