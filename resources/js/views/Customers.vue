<template>
    <Master>
        <section class="section dashboard">
          <div class="row">
    
                <!-- Top Selling -->
                <div class="col-12">
                  <div class="card top-selling overflow-auto">
    
    
                    <div class="card-body pb-0">
                      <h5 class="card-title">Customers <span>| Clients who have visited AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addCustomer()"
                                >
                                  Add Customer
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
    
                      <table id="CustomersTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Phone</th>
                            <th scope="col">Visits</th>
                            <th scope="col">Loyalty Card</th>
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
<tr v-for="customer in customers" :key="customer.id">

  <!-- NAME + AVATAR -->
  <td class="d-flex align-items-center gap-2">

    <!-- REAL IMAGE -->
    <img
      v-if="customer.image && customer.image !== 'null' && customer.image !== ''"
      :src="'/storage/' + customer.image"
      class="rounded-circle border flex-shrink-0"
      style="width:40px; height:40px; object-fit:cover;"
    />

    <!-- GOOGLE STYLE INITIALS AVATAR -->
    <div
      v-else
      class="rounded-circle border d-flex align-items-center justify-content-center text-white fw-bold flex-shrink-0"
      :style="{
        width: '40px',
        height: '40px',
        backgroundColor: getAvatarColor(customer.name),
        fontSize: '14px'
      }"
    >
      {{ getInitials(customer.name) }}
    </div>

    <!-- NAME -->
    <span>
      {{ customer.name }}
    </span>

    <!-- RISK BADGE -->
    <span v-if="customer.is_risky" class="badge bg-danger ms-2" title="Has unpaid invoices">
      RISKY
    </span>

  </td>

  <!-- PHONE -->
  <td>{{ customer.phone ?? "N/A" }}</td>

  <!-- VISITS -->
  <td>{{ customer.visits_count }}</td>

  <!-- LOYALTY -->
  <td>
    <span v-if="customer.loyalty_card" class="badge bg-success">Issued</span>
    <span v-else class="badge bg-secondary">None</span>
  </td>

  <!-- ACTIONS -->
  <td>
    <div class="btn-group" role="group">

      <button
        type="button"
        class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
        style="background-color: darkgreen; border-color: darkgreen;"
        data-bs-toggle="dropdown"
      >
        Action
      </button>

      <div class="dropdown-menu">

        <a @click="viewCustomer(customer)" class="dropdown-item" href="#">
          <i class="ri-eye-fill mr-2"></i>View
        </a>

        <a
          v-if="customer.visits_count >= 1 && !customer.loyalty_card"
          @click="openLoyaltyModal(customer)"
          class="dropdown-item"
          href="#"
        >
          <i class="ri-star-fill mr-2"></i>Issue Loyalty Card
        </a>

        <a
          v-if="customer.loyalty_card"
          @click="viewLoyaltyCard(customer)"
          class="dropdown-item"
          href="#"
        >
          <i class="ri-vip-crown-fill mr-2"></i>View Loyalty Card
        </a>

        <a @click="editCustomer(customer)" class="dropdown-item" href="#">
          <i class="ri-pencil-fill mr-2"></i>Edit
        </a>

        <a @click="deleteCustomer(customer.id)" class="dropdown-item" href="#">
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

                <div class="modal fade" id="LoyaltyCardModal" tabindex="-1">
                  <div class="modal-dialog modal-md">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">
                          {{ loyaltyMode === 'issue' ? 'Issue Loyalty Card' : 'View Loyalty Card' }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body" v-if="selectedCustomer">

                        <p><strong>Customer:</strong> {{ selectedCustomer.name }}</p>

                        <p>
                          <strong>Card Serial:</strong>
                          {{ selectedCustomer.card_serial ?? generateSerial(selectedCustomer) }}
                        </p>

                        <div class="d-flex flex-wrap">
                          <div
                            v-for="n in 10"
                            :key="n"
                            class="p-2 m-1 border text-center"
                            :style="{
                              width: '32px',
                              height: '32px',
                              backgroundColor: n <= selectedCustomer.visits_count ? 'darkgreen' : '#f1f1f1',
                              color: n <= selectedCustomer.visits_count ? 'white' : 'black'
                            }"
                          >
                            {{ n }}
                          </div>
                        </div>

                        <p class="mt-2">
                          <small>Each visit punches a box. Complete 10 visits for a reward!</small>
                        </p>

                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>

                        <button
                          v-if="loyaltyMode === 'issue'"
                          class="btn btn-success"
                          @click="confirmIssueCard(selectedCustomer)"
                          style="background: darkgreen; border-color: darkgreen;"
                        >
                          Issue Card
                        </button>

                      </div>

                    </div>
                  </div>
                </div>

              <!-- View Customer Modal -->
              <div class="modal fade" id="viewCustomerModal" tabindex="-1" aria-labelledby="viewCustomerModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Customer Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedCustomer">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedCustomer.name">
                          <strong>Full Name:</strong> <br> {{ selectedCustomer.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedCustomer.email">
                          <strong>Email:</strong> <br> {{ selectedCustomer.email }}
                        </div>

                        <div class="col-md-6" v-if="selectedCustomer.phone">
                          <strong>Phone:</strong> <br> {{ selectedCustomer.phone }}
                        </div>

                        <div class="col-md-6" v-if="selectedCustomer.gender">
                          <strong>Gender:</strong> <br> {{ selectedCustomer.gender }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Customer Modal -->
                <div class="modal fade" id="AddCustomerModal" tabindex="-1" aria-labelledby="AddCustomerModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddCustomerModalLabel">Add Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="data.id" />

                          <!-- First & Last Name -->
                          <div class="col-md-6">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name" class="form-control" v-model="data.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="email" class="form-control" v-model="data.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" id="phone" class="form-control" v-model="data.phone">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" v-model="data.gender">
                              <option value="">Select</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Customer Image</label>
                            <input type="file" class="form-control" @change="handleImageUpload">
                          </div>

                          <img v-if="imagePreview" :src="imagePreview" width="80" class="mt-2 rounded-circle border">


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


                <!-- EDIT Customer MODAL -->
                <div class="modal fade" id="EditCustomerModal" tabindex="-1" aria-labelledby="EditCustomerModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Customer</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- First & Last Name -->
                          <div class="col-md-12">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name_edit" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Email</label>
                            <input type="email" id="mail_edit" class="form-control" v-model="form.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" v-model="form.phone">
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Gender</label>
                            <select class="form-select" v-model="form.gender">
                              <option value="">Select</option>
                              <option value="male">Male</option>
                              <option value="female">Female</option>
                              <option value="other">Other</option>
                            </select>
                          </div>

                          <div class="col-md-6">
                            <label class="form-label">Customer Image</label>
                            <input type="file" class="form-control" @change="handleEditImageUpload">
                          </div>

                          <img v-if="editImagePreview" :src="editImagePreview" width="80" class="mt-2 rounded-circle border">

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
            selectedCustomer: {},
            errors: {},
            initializing: true,
            submitting: false,
            showLoyaltyCardModal: false,

            data: {        // ADD customer
            id: "",
            name: "",
            email: "",
            phone: "",
            gender: ""
            },

            form: {        // EDIT customer
            id: "",
            name: "",
            email: "",
            phone: "",
            gender: ""
            },
            imageFile: null,
            imagePreview: null,

            editImageFile: null,
            editImagePreview: null,
        }
      },      
      methods: { 
        handleImageUpload(e) {
          const file = e.target.files[0];
          this.imageFile = file;

          if (file) {
            this.imagePreview = URL.createObjectURL(file);
          }
        },
        getAvatarColor(name) {
          const colors = [
            '#1abc9c', '#2ecc71', '#3498db', '#9b59b6',
            '#f39c12', '#e74c3c', '#16a085', '#27ae60',
            '#2980b9', '#8e44ad', '#d35400', '#c0392b'
          ];

          let hash = 0;
          for (let i = 0; i < name.length; i++) {
            hash = name.charCodeAt(i) + ((hash << 5) - hash);
          }

          return colors[Math.abs(hash) % colors.length];
        },

        getInitials(name) {
          if (!name) return '?';

          return name
            .split(' ')
            .map(n => n[0])
            .join('')
            .toUpperCase()
            .slice(0, 2);
        },
        handleEditImageUpload(e) {
          const file = e.target.files[0];
          this.editImageFile = file;

          if (file) {
            this.editImagePreview = URL.createObjectURL(file);
          }
        },        
        openLoyaltyModal(customer){
            this.selectedCustomer = customer
            this.loyaltyMode = 'issue'

            let modal = new bootstrap.Modal(document.getElementById('LoyaltyCardModal'))
            modal.show()
        },

        viewLoyaltyCard(customer){
            this.selectedCustomer = customer
            this.loyaltyMode = 'view'

            let modal = new bootstrap.Modal(document.getElementById('LoyaltyCardModal'))
            modal.show()
        },
        confirmIssueCard(customer) {
            // Call API to create a loyalty card record
            axios.post('/api/loyalty-cards', { customer_id: customer.id, serial: this.generateSerial(customer) })
                .then(res => {
                    customer.loyalty_card = res.data;
                    customer.cardIssued = true;   // ⭐ important
                    customer.visits = 0;

                    // SweetAlert success popup with emoji
                    Swal.fire({
                        icon: 'success',
                        title: '🎉 Loyalty Card Issued!',
                        text: `Card for ${customer.name} has been successfully issued.`,
                        timer: 2000,
                        showConfirmButton: false
                    });

                    // Optional toast for extra subtle notification
                    toast.fire({
                        icon: 'success',
                        title: `✨ ${customer.name}'s loyalty card is now active!`
                    });
                })
                .catch(err => {
                    console.error(err);

                    // Error popup
                    Swal.fire({
                        icon: 'error',
                        title: '❌ Failed to Issue Card',
                        text: 'Something went wrong while issuing the loyalty card.'
                    });
                });

            // Hide the modal after issuing
            const modal = bootstrap.Modal.getInstance(document.getElementById('LoyaltyCardModal'));
            modal.hide();
        },
        generateSerial(customer) {
          return 'CYB-' + String(customer.id).padStart(4, '0');
        }, 
              
        viewCustomer(customer)
        {
          console.log(this.selectedCustomer)
          this.selectedCustomer = customer;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewCustomerModal'));
          modal.show();
        },
        editCustomer(customer) {
        this.form = {
            id: customer.id,
            name: customer.name,
            email: customer.email,
            phone: customer.phone,
            gender: customer.gender
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditCustomerModal')
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
        async submit() {
          if (!this.validateForm()) return;

          this.submitting = true;

          try {
            let formData = new FormData();
            formData.append("name", this.data.name);
            formData.append("email", this.data.email);
            formData.append("phone", this.data.phone);
            formData.append("gender", this.data.gender);

            if (this.imageFile) {
              formData.append("image", this.imageFile);
            }

            await axios.post('/api/customers', formData, {
              headers: { "Content-Type": "multipart/form-data" }
            });

            toast.fire('Success!', 'Customer added successfully', 'success');

            bootstrap.Modal.getInstance(
              document.getElementById('AddCustomerModal')
            ).hide();

            this.resetAddForm();
            this.loadLists();

          } catch (error) {
            console.error(error);
            toast.fire('Error!', 'Something went wrong', 'error');
          } finally {
            this.submitting = false;
          }
        },
        addCustomer()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddCustomerModal'));
          modal.show();
        },
        async submitChanges() {
          if (!this.validateEditForm()) return;

          this.submitting = true;

          try {
            let formData = new FormData();
            formData.append("name", this.form.name);
            formData.append("email", this.form.email);
            formData.append("phone", this.form.phone);
            formData.append("gender", this.form.gender);
            formData.append("_method", "PUT");

            if (this.editImageFile) {
              formData.append("image", this.editImageFile);
            }

            await axios.post(`/api/customers/${this.form.id}`, formData, {
              headers: { "Content-Type": "multipart/form-data" }
            });

            toast.fire('Success!', 'Customer updated successfully', 'success');

            bootstrap.Modal.getInstance(
              document.getElementById('EditCustomerModal')
            ).hide();

            this.loadLists();

          } catch (error) {
            console.error(error);
            toast.fire('Error!', 'Update failed', 'error');
          } finally {
            this.submitting = false;
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
        async submitChanges() {
          if (!this.validateEditForm()) return;

          this.submitting = true;

          try {
            let formData = new FormData();
            formData.append("name", this.form.name);
            formData.append("email", this.form.email);
            formData.append("phone", this.form.phone);
            formData.append("gender", this.form.gender);
            formData.append("_method", "PUT");

            if (this.editImageFile) {
              formData.append("image", this.editImageFile);
            }

            await axios.post(`/api/customers/${this.form.id}`, formData, {
              headers: { "Content-Type": "multipart/form-data" }
            });

            toast.fire('Success!', 'Customer updated successfully', 'success');

            bootstrap.Modal.getInstance(
              document.getElementById('EditCustomerModal')
            ).hide();

            this.loadLists();

          } catch (error) {
            console.error(error);
            toast.fire('Error!', 'Update failed', 'error');
          } finally {
            this.submitting = false;
          }
        },
        navigateTo(location){
            this.$router.push(location)
        },
        deleteCustomer(id){
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
                  axios.delete('/api/customers/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Customer has been deleted.',
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
          axios.get('/api/customers')
            .then((response) => {
              this.customers = response.data;
              console.log(response)

              setTimeout(() => {
                $("#CustomersTable").DataTable();
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
    
    
    