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
                      <h5 class="card-title">Transactions <span>| Day to day transactions</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addTransaction()"
                                >
                                  Add Transaction
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
    
                      <table id="TransactionsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Account</th>
                            <th scope="col">Type</th>
                            <th scope="col">Amount</th>
                            <th scope="col">Method</th>
                            <th scope="col">Date</th>
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
                          <tr v-for="transaction in personalTransactions" :key="transaction.id">
                            <td>{{transaction.account.name}}</td>
                            <td>
                              <span
                                class="badge"
                                :class="{
                                  'bg-danger': transaction.type === 'expense',
                                  'bg-info text-dark': transaction.type === 'income'
                                }"
                              >
                                {{ transaction.type ? transaction.type.replace('_', ' ').toUpperCase() : 'N/A' }}
                              </span>
                            </td>
                            <td>{{transaction.amount ?? "N/A"}}</td>
                            <td>
                              <span
                                class="badge"
                                :class="{
                                  'bg-success': transaction.payment_method === 'cash',
                                  'bg-primary': transaction.payment_method === 'mpesa',
                                  'bg-warning text-dark': transaction.payment_method === 'bank',
                                  'bg-secondary': !transaction.payment_method
                                }"
                              >
                                {{ transaction.payment_method ? transaction.payment_method.toUpperCase() : 'N/A' }}
                              </span>
                            </td>
                            <td>{{formatDate(transaction.transaction_date) ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewTransaction(transaction)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editTransaction(transaction)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteTransaction(transaction.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Transaction Modal -->
              <div class="modal fade" id="viewTransactionModal" tabindex="-1" aria-labelledby="viewTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Transaction Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedTransaction">

                      <div class="row g-3">

                        <!-- Account -->
                        <div class="col-md-6" v-if="selectedTransaction.account">
                          <strong>Account:</strong> <br>
                          {{ selectedTransaction.account.name }} ({{ selectedTransaction.account.currency }} {{ selectedTransaction.account.balance }})
                        </div>

                        <!-- Category -->
                        <div class="col-md-6" v-if="selectedTransaction.category">
                          <strong>Category:</strong> <br>
                          {{ selectedTransaction.category.name }}
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                          <strong>Type:</strong> <br>
                          {{ selectedTransaction.type | capitalize }}
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                          <strong>Amount:</strong> <br>
                          {{ selectedTransaction.amount }}
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6" v-if="selectedTransaction.payment_method">
                          <strong>Payment Method:</strong> <br>
                          {{ selectedTransaction.payment_method }}
                        </div>

                        <!-- Transaction Date -->
                        <div class="col-md-6">
                          <strong>Transaction Date:</strong> <br>
                          {{ formatDateTime(selectedTransaction.transaction_date) }}
                        </div>

                        <!-- Description -->
                        <div class="col-12" v-if="selectedTransaction.description">
                          <strong>Description:</strong> <br>
                          {{ selectedTransaction.description }}
                        </div>

                      </div>

                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>

              <!-- Add Transaction Modal -->
              <div class="modal fade" id="AddTransactionModal" tabindex="-1" aria-labelledby="AddTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title" id="AddTransactionModalLabel">Add Transaction</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <form class="row g-3 needs-validation" novalidate>
                        
                        <!-- Hidden ID -->
                        <input type="hidden" v-model="data.id" />

                        <!-- Account -->
                        <div class="col-md-6">
                          <label class="form-label">Account*</label>
                          <select class="form-select" v-model="data.account_id" required>
                            <option value="">Select Account</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                              {{ account.name }} ({{ account.currency }} {{ account.balance }})
                            </option>
                          </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                          <label class="form-label">Category</label>
                          <select class="form-select" v-model="data.category_id">
                            <option value="" disabled selected>Select </option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                              {{ category.name }}
                            </option>
                          </select>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                          <label class="form-label">Type*</label>
                          <select class="form-select" v-model="data.type" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                          </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                          <label class="form-label">Amount*</label>
                          <input type="number" step="0.01" id="amount" class="form-control" v-model.number="data.amount" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                          <label class="form-label">Payment Method</label>
                          <select class="form-select" v-model="data.payment_method">
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank</option>
                          </select>
                        </div>

                        <!-- Description -->
                        <div class="col-md-6">
                          <label class="form-label">Description</label>
                          <input type="text" class="form-control" v-model="data.description" placeholder="Optional notes">
                        </div>

                        <!-- Transaction Date -->
                        <div class="col-md-6">
                          <label class="form-label">Date</label>
                          <input type="date" class="form-control" v-model="data.transaction_date">
                        </div>

                      </form>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      <button class="btn btn-success" @click="submitTransaction" style="background: darkgreen; border-color: darkgreen;">
                        Save Transaction
                      </button>
                    </div>

                  </div>
                </div>
              </div>



              <!-- EDIT Transaction MODAL -->
              <div class="modal fade" id="EditTransactionModal" tabindex="-1" aria-labelledby="EditTransactionModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">Edit Transaction</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                      <form class="row g-3">

                        <!-- Account -->
                        <div class="col-md-6">
                          <label class="form-label">Account*</label>
                          <select class="form-select" v-model="form.account_id" required>
                            <option value="">Select Account</option>
                            <option v-for="account in accounts" :key="account.id" :value="account.id">
                              {{ account.name }} ({{ account.currency }} {{ account.balance }})
                            </option>
                          </select>
                        </div>

                        <!-- Category -->
                        <div class="col-md-6">
                          <label class="form-label">Category</label>
                          <select class="form-select" v-model="form.category_id">
                            <option value="">Select Category</option>
                            <option v-for="category in categories" :key="category.id" :value="category.id">
                              {{ category.name }}
                            </option>
                          </select>
                        </div>

                        <!-- Type -->
                        <div class="col-md-6">
                          <label class="form-label">Type*</label>
                          <select class="form-select" v-model="form.type" required>
                            <option value="income">Income</option>
                            <option value="expense">Expense</option>
                          </select>
                        </div>

                        <!-- Amount -->
                        <div class="col-md-6">
                          <label class="form-label">Amount*</label>
                          <input type="number" step="0.01" id="amount_edit" class="form-control" v-model="form.amount" required>
                        </div>

                        <!-- Payment Method -->
                        <div class="col-md-6">
                          <label class="form-label">Payment Method</label>
                          <select class="form-select" v-model="form.payment_method">
                            <option value="">Select Method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">Mpesa</option>
                            <option value="bank">Bank</option>
                          </select>
                        </div>

                        <!-- Transaction Date -->
                        <div class="col-md-6">
                          <label class="form-label">Transaction Date</label>
                          <input type="datetime-local" class="form-control" v-model="form.transaction_date">
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                          <label class="form-label">Description</label>
                          <textarea class="form-control" rows="3" v-model="form.description"></textarea>
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
            personalTransactions: [],
            selectedTransaction: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {
              id: null,
              account_id: null,
              category_id: null,
              type: "expense",
              amount: null,
              payment_method: null,
              description: "",
              transaction_date: new Date().toISOString().slice(0, 10),
            },

            form: {        // EDIT customer
            id: "",
            name: "",
            email: "",
            phone: "",
            gender: ""
            }
        }
      },      
      methods: { 
        // Format date as dd/mm/yyyy
        formatDate(date) {
          if (!date) return "N/A";
          const d = new Date(date);
          const day = String(d.getDate()).padStart(2, '0');
          const month = String(d.getMonth() + 1).padStart(2, '0'); // Months are 0-based
          const year = d.getFullYear();
          return `${day}/${month}/${year}`;
        },                       
        viewTransaction(transaction)
        {
          console.log(this.selectedTransaction)
          this.selectedTransaction = transaction;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewTransactionModal'));
          modal.show();
        },
        editTransaction(transaction) {
        this.form = {
            id: transaction.id,
            account_id: transaction.account_id,
            category_id: transaction.category_id,
            type: transaction.type,
            amount: transaction.amount,
            payment_method: transaction.payment_method,
            description: transaction.description,
            transaction_date: transaction.transaction_date
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditTransactionModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.amount) {
            document.getElementById('amount_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('amount_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/personal-transactions/${this.form.id}`, this.form);

            toast.fire('Success!', 'Transaction updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditTransactionModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update transaction',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addTransaction()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddTransactionModal'));
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

        if (!this.data.amount) {
            document.getElementById('amount').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('amount').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitTransaction() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/personal-transactions', this.data);

            toast.fire('Success!', 'Transaction added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddTransactionModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              account_id: "",
              category: "",
              type: "",
              amount: "",
              payment_method: "",
              description: "",
              transaction_id: ""
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
        formatDateTime(dateTime) {
          const date = new Date(dateTime);

          // Format date components
          const day = String(date.getDate()).padStart(2, '0');
          const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
          const year = date.getFullYear();

          // Format time components
          const hours = String(date.getHours()).padStart(2, '0');
          const minutes = String(date.getMinutes()).padStart(2, '0');
          const seconds = String(date.getSeconds()).padStart(2, '0');

          // Combine to format 'DD/MM/YYYY HH:mm:ss'
          return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        },
        deleteTransaction(id){
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
                  axios.delete('/api/personal-transactions/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Transaction has been deleted.',
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
          axios.get('/api/personal-transactions')
            .then((response) => {
              this.personalTransactions = response.data.personalTransactions;
              this.categories = response.data.categories;
              this.accounts = response.data.accounts;
              console.log(response)

              setTimeout(() => {
                $("#TransactionsTable").DataTable();
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
    
    
    