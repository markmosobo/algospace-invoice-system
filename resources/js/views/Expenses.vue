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
                      <h5 class="card-title">Expenses <span>| Money paid out by AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addExpense()"
                                >
                                  Add Expense
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
    
                      <table id="ExpensesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Amount</th>
                            <th scope="col">Description</th>
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
                          <tr v-for="item in expenses" :key="item.id">
                            <td>{{item.amount ?? "N/A"}}</td>
                            <td>
                              <div>
                                <small class="text-muted">{{ item.expense_date }}</small><br>

                                <span v-if="item.type === 'provider_service'">
                                  {{ item.provider_service_name || 'Provider Service' }}
                                </span>

                                <span v-else>
                                  {{ item.description || 'No description provided' }}
                                </span>
                              </div>
                            </td>


                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewExpense(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editExpense(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteExpense(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Expense Modal -->
              <div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-labelledby="viewExpenseModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Expense Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedExpense">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedExpense.type">
                        <strong>Type:</strong> <br>

                        <span v-if="selectedExpense.type === 'expense'" class="badge bg-danger">
                          Expense
                        </span>

                        <span v-else-if="selectedExpense.type === 'provider_service'" class="badge bg-primary">
                          Service(s) rendered
                        </span>

                        <span v-else-if="selectedExpense.type === 'inventory'" class="badge bg-success">
                          Inventory
                        </span>

                        <span v-else class="badge bg-secondary">
                          Other
                        </span>
                        </div>


                        <div class="col-md-6" v-if="selectedExpense.service_provider_id">
                          <strong>Provider:</strong> <br> {{ selectedExpense.service_provider.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedExpense.provider_service_id">
                          <strong>Service:</strong> <br> {{ selectedExpense.provider_service.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedExpense.amount">
                          <strong>Amount:</strong> <br> {{ selectedExpense.amount }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Expense Modal -->
                <div class="modal fade" id="addExpenseModal" tabindex="-1" aria-labelledby="addExpenseModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addExpenseModalLabel">Add Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type" v-model="data.type">
                              <option value="">Select</option>
                              <option value="expense">Expense</option>
                              <option value="provider_service">Service(s) rendered</option>
                              <option value="inventory">Inventory</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Service Provider -->
                        <div v-if="data.type == 'provider_service'" class="col-md-6">
                            <label class="form-label">Service Provider</label>
                            <select name="service_provider" v-model="data.service_provider_id" class="form-select" id="service_provider">
                                <option value="0" selected disabled>Select Provider</option>
                                <option v-for="provider in serviceProviders" :value="provider.id"
                                :selected="provider.id == data.service_provider_id" :key="provider.id">{{ provider.name}} </option>
    
                            </select>
                        </div>

                        <!-- Service(s) rendered -->
                        <div v-if="data.type == 'provider_service'" class="col-md-6">
                            <label class="form-label">Service(s) Rendered</label>
                            <select name="service_provider" v-model="data.provider_service_id" class="form-select" id="service_provider">
                                <option value="0" selected disabled>Select Service</option>
                                <option v-for="service in providerServices" :value="service.id"
                                :selected="service.id == data.provider_service_id" :key="service.id">{{ service.name}} </option>
    
                            </select>
                        </div>                        

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input
                              type="number"
                              class="form-control"
                              v-model="data.amount"
                              :readonly="data.type === 'provider_service' && !allowOverride"
                            />

                            <button
                              v-if="data.type === 'provider_service' && !allowOverride"
                              type="button"
                              class="btn btn-sm btn-outline-secondary mt-1"
                              @click="allowOverride = true"
                            >
                              Override amount
                            </button>

                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea type="text" id="description" class="form-control" v-model="data.description"/>

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


                <!-- EDIT Expense MODAL -->
                <div class="modal fade" id="editExpenseModal" tabindex="-1" aria-labelledby="editExpenseModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" id="type_edit" v-model="form.type">
                              <option value="">Select</option>
                              <option value="expense">Expense</option>
                              <option value="provider_service">Service(s) rendered</option>
                              <option value="inventory">Inventory</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Service Provider -->
                        <div v-if="form.type == 'provider_service'" class="col-md-6">
                            <label class="form-label">Service Provider</label>
                            <select name="service_provider" v-model="form.service_provider_id" class="form-select" id="service_provider">
                                <option value="0" selected disabled>Select Provider</option>
                                <option v-for="provider in serviceProviders" :value="provider.id"
                                :selected="provider.id == form.service_provider_id" :key="provider.id">{{ provider.name}} </option>
    
                            </select>
                        </div>

                        <!-- Service(s) rendered -->
                        <div v-if="form.type == 'provider_service'" class="col-md-6">
                            <label class="form-label">Service(s) Rendered</label>
                            <select name="service_provider" v-model="form.provider_service_id" class="form-select" id="service_provider">
                                <option value="0" selected disabled>Select Service</option>
                                <option v-for="service in providerServices" :value="service.id"
                                :selected="service.id == form.provider_service_id" :key="service.id">{{ service.name}} </option>
    
                            </select>
                        </div>                        

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" id="amount" class="form-control" v-model="form.amount">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Description</label>
                            <textarea type="text" id="description" class="form-control" v-model="form.description"/>

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
            expenses: [],
            serviceProviders: [],
            providerServices: [],
            selectedExpense: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD expense
              id: "",
              type: "",
              service_provider_id: "",
              provider_service_id: "",
              description: "",
              amount: "",
              invoice_id: null
            },

            form: {        // EDIT expense
              id: "",
              type: "",
              service_provider_id: "",
              provider_service_id: "",
              description: "",
              amount: "",
              invoice_id: null
            },
            allowOverride: false,
        }
      },   
      watch: {
        'data.provider_service_id'(id) {
          if (!id) return;

          const service = this.providerServices.find(s => s.id === id);
          if (service) {
            this.data.amount = service.price;
            this.allowOverride = false; // 👈 reset
          }
        },

        'data.type'(val) {
          if (val !== 'provider_service') {
            this.allowOverride = true; // free edit for other types
          }
        }
      },

   
      methods: {                
        async viewExpense(expense) {
          try {
            const res = await axios.get(`/api/expenses/${expense.id}`);
            this.selectedExpense = res.data.expense;

            const modal = new bootstrap.Modal(document.getElementById('viewExpenseModal'));
            modal.show();

          } catch (error) {
            toast.fire('Error!', 'Failed to load expense details', 'error');
          }
        },
        editExpense(item) {
        this.form = {
            id: item.id,
            type: item.type,
            service_provider_id: item.service_provider_id,
            provider_service_id: item.provider_service_id,
            description: item.description,
            amount: item.amount,
            expense_date: item.expense_date,
            invoice_id: item.expense_date
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editExpenseModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.type) {
            document.getElementById('type_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('type_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/expenses/${this.form.id}`, this.form);

            toast.fire('Success!', 'Expense updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editExpenseModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update expense',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addExpense()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addExpenseModal'));
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

        if (!this.data.type) {
            document.getElementById('type').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('type').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/expenses', this.data);

            toast.fire('Success!', 'Expenses added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addExpenseModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              type: "",
              service_provider_id: "",
              provider_service_id: "",
              description: "",
              amount: "",
              invoice_id: null
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
        deleteExpense(id){
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
                  axios.delete('/api/expenses/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Expense has been deleted.',
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
          axios.get('/api/expenses')
            .then((response) => {
              this.expenses = response.data.expenses;
              this.invoices = response.data.invoices;
              this.serviceProviders = response.data.serviceProviders;
              this.providerServices = response.data.providerServices;
              console.log(response)

              setTimeout(() => {
                $("#ExpensesTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching expenses list:', error);
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
    
    
    