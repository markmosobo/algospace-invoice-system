<template>
  <main class="background-image">
      <section class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4">
        <div class="container">
          <div class="row justify-content-center">
            <div class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center">
              <div class="d-flex justify-content-center py-4">
                <a href="/" class="logo d-flex align-items-center w-auto">
                  <img src="@/assets/img/algospacelogo.png" alt="AlgoSpace Cyber Logo">
                  <span class="d-none d-lg-block" style="color: white;">
                    ALGOSPACE CYBER
                  </span>
                </a>
              </div>
              <!-- End Logo -->

              <div class="card mb-3">
                <div class="card-body">
                  <div class="pt-4 pb-2">
                    <h5 class="card-title text-center pb-0 fs-4" style="color: darkgreen;">Create an Account</h5>
                    <p class="text-center small">Enter your personal details to create account</p>
                  </div>

                  <form v-on:submit.prevent="create_user" class="needs-validation" novalidate>
                    <!-- First Name and Last Name -->
                    <div class="row g-3">
                      <div class="col-12 col-md-6">
                        <label for="yourfirstName" class="form-label">First Name</label>
                        <input type="text" name="first_name" placeholder="First Name" class="form-control" id="first_name" v-model="form.first_name" required>
                        <div class="invalid-feedback" v-if="!form.first_name">Please enter first name!</div>
                      </div>
                      <div class="col-12 col-md-6">
                        <label for="yourName" class="form-label">Last Name</label>
                        <input type="text" placeholder="Last Name" name="last_name" class="form-control" id="last_name" v-model="form.last_name" required>
                        <div class="invalid-feedback" v-if="!form.last_name">Please enter last name!</div>
                      </div>
                    </div>

                    <!-- Email and Password -->
                    <div class="row g-3">
                      <div class="col-12 col-md-6">
                        <label for="yourEmail" class="form-label">Phone Number</label>
                        <input type="text" name="phone" placeholder="Phone Number" class="form-control" id="phone" v-model="form.phone" required>
                        <div class="invalid-feedback" v-if="!form.phone">Please enter phone number!</div>
                      </div>
                      <div class="col-12 col-md-6">
                        <label for="yourEmail" class="form-label">Email Address</label>
                        <input type="email" name="email" placeholder="Email Address" class="form-control" id="email" v-model="form.email" required>
                        <div class="invalid-feedback" v-if="!form.email">Please enter email address!</div>
                      </div>
                      <div class="col-12 password-container">
                        <label for="yourPassword" class="form-label">Password</label>
                        <div class="input-group">
                          <input
                            :type="isPasswordVisible ? 'text' : 'password'"
                            name="password"
                             placeholder="Password"
                            class="form-control"
                            id="password"
                            v-model="form.password"
                            required
                          />
                          <span class="input-group-text" @click="togglePasswordVisibility">
                            <i :class="isPasswordVisible ? 'fa fa-eye' : 'fa fa-eye-slash'"></i>
                          </span>
                        </div>
                        <div class="invalid-feedback" v-if="!form.password">Please enter password!</div>

                      </div>
   <!--                    <div class="col-12">
                        <label for="yourPassword" class="form-label">Password</label>
                        <input type="password" placeholder="Password" name="password" class="form-control" id="password" v-model="form.password" required>
                        <div class="invalid-feedback" v-if="!form.password">Please enter password!</div>
                      </div> -->
                      <div class="col-12">
                        <label for="confirmPassword" class="form-label">Confirm Password</label>
                        <input type="password" placeholder="Confirm Password" name="confirm_password" class="form-control" id="confirm_password" v-model="form.confirm_password" required>
                        <div class="invalid-feedback" v-if="!form.confirm_password">Please confirm password!</div>
                        <div class="invalid-feedback" v-if="form.password !== form.confirm_password">Passwords do not match!</div>
                      </div>

                      <ul v-if="Object.keys(errors).length" class="alert alert-danger">
                        <li v-for="(error, key) in errors" :key="key">
                          {{ error[0] }}
                        </li>
                      </ul>

                    </div>

                    <div class="row g-3">
                      <div class="col-12">
                        <div class="form-check">
                          <input class="form-check-input" name="terms" type="checkbox" value="" id="acceptTerms" required>
                          <label class="form-check-label" for="acceptTerms">I agree and accept the <a href="#" style="color: orange;">terms and conditions</a></label>
                          <div class="invalid-feedback">You must agree before submitting.</div>
                        </div>
                      </div>
                      <div class="col-12">
                        <button
                          class="btn btn-success rounded-pill w-100"
                          type="submit"
                          :disabled="loading || !form.email || !form.password"
                        >
                          <span v-if="!loading">Create Account</span>
                          <span v-else>Creating Account...</span>
                        </button>
                      </div>
                      <div class="col-12">
                        <p class="small mb-0">Already have an account? <router-link to="login" style="color: orange;">Log In</router-link></p>
                      </div>
                    </div>
                  </form>
                </div>
              </div>

              <div class="credits">
                <!-- All the links in the footer should remain intact. -->
                <!-- You can delete the links only if you purchased the pro version. -->
                <!-- Licensing information: https://bootstrapmade.com/license/ -->
                <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
                <!-- Designed by <a href="#">BootstrapMade</a> -->
              </div>
            </div>
          </div>
        </div>
      </section>
  </main>
</template>
<script>
  import axios from 'axios'
  import Swal from 'sweetalert2'

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
        form: {
          first_name: '',
          last_name: '',
          email: '',
          phone: '',
          password: '',
          confirm_password: '',
          role: 'client',

          dob: '',
          address: '',
          city: '',
          postal_code: '',

          membership_type: 'basic',
          borrow_limit: 0,

          profile_photo_file: null,
          profile_photo_url: ''
        },
        loading: false,
        errors: {},
        isPasswordVisible: false,
      }
    },
    methods: {
        togglePasswordVisibility() {
          this.isPasswordVisible = !this.isPasswordVisible;
        },
        validateForm() {
          let isValid = true;

          const fields = [
            'first_name',
            'last_name',
            'phone',
            'email',
            'password',
            'confirm_password'
          ];

          fields.forEach(field => {
            const el = document.getElementById(field);

            if (!this.form[field] || (field === 'confirm_password' && this.form.password !== this.form.confirm_password)) {
              isValid = false;
              el?.classList.add('is-invalid');
            } else {
              el?.classList.remove('is-invalid');
            }
          });

          return isValid;
        },
        async create_user() {
          if (!this.validateForm()) return;

          this.errors = {};
          this.loading = true;

          try {
            const response = await axios.post('/api/register', this.form);

            if (response.status === 201 || response.status === 200) {

              toast.fire({
                title: 'Account Created',
                text: 'We have sent a verification email. Please verify your email before logging in.',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false
              });

              // keep loading during UX delay
              setTimeout(() => {
                this.loading = false;
                this.$router.push('/login');
              }, 2500);
            }

          } catch (error) {
            this.loading = false;

            const status = error.response?.status;

            if (status === 422) {
              this.errors = error.response.data.errors;

              Swal.fire({
                title: 'Validation Error',
                text: Object.values(this.errors)[0][0],
                icon: 'warning',
                confirmButtonColor: '#d33'
              });
              return;
            }

            if (status === 409) {
              Swal.fire({
                title: 'Account Exists',
                text: 'This email is already registered.',
                icon: 'info'
              });
              return;
            }

            if (status === 403) {
              Swal.fire({
                title: 'Registration Not Allowed',
                text: error.response.data.message,
                icon: 'error'
              });
              return;
            }

            Swal.fire({
              title: 'System Error',
              text: 'Please try again later.',
              icon: 'error'
            });
          }
        }

  }
}
  </script>

  <style scoped>
main {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-size: cover;
  background-attachment: fixed;
  margin: 0;
  padding: 0;
}

/* Full background image */
.background-image {
  background-image: url('@/assets/img/cyber.jpg');
  background-size: cover;
  background-position: center;
  background-repeat: no-repeat;
  background-attachment: fixed;
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0;
  padding: 0;
}

/* Centers everything with NO margins */
.section.register {
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
}

/* Wrapper to avoid Bootstrap's container margins */
.login-wrapper {
  width: 100%;
  max-width: 420px;
}

/* Glass-like card */
.card {
  background: rgba(255, 255, 255, 0.95);
  border-radius: 12px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.25);
}

</style>