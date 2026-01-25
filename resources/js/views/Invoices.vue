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
                      <h5 class="card-title">Invoices <span>| Invoices of AlgoSpace Cyber customers</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <!-- <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addInvoice()"
                                >
                                  Add Invoice
                                </a> -->
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
    
                      <table id="InvoicesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Customer / Vendor</th>
                            <th scope="col">Invoice Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Amount</th>
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
                          <tr v-for="item in invoices" :key="item.id">
                            <!-- 1. Customer OR Vendor -->
                            <td>
                              <span v-if="item.invoice_type === 'expense'">
                                <span class="badge bg-secondary">VENDOR</span>
                                {{ item.vendor_name }}
                              </span>

                              <span v-else>
                                <span class="badge bg-primary">CUSTOMER</span>
                                {{ item.customer?.name ?? "N/A" }}
                              </span>
                            </td>


                            <td>{{ item.invoice_date ?? "N/A" }}</td>
                            <td>{{ item.due_date ?? "N/A" }}</td>
                            <td>{{ item.total_amount ?? "N/A" }}</td>

                            <td>
                              <span
                                class="badge"
                                :class="{
                                  'bg-secondary': item.status === 'pending',
                                  'bg-warning': item.status === 'partial',
                                  'bg-success': item.status === 'paid'
                                }"
                              >
                                {{ item.status.toUpperCase() }}
                              </span>

                            </td>

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
                                  <a @click="viewInvoice(item)" class="dropdown-item" href="#">
                                    <i class="ri-eye-fill mr-2"></i>View
                                  </a>
                                  <a @click="editInvoice(item)" class="dropdown-item" href="#">
                                    <i class="ri-pencil-fill mr-2"></i>Edit
                                  </a>
                                  <a
                                    v-if="item.status !== 'paid'"
                                    @click="addPayment(item)"
                                    class="dropdown-item text-success"
                                    href="#"
                                  >
                                    <i class="ri-money-dollar-circle-line me-2"></i>
                                    Add Payment
                                    <small class="text-muted">
                                      (KES {{ (item.total_amount - item.amount_paid).toFixed(2) }})
                                    </small>
                                  </a>


                                    <div class="dropdown-divider"></div>
                                  <a @click="deleteInvoice(item.id)" class="dropdown-item" href="#">
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

                <!-- View Invoice Modal -->
                <div class="modal fade" id="viewInvoiceModal" tabindex="-1" aria-labelledby="viewInvoiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">View Invoice Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body" v-if="selectedInvoice">

                        <div class="row g-3">

                          <!-- CUSTOMER / VENDOR -->
                          <div class="col-md-6" v-if="selectedInvoice.invoice_type === 'sales'">
                            <strong>Customer:</strong> <br> {{ selectedInvoice.customer?.name || "N/A" }}
                          </div>

                          <div class="col-md-6" v-if="selectedInvoice.invoice_type === 'expense'">
                            <strong>Vendor:</strong> <br> {{ selectedInvoice.vendor_name || "N/A" }}
                          </div>

                          <!-- INVOICE NUMBER -->
                          <div class="col-md-6" v-if="selectedInvoice.invoice_number">
                            <strong>Invoice Number:</strong> <br> {{ selectedInvoice.invoice_number }}
                          </div>

                          <!-- INVOICE DATE -->
                          <div class="col-md-6" v-if="selectedInvoice.invoice_date">
                            <strong>Invoice Date:</strong> <br> {{ selectedInvoice.invoice_date }}
                          </div>

                          <!-- DUE DATE -->
                          <div class="col-md-6" v-if="selectedInvoice.due_date">
                            <strong>Due Date:</strong> <br> {{ selectedInvoice.due_date }}
                          </div>

                          <!-- STATUS BADGE -->
                          <div class="col-md-6" v-if="selectedInvoice.status">
                            <strong>Status:</strong> <br>
                            <span
                              class="badge"
                              :class="{
                                'bg-success': selectedInvoice.status === 'paid',
                                'bg-warning': selectedInvoice.status === 'pending',
                                'bg-danger': selectedInvoice.status === 'overdue'
                              }"
                            >
                              {{ selectedInvoice.status.toUpperCase() }}
                            </span>
                          </div>

                          <!-- TOTAL AMOUNT -->
                          <div class="col-md-6" v-if="selectedInvoice.total_amount">
                            <strong>Total Amount:</strong> <br> {{ selectedInvoice.total_amount }}
                          </div>

                          <!-- AMOUNT PAID -->
                          <div class="col-md-6">
                            <strong>
                              {{ selectedInvoice.invoice_type === 'sales'
                                ? 'Amount Paid (Received)'
                                : 'Amount Paid (Paid Out)' }}
                            </strong><br>
                            <span class="text-success fw-bold">
                              {{ selectedInvoice.amount_paid }}
                            </span>
                          </div>


                          <!-- AMOUNT DUE -->
                          <div class="col-md-6">
                            <strong>
                              {{ selectedInvoice.invoice_type === 'sales'
                                ? 'Balance (Customer Owes)'
                                : 'Balance (You Owe Vendor)' }}
                            </strong><br>
                            <span class="text-danger fw-bold">
                              {{ (selectedInvoice.total_amount - selectedInvoice.amount_paid) }}
                            </span>
                          </div>
                         

                          <!-- ITEMS LIST -->
                          <div class="row g-3 mt-4" v-if="selectedInvoice.items && selectedInvoice.items.length">
                            <div class="col-12">
                              <h6>Invoice Items</h6>
                              <table class="table table-bordered">
                                <thead>
                                  <tr>
                                    <th>#</th>
                                    <th>Item</th>
                                    <th>Qty</th>
                                    <th>Unit Price</th>
                                    <th>Total</th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr v-for="(item, index) in selectedInvoice.items" :key="item.id">
                                    <td>{{ index + 1 }}</td>
                                    <td>{{ item.service_name || item.provider_service_name || item.expense_name }}</td>
                                    <td>{{ item.quantity }}</td>
                                    <td>{{ item.unit_price }}</td>
                                    <td>{{ item.line_total }}</td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>

                        </div>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Edit Invoice MODAL -->
                <div class="modal fade" id="EditInvoiceModal" tabindex="-1" aria-labelledby="EditInvoiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- INVOICE TYPE -->
                          <div class="col-md-6">
                            <label class="form-label">Invoice Type</label>
                            <select v-model="form.invoice_type" class="form-select" disabled>
                              <option value="sales">Sales</option>
                              <option value="expense">Expense</option>
                            </select>
                          </div>

                          <!-- CUSTOMER (Sales only) -->
                          <div class="col-md-6" v-if="form.invoice_type === 'sales'">
                            <label class="form-label">Customer</label>
                            <select v-model="form.customer_id" class="form-select" id="customer_edit">
                              <option value="" disabled>Select Customer</option>
                              <option v-for="customer in customers" :value="customer.id" :key="customer.id">
                                {{ customer.name }}
                              </option>
                            </select>
                          </div>

                          <!-- VENDOR (Expense only) -->
                          <div class="col-md-6" v-if="form.invoice_type === 'expense'">
                            <label class="form-label">Vendor Name</label>
                            <input type="text" v-model="form.vendor_name" id="vendor_edit" class="form-control" placeholder="Vendor Name">
                          </div>

                          <!-- Invoice Date -->
                          <div class="col-md-6">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" id="invoice_date_edit" class="form-control" v-model="form.invoice_date" required>
                          </div>

                          <!-- Due Date -->
                          <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" id="due_date_edit" class="form-control" v-model="form.due_date">
                          </div>

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" id="total_amount_edit" class="form-control" v-model="form.total_amount">
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

                <!-- Add Payment Modal -->
                <div class="modal fade" id="addPaymentModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Add Payment</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">

                        <!-- INVOICE SUMMARY -->
                        <div v-if="selectedInvoice" class="mb-3">
                          <p class="mb-1"><strong>Invoice:</strong> {{ selectedInvoice.invoice_number }}</p>
                          <p class="mb-1">
                            <strong>Total:</strong> {{ selectedInvoice.total_amount }}
                          </p>
                          <p class="mb-1 text-success">
                            <strong>Paid:</strong> {{ selectedInvoice.amount_paid }}
                          </p>
                          <p class="mb-1 text-danger">
                            <strong>Balance:</strong>
                            {{ remainingBalance.toFixed(2) }}
                          </p>
                        </div>

                        <hr>

                        <!-- PAYMENT FORM -->
                        <div class="mb-3">
                          <label class="form-label">Amount Paying</label>
                          <input
                            type="number"
                            class="form-control"
                            v-model.number="paymentForm.amount"
                            :max="remainingBalance"
                            min="1"
                          >
                          <small class="text-muted">
                            Max: {{ remainingBalance }}
                          </small>
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Payment Method</label>
                          <select v-model="paymentForm.method" class="form-select">
                            <option value="cash">Cash</option>
                            <option value="mpesa">M-Pesa</option>
                            <option value="bank">Bank</option>
                          </select>
                        </div>

                        <div class="mb-3" v-if="paymentForm.method === 'mpesa'">
                          <label class="form-label">Mpesa Code</label>
                          <input type="text" class="form-control" v-model="paymentForm.mpesa_code">
                        </div>

                        <div class="mb-3">
                          <label class="form-label">Payment Date</label>
                          <input
                            type="date"
                            class="form-control"
                            v-model="paymentForm.payment_date"
                          >
                        </div>


                        <div class="mb-3">
                          <label class="form-label">Comment (optional)</label>
                          <textarea class="form-control" v-model="paymentForm.comment"></textarea>
                        </div>

                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button
                          class="btn btn-success"
                          :disabled="submitting || paymentForm.amount > remainingBalance"
                          @click="submitPayment"
                        >
                          Pay {{ paymentForm.amount || 0 }}
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
            customers: [],
            invoices: [],
            selectedInvoice: {},
            errors: {},
            initializing: true,
            submitting: false,

            paymentForm: {
              invoice_id: null,
              amount: null,
              method: 'cash',
              mpesa_code: null,
              comment: null,
              payment_date: new Date().toISOString().substr(0, 10),
            },

            form: {
              id: "",
              invoice_type: "",
              customer_id: "",
              vendor_name: "",
              invoice_date: "",
              due_date: "",
              total_amount: "",
              status: "pending"
            }

        }
      },
      computed: {
        remainingBalance() {
          if (!this.selectedInvoice) return 0;
          return Number(this.selectedInvoice.total_amount) -
                Number(this.selectedInvoice.amount_paid);
        }
      },      
      methods: {   
        addPayment(invoice) {
          this.selectedInvoice = invoice;
          this.paymentForm.invoice_id = invoice.id;
          this.paymentForm.amount = '';
          this.paymentForm.method = 'cash';

          const modal = new bootstrap.Modal(
            document.getElementById('addPaymentModal')
          );
          modal.show();
        },   
        async submitPayment() {
          if (this.paymentForm.amount <= 0) return;

          this.submitting = true;

          try {
            await axios.post('/api/payments', this.paymentForm);

            toast.fire('Success', 'Payment recorded', 'success');

            const modal = bootstrap.Modal.getInstance(
              document.getElementById('addPaymentModal')
            );
            modal.hide();

            this.loadLists(); // reload invoices

          } catch (err) {
            toast.fire(
              'Error',
              err.response?.data?.message || 'Failed to record payment',
              'error'
            );
          } finally {
            this.submitting = false;
          }
        },
          
        async viewInvoice(invoice) {
          try {
            const res = await axios.get(`/api/invoices/${invoice.id}`);
            this.selectedInvoice = res.data.invoice;

            const modal = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
            modal.show();

          } catch (error) {
            toast.fire('Error!', 'Failed to load invoice details', 'error');
          }
        },

        editInvoice(invoice) {
          this.form = {
            id: invoice.id,
            invoice_type: invoice.invoice_type,
            customer_id: invoice.customer_id,
            vendor_name: invoice.vendor_name,
            invoice_date: invoice.invoice_date,
            due_date: invoice.due_date,
            total_amount: invoice.total_amount,
            status: invoice.status
          };

          const modal = new bootstrap.Modal(
            document.getElementById('EditInvoiceModal')
          );
          modal.show();
        },


        validateEditForm() {
        let isValid = true;

        if (!this.form.customer_id) {
            document.getElementById('customer_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('customer_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/invoices/${this.form.id}`, this.form);

            toast.fire('Success!', 'Invoice updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditInvoiceModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update customer',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },
        navigateTo(location){
            this.$router.push(location)
        },
        deleteInvoice(id){
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
                  axios.delete('/api/invoices/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Invoice has been deleted.',
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
          axios.get('/api/invoices')
            .then((response) => {
              this.invoices = response.data.invoices;
              this.customers = response.data.customers;
              console.log(response)

              setTimeout(() => {
                $("#InvoicesTable").DataTable();
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
    
    
    