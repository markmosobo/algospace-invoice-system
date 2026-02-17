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
                      <h5 class="card-title">Harvests <span>| Harvests realized from farm ventures</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addHarvest()"
                                >
                                  Add Harvest
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
    
                      <table id="HarvestsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Crop</th>
                            <th scope="col">Harvest Date</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Quality</th>
                            <th scope="col">Remarks</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>

                        <!-- Spinner while initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="7" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <!-- Harvest rows -->
                        <tbody v-else>
                          <tr v-for="harvest in harvests" :key="harvest.id">
                            <td>{{ harvest.crop.crop_name || 'N/A' }}</td>
                            <td>{{ harvest.harvest_date || 'N/A' }}</td>
                            <td>{{ harvest.quantity != null ? harvest.quantity : 'N/A' }}</td>
                            <td>{{ harvest.unit || 'N/A' }}</td>
                            <td>{{ harvest.quality_grade || 'N/A' }}</td>
                            <td>{{ harvest.remarks || 'N/A' }}</td>

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
                                  <li><a class="dropdown-item" @click="viewHarvest(harvest)" href="#"><i class="ri-eye-fill mr-2"></i>View</a></li>
                                  <li><a class="dropdown-item" @click="editHarvest(harvest)" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a></li>
                                  <li><a class="dropdown-item" @click="deleteHarvest(harvest.id)" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a></li>
                                </ul>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>

    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Harvest Modal -->
                <div class="modal fade" id="viewHarvestModal" tabindex="-1" aria-labelledby="viewHarvestModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title">View Harvest Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedHarvest">
                        <div class="row g-3">

                          <!-- Crop Name -->
                          <div class="col-md-6" v-if="selectedHarvest.crop_name">
                            <strong>Crop:</strong> <br> {{ selectedHarvest.crop.crop_name }}
                          </div>

                          <!-- Harvest Date -->
                          <div class="col-md-6" v-if="selectedHarvest.harvest_date">
                            <strong>Harvest Date:</strong> <br> {{ selectedHarvest.harvest_date }}
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6" v-if="selectedHarvest.quantity != null">
                            <strong>Quantity:</strong> <br> {{ selectedHarvest.quantity }}
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6" v-if="selectedHarvest.unit">
                            <strong>Unit:</strong> <br> {{ selectedHarvest.unit }}
                          </div>

                          <!-- Quality Grade -->
                          <div class="col-md-6" v-if="selectedHarvest.quality_grade">
                            <strong>Quality Grade:</strong> <br> {{ selectedHarvest.quality_grade }}
                          </div>

                          <!-- Remarks -->
                          <div class="col-md-6" v-if="selectedHarvest.remarks">
                            <strong>Remarks:</strong> <br> {{ selectedHarvest.remarks }}
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

                <!-- Add Harvest Modal -->
                <div class="modal fade" id="addHarvestModal" tabindex="-1" aria-labelledby="addHarvestModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="addHarvestModalLabel">Add Harvest</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Crop -->
                          <div class="col-md-6">
                            <label class="form-label">Crop*</label>
                            <select class="form-select" id="crop" v-model="data.crop_id" required>
                              <option value="">Select Crop</option>
                              <option v-for="crop in crops" :key="crop.id" :value="crop.id">
                                {{ crop.crop_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Harvest Date -->
                          <div class="col-md-6">
                            <label class="form-label">Harvest Date*</label>
                            <input type="date" class="form-control" v-model="data.harvest_date" required>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity*</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="data.quantity" required>
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit*</label>
                            <input type="text" class="form-control" v-model="data.unit" placeholder="e.g., kg, bags" required>
                          </div>

                          <!-- Quality Grade -->
                          <div class="col-md-6">
                            <label class="form-label">Quality Grade</label>
                            <select class="form-select" v-model="data.quality_grade">
                              <option value="">Select Grade</option>
                              <option value="high">High</option>
                              <option value="medium">Medium</option>
                              <option value="low">Low</option>
                            </select>
                          </div>


                          <!-- Remarks -->
                          <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" v-model="data.remarks" rows="1" placeholder="Optional"></textarea>
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


                <!-- EDIT Harvest MODAL -->
                <div class="modal fade" id="EditHarvestModal" tabindex="-1" aria-labelledby="EditHarvestModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Harvest</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- Crop -->
                          <div class="col-md-6">
                            <label class="form-label">Crop*</label>
                            <select class="form-select" id="crop_edit" v-model="form.crop_id" required>
                              <option value="">Select Crop</option>
                              <option v-for="crop in crops" :key="crop.id" :value="crop.id">
                                {{ crop.crop_name }}
                              </option>
                            </select>
                          </div>

                          <!-- Harvest Date -->
                          <div class="col-md-6">
                            <label class="form-label">Harvest Date*</label>
                            <input type="date" class="form-control" v-model="form.harvest_date" required>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity*</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="form.quantity" required>
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit*</label>
                            <input type="text" class="form-control" v-model="form.unit" placeholder="e.g., kg, bags" required>
                          </div>

                          <!-- Quality Grade -->
                          <div class="col-md-6">
                            <label class="form-label">Quality Grade</label>
                            <select class="form-select" v-model="form.quality_grade">
                              <option value="">Select Grade</option>
                              <option value="high">High</option>
                              <option value="medium">Medium</option>
                              <option value="low">Low</option>
                            </select>
                          </div>

                          <!-- Remarks -->
                          <div class="col-md-6">
                            <label class="form-label">Remarks</label>
                            <textarea class="form-control" v-model="form.remarks" rows="1" placeholder="Optional"></textarea>
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
            harvests: [],
            crops: [],
            selectedHarvest: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD harvest
              id: "",
              crop_id: "",
              harvest_date: "",
              quantity: "",
              unit: "",
              quality_grade: "",
              remarks: ""
            },

            form: {        // EDIT harvest
              id: "",
              crop_id: "",
              harvest_date: "",
              quantity: "",
              unit: "",
              quality_grade: "",
              remarks: ""
            }
        }
      },      
      methods: {                
        viewHarvest(item)
        {
          console.log(this.selectedHarvest)
          this.selectedHarvest = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewHarvestModal'));
          modal.show();
        },
        editHarvest(item) {
        this.form = {
            id: item.id,
            crop_id: item.crop_id,
            harvest_date: item.harvest_date,
            quantity: item.quantity,
            unit: item.unit,
            quality_grade: item.quality_grade,
            remarks: item.remarks
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditHarvestModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.crop_id) {
            document.getElementById('crop_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('crop_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/harvests/${this.form.id}`, this.form);

            toast.fire('Success!', 'Harvest updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditHarvestModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update harvest',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addHarvest()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addHarvestModal'));
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

        if (!this.data.crop_id) {
            document.getElementById('crop').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('crop').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/harvests', this.data);

            toast.fire('Success!', 'Harvest added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addHarvestModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              crop_id: "",
              harvest_date: "",
              quantity: "",
              unit: "",
              quality_grade: "",
              remarks: ""
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
        deleteHarvest(id){
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
                  axios.delete('/api/harvests/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Harvest has been deleted.',
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
          axios.get('/api/harvests')
            .then((response) => {
              this.harvests = response.data.harvests;
              this.crops = response.data.crops;
              console.log(response)

              setTimeout(() => {
                $("#HarvestsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching harvests list:', error);
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
    
    
    