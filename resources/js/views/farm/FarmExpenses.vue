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
                      <h5 class="card-title">Farm Expenses <span>| Tracked costs for farm operations</span></h5>
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
                                  Add Farm Expense
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
                            <th scope="col">Category</th>
                            <th scope="col">Description</th>
                            <th scope="col">Amount (KES)</th>
                            <th scope="col">Expense Date</th>
                            <th scope="col">Paid By</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>

                        <!-- Spinner shown while data is initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="6" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>

                        <tbody v-else>
                          <tr v-for="expense in farmExpenses" :key="expense.id">
                            <td>{{ expense.expense_category }}</td>
                            <td>{{ expense.description ?? 'N/A' }}</td>
                            <td>{{ expense.amount }}</td>
                            <td>{{ expense.expense_date }}</td>
                            <td>{{ expense.paid_by ?? 'N/A' }}</td>

                            <td>
                              <div class="btn-group" role="group">
                                <button
                                  type="button"
                                  class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  data-bs-toggle="dropdown"
                                  aria-expanded="false"
                                >
                                  Action
                                </button>

                                <div class="dropdown-menu">
                                  <a @click="viewExpense(expense)" class="dropdown-item" href="#">
                                    <i class="ri-eye-fill mr-2"></i> View
                                  </a>
                                  <a @click="editExpense(expense)" class="dropdown-item" href="#">
                                    <i class="ri-pencil-fill mr-2"></i> Edit
                                  </a>
                                  <a @click="deleteExpense(expense.id)" class="dropdown-item text-danger" href="#">
                                    <i class="ri-delete-bin-line mr-2"></i> Delete
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

                <!-- View Expense Modal -->
                <div class="modal fade" id="viewExpenseModal" tabindex="-1" aria-labelledby="viewExpenseModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="viewExpenseModalLabel">
                          Expense Details
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body" v-if="selectedExpense">
                        <div class="row g-3">

                          <div class="col-md-6">
                            <strong>Expense Category:</strong><br>
                            {{ selectedExpense.expense_category }}
                          </div>

                          <div class="col-md-6">
                            <strong>Amount (KES):</strong><br>
                            {{ selectedExpense.amount }}
                          </div>

                          <div class="col-md-6">
                            <strong>Expense Date:</strong><br>
                            {{ selectedExpense.expense_date }}
                          </div>

                          <div class="col-md-6">
                            <strong>Paid By:</strong><br>
                            {{ selectedExpense.paid_by ?? 'N/A' }}
                          </div>

                          <div class="col-md-12" v-if="selectedExpense.description">
                            <strong>Description:</strong><br>
                            {{ selectedExpense.description }}
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
                <div class="modal fade" id="AddExpenseModal" tabindex="-1" aria-labelledby="AddExpenseModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddExpenseModalLabel">
                          Record Farm Expense
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID (for edit) -->
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

                          <!-- Expense Category -->
                          <div class="col-md-6">
                            <label class="form-label">Expense Category *</label>
                            <select class="form-select" v-model="data.expense_category" required>
                              <option value="">Select category</option>
                              <option value="seeds">Seeds</option>
                              <option value="fertilizer">Fertilizer</option>
                              <option value="labor">Labor</option>
                              <option value="transport">Transport</option>
                              <option value="pesticide">Pesticide</option>
                              <option value="equipment">Equipment</option>
                              <option value="utilities">Utilities</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount (KES) *</label>
                            <input
                              type="number"
                              min="0"
                              step="0.01"
                              class="form-control"
                              v-model="data.amount"
                              required
                            >
                          </div>

                          <!-- Expense Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expense Date *</label>
                            <input
                              type="date"
                              class="form-control"
                              v-model="data.expense_date"
                              required
                            >
                          </div>

                          <!-- Paid By -->
                          <div class="col-md-6">
                            <label class="form-label">Paid By</label>
                            <input
                              type="text"
                              class="form-control"
                              v-model="data.paid_by"
                              placeholder="e.g. Cash, M-Pesa, Bank"
                            >
                          </div>

                          <!-- Description -->
                          <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea
                              class="form-control"
                              rows="3"
                              v-model="data.description"
                              placeholder="Optional notes about this expense"
                            ></textarea>
                          </div>

                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button
                          class="btn btn-success"
                          @click="submit"
                          style="background: darkgreen; border-color: darkgreen;"
                        >
                          Save Expense
                        </button>
                      </div>

                    </div>
                  </div>
                </div>



                <!-- EDIT Expense MODAL -->
                <div class="modal fade" id="EditExpenseModal" tabindex="-1" aria-labelledby="EditExpenseModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Farm Expense</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID (for edit) -->
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

                          <!-- Expense Category -->
                          <div class="col-md-6">
                            <label class="form-label">Expense Category *</label>
                            <select class="form-select" v-model="form.expense_category" required>
                              <option value="">Select category</option>
                              <option value="seeds">Seeds</option>
                              <option value="fertilizer">Fertilizer</option>
                              <option value="labor">Labor</option>
                              <option value="transport">Transport</option>
                              <option value="pesticide">Pesticide</option>
                              <option value="equipment">Equipment</option>
                              <option value="utilities">Utilities</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount (KES) *</label>
                            <input
                              type="number"
                              min="0"
                              step="0.01"
                              class="form-control"
                              v-model="form.amount"
                              required
                            >
                          </div>

                          <!-- Expense Date -->
                          <div class="col-md-6">
                            <label class="form-label">Expense Date *</label>
                            <input
                              type="date"
                              class="form-control"
                              v-model="form.expense_date"
                              required
                            >
                          </div>

                          <!-- Paid By -->
                          <div class="col-md-6">
                            <label class="form-label">Paid By</label>
                            <input
                              type="text"
                              class="form-control"
                              v-model="form.paid_by"
                              placeholder="e.g. Cash, M-Pesa, Bank"
                            >
                          </div>

                          <!-- Description -->
                          <div class="col-md-12">
                            <label class="form-label">Description</label>
                            <textarea
                              class="form-control"
                              rows="3"
                              v-model="form.description"
                              placeholder="Optional notes about this expense"
                            ></textarea>
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
            farmExpenses: [],
            farmventures: [],
            selectedExpense: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD expense
              id: "",
              venture_id: "",
              expense_category: "",
              description: "",
              amount: "",
              expense_date: "",
              paid_by: "",
              receipt_no: ""
            },

            form: {        // EDIT expense
              id: "",
              venture_id: "",
              expense_category: "",
              description: "",
              amount: "",
              expense_date: "",
              paid_by: "",
              receipt_no: ""
            }
        }
      },      
      methods: {                
        viewExpense(item)
        {
          console.log(this.selectedExpense)
          this.selectedExpense = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewExpenseModal'));
          modal.show();
        },
        editExpense(item) {
        this.form = {
            id: item.id,
            venture_id: item.venture_id,
            expense_category: item.expense_category,
            description: item.description,
            amount: item.amount,
            expense_date: item.expense_date,
            paid_by: item.paid_by,
            receipt_no: item.receipt_no,
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditExpenseModal')
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
            await axios.put(`/api/farm-expenses/${this.form.id}`, this.form);

            toast.fire('Success!', 'Expense updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditExpenseModal')
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
          const modal = new bootstrap.Modal(document.getElementById('AddExpenseModal'));
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
            await axios.post('/api/farm-expenses', this.data);

            toast.fire('Success!', 'Expense added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddExpenseModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: "",
              venture_id: "",
              expense_category: "",
              description: "",
              amount: "",
              expense_date: "",
              paid_by: "",
              receipt_no: ""
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
                  axios.delete('/api/farm-expenses/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Farm expense has been deleted.',
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
          axios.get('/api/farm-expenses')
            .then((response) => {
              this.farmExpenses = response.data.farmexpenses;
              this.farmventures = response.data.farmventures;
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
    
    
    