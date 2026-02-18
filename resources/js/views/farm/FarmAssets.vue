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
                        Farm Assets <span>| Machinery, tools & equipment</span>
                      </h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addFarmAsset()"
                                >
                                  Add Farm Asset
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
    
                      <table id="FarmAssetsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Asset Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Cost</th>
                            <th scope="col">Condition</th>
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
                          <tr v-for="item in farmassets" :key="item.id">
                            <td>{{item.asset_name}}</td>
                            <td>{{item.asset_type ?? "N/A"}}</td>
                            <td>{{item.cost ?? "N/A"}}</td>
                            <td>{{item.condition ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewFarmAsset(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editFarmAsset(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteFarmAsset(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Farm Asset Modal -->
                <div class="modal fade" id="viewFarmAssetModal" tabindex="-1"
                    aria-labelledby="viewFarmAssetModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="viewFarmAssetModalLabel">
                          View Farm Asset
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedFarmAsset">

                        <div class="row g-3">

                          <!-- Asset Name -->
                          <div class="col-md-6" v-if="selectedFarmAsset.asset_name">
                            <strong>Asset Name:</strong><br>
                            {{ selectedFarmAsset.asset_name }}
                          </div>

                          <!-- Asset Type -->
                          <div class="col-md-6" v-if="selectedFarmAsset.asset_type">
                            <strong>Asset Type:</strong><br>
                            {{ selectedFarmAsset.asset_type }}
                          </div>

                          <!-- Purchase Date -->
                          <div class="col-md-6" v-if="selectedFarmAsset.purchase_date">
                            <strong>Purchase Date:</strong><br>
                            {{ selectedFarmAsset.purchase_date }}
                          </div>

                          <!-- Cost -->
                          <div class="col-md-6" v-if="selectedFarmAsset.cost">
                            <strong>Cost (KES):</strong><br>
                            {{ selectedFarmAsset.cost }}
                          </div>

                          <!-- Condition -->
                          <div class="col-md-6" v-if="selectedFarmAsset.condition">
                            <strong>Condition:</strong><br>
                            {{ selectedFarmAsset.condition }}
                          </div>

                          <!-- Notes -->
                          <div class="col-md-12" v-if="selectedFarmAsset.notes">
                            <strong>Notes:</strong><br>
                            {{ selectedFarmAsset.notes }}
                          </div>

                        </div>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                          Close
                        </button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Add Farm Asset Modal -->
                <div class="modal fade" id="AddFarmAssetModal" tabindex="-1"
                    aria-labelledby="AddFarmAssetModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="AddFarmAssetModalLabel">Add Farm Asset</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID (for edit) -->
                          <input type="hidden" v-model="data.id">

                          <!-- Asset Name -->
                          <div class="col-md-6">
                            <label class="form-label">Asset Name *</label>
                            <input type="text"
                                  class="form-control"
                                  id="asset_name"
                                  v-model="data.asset_name"
                                  required>
                          </div>
                          
                          <!-- Asset Type -->
                          <div class="col-md-6">
                            <label class="form-label">Asset Type *</label>
                            <select class="form-select"
                                    v-model="data.asset_type"
                                    required>
                              <option disabled value="">Select asset type</option>
                              <option value="tool">Tool</option>
                              <option value="machine">Machine</option>
                              <option value="building">Building</option>
                              <option value="vehicle">Vehicle</option>
                              <option value="other">Other</option>
                            </select>
                          </div>


                          <!-- Purchase Date -->
                          <div class="col-md-6">
                            <label class="form-label">Purchase Date</label>
                            <input type="date"
                                  class="form-control"
                                  v-model="data.purchase_date">
                          </div>

                          <!-- Cost -->
                          <div class="col-md-6">
                            <label class="form-label">Cost (KES)</label>
                            <input type="number"
                                  class="form-control"
                                  v-model="data.cost"
                                  min="0"
                                  step="0.01">
                          </div>

                          <!-- Condition -->
                          <div class="col-md-6">
                            <label class="form-label">Condition</label>
                            <select class="form-select" v-model="data.condition">
                              <option value="">Select condition</option>
                              <option value="new">New</option>
                              <option value="good">Good</option>
                              <option value="fair">Fair</option>
                              <option value="poor">Poor</option>
                              <option value="needs_repair">Needs Repair</option>
                            </select>
                          </div>

                          <!-- Notes -->
                          <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control"
                                      rows="3"
                                      v-model="data.notes"
                                      placeholder="Additional details about the asset"></textarea>
                          </div>

                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                          Close
                        </button>
                        <button class="btn btn-success"
                                @click="submit"
                                style="background: darkgreen; border-color: darkgreen;">
                          Save Asset
                        </button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- EDIT Farm Asset MODAL -->
                <div class="modal fade" id="EditFarmAssetModal" tabindex="-1" aria-labelledby="EditFarmAssetModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID (for edit) -->
                          <input type="hidden" v-model="data.id">

                          <!-- Asset Name -->
                          <div class="col-md-6">
                            <label class="form-label">Asset Name *</label>
                            <input type="text"
                                  class="form-control"
                                  id="asset_name_edit"
                                  v-model="form.asset_name"
                                  required>
                          </div>

                          <!-- Asset Type -->
                          <div class="col-md-6">
                            <label class="form-label">Asset Type *</label>
                            <select class="form-select"
                                    v-model="form.asset_type"
                                    required>
                              <option disabled value="">Select asset type</option>
                              <option value="tool">Tool</option>
                              <option value="machine">Machine</option>
                              <option value="building">Building</option>
                              <option value="vehicle">Vehicle</option>
                              <option value="other">Other</option>
                            </select>
                          </div>


                          <!-- Purchase Date -->
                          <div class="col-md-6">
                            <label class="form-label">Purchase Date</label>
                            <input type="date"
                                  class="form-control"
                                  v-model="form.purchase_date">
                          </div>

                          <!-- Cost -->
                          <div class="col-md-6">
                            <label class="form-label">Cost (KES)</label>
                            <input type="number"
                                  class="form-control"
                                  v-model="form.cost"
                                  min="0"
                                  step="0.01">
                          </div>

                          <!-- Condition -->
                          <div class="col-md-6">
                            <label class="form-label">Condition</label>
                            <select class="form-select" v-model="form.condition">
                              <option value="">Select condition</option>
                              <option value="new">New</option>
                              <option value="good">Good</option>
                              <option value="fair">Fair</option>
                              <option value="poor">Poor</option>
                              <option value="needs_repair">Needs Repair</option>
                            </select>
                          </div>

                          <!-- Notes -->
                          <div class="col-md-12">
                            <label class="form-label">Notes</label>
                            <textarea class="form-control"
                                      rows="3"
                                      v-model="form.notes"
                                      placeholder="Additional details about the asset"></textarea>
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
            farmassets: [],
            selectedFarmAsset: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD farm asset
              id: "",
              asset_name: "",
              asset_type: "",
              purchase_date: "",
              cost: "",
              condition: "",
              notes: ""
            },

            form: {        // EDIT asset
              id: "",
              asset_name: "",
              asset_type: "",
              purchase_date: "",
              cost: "",
              condition: "",
              notes: ""
            }
        }
      },      
      methods: {                
        viewFarmAsset(item)
        {
          console.log(this.selectedFarmAsset)
          this.selectedFarmAsset = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewFarmAssetModal'));
          modal.show();
        },
        editFarmAsset(item) {
        this.form = {
            id: item.id,
            asset_name: item.asset_name,
            asset_type: item.asset_type,
            purchase_date: item.purchase_date,
            cost: item.cost,
            condition: item.condition,
            notes: item.notes
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditFarmAssetModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.asset_name) {
            document.getElementById('asset_name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('asset_name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/farm-assets/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm asset updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditFarmAssetModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm asset',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addFarmAsset()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddFarmAssetModal'));
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

        if (!this.data.asset_name) {
            document.getElementById('asset_name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('asset_name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/farm-assets', this.data);

            toast.fire('Success!', 'Farm asset added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddFarmAssetModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              asset_name: "",
              asset_type: "",
              purchase_date: "",
              cost: "",
              condition: "",
              notes: ""
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
        deleteFarmAsset(id){
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
                  axios.delete('/api/farm-assets/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm asset has been deleted.',
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
          axios.get('/api/farm-assets')
            .then((response) => {
              this.farmassets = response.data;
              console.log(response)

              setTimeout(() => {
                $("#FarmAssetsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching assets list:', error);
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
    
    
    