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
                      <h5 class="card-title">Restocks <span>| Product restocks at AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addRestock()"
                                >
                                  Add Restock
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
    
                      <table id="RestocksTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Product</th>
                            <th scope="col">Supplier</th>
                            <th scope="col">Quantity</th>
                            <th scope="col">Price</th>
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
                          <tr v-for="item in restocks" :key="item.id">
                            <td>{{item.supply.item}}</td>
                            <td>{{item.supplier.name ?? "N/A"}}</td>
                            <td>{{item.quantity ?? "N/A"}}</td>
                            <td>{{item.buying_price ?? "N/A"}}</td>
                            <td>
                                <span class="badge bg-success" v-if="item.status === 'completed'">Completed</span>
                                <span class="badge bg-warning" v-else>Pending</span>
                            </td>
                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewRestock(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editRestock(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteRestock(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Restock Modal -->
              <div class="modal fade" id="viewRestockModal" tabindex="-1" aria-labelledby="viewRestockModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Restock Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedRestock">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedRestock.supply_id">
                          <strong>Product:</strong> <br> {{ selectedRestock.supply.item }}
                        </div>

                        <div class="col-md-6" v-if="selectedRestock.supplier_id">
                          <strong>Supplier:</strong> <br> {{ selectedRestock.supplier.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedRestock.quantity">
                          <strong>Quantity:</strong> <br> {{ selectedRestock.quantity }}
                        </div>

                        <div class="col-md-6" v-if="selectedRestock.buying_price">
                          <strong>Buying Price:</strong> <br> KES {{ selectedRestock.buying_price }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Restock Modal -->
                <div class="modal fade" id="addRestockModal" tabindex="-1" aria-labelledby="addRestockModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addRestockModalLabel">Add Restock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Product Name -->
                          <div class="col-md-6">
                            <label class="form-label">Product*</label>
                            <select name="supply" v-model="data.supply_id" class="form-select" id="supply">
                                <option value="0" selected disabled>Select Product</option>
                                <option v-for="item in supplies" :value="item.id"
                                :selected="item.id == data.supply_id" :key="item.id">{{ item.item}} </option>
    
                            </select>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier" v-model="data.supplier_id" class="form-select" id="supplier">
                                <option value="0" selected disabled>Select Supplier</option>
                                <option v-for="supplier in suppliers" :value="supplier.id"
                                :selected="supplier.id == data.supplier_id" :key="supplier.id">{{ supplier.name}} </option>
    
                            </select>
                        </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="quantity" class="form-control" v-model="data.quantity">
                          </div>

                          <!-- Buying Price -->
                          <div class="col-md-6">
                            <label class="form-label">Buying Price</label>
                            <input type="number" id="buying_price" class="form-control" v-model="data.buying_price">
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


                <!-- Edit Restock Modal -->
                <div class="modal fade" id="EditRestockModal" tabindex="-1" aria-labelledby="EditRestockModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Restock</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Product Name -->
                          <div class="col-md-6">
                            <label class="form-label">Product*</label>
                            <select name="supply" v-model="form.supply_id" class="form-select" id="supply_edit">
                                <option value="0" selected disabled>Select Product</option>
                                <option v-for="item in supplies" :value="item.id"
                                :selected="item.id == form.supply_id" :key="item.id">{{ item.item}} </option>
    
                            </select>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier" v-model="form.supplier_id" class="form-select" id="supplier_edit">
                                <option value="0" selected disabled>Select Supplier</option>
                                <option v-for="supplier in suppliers" :value="supplier.id"
                                :selected="supplier.id == form.supplier_id" :key="supplier.id">{{ supplier.name}} </option>
    
                            </select>
                        </div>

                          <!-- Quantity -->
                          <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" id="quantity_edit" class="form-control" v-model="form.quantity">
                          </div>

                          <!-- Buying Price -->
                          <div class="col-md-6">
                            <label class="form-label">Buying Price</label>
                            <input type="number" id="buying_price_edit" class="form-control" v-model="form.buying_price">
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
            supplies: [],
            suppliers: [],
            restocks: [],
            selectedRestock: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // Add restock
                supply_id: "",
                supplier_id: "",
                quantity: "",
                buying_price: "",
            },

            form: {        // Edit restock
                id: "",
                supply_id: "",
                supplier_id: "",
                quantity: "",
                buying_price: "",
            }
        }
      },      
      methods: {                
        viewRestock(restock)
        {
          console.log(this.selectedRestock)
          this.selectedRestock = restock;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewRestockModal'));
          modal.show();
        },
        editRestock(restock) {
        this.form = {
            supply_id: restock.supply_id,
            supplier_id: restock.supplier_id,
            quantity: restock.quantity,
            buying_price: restock.buying_price,
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditRestockModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.supply_id) {
            document.getElementById('supply_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('supply_edit').classList.remove('is-invalid');
        }

        if (!this.form.supplier_id) {
            document.getElementById('supplier_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('supplier_edit').classList.remove('is-invalid');
        }        

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/restocks/${this.form.id}`, this.form);

            toast.fire('Success!', 'Restock updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editRestockModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update restock',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addRestock()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addRestockModal'));
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

        if (!this.data.supply_id) {
            document.getElementById('supply').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('supply').classList.remove('is-invalid');
        }

        if (!this.data.supplier_id) {
            document.getElementById('supplier').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('supplier').classList.remove('is-invalid');
        }        

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/restocks', this.data);

            toast.fire('Success!', 'Restock added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addRestockModal')
            );
            modal.hide();

            // Reset form
            this.data = {
                supply_id: "",
                supplier_id: "",
                status: "pending",
                quantity: "",
                buying_price: ""
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
        deleteRestock(id){
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
                  axios.delete('/api/restocks/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Restock has been deleted.',
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
          axios.get('/api/restocks')
            .then((response) => {
              this.restocks = response.data.restocks;
              this.supplies = response.data.supplies;
              this.suppliers = response.data.suppliers;
              console.log(response)

              setTimeout(() => {
                $("#RestocksTable").DataTable();
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
    
    
    