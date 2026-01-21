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
                            <th scope="col">Email Address</th>
                            <th scope="col">Phone</th>
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
                          <tr v-for="customer in customers" :key="user.id">
                            <td>{{customer.name}}</td>
                            <td>{{customer.email ?? "N/A"}}</td>
                            <td>{{customer.phone ?? "N/A"}}</td>

                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewCustomer(user)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editCustomer(user)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteCustomer(user.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

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


                <!-- Add Landlord Modal -->
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
                            <label class="form-label">Email*</label>
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

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id" />

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
      data(){
        return {
          customers: [],
          user: [],
          selectedCustomer: {},
          showPassword: false,
          defaultProfile: DefaultProfile,
          photoMode: 'file', // 'file' or 'url' — default to file
          errors: {},
          form: {
            id: "",
            name: "",
            email: "",
            password: "",
            phone: "",
            address: "",
            city: "",
            county: "",
            postal_code: "",
            dob: "",
            gender: "",
            property_count: 0,
            assigned_properties: "",
            skills: [],
            status: "active",

            profile_photo_file: null,
            profile_photo_preview: null,
            profile_photo_url: '' // for URL input
          },
          data: {
            id: "",
            first_name: "",
            last_name: "",
            email: "",
            password: "",
            phone: "",
            address: "",
            city: "",
            county: "",
            postal_code: "",
            dob: "",
            gender: "",
            property_count: 0,
            assigned_properties: "",
            skills: [],
            status: "active",

            profile_photo_file: null,
            profile_photo_preview: null,
            profile_photo_url: '' // for URL input
          },
          availableSkills: [
            'Plumbing',
            'Electrical',
            'Carpentry',
            'Painting',
            'Landscaping',
            'Cleaning',
            'Security',
          ],
          initializing: true

        }
      },
      watch: {
        // When mode changes, clear the other input and reset preview
        photoMode(newMode) {
          if (newMode === 'file') {
            this.data.profile_photo_url = '';
            this.data.profile_photo_preview = this.data.profile_photo_file 
              ? this.data.profile_photo_preview 
              : '';
          } else if (newMode === 'url') {
            this.data.profile_photo_file = null;
            this.data.profile_photo_preview = this.data.profile_photo_url || '';
          }
        }
      },      
      methods: {       
        handlePhotoUpload(event) {
          const file = event.target.files[0];
          if (!file) return;

          // Validate type & size
          if (!file.type.startsWith('image/')) {
            this.errors.profile_photo = 'Selected file is not an image';
            return;
          }
          if (file.size > 5 * 1024 * 1024) { // 5MB limit
            this.errors.profile_photo = 'Image must be <= 5 MB';
            return;
          }

          this.errors.profile_photo = null;
          this.data.profile_photo_file = file;

          // Preview
          const reader = new FileReader();
          reader.onload = e => this.data.profile_photo_preview = e.target.result;
          reader.readAsDataURL(file);
          },
          handleEditPhotoUpload(event) {
            const file = event.target.files[0];
            if (file) {
              this.form.profile_photo_file = file;
              this.form.profile_photo_preview = URL.createObjectURL(file);
            }
          },

          toggleSkill(skill) {
          const index = this.data.skills.indexOf(skill);
          if (index > -1) {
            // Remove if already selected
            this.data.skills.splice(index, 1);
          } else {
            // Add if not selected
            this.data.skills.push(skill);
          }
        },
        updatePreviewFromUrl() {
          const url = this.data.profile_photo_url?.trim();
          if (!url) {
            this.data.profile_photo_preview = '';
            return;
          }

          // Optional: simple extension check
          const lower = url.toLowerCase();
          if (!lower.match(/\.(jpeg|jpg|png|gif|svg|webp)(\?.*)?$/)) {
            // Could warn user if desired
          }

          this.data.profile_photo_preview = url;
          this.errors.profile_photo = null;
        }, 
        updateEditPreviewFromUrl() {
          this.form.profile_photo_preview = this.form.profile_photo_url;
        },
          
        viewCustomer(customer)
        {
          console.log(this.selectedCustomer)
          this.selectedCustomer = customer;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewCustomerModal'));
          modal.show();
        },
        editCustomer(landlord)
        {
          this.form = {
            id: landlord.id,
            name: landlord.name ?? "",
            email: landlord.email ?? "",
            role: landlord.role ?? "",
            phone: landlord.phone ?? "",
            address: landlord.address ?? "",
            city: landlord.city ?? "",
            county: landlord.county ?? "",
            postal_code: landlord.postal_code ?? "",
            dob: landlord.dob ?? "",
            gender: landlord.gender ?? "",
            status: landlord.status ?? "",

            property_count: landlord.property_count ?? "",
            assigned_properties: landlord.assigned_properties ?? "",
            skills: landlord.skills ?? [],
            profile_photo_url: landlord.profile_photo_url || null,
            profile_photo_preview: landlord.profile_photo 
                ? `/storage/${landlord.profile_photo}`
                : landlord.profile_photo_url
          }; 
           // Set correct mode
          this.photoMode = landlord.profile_photo ? "file" : "url";         
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('editCustomerModal'));
          modal.show();
        },
        validateFormChanges() {
          let isValid = true;

          if (!this.form.name) {
            isValid = false;
            document.getElementById('name_edit').classList.add('is-invalid');
          } else {
            document.getElementById('name_edit').classList.remove('is-invalid');
          }

          if (!this.form.email) {
            isValid = false;
            document.getElementById('mail_edit').classList.add('is-invalid');
          } else {
            document.getElementById('mail_edit').classList.remove('is-invalid');
          }

          return isValid;
        },
          
        async submitChanges() {
            if (this.validateFormChanges()) {        
                // Start submitting process
                this.submitting = true;
                
                try {
                    // Simulate asynchronous submission process (you would replace this with your actual submission logic)
                    await this.submitFormChanges();

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
        async submitFormChanges() {
          try {
            let formData = new FormData();

            // Merge names back to single "name" for backend
            const fullName = `${this.form.first_name} ${this.form.last_name}`.trim();
            formData.append("name", fullName);

            // Append normal fields
            const fields = [
              "email", "role", "phone", "address", "city",
              "county", "postal_code", "dob", "gender",
              "status", "property_count", "assigned_properties"
            ];

            fields.forEach(field => {
              if (this.form[field] !== undefined) {
                formData.append(field, this.form[field]);
              }
            });

            // Handle Skills (array)
            if (Array.isArray(this.form.skills)) {
              formData.append("skills", JSON.stringify(this.form.skills));
            }

            // Handle Photo Upload
            if (this.photoMode === "file" && this.form.profile_photo_file) {
              formData.append("profile_photo", this.form.profile_photo_file);
            }

            // Handle Photo URL
            if (this.photoMode === "url" && this.form.profile_photo_url) {
              formData.append("profile_photo_url", this.form.profile_photo_url);
            }

            const response = await axios.post(
              `/api/customers/${this.form.id}?_method=PUT`,
              formData,
              { headers: { "Content-Type": "multipart/form-data" } }
            );

            // Success
            toast.fire('Success!', 'Customer details updated!', 'success');

            const modal = bootstrap.Modal.getInstance(
              document.getElementById('EditCustomerModal')
            );
            modal.hide();

            this.loadLists();

          } catch (error) {
            console.error(error);
            toast.fire(
              'Error!',
              error.response?.data?.message || 'Something went wrong.',
              'error'
            );
          }
        },

        addLandlord()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddCustomerModal'));
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

          const fields = [
            { id: 'first_name', value: this.data.first_name },
            { id: 'last_name',  value: this.data.last_name },
            { id: 'email',      value: this.data.email },
            { id: 'password',   value: this.data.password },
          ];

          fields.forEach(field => {
            const el = document.getElementById(field.id);

            if (!field.value || field.value === "") {
              el.classList.add('is-invalid');
              isValid = false;
            } else {
              el.classList.remove('is-invalid');
            }
          });

          return isValid;
        },
       
        async submitForm() {
          try {
            // Prepare FormData for file upload + other fields
            const formData = new FormData();

            // Append all fields
            for (const key in this.data) {
              if (key === 'profile_photo_file' && this.data.profile_photo_file) {
                // append the actual file
                formData.append('profile_photo', this.data.profile_photo_file);
              } else if (key !== 'profile_photo_file') {
                formData.append(key, this.data[key]);
              }
            }

            // Send POST request as multipart/form-data
            const response = await axios.post("api/customers", formData, {
              headers: {
                'Content-Type': 'multipart/form-data'
              }
            });

            console.log(response);

            toast.fire(
              'Success!',
              'Customer added!',
              'success'
            );

            // Close the modal after submit
            const modal = bootstrap.Modal.getInstance(document.getElementById('AddLandlordModal'));
            modal.hide();

            // Reset form properly (avoid assigning '')
            this.data = {
              id: "",
              first_name: "",
              last_name: "",
              email: "",
              password: "",
              phone: "",
              address: "",
              city: "",
              county: "",
              role: "landlord",
              postal_code: "",
              dob: "",
              gender: "",
              property_count: 0,
              assigned_properties: "",
              skills: "",
              status: "active",
              profile_photo_file: null,
              profile_photo_preview: null,
              profile_photo_url: ''
            };

            this.loadLists();

          } catch (error) {
            console.log(error);
            toast.fire(
              'Error!',
              error.response?.data?.message || 'An error occurred while adding the user.',
              'error'
            );
          }
        },

        getPhoto(user) {
            // user can be an object containing profile_photo and profile_photo_url
            if (user.profile_photo && user.profile_photo !== '') {
                // file stored in local storage
                return `/storage/${user.profile_photo}`;
            } else if (user.profile_photo_url && user.profile_photo_url !== '') {
                // external URL
                return user.profile_photo_url;
            } else {
                // fallback placeholder
                return '/images/default-profile.png';
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
    
    
    