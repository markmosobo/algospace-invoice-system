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
                      <h5 class="card-title">Farm Inputs <span>| Inputs bought for farm operations</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addFarmInput()"
                                >
                                  Add Farm Input
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
    
                      <table id="FarmInputsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Venture Name</th>
                            <th scope="col">Input Name</th>
                            <th scope="col">Type</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Date Applied</th>
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
                          <tr v-for="item in farminputs" :key="item.id">
                            <td>{{item.venture.venture_name}}</td>
                            <td>{{item.input_name ?? "N/A"}}</td>
                            <td>{{item.input_type ?? "N/A"}}</td>
                            <td>{{item.quantity ?? "N/A"}} {{ item.unit }}</td>
                            <td>{{item.date_applied ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewCustomer(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editFarmInput(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteFarmInput(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Farm Input Modal -->
              <div class="modal fade" id="viewFarmInputModal" tabindex="-1" aria-labelledby="viewFarmInputModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Farm Input Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedFarmInput">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedFarmInput.venture_id">
                          <strong>Venture Name:</strong> <br> {{ selectedFarmInput.venture.venture_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmInput.input_name">
                          <strong>Input Name:</strong> <br> {{ selectedFarmInput.input_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmInput.input_type">
                          <strong>Input Type:</strong> <br> {{ selectedFarmInput.input_type }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmInput.quantity">
                          <strong>Quantity:</strong> <br> {{ selectedFarmInput.quantity }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmInput.unit">
                          <strong>Unit:</strong> <br> {{ selectedFarmInput.unit }}
                        </div>

                        <div class="col-md-6" v-if="selectedFarmInput.date_applied">
                          <strong>Date Applied:</strong> <br> {{ selectedFarmInput.date_applied }}
                        </div>                        

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Farm Input Modal -->
                <div class="modal fade" id="AddFarmInputModal" tabindex="-1" aria-labelledby="AddFarmInputModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddFarmInputModalLabel">Add Farm Input</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture" v-model="data.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Input Name</label>
                            <input type="text" id="input_name" class="form-control" v-model="data.input_name" required>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Input Type</label>
                            <select class="form-select" v-model="data.input_type">
                              <option value="">Select</option>
                              <option value="fertilizer">Fertilizer</option>
                              <option value="seed">Seed</option>
                              <option value="chemical">Chemical</option>
                              <option value="feed">Feed</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="quantity" class="form-control" v-model="data.quantity">
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <input type="text" id="unit" placeholder="e.g bag, kgs" class="form-control" v-model="data.unit">
                          </div>                          

                          <!-- Date applied -->
                          <div class="col-md-6">
                            <label class="form-label">Date applied</label>
                            <input type="date" id="date_applied" class="form-control" v-model="data.date_applied">
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


                <!-- Edit Farm Input Modal -->
                <div class="modal fade" id="EditFarmInputModal" tabindex="-1" aria-labelledby="EditFarmInputModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Input</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture_edit" v-model="form.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Input Name</label>
                            <input type="text" id="input_name_edit" class="form-control" v-model="form.input_name" required>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Input Type</label>
                            <select class="form-select" v-model="form.input_type">
                              <option value="">Select</option>
                              <option value="fertilizer">Fertilizer</option>
                              <option value="seed">Seed</option>
                              <option value="chemical">Chemical</option>
                              <option value="feed">Feed</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="quantity" class="form-control" v-model="form.quantity">
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <input type="text" id="unit" placeholder="e.g bag, kgs" class="form-control" v-model="form.unit">
                          </div>                          

                          <!-- Date applied -->
                          <div class="col-md-6">
                            <label class="form-label">Date applied</label>
                            <input type="date" id="date_applied" class="form-control" v-model="form.date_applied">
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
            farminputs: [],
            farmventures: [],
            selectedFarmInput: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD farm input
              id: "",
              venture_id: "",
              input_name: "",
              input_type: "",
              quantity: "",
              unit: "",
              date_applied: ""
            },

            form: {        // EDIT farm input
              id: "",
              venture_id: "",
              input_name: "",
              input_type: "",
              quantity: "",
              unit: "",
              date_applied: ""
            }
        }
      },      
      methods: {                
        viewCustomer(item)
        {
          console.log(this.selectedFarmInput)
          this.selectedFarmInput = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewFarmInputModal'));
          modal.show();
        },
        editFarmInput(item) {
        this.form = {
            id: item.id,
            venture_id: item.venture_id,
            input_name: item.input_name,
            input_type: item.input_type,
            quantity: item.quantity,
            unit: item.unit,
            date_applied: item.date_applied,

        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditFarmInputModal')
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
            await axios.put(`/api/farm-inputs/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm input updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditFarmInputModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update farm input',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addFarmInput()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddFarmInputModal'));
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
            await axios.post('/api/farm-inputs', this.data);

            toast.fire('Success!', 'Farm input added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddFarmInputModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              venture_id: "",
              input_name: "",
              input_type: "",
              quantity: "",
              unit: "",
              date_applied: ""
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
        deleteFarmInput(id){
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
                  axios.delete('/api/farm-inputs/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm input has been deleted.',
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
          axios.get('/api/farm-inputs')
            .then((response) => {
              this.farminputs = response.data.farminputs;
              this.farmventures = response.data.farmventures;
              console.log(response)

              setTimeout(() => {
                $("#FarmInputsTable").DataTable();
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
    
    
    