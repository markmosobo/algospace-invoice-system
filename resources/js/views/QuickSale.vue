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
                      <h5 class="card-title">Quick Sales <span>| Sales at AlgoSpace Cyber</span></h5>
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
                            <span
                              class="badge"
                              :class="{
                                'bg-success': sale.status === 'paid',
                                'bg-warning': sale.status === 'pending',
                                'bg-danger': sale.status === 'overdue'
                              }"
                            >
                              {{ sale.status.toUpperCase() }}
                            </span>
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
                                <div class="dropdown-divider"></div>

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

                        <!-- Services Dropdown -->
                        <div class="row g-3">
                            <div class="col-md-6">
                            <label class="form-label">Select Service</label>
                            <select class="form-control" v-model="selectedService">
                                <option v-for="s in services" :value="s.id">
                                {{ s.name }} - KES {{ s.price }} / {{ s.unit }}
                                </option>
                            </select>
                            </div>

                            <div class="col-md-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" v-model="quantity" min="1">
                            </div>

                            <div class="col-md-3">
                            <label class="form-label">Add</label>
                            <button class="btn btn-primary w-100" @click="addItem">Add</button>
                            </div>
                        </div>

                        <!-- Selected Items Table -->
                        <div class="mt-3">
                            <table class="table table-bordered">
                            <thead>
                                <tr>
                                <th>Service</th>
                                <th>Unit Price</th>
                                <th>Qty</th>
                                <th>Line Total</th>
                                <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="item in invoiceForm.items" :key="item.service_id">
                                <td>{{ item.name }}</td>
                                <td>{{ item.price }}</td>
                                <td>{{ item.quantity }}</td>
                                <td>{{ item.line_total }}</td>
                                <td>
                                    <button class="btn btn-sm btn-danger" @click="removeItem(item.service_id)">Remove</button>
                                </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>

                        <!-- Due date + Total -->
                        <div class="row g-3 mt-2">
                            <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" class="form-control" v-model="invoiceForm.due_date">
                            </div>

                            <div class="col-md-6">
                            <label class="form-label">Total Amount</label>
                            <input type="number" class="form-control" :value="invoiceForm.total_amount" readonly>
                            </div>
                        </div>

                        <!-- Invoice Preview Button -->
                        <div class="mt-3" v-if="invoiceForm.items.length > 0">
                        <button class="btn btn-info mb-2" @click="previewInvoice">
                            Generate Preview
                        </button>

                        <!-- PDF Preview iframe -->
                        <div v-if="previewPdf" class="mt-2">
                            <iframe 
                            :src="previewPdf" 
                            style="width:100%; height:400px;" 
                            frameborder="0">
                            </iframe>
                        </div>
                        </div>

                        <!-- Invoice Sharing Options -->
                        <div class="mt-4 border-top pt-3" v-if="invoiceForm.items.length > 0">
                        <h6 class="fw-bold">Share Invoice</h6>

                        <div class="btn-group w-100">
                            <button class="btn btn-success" @click="shareWhatsApp">
                            WhatsApp
                            </button>
                            <button class="btn btn-primary" @click="shareEmail">
                            Email
                            </button>
                            <button class="btn btn-secondary" @click="downloadInvoice">
                            Download PDF
                            </button>
                            <button class="btn btn-outline-dark" @click="printInvoice">
                            Print
                            </button>
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

                            <div class="col-md-12">
                            <label class="form-label">Comment</label>
                            <textarea class="form-control" v-model="paymentForm.comment" rows="2"></textarea>
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

                <!-- Edit Modal -->
                <div class="modal fade" id="editSaleModal" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Sale</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-2">
                        <label>Amount</label>
                        <input v-model="editForm.amount" class="form-control" type="number">
                        </div>
                        <div class="mb-2">
                        <label>Method</label>
                        <select v-model="editForm.method" class="form-control">
                            <option value="cash">Cash</option>
                            <option value="mpesa">Mpesa</option>
                            <option value="bank">Bank</option>
                        </select>
                        </div>
                        <div class="mb-2">
                        <label>Payment Date</label>
                        <input v-model="editForm.payment_date" class="form-control" type="date">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" @click="updateSale()">Save</button>
                    </div>
                    </div>
                </div>
                </div>

                <!-- VIEW SALE MODAL -->
                <div class="modal fade" id="viewSaleModal" tabindex="-1">
                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">
                        Invoice {{ viewSaleData.invoice_no }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">

                        <!-- CUSTOMER INFO -->
                        <div class="mb-3">
                        <h6 class="fw-bold">Customer</h6>
                        <p class="mb-1"><strong>Name:</strong> {{ viewSaleData.customer_name }}</p>
                        </div>

                        <hr>

                        <!-- INVOICE ITEMS -->
                        <h6 class="fw-bold">Items</h6>
                        <table class="table table-sm table-bordered">
                        <thead>
                            <tr>
                            <th>Service</th>
                            <th>Unit Price</th>
                            <th>Qty</th>
                            <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="item in viewSaleData.items" :key="item.id">
                            <td>{{ item.service_name }}</td>
                            <td>KES {{ item.unit_price }}</td>
                            <td>{{ item.quantity }}</td>
                            <td>KES {{ item.line_total }}</td>
                            </tr>
                        </tbody>
                        </table>

                        <hr>

                        <!-- TOTALS -->
                        <div class="row">
                        <div class="col-md-6">
                            <p><strong>Invoice Total:</strong> KES {{ viewSaleData.invoice_total }}</p>
                            <p><strong>Total Paid:</strong> KES {{ viewSaleData.total_paid }}</p>
                            <p>
                            <strong>Status:</strong>
                            <span
                                class="badge"
                                :class="viewSaleData.status === 'paid' ? 'bg-success' : 'bg-warning'"
                            >
                                {{ viewSaleData.status }}
                            </span>
                            </p>
                        </div>

                        <div class="col-md-6">
                            <p><strong>Payment Method:</strong> {{ viewSaleData.method }}</p>
                            <p><strong>Payment Date:</strong> {{ viewSaleData.payment_date }}</p>
                            <p v-if="viewSaleData.mpesa_code">
                            <strong>M-Pesa Code:</strong> {{ viewSaleData.mpesa_code }}
                            </p>
                            <p v-if="viewSaleData.comment">
                            <strong>Comment:</strong> {{ viewSaleData.comment }}
                            </p>
                        </div>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">
                        Close
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
      customers: [],
      services: [],
      quickSales: [],
      initializing: true,
      selectedService: null,
      quantity: 1, 
      viewSaleData: {
        invoice_no: '',
        customer_name: '',
        items: [],
        invoice_total: 0,
        total_paid: 0,
        status: '',
        method: '',
        payment_date: '',
        mpesa_code: '',
        comment: ''
     },
   

      step: 1,
      invoiceId: null,
      invoicePdfUrl: null,
      previewPdf: null, // URL of generated preview PDF
      printUrl: null,   // print URL

      customerForm: { customer_id: null, type: 'existing', name: '', email: '', phone: '' },
      invoiceForm: { due_date: new Date().toISOString().substr(0, 10), total_amount: 0, items: [] },
      paymentForm: { 
        amount: '', 
        payment_date: new Date().toISOString().slice(0, 10), 
        method: 'cash',
        mpesa_code: '',
        comment: ''
      },
      editForm: {
        id: null,
        amount: '',
        method: '',
        payment_date: ''
      },

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
    previewInvoice() {
        if (!this.invoiceForm.items.length) {
            toast.fire({ icon: 'warning', title: 'Add at least one item to preview' });
            return;
        }

        axios.post('/api/invoices/preview', {
            customer: this.customerForm,
            items: this.invoiceForm.items,
            due_date: this.invoiceForm.due_date
        }).then(res => {
            this.previewPdf = res.data.pdf_url;
            this.printUrl = res.data.print_url;
            this.invoicePdfUrl = res.data.pdf_url; // optional: for sharing buttons
            toast.fire({ icon: 'success', title: 'Preview generated!' });
        }).catch(err => {
            console.error(err);
            toast.fire({ icon: 'error', title: 'Failed to generate preview' });
        });
    },

    shareWhatsApp() {
        if (!this.previewPdf) {
            return toast.fire({ icon: 'info', title: 'Generate preview first' });
        }

        const amount = this.invoiceForm.total_amount;
        const dueDate = this.invoiceForm.due_date;
        const invoiceNo = 'PREVIEW'; // or store preview invoice no if returned
        const customerName = this.customerForm.name || 'Customer';

        const text = `
    Dear ${customerName},

    🧾 *INVOICE PREVIEW*
    Invoice No: ${invoiceNo}
    Amount Due: KES ${amount}
    Due Date: ${dueDate}

    💳 *Payment Options*
    • Cash
    • M-Pesa Pochi: 0112514440
    Account: INVOICE-${invoiceNo}
    • Bank Transfer (on request)

    📄 Download Invoice:
    ${this.previewPdf}

    Thank you for choosing AlgoSpace Cyber & Tech Services.
        `.trim();

        window.open(
            `https://wa.me/?text=${encodeURIComponent(text)}`,
            '_blank'
        );
    },


    shareEmail() {
        if (!this.previewPdf) return toast.fire({ icon: 'info', title: 'Generate preview first' });

        axios.post('/api/invoices/preview/email', {
            email: this.customerForm.email,
            pdf_path: this.previewPdf.replace(window.location.origin + '/', '')
        }).then(() => {
            toast.fire({ icon: 'success', title: 'Preview emailed!' });
        }).catch(() => {
            toast.fire({ icon: 'error', title: 'Failed to email preview' });
        });
    },

    downloadInvoice() {
        if (!this.previewPdf) return toast.fire({ icon: 'info', title: 'Generate preview first' });
        window.open(this.previewPdf, '_blank');
    },

    printInvoice() {
        if (!this.printUrl) return toast.fire({ icon: 'info', title: 'Generate preview first' });
        window.open(this.printUrl, '_blank');
    },

    async viewSale(sale) {
        try {
            const res = await axios.get(`/api/sales/${sale.id}`);

            this.viewSaleData = res.data;

            const modal = new bootstrap.Modal(
            document.getElementById('viewSaleModal')
            );
            modal.show();

        } catch (err) {
            toast.fire({
            icon: 'error',
            title: 'Failed to load sale details'
            });
        }
    },

    addQuickSale() {
        // Reset wizard state
        this.step = 1;

        this.customerForm = { name: '', email: '', phone: '' };
        this.invoiceForm = { 
            due_date: new Date().toISOString().substr(0, 10), 
            total_amount: 0, 
            items: [] // <--- important
        };
        this.paymentForm = {
            amount: '',
            payment_date: new Date().toISOString().slice(0, 10),
            due_date: new Date().toISOString().slice(0, 10),
            method: 'cash',
            mpesa_code: ''
        };        

        // Show the modal after fetching data
        const modal = new bootstrap.Modal(document.getElementById('quickSaleWizardModal'));
        modal.show();
    },

    // Wizard Controls
    nextStep() {
    // If going from step 2 -> step 3, auto-fill amount
    if (this.step === 2) {
        this.paymentForm.amount = this.invoiceForm.total_amount;
    }

    this.step++;
    },
    prevStep() { this.step--; },

    async submitWizard() {
    try {
        let customerId = this.customerForm.customer_id;

        if (!customerId) {
        const res = await axios.post('/api/customers', {
            name: this.customerForm.name,
            email: this.customerForm.email,
            phone: this.customerForm.phone
        });
        customerId = res.data.id;
        }

        const invoice = await axios.post('/api/invoices', {
        customer_id: customerId,
        due_date: this.invoiceForm.due_date,
        total_amount: this.invoiceForm.total_amount,
        items: this.invoiceForm.items
        });

        this.paymentForm.mpesa_code =
        this.paymentForm.method === 'mpesa'
            ? String(this.paymentForm.mpesa_code || "")
            : "";

        await axios.post('/api/payments', {
        invoice_id: invoice.data.invoice.id,
        amount: this.paymentForm.amount,
        payment_date: this.paymentForm.payment_date,
        method: this.paymentForm.method,
        mpesa_code: this.paymentForm.mpesa_code,
        comment: this.paymentForm.comment
        });

        // 🔥 CLOSE THE EXISTING MODAL
        const modal = bootstrap.Modal.getInstance(document.getElementById('quickSaleWizardModal'));
        modal.hide();

        toast.fire({
        icon: 'success',
        title: 'Quick sale created successfully'
        });

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
        this.invoiceForm = { 
            due_date: new Date().toISOString().substr(0, 10), 
            total_amount: 0, 
            items: [] // <--- important
        };
        this.paymentForm = {
            amount: '',
            payment_date: new Date().toISOString().slice(0, 10),
            method: 'cash',
            mpesa_code: ''
        };
    },

    addItem() {
        if (!this.invoiceForm.items) this.invoiceForm.items = [];
        if (!this.selectedService || this.quantity < 1) return;

        const service = this.services.find(s => s.id === this.selectedService);
        if (!service) return;

        const item = {
            service_id: service.id,
            name: service.name,
            price: service.price,
            quantity: this.quantity,
            line_total: service.price * this.quantity
        };

        this.invoiceForm.items.push(item);
        this.calculateTotal();
    },


    removeItem(id) {
        this.invoiceForm.items = this.invoiceForm.items.filter(i => i.service_id !== id);
        this.calculateTotal();
    },

    calculateTotal() {
        this.invoiceForm.total_amount = this.invoiceForm.items.reduce((acc, item) => {
        return acc + item.line_total;
        }, 0);
    },
    editSale(sale) {
        this.editForm.id = sale.id;
        this.editForm.amount = sale.amount;
        this.editForm.method = sale.method;
        this.editForm.payment_date = sale.payment_date;

        // open modal
        const modal = new bootstrap.Modal(document.getElementById('editSaleModal'));
        modal.show();
    },

    async updateSale() {
        if (!this.editForm.id) return;

        this.submitting = true;

        try {
            const res = await axios.put(
                `/api/sales/${this.editForm.id}`,
                this.editForm
            );

            toast.fire(
                'Success!',
                res.data.message || 'Sale updated successfully',
                'success'
            );

            // close modal
            const modal = bootstrap.Modal.getInstance(
                document.getElementById('editSaleModal')
            );
            modal.hide();

            // reload data
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
 
    async completePayment(sale) {
    try {
        await axios.post(`/api/payments/${sale.id}/complete`);

        toast.fire({
        icon: 'success',
        title: 'Payment marked as paid'
        });

        this.loadLists();
    } catch (err) {
        toast.fire({
        icon: 'error',
        title: 'Failed to update payment'
        });
    }
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
            axios.delete('/api/sales/'+id).then(() => {
            toast.fire(
            'Deleted!',
            'Sale has been deleted.',
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
    // Load quick sales list
    loadLists() {
        this.initializing = true; // Start spinner
        axios.get('/api/quick-sales')
        .then((response) => {
            this.quickSales = response.data.quickSales;
            this.customers = response.data.customers;
            this.services = response.data.services;
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

    
    
    