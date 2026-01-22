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
                      <h5 class="card-title">Quick sales <span>| Sales at AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addQuickSale()"
                                >
                                  Add Quick Sale
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
    
                    <table id="SalesTable" class="table table-borderless">
                    <thead>
                        <tr>
                        <th scope="col">Invoice No</th>
                        <th scope="col">Customer</th>
                        <th scope="col">Amount</th>
                        <th scope="col">Method</th>
                        <th scope="col">Payment Date</th>
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
                        <tr v-for="sale in quickSales" :key="sale.id">
                        <td>{{ sale.invoice_no }}</td>
                        <td>{{ sale.customer_name }}</td>
                        <td>{{ sale.amount }}</td>
                        <td>{{ sale.method }}</td>
                        <td>{{ sale.payment_date }}</td>
                        <td>
                            <span class="badge bg-success" v-if="sale.status === 'paid'">Paid</span>
                            <span class="badge bg-warning" v-else>Pending</span>
                        </td>

                        <td>
                            <div class="btn-group" role="group">
                            <button class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                                    data-bs-toggle="dropdown" aria-expanded="false">
                                Action
                            </button>
                            <div class="dropdown-menu">
                                <a @click="viewSale(sale)" class="dropdown-item" href="#">
                                <i class="ri-eye-fill mr-2"></i>View
                                </a>
                                <a @click="editSale(sale)" class="dropdown-item" href="#">
                                <i class="ri-pencil-fill mr-2"></i>Edit
                                </a>
                                <a @click="deleteSale(sale.id)" class="dropdown-item" href="#">
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


                <!-- Quick Sale Wizard Modal -->
                <div class="modal fade" id="quickSaleWizardModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Quick Sale Wizard</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- STEP INDICATOR -->
                        <div class="mb-3">
                        <span class="badge bg-success" v-if="step === 1">Step 1: Customer</span>
                        <span class="badge bg-success" v-if="step === 2">Step 2: Invoice</span>
                        <span class="badge bg-success" v-if="step === 3">Step 3: Payment</span>
                        </div>

<!-- STEP 1: CUSTOMER -->
<div v-if="step === 1">

  <!-- RADIO: Choose Existing or New -->
  <div class="mb-3">
    <label class="form-label">Customer Type</label>
    <div class="btn-group w-100" role="group">
      <button
        type="button"
        class="btn"
        :class="customerForm.type === 'existing' ? 'btn-success' : 'btn-outline-secondary'"
        @click="customerForm.type = 'existing'"
      >
        Existing
      </button>
      <button
        type="button"
        class="btn"
        :class="customerForm.type === 'new' ? 'btn-success' : 'btn-outline-secondary'"
        @click="customerForm.type = 'new'"
      >
        New
      </button>
    </div>
  </div>

  <!-- EXISTING CUSTOMER SELECT -->
  <div v-if="customerForm.type === 'existing'" class="col-md-12">
    <label class="form-label">Select Customer</label>
    <select class="form-select" v-model="customerForm.customer_id">
      <option value="">-- Search & Select --</option>
      <option v-for="c in customers" :key="c.id" :value="c.id">
        {{ c.name }} ({{ c.phone || 'No Phone' }})
      </option>
    </select>
  </div>

  <!-- NEW CUSTOMER FORM -->
  <div v-if="customerForm.type === 'new'" class="row g-3">

    <div class="col-md-4">
      <label class="form-label">Name*</label>
      <input type="text" class="form-control" v-model="customerForm.name" required>
    </div>

    <div class="col-md-4">
      <label class="form-label">Email</label>
      <input type="email" class="form-control" v-model="customerForm.email">
    </div>

    <div class="col-md-4">
      <label class="form-label">Phone</label>
      <input type="text" class="form-control" v-model="customerForm.phone">
    </div>

  </div>

</div>



                        <!-- STEP 2: INVOICE -->
                        <div v-if="step === 2">
                        <div class="row g-3">
                            <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" v-model="invoiceForm.due_date">
                            </div>

                            <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" v-model="invoiceForm.total_amount">
                            </div>
                        </div>
                        </div>

                        <!-- STEP 3: PAYMENT -->
                        <div v-if="step === 3">
                        <div class="row g-3">
                            <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" class="form-control" v-model="paymentForm.amount">
                            </div>

                            <div class="col-md-6">
                            <label class="form-label">Payment Date</label>
                            <input type="date" class="form-control" v-model="paymentForm.payment_date">
                            </div>

                            <div class="col-md-6">
                            <label class="form-label">Method</label>
                            <select class="form-select" v-model="paymentForm.method">
                                <option value="cash">Cash</option>
                                <option value="mpesa">M-Pesa</option>
                                <option value="bank">Bank</option>
                            </select>
                            </div>

                            <div class="col-md-6" v-if="paymentForm.method === 'mpesa'">
                            <label class="form-label">Mpesa Code</label>
                            <input type="text" class="form-control" v-model="paymentForm.mpesa_code">
                            </div>
                        </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" @click="prevStep" v-if="step > 1">Back</button>
                        <button class="btn btn-success" @click="nextStep" v-if="step < 3">Next</button>
                        <button class="btn btn-primary" @click="submitWizard" v-if="step === 3">Finish</button>
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
      customers: [],
      quickSales: [],
      initializing: true,

      step: 1,
      customerForm: { customer_id: null, type: 'existing', name: '', email: '', phone: '' },
      invoiceForm: { due_date: '', total_amount: '' },
      paymentForm: { 
        amount: '', 
        payment_date: new Date().toISOString().slice(0, 10), 
        method: 'cash',
        mpesa_code: ''
      }
    }
  },
  watch: {
    'customerForm.customer_id'(val) {
        const c = this.customers.find(x => x.id === val);
        if (c) {
        this.customerForm.name = c.name;
        this.customerForm.email = c.email;
        this.customerForm.phone = c.phone;
        }
    }
   },

  methods: {
    addQuickSale()
    {
        // Reset wizard state
        this.step = 1;

        this.customerForm = { name: '', email: '', phone: '' };
        this.invoiceForm = { due_date: '', total_amount: '' };
        this.paymentForm = {
        amount: '',
        payment_date: new Date().toISOString().slice(0, 10),
        method: 'cash'
        };        
        // Show the modal after fetching data
        const modal = new bootstrap.Modal(document.getElementById('quickSaleWizardModal'));
        modal.show();
    },
    // Wizard Controls
    nextStep() { this.step++; },
    prevStep() { this.step--; },

    async submitWizard() {
    try {
        let customerId = this.customerForm.customer_id;

        // If no customer selected, create new customer
        if (!customerId) {
        const res = await axios.post('/api/customers', {
            name: this.customerForm.name,
            email: this.customerForm.email,
            phone: this.customerForm.phone
        });
        customerId = res.data.id;
        }

        // Create invoice
        const invoice = await axios.post('/api/invoices', {
        customer_id: customerId,
        due_date: this.invoiceForm.due_date,
        total_amount: this.invoiceForm.total_amount
        });

        // Create payment
        await axios.post('/api/payments', {
        invoice_id: invoice.data.id,
        amount: this.paymentForm.amount,
        payment_date: this.paymentForm.payment_date,
        method: this.paymentForm.method,
        mpesa_code: this.paymentForm.mpesa_code
        });

        toast.fire({
        icon: 'success',
        title: 'Quick sale created successfully'
        });

        // Close modal
        $('#quickSaleWizardModal').modal('hide');
        this.loadLists();

    } catch (err) {
        console.error(err);
        toast.fire({
        icon: 'error',
        title: 'Failed to create quick sale'
        });
    }
    },


    resetWizard() {
      this.step = 1;
      this.customerForm = { name: '', email: '', phone: '' };
      this.invoiceForm = { due_date: '', total_amount: '' };
      this.paymentForm = {
        amount: '',
        payment_date: new Date().toISOString().slice(0, 10),
        method: 'cash',
        mpesa_code: ''
      };
    },

    // Load quick sales list
    loadLists() {
        this.initializing = true; // Start spinner
        axios.get('/api/quick-sales')
        .then((response) => {
            this.sales = response.data.quickSales;
            this.customers = response.data.customers;
            console.log(response)

            setTimeout(() => {
            $("#SalesTable").DataTable();
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

  components: {
    Master,
  },

  mounted(){
    this.loadLists();
  }
}
</script>

    
    
    