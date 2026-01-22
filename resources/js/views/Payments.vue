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
                      <h5 class="card-title">Payments <span>| Payments made to AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addPayment()"
                                >
                                  Add Payment
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
    
                    <table id="PaymentsTable" class="table table-borderless">
                    <thead>
                        <tr>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Payment Date</th>
                        <th scope="col">Method</th>
                        <th scope="col">Mpesa Code</th>
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
                        <tr v-for="payment in payments" :key="payment.id">
                        <td>{{ payment.invoice.invoice_number }}</td>
                        <td>{{ payment.amount }}</td>
                        <td>{{ payment.payment_date }}</td>
                        <td>{{ payment.method }}</td>
                        <td v-if="payment.method === 'mpesa'">
                            {{ payment.mpesa_code ?? 'N/A' }}
                        </td>
                        <td v-else>N/A</td>

                        <td>
                            <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button"
                                class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                                data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>

                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                <a @click="viewPayment(payment)" class="dropdown-item" href="#">
                                <i class="ri-eye-fill mr-2"></i>View
                                </a>

                                <a @click="editPayment(payment)" class="dropdown-item" href="#">
                                <i class="ri-pencil-fill mr-2"></i>Edit
                                </a>

                                <a @click="deletePayment(payment.id)" class="dropdown-item" href="#">
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

            <!-- View Payment Modal -->
            <div class="modal fade" id="viewPaymentModal" tabindex="-1" aria-labelledby="viewPaymentModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">View Payment Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body" v-if="selectedPayment">
                    <div class="row g-3">

                    <div class="col-md-6" v-if="selectedPayment.invoice_id">
                        <strong>Invoice No:</strong><br>
                        {{ selectedPayment.invoice_id }}
                    </div>

                    <div class="col-md-6" v-if="selectedPayment.amount">
                        <strong>Amount:</strong><br>
                        {{ selectedPayment.amount | currency }}
                    </div>

                    <div class="col-md-6" v-if="selectedPayment.payment_date">
                        <strong>Payment Date:</strong><br>
                        {{ selectedPayment.payment_date }}
                    </div>

                    <div class="col-md-6" v-if="selectedPayment.method">
                        <strong>Payment Method:</strong><br>
                        {{ selectedPayment.method }}
                    </div>

                    <div class="col-md-6" v-if="selectedPayment.method === 'mpesa' && selectedPayment.mpesa_code">
                        <strong>Mpesa Code:</strong><br>
                        {{ selectedPayment.mpesa_code }}
                    </div>

                    <div class="col-md-6" v-if="selectedPayment.status">
                        <strong>Status:</strong><br>
                        {{ selectedPayment.status }}
                    </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>

                </div>
            </div>
            </div>



            <!-- Add Payment Modal -->
            <div class="modal fade" id="AddPaymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form class="row g-3 needs-validation" novalidate>

                    <!-- Hidden ID -->
                    <input type="hidden" v-model="data.id" />

                    <!-- Invoice -->
                    <div class="col-md-6">
                        <label class="form-label">Invoice *</label>
                        <select class="form-select" v-model="data.invoice_id" required>
                        <option value="">Select Invoice</option>
                        <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">
                            {{ invoice.invoice_number }} - {{ invoice.customer.name }}
                        </option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">
                        <label class="form-label">Amount *</label>
                        <input type="number" class="form-control" v-model="data.amount" required>
                    </div>

                    <!-- Payment Date -->
                    <div class="col-md-6">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" class="form-control" v-model="data.payment_date" required>
                    </div>

                    <!-- Payment Method -->
                    <div class="col-md-6">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" v-model="data.method" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank">Bank</option>
                        </select>
                    </div>

                    <!-- Mpesa Code (Only when Mpesa selected) -->
                    <div class="col-md-6" v-if="data.method === 'mpesa'">
                    <label class="form-label">Mpesa Transaction Code *</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="data.mpesa_code"
                        required
                        placeholder="e.g. QK89H2J7XK"
                    >
                    </div>


                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" @click="submit" style="background: darkgreen;">
                    Save
                    </button>
                </div>

                </div>
            </div>
            </div>


            <!-- Edit Payment Modal -->
            <div class="modal fade" id="EditPaymentModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form class="row g-3">

                    <!-- Invoice -->
                    <div class="col-md-6">
                        <label class="form-label">Invoice *</label>
                        <select class="form-select" v-model="form.invoice_id" required>
                        <option value="">Select Invoice</option>
                        <option v-for="invoice in invoices" :key="invoice.id" :value="invoice.id">
                            {{ invoice.invoice_number }}
                        </option>
                        </select>
                    </div>

                    <!-- Amount -->
                    <div class="col-md-6">
                        <label class="form-label">Amount *</label>
                        <input type="number" class="form-control" v-model="form.amount" required>
                    </div>

                    <!-- Payment Date -->
                    <div class="col-md-6">
                        <label class="form-label">Payment Date *</label>
                        <input type="date" class="form-control" v-model="form.payment_date" required>
                    </div>

                    <!-- Method -->
                    <div class="col-md-6">
                        <label class="form-label">Payment Method *</label>
                        <select class="form-select" v-model="form.method" required>
                        <option value="">Select Method</option>
                        <option value="cash">Cash</option>
                        <option value="mpesa">M-Pesa</option>
                        <option value="bank">Bank</option>
                        </select>
                    </div>

                    <!-- Mpesa Code (Only when Mpesa selected) -->
                    <div class="col-md-6" v-if="form.method === 'mpesa'">
                    <label class="form-label">Mpesa Transaction Code *</label>
                    <input
                        type="text"
                        class="form-control"
                        v-model="form.mpesa_code"
                        required
                        placeholder="e.g. QK89H2J7XK"
                    >
                    </div>


                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" @click="submitChanges" style="background: darkgreen;">
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
            payments: [],
            invoices: [],
            selectedPayment: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {        // ADD payment
            invoice_id: "",
            amount: "",
            payment_date: new Date().toISOString().slice(0, 10), // today
            method: "cash",
            },

            form: {        // EDIT payment
            invoice_id: "",
            amount: "",
            payment_date: "",
            method: "",
            }
        }
      }, 
      watch: {
        'data.method'(val) {
            if (val !== 'mpesa') {
            this.data.mpesa_code = null;
            }
        },
        'form.method'(val) {
            if (val !== 'mpesa') {
            this.form.mpesa_code = null;
            }
        }
       },
     
      methods: {                
        viewPayment(payment)
        {
          console.log(this.selectedPayment)
          this.selectedPayment = payment;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewPaymentModal'));
          modal.show();
        },
        editPayment(payment) {
            this.form = {
                invoice_id: payment.invoice_id,
                amount: payment.amount,
                payment_date: payment.payment_date,
                method: payment.method,
            };

            const modal = new bootstrap.Modal(
                document.getElementById('EditPaymentModal')
            );
            modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.name) {
            document.getElementById('invoice_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('invoice_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/payment/${this.form.id}`, this.form);

            toast.fire('Success!', 'Payment updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditPaymentModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update payment',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addPayment()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddPaymentModal'));
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

        if (!this.data.invoice_id) {
            document.getElementById('invoice').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('invoice').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/payments', this.data);

            toast.fire('Success!', 'Payment added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddPaymentModal')
            );
            modal.hide();

            // Reset form
            this.data = {
                invoice_id: "",
                amount: "",
                payment_date: "",
                method: "",
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
        deletePayment(id){
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
                  axios.delete('/api/payments/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Payment has been deleted.',
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
          axios.get('/api/payments')
            .then((response) => {
              this.payments = response.data.payments;
              this.invoices = response.data.invoices;
              console.log(response)

              setTimeout(() => {
                $("#PaymentsTable").DataTable();
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
    
    
    