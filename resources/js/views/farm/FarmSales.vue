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
                      <h5 class="card-title">Farm Sales <span>| Transactions that have generated revenue from the farm</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addSale()"
                                >
                                  Add Farm Sale
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
    
                      <table id="FarmSalesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Venture Name</th>
                            <th scope="col">Product Name</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Buyer</th>
                            <th scope="col">Sale Date</th>
                            <th scope="col">Total Amount</th>
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
                          <tr v-for="item in farmsales" :key="item.id">
                            <td>{{item.venture.venture_name}}</td>
                            <td>{{item.product_name ?? "N/A"}}</td>
                            <td>{{item.quantity ?? "N/A"}} {{item.unit ?? "N/A"}}</td>
                            <td>{{item.buyer ?? "N/A"}}</td>
                            <td>{{item.sale_date ?? "N/A"}}</td>
                            <td>{{item.total_amount ?? "N/A"}}</td>
                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewSale(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editSale(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteSale(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Sale Modal -->
              <div class="modal fade" id="viewSaleModal" tabindex="-1" aria-labelledby="viewSaleModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Farm Sale</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedSale">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedSale.venture_id">
                          <strong>Venture Name:</strong> <br> {{ selectedSale.venture.venture_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedSale.product_name">
                          <strong>Product Name:</strong> <br> {{ selectedSale.product_name }}
                        </div>

                        <div class="col-md-6" v-if="selectedSale.quantity">
                          <strong>Quantity:</strong> <br> {{ selectedSale.quantity }}
                        </div>

                        <div class="col-md-6" v-if="selectedSale.unit">
                          <strong>Unit:</strong> <br> {{ selectedSale.unit }}
                        </div>

                        <div class="col-md-6" v-if="selectedSale.price_per_unit">
                          <strong>Price per Unit:</strong> <br> {{ selectedSale.price_per_unit }}
                        </div> 
                        
                        <div class="col-md-6" v-if="selectedSale.buyer">
                          <strong>Buyer:</strong> <br> {{ selectedSale.buyer }}
                        </div> 
                        
                        <div class="col-md-6" v-if="selectedSale.sale_date">
                          <strong>Sale Date:</strong> <br> {{ selectedSale.sale_date }}
                        </div>
                        
                        <div class="col-md-6" v-if="selectedSale.total_amount">
                          <strong>Total Amount:</strong> <br> {{ selectedSale.total_amount }}
                        </div>                        

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Sale Modal -->
                <div class="modal fade" id="AddSaleModal" tabindex="-1" aria-labelledby="AddSaleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddSaleModalLabel">Add Farm sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Farm Venture -->
                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture" v-model="data.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Product Name -->
                          <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="product_name" class="form-control" v-model="data.product_name" required>
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

                          <!-- Price per unit -->
                          <div class="col-md-6">
                            <label class="form-label">Price per Unit</label>
                            <input type="number" id="price_per_unit" class="form-control" v-model="data.price_per_unit">
                          </div>

                          <!-- Buyer -->
                          <div class="col-md-6">
                            <label class="form-label">Buyer</label>
                            <input type="text" id="buyer" class="form-control" v-model="data.buyer">
                          </div>   
                          
                          <!-- Sale Date -->
                          <div class="col-md-6">
                            <label class="form-label">Sale Date</label>
                            <input type="date" id="sale_date" class="form-control" v-model="data.sale_date">
                          </div>

                          <!-- Total amount -->
                          <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" id="total_amount" class="form-control" v-model="data.total_amount" readonly>
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


                <!-- EDIT Sale MODAL -->
                <div class="modal fade" id="EditSaleModal" tabindex="-1" aria-labelledby="EditSaleModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- Farm Venture -->
                          <div class="col-md-6">
                            <label class="form-label">Farm Venture*</label>
                            <select class="form-select" id="venture_edit" v-model="form.venture_id" required>
                              <option value="">Select Venture</option>
                              <option v-for="venture in farmventures" :key="venture.id" :value="venture.id">
                                {{ venture.venture_name }} 
                              </option>
                            </select>
                          </div>

                          <!-- Product Name -->
                          <div class="col-md-6">
                            <label class="form-label">Product Name</label>
                            <input type="text" id="product_name" class="form-control" v-model="form.product_name" required>
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

                          <!-- Price per unit -->
                          <div class="col-md-6">
                            <label class="form-label">Price per Unit</label>
                            <input type="number" id="price_per_unit" class="form-control" v-model="form.price_per_unit">
                          </div>

                          <!-- Buyer -->
                          <div class="col-md-6">
                            <label class="form-label">Buyer</label>
                            <input type="text" id="buyer" class="form-control" v-model="form.buyer">
                          </div>   
                          
                          <!-- Sale Date -->
                          <div class="col-md-6">
                            <label class="form-label">Sale Date</label>
                            <input type="date" id="sale_date" class="form-control" v-model="form.sale_date">
                          </div>

                          <!-- Total amount -->
                          <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" id="total_amount" class="form-control" v-model="form.total_amount" readonly>
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
            farmsales: [],
            farmventures: [],
            selectedSale: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD sale
            id: "",
            venture_id: "",
            product_name: "",
            quantity: "",
            unit: "",
            price_per_unit: "",
            buyer: "",
            sale_date: "",
            total_amount: ""
            },

            form: {        // EDIT sale
            id: "",
            venture_id: "",
            product_name: "",
            quantity: "",
            unit: "",
            price_per_unit: "",
            buyer: "",
            sale_date: "",
            total_amount: ""
            }
        }
      },      
      methods: {                
        viewSale(item)
        {
          console.log(this.selectedSale)
          this.selectedSale = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewSaleModal'));
          modal.show();
        },
        editSale(item) {
        this.form = {
            id: item.id,
            venture_id: item.venture_id,
            product_name: item.product_name,
            quantity: item.quantity,
            unit: item.unit,
            price_per_unit: item.price_per_unit,
            buyer: item.buyer,
            sale_date: item.sale_date,
            total_amount: item.total_amount,

        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditSaleModal')
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
            await axios.put(`/api/farm-sales/${this.form.id}`, this.form);

            toast.fire('Success!', 'Farm sale updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditSaleModal')
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

        addSale()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddSaleModal'));
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
            await axios.post('/api/farm-sales', this.data);

            toast.fire('Success!', 'Farm sale added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddSaleModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              venture_id: "",
              product_name: "",
              quantity: "",
              unit: "",
              price_per_unit: "",
              buyer: "",
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
        deleteSale(id){
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
                  axios.delete('/api/farm-sales/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm sale has been deleted.',
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
          axios.get('/api/farm-sales')
            .then((response) => {
              this.farmsales = response.data.farmsales;
              this.farmventures = response.data.farmventures;
              console.log(response)

              setTimeout(() => {
                $("#FarmSalesTable").DataTable();
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
    
    
    