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
                      <h5 class="card-title">Seedling Sales <span>| Track every little sprout sold</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addSeedlingSale()"
                                >
                                  Add Seedling Sale
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
    
                      <table id="SeedlingSalesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Seedling</th>
                            <th scope="col">Buyer Name</th>
                            <th scope="col">Quantity Sold</th>
                            <th scope="col">Price per Unit (KES)</th>
                            <th scope="col">Total Amount (KES)</th>
                            <th scope="col">Sale Date</th>
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
                          <tr v-for="sale in seedlingSales" :key="sale.id">
                            
                            <!-- Seedling Name / Type -->
                            <td>
                              {{ sale.seedling.species_name }} ({{ sale.seedling.seedling_type }})
                            </td>

                            <!-- Buyer Name -->
                            <td>{{ sale.buyer_name }}</td>

                            <!-- Quantity Sold -->
                            <td>{{ sale.quantity_sold }}</td>

                            <!-- Price per Unit -->
                            <td>{{ sale.price_per_unit }}</td>

                            <!-- Total Amount -->
                            <td>{{ sale.total_amount }}</td>

                            <!-- Sale Date -->
                            <td>{{ sale.sale_date }}</td>

                            <!-- Actions -->
                            <td>
                              <div class="btn-group" role="group">
                                <button id="btnGroupDrop1" type="button"
                                        style="background-color: darkgreen; border-color: darkgreen;"
                                        class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                                        data-toggle="dropdown" data-bs-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                  Action
                                </button>
                                <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                  <a @click="viewSeedlingSale(sale)" class="dropdown-item" href="#">
                                    <i class="ri-eye-fill mr-2"></i>View
                                  </a> 
                                  <a @click="editSeedlingSale(sale)" class="dropdown-item" href="#">
                                    <i class="ri-pencil-fill mr-2"></i>Edit
                                  </a>
                                  <a @click="deleteSeedlingSale(sale.id)" class="dropdown-item" href="#">
                                    <i class="ri-delete-bin-line mr-2"></i>Delete
                                  </a>
                                </div>
                              </div>
                            </td>
                            
                          </tr>
                        </tbody>
                      </table>

    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Seedling Sale Modal -->
                <div class="modal fade" id="viewSeedlingSaleModal" tabindex="-1" aria-labelledby="viewSeedlingSaleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title">View Seedling Sale Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedSeedlingSale">
                        <div class="row g-3">

                          <!-- Seedling -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.seedling">
                            <strong>Seedling:</strong> <br>
                            {{ selectedSeedlingSale.seedling.species_name }} ({{ selectedSeedlingSale.seedling.seedling_type }})
                          </div>

                          <!-- Buyer Name -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.buyer_name">
                            <strong>Buyer Name:</strong> <br> {{ selectedSeedlingSale.buyer_name }}
                          </div>

                          <!-- Quantity Sold -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.quantity_sold !== null">
                            <strong>Quantity Sold:</strong> <br> {{ selectedSeedlingSale.quantity_sold }}
                          </div>

                          <!-- Price per Unit -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.price_per_unit !== null">
                            <strong>Price per Unit:</strong> <br> KES {{ selectedSeedlingSale.price_per_unit }}
                          </div>

                          <!-- Sale Date -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.sale_date">
                            <strong>Sale Date:</strong> <br> {{ selectedSeedlingSale.sale_date }}
                          </div>

                          <!-- Total Amount -->
                          <div class="col-md-6" v-if="selectedSeedlingSale.total_amount !== null">
                            <strong>Total Amount:</strong> <br> KES {{ selectedSeedlingSale.total_amount }}
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

                <!-- Add Seedling Sale Modal -->
                <div class="modal fade" id="AddSeedlingSaleModal" tabindex="-1" aria-labelledby="AddSeedlingSaleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="AddSeedlingSaleModalLabel">Add Seedling Sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Seedling Selection -->
                          <div class="col-md-6">
                            <label class="form-label">Seedling*</label>
                            <select class="form-select" id="seedling" v-model="data.seedling_id" required>
                              <option value="">Select Seedling</option>
                              <option v-for="seedling in seedlings" :key="seedling.id" :value="seedling.id">
                                {{ seedling.species_name }} ({{ seedling.seedling_type }})
                              </option>
                            </select>
                          </div>

                          <!-- Buyer Name -->
                          <div class="col-md-6">
                            <label class="form-label">Buyer Name*</label>
                            <input type="text" class="form-control" v-model="data.buyer_name" required>
                          </div>

                          <!-- Quantity Sold -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity Sold*</label>
                            <input type="number" min="1" class="form-control" v-model="data.quantity_sold" required>
                          </div>

                          <!-- Price per Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Price per Unit*</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="data.price_per_unit" required>
                          </div>

                          <!-- Sale Date -->
                          <div class="col-md-6">
                            <label class="form-label">Sale Date*</label>
                            <input type="date" class="form-control" v-model="data.sale_date" required>
                          </div>

                          <!-- Total Amount (optional, auto-calculated) -->
                          <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" :value="data.quantity_sold * data.price_per_unit" readonly>
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

                <!-- EDIT Seedling Sale MODAL -->
                <div class="modal fade" id="EditSeedlingSaleModal" tabindex="-1" aria-labelledby="EditSeedlingSaleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Seedling Sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- Seedling Selection -->
                          <div class="col-md-6">
                            <label class="form-label">Seedling*</label>
                            <select class="form-select" id="seedling_edit" v-model="form.seedling_id" required>
                              <option value="">Select Seedling</option>
                              <option v-for="seedling in seedlings" :key="seedling.id" :value="seedling.id">
                                {{ seedling.species_name }} ({{ seedling.seedling_type }})
                              </option>
                            </select>
                          </div>

                          <!-- Buyer Name -->
                          <div class="col-md-6">
                            <label class="form-label">Buyer Name*</label>
                            <input type="text" class="form-control" v-model="form.buyer_name" required>
                          </div>

                          <!-- Quantity Sold -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity Sold*</label>
                            <input type="number" min="1" class="form-control" v-model="form.quantity_sold" required>
                          </div>

                          <!-- Price per Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Price per Unit*</label>
                            <input type="number" min="0" step="0.01" class="form-control" v-model="form.price_per_unit" required>
                          </div>

                          <!-- Sale Date -->
                          <div class="col-md-6">
                            <label class="form-label">Sale Date*</label>
                            <input type="date" class="form-control" v-model="form.sale_date" required>
                          </div>

                          <!-- Total Amount (optional, auto-calculated) -->
                          <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" :value="form.quantity_sold * form.price_per_unit" readonly>
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
            seedlingSales: [],
            seedlings: [],
            selectedSeedlingSale: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD sale
              id: "",
              seedling_id: "",
              buyer_name: "",
              quantity_sold: "",
              price_per_unit: "",
              sale_date: "",
              total_amount: ""
            },

            form: {        // EDIT sale
              id: "",
              seedling_id: "",
              buyer_name: "",
              quantity_sold: "",
              price_per_unit: "",
              sale_date: "",
              total_amount: ""
            }
        }
      },      
      methods: {                
        viewSeedlingSale(item)
        {
          console.log(this.selectedSeedlingSale)
          this.selectedSeedlingSale = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewSeedlingSaleModal'));
          modal.show();
        },
        editSeedlingSale(item) {
        this.form = {
            id: item.id,
            seedling_id: item.seedling_id,
            buyer_name: item.buyer_name,
            quantity_sold: item.quantity_sold,
            price_per_unit: item.price_per_unit,
            sale_date: item.sale_date,
            total_amount: item.total_amount

        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditSeedlingSaleModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.seedling_id) {
            document.getElementById('seedling_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('seedling_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/seedling-sales/${this.form.id}`, this.form);

            toast.fire('Success!', 'Seedling sale updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditSeedlingSaleModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update sale',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addSeedlingSale()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddSeedlingSaleModal'));
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

        if (!this.data.seedling_id) {
            document.getElementById('seedling').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('seedling').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/seedling-sales', this.data);

            toast.fire('Success!', 'Seedling sale added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddSeedlingSaleModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              seedling_id: "",
              buyer_name: "",
              quantity_sold: "",
              price_per_unit: "",
              sale_date: "",
              total_amount: ""
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
        deleteSeedlingSale(id){
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
                  axios.delete('/api/seedling-sales/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Seedling sale has been deleted.',
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
          axios.get('/api/seedling-sales')
            .then((response) => {
              this.seedlingSales = response.data.seedlingsales;
              this.seedlings = response.data.seedlings;
              console.log(response)

              setTimeout(() => {
                $("#SeedlingSalesTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching sales list:', error);
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
    
    
    