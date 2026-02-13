<template>
  <main class="background-image">
      <section
        class="section register min-vh-100 d-flex flex-column align-items-center justify-content-center py-4"
      >
        <div class="container">
          <div class="row justify-content-center">
            <div
              class="col-lg-4 col-md-6 d-flex flex-column align-items-center justify-content-center"
            >
              <div class="d-flex justify-content-center py-4">
                <router-link to="/" class="logo d-flex align-items-center w-auto">

                  <span class="d-none d-lg-block" style="color: white;">ALGOSPACE CYBER</span>
                </router-link>
              </div>
              <!-- End Logo -->

              <div class="card mb-3 w-100 shadow-sm">
                <div class="card-body">

                  <!-- Welcome Section with Centered Logo -->
                  <div class="text-center pt-4 pb-3">

                    <!-- Logo: professional size, centered -->
                    <img 
                      src="@/assets/img/algospacelogo.png" 
                      alt="AlgoSpace Cyber Logo" 
                      class="login-logo mb-3"
                    />

                    <!-- Welcome text -->
                    <h5 class="card-title fs-4" style="color: purple;">
                      Welcome to AlgoSpace
                    </h5>
                    <p class="text-center small">
                      Choose account to sign into 
                    </p>
                  </div>

                  <!-- Quick Login Buttons -->
                  <div class="mt-4 text-center">
                    <p class="small mb-2" style="color: purple;">Quick Sign-In</p>
                    <div class="d-grid gap-2">
                      <button type="button" class="btn btn-outline-success rounded-pill" @click="autoLogin('office')">Office</button>
                      <button type="button" class="btn btn-outline-info rounded-pill" @click="autoLogin('farm')">Farm</button>
                      <button type="button" class="btn btn-outline-warning rounded-pill" @click="autoLogin('personal')">Personal</button>
                      <button type="button" class="btn btn-outline-dark rounded-pill" @click="logout()">Logout</button>
                    </div>
                  </div>

                </div>
              </div>

              <div class="credits">
                <!-- Footer credits (optional) -->
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
  name: 'Login',
  data() {
    return {
      form: {
        email: '',
        password: ''
      },
      errors: {},
      isPasswordVisible: false,
      loading: false,
      current_user: '',
      current_user_id: ''
    }
  },
  methods: {
    async logout() {
      try {
        if (this.reminderInterval) {
          clearInterval(this.reminderInterval);
        }

        await axios.post('/api/logout', {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem('token')}`
          }
        });

        localStorage.removeItem('token');
        localStorage.removeItem('user');

        this.$router.replace('/login');

      } catch (error) {
        localStorage.removeItem('token');
      }
    },
    togglePasswordVisibility() {
      this.isPasswordVisible = !this.isPasswordVisible
    },
    async login_user() {
    this.errors = {}

    if (!this.form.email) {
        this.errors.email = 'Email is required.'
        return
    }
    if (!this.form.password) {
        this.errors.password = 'Password is required.'
        return
    }

    this.loading = true

    try {
        const response = await axios.post('/api/login', this.form)
        this.loading = false
        this.form.email = ''
        this.form.password = ''

        if (response.status === 200 && response.data.token) {
        // ✅ Save both user info and token
        localStorage.setItem('token', response.data.token)
        localStorage.setItem('user', JSON.stringify(response.data.user))

        // Optionally set the token for all future axios calls
        axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`

        // Update UI state
        const loggedInUser = response.data.user
        this.current_user = `${loggedInUser.first_name || ''} ${loggedInUser.last_name || ''}`
        this.current_user_id = loggedInUser.id

        // Success alert (optional)
        toast.fire({
            title: 'Welcome!',
            text: `Hello ${this.current_user}, you are now signed in.`,
            icon: 'success',
            timer: 2000,
            showConfirmButton: false
        })

        // Redirect to dashboard
        this.$router.push('/dashboard')
        } else {
        Swal.fire({
            title: 'Oops!',
            text: response.data.message || 'Login failed. Please try again.',
            icon: 'warning'
        })
        }
    } catch (error) {
        this.loading = false
        Swal.fire({
        title: 'Error',
        text: error.response?.data?.error || 'An error occurred during login. Please try again.',
        icon: 'error'
        })
    }
    },
    autoLogin(role) {
      const presets = {
        office: {
          email: "office@algospace.co.ke",
          password: "password123"
        },
        farm: {
          email: "farm@algospace.co.ke",
          password: "password123"
        },        
        personal: {
          email: "personal@algospace.co.ke",
          password: "password123"
        }        
      };

      if (presets[role]) {
        this.form.email = presets[role].email;
        this.form.password = presets[role].password;

        // Automatically trigger login
        this.login_user();
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
  background-image: url('@/assets/img/algospace-cyber.jpg');
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
.login-logo {
  max-width: 120px;      /* professional standard size */
  height: auto;          /* maintain aspect ratio */
  display: block;        /* ensures centered alignment */
  margin-left: auto;
  margin-right: auto;
}
</style>
