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
                      <h5 class="card-title">Farm Ventures <span>| Activities undertaken on farms</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addVenture()"
                                >
                                  Add Venture
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
    
                      <table id="VenturesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Venture Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Farm</th>
                            <th scope="col">Status</th>
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
                          <tr v-for="item in farmventures" :key="item.id">
                            <td>{{item.venture_name}}</td>
                            <td>{{item.venture_type ?? "N/A"}}</td>
                            <td>{{item.farm.name ?? "N/A"}}</td>
                            <td>{{item.status ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewFarmVenture(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editFarmVenture(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteFarmVenture(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Farm Venture Modal -->
              <div class="modal fade" id="viewFarmVentureModal" tabindex="-1" aria-labelledby="viewFarmVentureModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Farm Venture</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedFarmVenture">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedFarmVenture.venture_name">
                          <strong>Venture Name:</strong> <br> {{ selectedFarmVenture.venture_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmVenture.venture_type">
                          <strong>Type:</strong> <br> {{ selectedFarmVenture.venture_type }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmVenture.farm_id">
                          <strong>Farm:</strong> <br> {{ selectedFarmVenture.farm.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmVenture.notes">
                          <strong>Notes:</strong> <br> {{ selectedFarmVenture.notes }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Farm Venture Modal -->
                <div class="modal fade" id="addVentureModal" tabindex="-1" aria-labelledby="addVentureModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addVentureModalLabel">Add Farm Venture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Venture Name*</label>
                            <input type="text" id="venture_name" class="form-control" v-model="data.venture_name" required>
                          </div>

                          <!-- Type -->
                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" v-model="data.venture_type">
                                <option value="crop">Crop</option>
                                <option value="livestock">Livestock</option>
                                <option value="nursery">Nursery</option>
                                <option value="mixed">Mixed</option>
                                  <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Farm -->
                          <div class="col-md-6">
                            <label class="form-label">Farm</label>
                            <select class="form-select" v-model="data.farm_id">
                            <option value="">-- Search & Select --</option>
                            <option v-for="c in farms" :key="c.id" :value="c.id">
                                {{ c.name }} ({{ c.size || 'No Size' }})
                            </option>
                            </select>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="data.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="paused">Paused</option>
                              <option value="closed">Closed</option>
                            </select>
                          </div>

                          <!-- Notes -->
                          <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <textarea id="notes" class="form-control" v-model="data.notes" required />
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


                <!-- EDIT Venture MODAL -->
                <div class="modal fade" id="editFarmVentureModal" tabindex="-1" aria-labelledby="editFarmVentureModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Venture</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Venture Name*</label>
                            <input type="text" id="venture_name_edit" class="form-control" v-model="form.venture_name" required>
                          </div>

                          <!-- Type -->
                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="venture_edit" v-model="form.venture_type">
                                <option value="crop">Crop</option>
                                <option value="livestock">Livestock</option>
                                <option value="nursery">Nursery</option>
                                <option value="mixed">Mixed</option>
                                  <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Farm -->
                          <div class="col-md-6">
                            <label class="form-label">Farm</label>
                            <select class="form-select" id="farm_edit" v-model="form.farm_id">
                            <option value="">-- Search & Select --</option>
                            <option v-for="c in farms" :key="c.id" :value="c.id">
                                {{ c.name }} ({{ c.size || 'No Size' }})
                            </option>
                            </select>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="status_edit" v-model="form.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="paused">Paused</option>
                              <option value="closed">Closed</option>
                            </select>
                          </div>

                          <!-- Notes -->
                          <div class="col-md-6">
                            <label class="form-label">Notes</label>
                            <textarea id="notes_edit" class="form-control" v-model="form.notes" required />
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
            farmventures: [],
            farms: [],
            selectedFarmVenture: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD venture
              id: "",
              venture_name: "",
              venture_type: "",
              status: "active",
              notes: "",
              farm_id: ''
            },

            form: {        // EDIT venture
              id: "",
              venture_name: "",
              venture_type: "",
              status: "active",
              notes: "",
              farm_id: ''
            }
        }
      },      
      methods: {                
        viewFarmVenture(item)
        {
          console.log(this.selectedFarmVenture)
          this.selectedFarmVenture = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewFarmVentureModal'));
          modal.show();
        },
        editFarmVenture(item) {
        this.form = {
            id: item.id,
            venture_name: item.venture_name,
            venture_type: item.venture_type,
            status: item.status,
            notes: item.notes,
            farm_id: item.farm_id
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editFarmVentureModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.venture_name) {
            document.getElementById('venture_name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('venture_name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/farm-ventures/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm venture updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editFarmVentureModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm venture',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addVenture()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addVentureModal'));
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

        if (!this.data.venture_name) {
            document.getElementById('venture_name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('venture_name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/farm-ventures', this.data);

            toast.fire('Success!', 'Farm venture added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addVentureModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              venture_name: "",
              venture_type: "",
              status: "active",
              notes: "",
              farm_id: ''
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
        deleteFarmVenture(id){
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
                  axios.delete('/api/farm-ventures/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm venture has been deleted.',
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
          axios.get('/api/farm-ventures')
            .then((response) => {
              this.farmventures = response.data.farmventures;
              this.farms = response.data.farms;
              console.log(response)

              setTimeout(() => {
                $("#VenturesTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching ventures list:', error);
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
    
    
    