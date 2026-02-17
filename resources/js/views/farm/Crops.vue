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
                      <h5 class="card-title">Crops <span>| Crops involved in farm ventures</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addCrop()"
                                >
                                  Add Crop
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
    
                      <table id="CropsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Crop Name</th>
                            <th scope="col">Variety</th>
                            <th scope="col">Venture</th>
                            <th scope="col">Planting Date</th>
                            <th scope="col">Expected Harvest</th>
                            <th scope="col">Acreage (ha)</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>

                        <!-- Spinner while initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="8" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <!-- Crop rows -->
                        <tbody v-else>
                          <tr v-for="item in crops" :key="item.id">
                            <td>{{ item.crop_name }}</td>
                            <td>{{ item.variety || 'N/A' }}</td>
                            <td>{{ item.venture_name || 'N/A' }}</td>
                            <td>{{ item.planting_date || 'N/A' }}</td>
                            <td>{{ item.expected_harvest_date || 'N/A' }}</td>
                            <td>{{ item.acreage != null ? item.acreage : 'N/A' }}</td>
                            <td>
                              <span 
                                class="badge" 
                                :class="{
                                  'bg-success': item.status === 'active',
                                  'bg-warning text-dark': item.status === 'dormant',
                                  'bg-secondary': item.status === 'inactive'
                                }">
                                {{ item.status || 'N/A' }}
                              </span>
                            </td>

                            <td>
                              <div class="btn-group" role="group">
                                <button 
                                  id="btnGroupDrop1" 
                                  type="button" 
                                  class="btn btn-sm btn-success rounded-pill dropdown-toggle" 
                                  data-bs-toggle="dropdown" 
                                  aria-expanded="false">
                                  Action
                                </button>
                                <ul class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                  <li><a class="dropdown-item" @click="viewCrop(item)" href="#"><i class="ri-eye-fill mr-2"></i>View</a></li>
                                  <li><a class="dropdown-item" @click="editCrop(item)" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a></li>
                                  <li><a class="dropdown-item" @click="deleteCrop(item.id)" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a></li>
                                </ul>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>

    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Crop Modal -->
                <div class="modal fade" id="viewCropModal" tabindex="-1" aria-labelledby="viewCropModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title">View Crop Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedCrop">
                        <div class="row g-3">

                          <!-- Venture -->
                          <div class="col-md-6" v-if="selectedCrop.venture_name">
                            <strong>Venture:</strong> <br> {{ selectedCrop.venture_name }}
                          </div>

                          <!-- Crop Name -->
                          <div class="col-md-6" v-if="selectedCrop.crop_name">
                            <strong>Crop Name:</strong> <br> {{ selectedCrop.crop_name }}
                          </div>

                          <!-- Variety -->
                          <div class="col-md-6" v-if="selectedCrop.variety">
                            <strong>Variety:</strong> <br> {{ selectedCrop.variety }}
                          </div>

                          <!-- Planting Date -->
                          <div class="col-md-6" v-if="selectedCrop.planting_date">
                            <strong>Planting Date:</strong> <br> {{ selectedCrop.planting_date }}
                          </div>

                          <!-- Expected Harvest Date -->
                          <div class="col-md-6" v-if="selectedCrop.expected_harvest_date">
                            <strong>Expected Harvest Date:</strong> <br> {{ selectedCrop.expected_harvest_date }}
                          </div>

                          <!-- Acreage -->
                          <div class="col-md-6" v-if="selectedCrop.acreage">
                            <strong>Acreage (ha):</strong> <br> {{ selectedCrop.acreage }}
                          </div>

                          <!-- Status -->
                          <div class="col-md-6" v-if="selectedCrop.status">
                            <strong>Status:</strong> <br> {{ selectedCrop.status }}
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

                <!-- Add Crop Modal -->
                <div class="modal fade" id="AddCropModal" tabindex="-1" aria-labelledby="AddCropModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="AddCropModalLabel">Add Crop</h5>
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
                            <select class="form-select" v-model="data.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Crop Name -->
                          <div class="col-md-6">
                            <label class="form-label">Crop Name*</label>
                            <input type="text" id="crop_name" class="form-control" v-model="data.crop_name" required>
                          </div>

                          <!-- Variety -->
                          <div class="col-md-6">
                            <label class="form-label">Variety</label>
                            <input type="text" class="form-control" v-model="data.variety">
                          </div>

                          <!-- Planting Date -->
                          <div class="col-md-6">
                            <label class="form-label">Planting Date</label>
                            <input type="date" class="form-control" v-model="data.planting_date">
                          </div>

                          <!-- Expected Harvest Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expected Harvest Date</label>
                            <input type="date" class="form-control" v-model="data.expected_harvest_date">
                          </div>

                          <!-- Acreage -->
                          <div class="col-md-6">
                            <label class="form-label">Acreage (ha)</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="data.acreage">
                          </div>

                          <!-- Status -->
                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="data.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="inactive">Inactive</option>
                              <option value="dormant">Dormant</option>
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

                <!-- EDIT Crop MODAL -->
                <div class="modal fade" id="EditCropModal" tabindex="-1" aria-labelledby="EditCropModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Crop</h5>
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
                            <select class="form-select" v-model="form.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Crop Name -->
                          <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" id="crop_name_edit" class="form-control" v-model="form.crop_name" required>
                          </div>

                          <!-- Variety -->
                          <div class="col-md-6">
                            <label class="form-label">Variety</label>
                            <input type="text" class="form-control" v-model="form.variety">
                          </div>

                          <!-- Planting Date -->
                          <div class="col-md-6">
                            <label class="form-label">Planting Date</label>
                            <input type="date" class="form-control" v-model="form.planting_date">
                          </div>

                          <!-- Expected Harvest Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expected Harvest Date</label>
                            <input type="date" class="form-control" v-model="form.expected_harvest_date">
                          </div>

                          <!-- Acreage -->
                          <div class="col-md-6">
                            <label class="form-label">Acreage (ha)</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="form.acreage">
                          </div>

                          <!-- Status -->
                          <div class="col-md-6">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="form.status">
                              <option value="">Select</option>
                              <option value="active">Active</option>
                              <option value="inactive">Inactive</option>
                              <option value="dormant">Dormant</option>
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
        timer: 3000,
        timerProgressBar: true
    });
    
    window.toast = toast;
    
    export default {
      data() {
        return {
            crops: [],
            farmventures: [],
            selectedCrop: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD crop
              id: "",
              crop_name: "",
              variety: "",
              planting_date: "",
              expected_harvest_date: "",
              acreage: "",
              status: "active",
              venture_id: ""
            },

            form: {        // EDIT crop
              id: "",
              crop_name: "",
              variety: "",
              planting_date: "",
              expected_harvest_date: "",
              acreage: "",
              status: "",
              venture_id: ""
            }
        }
      },      
      methods: {                
        viewCrop(item)
        {
          console.log(this.selectedCrop)
          this.selectedCrop = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewCropModal'));
          modal.show();
        },
        editCrop(item) {
        this.form = {
            id: item.id,
            crop_name: item.crop_name,
            variety: item.variety,
            planting_date: item.planting_date,
            expected_harvest_date: item.expected_harvest_date,
            acreage: item.acreage,
            status: item.status,
            venture_id: item.venture_id
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditCropModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.crop_name) {
            document.getElementById('crop_name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('crop_name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/crops/${this.form.id}`, this.form);

            toast.fire('Success!', 'Crop updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditCropModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update crop',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addCrop()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddCropModal'));
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

        if (!this.data.crop_name) {
            document.getElementById('crop_name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('crop_name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/crops', this.data);

            toast.fire('Success!', 'Crop added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddCropModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              crop_name: "",
              variety: "",
              planting_date: "",
              expected_harvest_date: "",
              acreage: "",
              status: "",
              venture_id: ""
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
        deleteCrop(id){
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
                  axios.delete('/api/crops/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Crop has been deleted.',
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
          axios.get('/api/crops')
            .then((response) => {
              this.crops = response.data.crops;
              this.farmventures = response.data.farmventures;
              console.log(response)

              setTimeout(() => {
                $("#CropsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching crops list:', error);
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
    
    
    