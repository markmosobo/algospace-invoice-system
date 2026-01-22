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
                      <h5 class="card-title">Pending Invoices <span>| Pending invoices of AlgoSpace Cyber customers</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addInvoice()"
                                >
                                  Add Invoice
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
    
                      <table id="InvoicesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Customer</th>
                            <th scope="col">Invoice Date</th>
                            <th scope="col">Due Date</th>
                            <th scope="col">Amount</th>
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
                            <td>{{item.customer.name}}</td>
                            <td>{{item.invoice_date ?? "N/A"}}</td>
                            <td>{{item.due_date ?? "N/A"}}</td>

                            <td>{{item.total_amount ?? "N/A"}}</td>
                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewInvoice(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editInvoice(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteInvoice(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
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

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedInvoice.customer_id">
                          <strong>Customer:</strong> <br> {{ selectedInvoice.customer.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedInvoice.invoice_number">
                          <strong>Invoice Number:</strong> <br> {{ selectedInvoice.invoice_number }}
                        </div>

                        <div class="col-md-6" v-if="selectedInvoice.invoice_date">
                          <strong>Invoice Date:</strong> <br> {{ selectedInvoice.invoice_date }}
                        </div>

                        <div class="col-md-6" v-if="selectedInvoice.due_date">
                          <strong>Due Date:</strong> <br> {{ selectedInvoice.due_date }}
                        </div>

                        <div class="col-md-6" v-if="selectedInvoice.total_amount">
                          <strong>Amount:</strong> <br> {{ selectedInvoice.total_amount }}
                        </div>                        

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Invoice Modal -->
                <div class="modal fade" id="AddInvoiceModal" tabindex="-1" aria-labelledby="AddInvoiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddInvoiceModalLabel">Add Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Customer Name -->
                          <div class="col-md-6">
                            <label class="form-label">Customer*</label>
                            <select name="customer" v-model="data.customer_id" class="form-select" id="customer">
                                <option value="0" selected disabled>Select Customer</option>
                                <option v-for="customer in customers" :value="customer.id"
                                :selected="customer.id == data.customer_id" :key="customer.id">{{ customer.name}} </option>
    
                            </select>
                          </div>

                          <!-- Invoice Date -->
                          <div class="col-md-6">
                            <label class="form-label">Invoice Date</label>
                            <input type="date" id="invoice_date" class="form-control" v-model="data.invoice_date" required>
                          </div>

                          <!-- Due Date -->
                          <div class="col-md-6">
                            <label class="form-label">Due Date</label>
                            <input type="date" id="due_date" class="form-control" v-model="data.due_date">
                          </div>

                          <!-- Amount -->
                          <div class="col-md-6">
                            <label class="form-label">Amount</label>
                            <input type="number" id="total_amount" class="form-control" v-model="data.total_amount">
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


                <!-- EDIT Invoice MODAL -->
                <div class="modal fade" id="EditInvoiceModal" tabindex="-1" aria-labelledby="EditInvoiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Invoice</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <div class="col-md-6">
                            <label class="form-label">Customer</label>
                            <select name="customer" v-model="form.customer_id" class="form-select" id="customer_edit">
                                <option value="0" selected disabled>Select Customer</option>
                                <option v-for="customer in customers" :value="customer.id"
                                :selected="customer.id == form.customer_id" :key="customer.id">{{ customer.name}} </option>
    
                            </select>
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

            data: {        // ADD invoice
              customer_id: "",
              invoice_number: "",
              invoice_date: "",
              due_date: "",
              total_amount: "",
              status: "pending"
            },

            form: {        // EDIT invoice
              customer_id: "",
              invoice_number: "",
              invoice_date: "",
              due_date: "",
              total_amount: "",
              status: "pending"
            }
        }
      },      
      methods: {                
        viewInvoice(invoice)
        {
          console.log(this.selectedInvoice)
          this.selectedInvoice = invoice;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
          modal.show();
        },
        editInvoice(invoice) {
        this.form = {
            id: invoice.id,
            invoice_number: invoice.invoice_number,
            invoice_date: invoice.invoice_date,
            due_date: invoice.due_date,
            total_amount: invoice.total_amount
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditInvoiceModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.name) {
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

        addInvoice()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddInvoiceModal'));
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

        if (!this.data.customer_id) {
            document.getElementById('customer').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('customer').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/invoices', this.data);

            toast.fire('Success!', 'Invoice added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddInvoiceModal')
            );
            modal.hide();

            // Reset form
            this.data = {
            customer_id: "",
            invoice_number: "",
            invoice_date: "",
            due_date: "",
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
              this.invoices = response.data.draftinvoices;
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
    
    
    