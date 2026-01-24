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
                      <h5 class="card-title">Service Providers <span>| Service providers who have rendered service(s) to AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addServiceProvider()"
                                >
                                  Add Service Provider
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
    
                      <table id="ServiceProvidersTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email Address</th>
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
                          <tr v-for="item in serviceProviders" :key="item.id">
                            <td>{{item.name}}</td>
                            <td>{{item.email ?? "N/A"}}</td>
                            <td>{{item.phone ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewServiceProvider(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editServiceProvider(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteServiceProvider(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Service Provider Modal -->
              <div class="modal fade" id="viewServiceProviderModal" tabindex="-1" aria-labelledby="viewServiceProviderModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Service Provider Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedServiceProvider">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedServiceProvider.name">
                          <strong>Full Name:</strong> <br> {{ selectedServiceProvider.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedServiceProvider.email">
                          <strong>Email:</strong> <br> {{ selectedServiceProvider.email }}
                        </div>

                        <div class="col-md-6" v-if="selectedServiceProvider.phone">
                          <strong>Phone:</strong> <br> {{ selectedServiceProvider.phone }}
                        </div>

                        <div class="col-md-6" v-if="selectedServiceProvider.gender">
                          <strong>Gender:</strong> <br> {{ selectedServiceProvider.gender }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Service Provider Modal -->
                <div class="modal fade" id="addServiceProviderModal" tabindex="-1" aria-labelledby="addServiceProviderModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addServiceProviderModalLabel">Add Service Provider</h5>
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
                            <label class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" v-model="data.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="phone" class="form-control" v-model="data.phone">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" v-model="data.gender">
                              <option value="">Select</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="other">Other</option>
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


                <!-- EDIT Service Provider MODAL -->
                <div class="modal fade" id="editServiceProviderModal" tabindex="-1" aria-labelledby="editServiceProviderModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Service Provider</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- First & Last Name -->
                          <div class="col-md-12">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name_edit" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="mail_edit" class="form-control" v-model="form.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" v-model="form.phone">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" v-model="form.gender">
                              <option value="">Select</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="other">Other</option>
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
            serviceProviders: [],
            selectedServiceProvider: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD service provider
                id: "",
                name: "",
                email: "",
                phone: "",
                gender: ""
            },

            form: {        // EDIT service provider
                id: "",
                name: "",
                email: "",
                phone: "",
                gender: ""
            }
        }
      },      
      methods: {                
        viewServiceProvider(item)
        {
          console.log(this.selectedServiceProvider)
          this.selectedServiceProvider = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewServiceProviderModal'));
          modal.show();
        },
        editServiceProvider(item) {
        this.form = {
            id: item.id,
            name: item.name,
            email: item.email,
            phone: item.phone,
            gender: item.gender
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editServiceProviderModal')
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
            await axios.put(`/api/service-providers/${this.form.id}`, this.form);

            toast.fire('Success!', 'Service provider updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editServiceProviderModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update service provider',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addServiceProvider()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addServiceProviderModal'));
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
            await axios.post('/api/service-providers', this.data);

            toast.fire('Success!', 'Service provider added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addServiceProviderModal')
            );
            modal.hide();

            // Reset form
            this.data = {
                id: "",
                name: "",
                email: "",
                phone: "",
                gender: ""
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
        deleteServiceProvider(id){
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
                  axios.delete('/api/service-providers/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Service provider has been deleted.',
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
          axios.get('/api/service-providers')
            .then((response) => {
              this.serviceProviders = response.data.serviceProviders;
              this.providerServices = response.data.providerServices;
              console.log(response)

              setTimeout(() => {
                $("#ServiceProvidersTable").DataTable();
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
    
    
    