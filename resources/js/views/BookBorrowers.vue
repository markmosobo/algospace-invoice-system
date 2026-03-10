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
                      <h5 class="card-title">Book Borrowers <span>| Borrowers of AlgoSpace Library Books</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                            <!-- <router-link v-if="addLandlordPermission" to="/add-pmslandlord" custom v-slot="{ href, navigate, isActive }"> -->
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addBorrower()"
                                >
                                  Add Book Borrower
                                </a>
                            <!-- </router-link> -->
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
    
                      <table id="BorrowersTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Full Name</th>
                            <th scope="col">Email Address</th>
                            <th scope="col">Phone</th>
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
                          <tr v-for="user in borrowers" :key="user.id">
                            <td>{{user.name}}</td>
                            <td>{{user.email ?? "N/A"}}</td>
                            <td>{{user.phone ?? "N/A"}}</td>
                            <td>
                              <!-- ACTIVE -->
                              <span v-if="user.status == 'active'" class="badge bg-success">
                                <i class="bi bi-check-circle me-1"></i> Active
                              </span>

                              <!-- PENDING -->
                              <span v-else-if="user.status == 'pending'" class="badge bg-warning text-dark">
                                <i class="bi bi-hourglass-split me-1"></i> Pending
                              </span>

                              <!-- SUSPENDED -->
                              <span v-else-if="user.status == 'suspended'" class="badge bg-danger">
                                <i class="bi bi-slash-circle me-1"></i> Suspended
                              </span>
                            </td>
                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewLandlord(user)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editBorrower(user)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="deleteLandlord(user.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Landlord Modal -->
              <div class="modal fade" id="viewLandlordModal" tabindex="-1" aria-labelledby="viewLandlordModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Borrower Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedLandlord">

                      <!-- Profile Photo -->
                      <div class="text-center mb-3" v-if="selectedLandlord">
                        <img 
                          :src="selectedLandlord.profile_photo
                                  ? `/storage/${selectedLandlord.profile_photo}` 
                                  : (selectedLandlord.profile_photo_url 
                                      ? selectedLandlord.profile_photo_url 
                                      : defaultProfile)"
                          @error="($event.target.src = defaultProfile)" 
                          alt="Profile Photo" 
                          class="rounded-circle border" 
                          style="height: 120px; width: 120px; object-fit: cover; object-position: center;"
                        />
                      </div>


                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedLandlord.name">
                          <strong>Full Name:</strong> <br> {{ selectedLandlord.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.email">
                          <strong>Email:</strong> <br> {{ selectedLandlord.email }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.phone">
                          <strong>Phone:</strong> <br> {{ selectedLandlord.phone }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.role">
                          <strong>Role:</strong> <br> {{ selectedLandlord.role }}
                        </div>

                        <!-- STATUS + LOGIN -->
                        <div class="col-md-6" v-if="selectedLandlord.status">
                          <strong>Status:</strong> <br> {{ selectedLandlord.status }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.last_login">
                          <strong>Last Login:</strong> <br> {{ selectedLandlord.last_login }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.email_verified_at">
                          <strong>Email Verified At:</strong> <br> {{ selectedLandlord.email_verified_at }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.is_email_verified !== null">
                          <strong>Is Email Verified:</strong> <br> {{ selectedLandlord.is_email_verified ? 'Yes' : 'No' }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord && selectedLandlord['2fa_enabled'] !== undefined">
                          <strong>2FA Enabled:</strong> {{ selectedLandlord['2fa_enabled'] ? 'Yes' : 'No' }}
                        </div>


                        <!-- ADDRESS INFORMATION -->
                        <div class="col-md-6" v-if="selectedLandlord.address">
                          <strong>Address:</strong> <br> {{ selectedLandlord.address }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.city">
                          <strong>City:</strong> <br> {{ selectedLandlord.city }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.county">
                          <strong>County:</strong> <br> {{ selectedLandlord.county }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.postal_code">
                          <strong>Postal Code:</strong> <br> {{ selectedLandlord.postal_code }}
                        </div>

                        <!-- PERSONAL INFO -->
                        <div class="col-md-6" v-if="selectedLandlord.dob">
                          <strong>Date of Birth:</strong> <br> {{ selectedLandlord.dob }}
                        </div>

                        <div class="col-md-6" v-if="selectedLandlord.gender">
                          <strong>Gender:</strong> <br> {{ selectedLandlord.gender }}
                        </div>



                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Book Borrower Modal -->
                <div class="modal fade" id="AddBorrowerModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Add Book Borrower</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Full Name -->
                          <div class="col-md-6">
                            <label class="form-label">Full Name*</label>
                            <input type="text" class="form-control" v-model="data.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Email*</label>
                            <input type="email" class="form-control" v-model="data.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone*</label>
                            <input type="text" class="form-control" v-model="data.phone" required>
                          </div>

                          <!-- Date of Birth -->
                          <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" v-model="data.dob">
                          </div>

                          <!-- Address -->
                          <div class="col-md-6">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="form-control" v-model="data.address">
                          </div>

                          <!-- City -->
                          <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" v-model="data.city">
                          </div>

                          <!-- Postal Code -->
                          <div class="col-md-6">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control" v-model="data.postal_code">
                          </div>

                          <!-- Membership Type -->
                          <div class="col-md-6">
                            <label class="form-label">Membership Type</label>
                            <select class="form-select" v-model="data.membership_type">
                              <option value="student">Student</option>
                              <option value="staff">Staff</option>
                              <option value="public">Public</option>
                              <option value="premium">Premium</option>
                            </select>
                          </div>

                          <!-- Borrowing Limit -->
                          <div class="col-md-6">
                            <label class="form-label">Borrowing Limit</label>
                            <input type="number" class="form-control" v-model="data.borrow_limit" min="1">
                          </div>

                          <!-- Account Status -->
                          <div class="col-md-6">
                            <label class="form-label">Account Status</label>
                            <select class="form-select" v-model="data.status">
                              <option value="active">Active</option>
                              <option value="pending">Pending</option>
                              <option value="suspended">Suspended</option>
                            </select>
                          </div>

                          <!-- Password -->
                          <div class="col-md-6">
                            <label class="form-label">
                              Password*
                              <small class="text-muted">(auto-generate or type manually)</small>
                            </label>

                            <div class="input-group">
                              <input
                                :type="showPassword ? 'text' : 'password'"
                                class="form-control"
                                v-model="data.password"
                                required
                              />

                              <span class="input-group-text" style="cursor:pointer" @click="showPassword = !showPassword">
                                <i :class="showPassword ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                              </span>

                              <button class="btn btn-outline-secondary" type="button" @click="generatePassword">
                                Generate
                              </button>
                            </div>
                          </div>

                          <!-- Role (fixed as borrower) -->
                          <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" value="borrower" disabled>
                          </div>

                        </form>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submitBorrower" style="background: darkgreen; border-color: darkgreen;">
                          Save Borrower
                        </button>
                      </div>

                    </div>
                  </div>
                </div>


                <!-- EDIT BORROWER MODAL -->
                <div class="modal fade" id="EditBorrowerModal" tabindex="-1" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Book Borrower</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID -->
                          <input type="hidden" v-model="form.id">

                          <!-- Name -->
                          <div class="col-md-6">
                            <label class="form-label">Full Name*</label>
                            <input type="text" id="name_edit" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Email -->
                          <div class="col-md-6">
                            <label class="form-label">Email*</label>
                            <input type="email" id="mail_edit" class="form-control" v-model="form.email" required>
                          </div>

                          <!-- Phone -->
                          <div class="col-md-6">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" v-model="form.phone">
                          </div>

                          <!-- DOB -->
                          <div class="col-md-6">
                            <label class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" v-model="form.dob">
                          </div>

                          <!-- Address -->
                          <div class="col-md-6">
                            <label class="form-label">Street Address</label>
                            <input type="text" class="form-control" v-model="form.address">
                          </div>

                          <!-- City -->
                          <div class="col-md-6">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" v-model="form.city">
                          </div>

                          <!-- Postal Code -->
                          <div class="col-md-6">
                            <label class="form-label">Postal Code</label>
                            <input type="text" class="form-control" v-model="form.postal_code">
                          </div>

                          <!-- Membership Type -->
                          <div class="col-md-6">
                            <label class="form-label">Membership Type</label>
                            <select class="form-select" v-model="form.membership_type">
                              <option value="student">Student</option>
                              <option value="staff">Staff</option>
                              <option value="public">Public</option>
                              <option value="premium">Premium</option>
                            </select>
                          </div>

                          <!-- Borrowing Limit -->
                          <div class="col-md-6">
                            <label class="form-label">Borrowing Limit</label>
                            <input type="number" class="form-control" v-model="form.borrow_limit" min="1">
                          </div>

                          <!-- Status -->
                          <div class="col-md-6">
                            <label class="form-label">Account Status</label>
                            <select class="form-select" v-model="form.status">
                              <option value="active">Active</option>
                              <option value="pending">Pending</option>
                              <option value="suspended">Suspended</option>
                            </select>
                          </div>

                          <!-- Role -->
                          <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" class="form-control" v-model="form.role" disabled>
                          </div>

                          <!-- Profile Photo/File or URL -->
                          <div class="col-md-12">
                            <label class="form-label">Profile Photo</label>

                            <div class="mb-2">
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="file" v-model="photoMode">
                                <label class="form-check-label">Upload file</label>
                              </div>
                              <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" value="url" v-model="photoMode">
                                <label class="form-check-label">Use URL</label>
                              </div>
                            </div>

                            <div v-if="photoMode === 'file'">
                              <input type="file" class="form-control" @change="handleEditPhotoUpload">
                            </div>

                            <div v-if="photoMode === 'url'">
                              <input type="url" class="form-control" v-model="form.profile_photo_url" @input="updateEditPreviewFromUrl">
                            </div>

                            <div v-if="form.profile_photo_preview" class="mt-2">
                              <img :src="form.profile_photo_preview" class="img-thumbnail" style="max-height: 130px;">
                            </div>

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
          borrowers: [],
          user: [],
          selectedLandlord: {},
          showPassword: false,
          defaultProfile: DefaultProfile,
          photoMode: 'file', // 'file' or 'url' — default to file
          errors: {},
          form: {
            id: "",
            name: "",
            email: "",
            phone: "",
            dob: "",
            address: "",
            city: "",
            postal_code: "",
            membership_type: "public",
            borrow_limit: 3,
            status: "active",
            password: "",
            role: "borrower",

            profile_photo_file: null,
            profile_photo_preview: null,
            profile_photo_url: '' // for URL input
          },
          data: {
            name: "",
            email: "",
            phone: "",
            dob: "",
            address: "",
            city: "",
            postal_code: "",
            membership_type: "public",
            borrow_limit: 3,
            status: "active",
            password: "",
            role: "borrower"
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
        generatePassword() {
          const length = 12;
          const chars =
            'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789!@#$%^&*()_+-=';

          let password = '';
          for (let i = 0; i < length; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
          }

          this.data.password = password;
          this.showPassword = true;

          // Optional toast feedback
          toast.fire({
            icon: 'success',
            title: 'Password generated',
            text: 'You can copy or change it before saving'
          });
        },        
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
          
        viewLandlord(landlord)
        {
          console.log(this.selectedLandlord)
          this.selectedLandlord = landlord;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewLandlordModal'));
          modal.show();
        },
        editBorrower(user) {
          this.form = {
            id: user.id,
            name: user.name || "",
            email: user.email || "",
            role: user.role || "borrower",
            phone: user.phone || "",
            dob: user.dob || "",
            address: user.address || "",
            city: user.city || "",
            postal_code: user.postal_code || "",
            membership_type: user.membership_type || "public",
            borrow_limit: user.borrow_limit || 3,
            status: user.status || "active",
            profile_photo_file: null,
            profile_photo_preview: user.profile_photo ? `/storage/${user.profile_photo}` : user.profile_photo_url || "",
            profile_photo_url: user.profile_photo_url || ""
          };
          this.photoMode = user.profile_photo ? "file" : "url";
          const modal = new bootstrap.Modal(document.getElementById("EditBorrowerModal"));
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

            // Append normal fields
            const fields = [
              "name", "email", "role", "phone", "address", "city",
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
              `/api/users/${this.form.id}?_method=PUT`,
              formData,
              { headers: { "Content-Type": "multipart/form-data" } }
            );

            // Success
            toast.fire('Success!', 'Book borrower details updated!', 'success');

            const modal = bootstrap.Modal.getInstance(
              document.getElementById('EditBorrowerModal')
            );
            modal.hide();

            this.loadBorrowers();

          } catch (error) {
            console.error(error);
            toast.fire(
              'Error!',
              error.response?.data?.message || 'Something went wrong.',
              'error'
            );
          }
        },

        addBorrower() {
          this.data = { name: "", email: "", password: "", role: "borrower" };
          const modal = new bootstrap.Modal(document.getElementById("AddBorrowerModal"));
          modal.show();
        },

        submitBorrower() {
          // validate required fields
          if (!this.data.name || !this.data.email || !this.data.phone || !this.data.password) {
            toast.fire('Error', 'Please fill all required fields', 'error');
            return;
          }
          axios.post('/api/borrowers', this.data)
            .then(() => {
              toast.fire('Success', 'Borrower added successfully', 'success');
              const modal = bootstrap.Modal.getInstance(document.getElementById('AddBorrowerModal'));
              modal.hide();
              this.loadBorrowers(); // your method to refresh table
            })
            .catch(err => {
              toast.fire('Error', err.response?.data?.message || 'Failed to add borrower', 'error');
            });
        },

        loadBorrowers() {
          this.initializing = true;
          axios.get('/api/borrowers')
            .then(res => {
              this.borrowers = res.data;
              setTimeout(() => { $("#BorrowersTable").DataTable(); }, 10);

            })
            .catch(err => console.error(err))
            .finally(() => this.initializing = false);
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
        deleteLandlord(id){
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
                  axios.delete('/api/users/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Service provider has been deleted.',
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
      },
      components : {
          Master,
      },
      mounted(){
        this.loadBorrowers();
        // this.user = localStorage.getItem('user');
        // this.user = JSON.parse(this.user);
        // this.userId = this.user.id;
        // this.currentUser = JSON.parse(localStorage.getItem('user')) || {};
        // this.current_user_id = this.currentUser.id;
        // this.current_user = this.currentUser.first_name + " " + this.currentUser.last_name;

      }
    }
    </script>
    
    
    