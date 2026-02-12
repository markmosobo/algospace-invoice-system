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
                      <h5 class="card-title">Personal Accounts <span>| Accounts asssociated with me</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addAccount()"
                                >
                                  Add Account
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
    
                      <table id="PersonalAccountsTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col"> Name</th>
                            <th scope="col">Balance</th>
                            <th scope="col">Currency</th>
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
                          <tr v-for="account in personalAccounts" :key="account.id">
                            <td>{{account.name}}</td>
                            <td>{{account.balance ?? "N/A"}}</td>
                            <td>{{account.currency ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewAccount(account)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editAccount(account)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteAccount(account.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>

                        <div v-if="personalAccounts" class="mt-2">
                          <strong>
                            Grand Total: {{ accountTotal }}
                            Liquid: {{ liquidTotal }}
                            Savings & Shares: {{ savingsTotal }}
                          </strong>
                        </div>    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Account Modal -->
                <div
                  class="modal fade"
                  id="viewAccountModal"
                  tabindex="-1"
                  aria-labelledby="viewAccountModalLabel"
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="viewAccountModalLabel">
                          Account Details
                        </h5>
                        <button
                          type="button"
                          class="btn-close"
                          data-bs-dismiss="modal"
                          aria-label="Close"
                        ></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body" v-if="selectedAccount">
                        <div class="row gy-3">

                          <!-- Account Name -->
                          <div class="col-md-6">
                            <small class="text-muted">Account Name</small>
                            <div class="fw-semibold">
                              {{ selectedAccount.name }}
                            </div>
                          </div>

                          <!-- Balance -->
                          <div class="col-md-6">
                            <small class="text-muted">Balance</small>
                            <div class="fw-bold text-success">
                              {{ selectedAccount.currency }}
                              {{ Number(selectedAccount.balance).toLocaleString() }}
                            </div>
                          </div>

                          <!-- Currency -->
                          <div class="col-md-6">
                            <small class="text-muted">Currency</small>
                            <div class="fw-semibold">
                              {{ selectedAccount.currency }}
                            </div>
                          </div>

                          <!-- Created At -->
                          <div class="col-md-6">
                            <small class="text-muted">Created At</small>
                            <div class="fw-semibold">
                              {{ formatDate(selectedAccount.created_at) }}
                            </div>
                          </div>

                          <!-- Updated At -->
                          <div class="col-md-6">
                            <small class="text-muted">Last Updated</small>
                            <div class="fw-semibold">
                              {{ formatDate(selectedAccount.updated_at) }}
                            </div>
                          </div>

                        </div>
                      </div>

                      <!-- Empty State -->
                      <div class="modal-body text-center text-muted" v-else>
                        No account selected.
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button
                          type="button"
                          class="btn btn-secondary"
                          data-bs-dismiss="modal"
                        >
                          Close
                        </button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Add Account Modal -->
                <div
                  class="modal fade"
                  id="AddAccountModal"
                  tabindex="-1"
                  aria-labelledby="AddAccountModalLabel"
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="AddAccountModalLabel">
                          Add Personal Account
                        </h5>
                        <button
                          type="button"
                          class="btn-close"
                          data-bs-dismiss="modal"
                          aria-label="Close"
                        ></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Hidden ID (future edit support) -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Account Name -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Account Name <span class="text-danger">*</span>
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              v-model="data.name"
                              id="name"
                              placeholder="e.g. Cash, Mpesa, Bank Account"
                              required
                            >
                          </div>

                          <!-- Opening Balance -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Opening Balance
                            </label>
                            <input
                              type="number"
                              step="0.01"
                              min="0"
                              class="form-control"
                              v-model="data.balance"
                              placeholder="0.00"
                            >
                          </div>

                          <!-- Currency -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Currency
                            </label>
                            <select class="form-select" v-model="data.currency">
                              <option value="KES">KES</option>
                              <option value="USD">USD</option>
                              <option value="EUR">EUR</option>
                            </select>
                          </div>

                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button
                          type="button"
                          class="btn btn-secondary"
                          data-bs-dismiss="modal"
                        >
                          Close
                        </button>

                        <button
                          type="button"
                          class="btn btn-success"
                          @click="submit"
                        >
                          Save Account
                        </button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- EDIT Account MODAL -->
                <div
                  class="modal fade"
                  id="EditAccountModal"
                  tabindex="-1"
                  aria-labelledby="EditAccountModalLabel"
                  aria-hidden="true"
                >
                  <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content">

                      <!-- Header -->
                      <div class="modal-header">
                        <h5 class="modal-title" id="EditAccountModalLabel">
                          Edit Personal Account
                        </h5>
                        <button
                          type="button"
                          class="btn-close"
                          data-bs-dismiss="modal"
                          aria-label="Close"
                        ></button>
                      </div>

                      <!-- Body -->
                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

                          <!-- Account Name -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Account Name <span class="text-danger">*</span>
                            </label>
                            <input
                              type="text"
                              class="form-control"
                              id="name_edit"
                              v-model="form.name"
                              required
                            >
                          </div>

                          <!-- Balance -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Balance
                            </label>
                            <input
                              type="number"
                              step="0.01"
                              min="0"
                              class="form-control"
                              v-model="form.balance"
                            >
                          </div>

                          <!-- Currency -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Currency
                            </label>
                            <select class="form-select" v-model="form.currency">
                              <option value="KES">KES</option>
                              <option value="USD">USD</option>
                              <option value="EUR">EUR</option>
                            </select>
                          </div>

                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button
                          class="btn btn-secondary"
                          data-bs-dismiss="modal"
                        >
                          Close
                        </button>

                        <button
                          class="btn btn-success"
                          @click="submitChanges"
                        >
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
            personalAccounts: [],
            accountTotal: null,
            liquidTotal: null,
            savingsTotal: null,
            selectedAccount: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD account
              id: null,
              name: '',
              balance: 0,
              currency: 'KES'
            },

            form: {
              id: null,
              name: '',
              balance: 0,
              currency: 'KES'
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
        viewAccount(account)
        {
          console.log(this.selectedAccount)
          this.selectedAccount = account;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewAccountModal'));
          modal.show();
        },
        editAccount(account) {
        this.form = {
            id: account.id,
            name: account.name,
            balance: account.balance,
            currency: account.currency,
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditAccountModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.name) {
            document.getElementById('name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/personal-accounts/${this.form.id}`, this.form);

            toast.fire('Success!', 'Account updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditAccountModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update account',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addAccount()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddAccountModal'));
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

        if (!this.data.name) {
            document.getElementById('name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/personal-accounts', this.data);

            toast.fire('Success!', 'Account added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddAccountModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              name: "",
              balance: "",
              currency: "",
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
        deleteAccount(id){
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
                  axios.delete('/api/personal-accounts/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Account has been deleted.',
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
          axios.get('/api/personal-accounts')
            .then((response) => {
              this.personalAccounts = response.data.personalAccounts;
              this.accountTotal = response.data.accountTotal;
              this.liquidTotal = response.data.liquidTotal;
              this.savingsTotal = response.data.savingsTotal;
              console.log(response)

              setTimeout(() => {
                $("#PersonalAccountsTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching accounts list:', error);
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
    
    
    