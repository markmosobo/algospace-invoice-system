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
                      <h5 class="card-title">
                        Seedlings
                        <span class="text-muted">| Track seedling types, quantities, and growth details</span>
                      </h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addSeedling()"
                                >
                                  Add Seedling
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
    
                      <table id="SeedlingsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Venture</th>
                            <th scope="col">Seedling Type</th>
                            <th scope="col">Species Name</th>
                            <th scope="col">Date Planted</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Expected Ready</th>
                            <th scope="col">Survival Rate (%)</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>

                        <!-- Spinner shown while data is initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="8" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <tbody v-else>
                          <tr v-for="seedling in seedlings" :key="seedling.id">
                            <td>{{ seedling.venture?.venture_name ?? "N/A" }}</td>
                            <td>{{ seedling.seedling_type }}</td>
                            <td>{{ seedling.species_name }}</td>
                            <td>{{ seedling.date_planted }}</td>
                            <td>{{ seedling.quantity }}</td>
                            <td>{{ seedling.expected_ready_date ?? "N/A" }}</td>
                            <td>{{ seedling.survival_rate !== null ? seedling.survival_rate + '%' : "N/A" }}</td>

                            <td>
                              <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                  <a @click="viewSeedling(seedling)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>
                                  <a @click="editSeedling(seedling)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteSeedling(seedling.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>

    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Seedling Modal -->
                <div class="modal fade" id="viewSeedlingModal" tabindex="-1" aria-labelledby="viewSeedlingModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title">View Seedling Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedSeedling">
                        <div class="row g-3">

                          <!-- Venture -->
                          <div class="col-md-6" v-if="selectedSeedling.venture">
                            <strong>Venture:</strong> <br> {{ selectedSeedling.venture.venture_name }}
                          </div>

                          <!-- Seedling Type -->
                          <div class="col-md-6" v-if="selectedSeedling.seedling_type">
                            <strong>Seedling Type:</strong> <br> {{ selectedSeedling.seedling_type }}
                          </div>

                          <!-- Species Name -->
                          <div class="col-md-6" v-if="selectedSeedling.species_name">
                            <strong>Species Name:</strong> <br> {{ selectedSeedling.species_name }}
                          </div>

                          <!-- Date Planted -->
                          <div class="col-md-6" v-if="selectedSeedling.date_planted">
                            <strong>Date Planted:</strong> <br> {{ selectedSeedling.date_planted }}
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6" v-if="selectedSeedling.quantity">
                            <strong>Quantity:</strong> <br> {{ selectedSeedling.quantity }}
                          </div>

                          <!-- Expected Ready Date -->
                          <div class="col-md-6" v-if="selectedSeedling.expected_ready_date">
                            <strong>Expected Ready Date:</strong> <br> {{ selectedSeedling.expected_ready_date }}
                          </div>

                          <!-- Survival Rate -->
                          <div class="col-md-6" v-if="selectedSeedling.survival_rate !== null">
                            <strong>Survival Rate:</strong> <br> {{ selectedSeedling.survival_rate }}%
                          </div>

                        </div>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Add Seedling Modal -->
                <div class="modal fade" id="AddSeedlingModal" tabindex="-1" aria-labelledby="AddSeedlingModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="AddSeedlingModalLabel">Add Seedling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Venture -->
                          <div class="col-md-6">
                            <label class="form-label">Venture*</label>
                            <select class="form-select" id="venture" v-model="data.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Seedling Type -->
                          <div class="col-md-6">
                            <label class="form-label">Seedling Type*</label>
                            <input type="text" class="form-control" v-model="data.seedling_type" placeholder="e.g., tree, coffee" required>
                          </div>

                          <!-- Species Name -->
                          <div class="col-md-6">
                            <label class="form-label">Species Name*</label>
                            <input type="text" class="form-control" v-model="data.species_name" placeholder="e.g., Arabica" required>
                          </div>

                          <!-- Date Planted -->
                          <div class="col-md-6">
                            <label class="form-label">Date Planted*</label>
                            <input type="date" class="form-control" v-model="data.date_planted" required>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity*</label>
                            <input type="number" min="1" step="1" class="form-control" v-model="data.quantity" required>
                          </div>

                          <!-- Expected Ready Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expected Ready Date</label>
                            <input type="date" class="form-control" v-model="data.expected_ready_date">
                          </div>

                          <!-- Survival Rate -->
                          <div class="col-md-6">
                            <label class="form-label">Survival Rate</label>
                            <select class="form-select" v-model="data.survival_rate">
                              <option value="">Select</option>
                              <option value="high">High</option>
                              <option value="medium">Medium</option>
                              <option value="low">Low</option>
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

                <!-- EDIT Seedling MODAL -->
                <div class="modal fade" id="EditSeedlingModal" tabindex="-1" aria-labelledby="EditSeedlingModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Seedling</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- Venture -->
                          <div class="col-md-6">
                            <label class="form-label">Venture*</label>
                            <select class="form-select" id="venture_edit" v-model="form.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Seedling Type -->
                          <div class="col-md-6">
                            <label class="form-label">Seedling Type*</label>
                            <input type="text" class="form-control" v-model="form.seedling_type" placeholder="e.g., tree, coffee" required>
                          </div>

                          <!-- Species Name -->
                          <div class="col-md-6">
                            <label class="form-label">Species Name*</label>
                            <input type="text" class="form-control" v-model="form.species_name" placeholder="e.g., Arabica" required>
                          </div>

                          <!-- Date Planted -->
                          <div class="col-md-6">
                            <label class="form-label">Date Planted*</label>
                            <input type="date" class="form-control" v-model="form.date_planted" required>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity*</label>
                            <input type="number" min="1" step="1" class="form-control" v-model="form.quantity" required>
                          </div>

                          <!-- Expected Ready Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expected Ready Date</label>
                            <input type="date" class="form-control" v-model="form.expected_ready_date">
                          </div>

                          <!-- Survival Rate -->
                          <div class="col-md-6">
                            <label class="form-label">Survival Rate</label>
                            <select class="form-select" v-model="form.survival_rate">
                              <option value="">Select</option>
                              <option value="high">High</option>
                              <option value="medium">Medium</option>
                              <option value="low">Low</option>
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
            seedlings: [],
            farmventures: [],
            selectedSeedling: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD seedling
              venture_id: "",
              seedling_type: "",
              species_name: "",
              date_planted: "",
              quantity: "",
              expected_ready_date: "",
              survival_rate: ""
            },

            form: {        // EDIT seedling
              venture_id: "",
              seedling_type: "",
              species_name: "",
              date_planted: "",
              quantity: "",
              expected_ready_date: "",
              survival_rate: ""
            }
        }
      },      
      methods: {                
        viewSeedling(item)
        {
          console.log(this.selectedSeedling)
          this.selectedSeedling = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewSeedlingModal'));
          modal.show();
        },
        editSeedling(item) {
        this.form = {
            id: item.id,
            venture_id: item.venture_id,
            seedling_type: item.seedling_type,
            species_name: item.species_name,
            date_planted: item.date_planted,
            quantity: item.quantity,
            expected_ready_date: item.expected_ready_date,
            survival_rate: item.survival_rate
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditSeedlingModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.venture_id) {
            document.getElementById('venture_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('venture_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/seedlings/${this.form.id}`, this.form);

            toast.fire('Success!', 'Seedling updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditSeedlingModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update seedling',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addSeedling()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddSeedlingModal'));
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

        if (!this.data.venture_id) {
            document.getElementById('venture').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('venture').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/seedlings', this.data);

            toast.fire('Success!', 'Seedling added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddSeedlingModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              venture_id: "",
              seedling_type: "",
              species_name: "",
              date_planted: "",
              quantity: "",
              expected_ready_date: "",
              survival_rate: ""
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
        deleteSeedling(id){
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
                  axios.delete('/api/seedlings/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Seedling has been deleted.',
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
          axios.get('/api/seedlings')
            .then((response) => {
              this.seedlings = response.data.seedlings;
              this.farmventures = response.data.farmventures;
              console.log(response)

              setTimeout(() => {
                $("#SeedlingsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching seedlings list:', error);
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
    
    
    