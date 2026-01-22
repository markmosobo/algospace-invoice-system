<template>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
  
      <div class="d-flex align-items-center justify-content-between">
        <a href="#" class="logo d-flex align-items-center">
          <!-- <img src="@/assets/img/apex-logo.png" alt=""> -->
        </a>
        <i class="bi bi-list toggle-sidebar-btn" @click="handleSidebar"></i>
        <span class="d-none d-lg-block" style="color: darkgreen;"><strong>ALGOSPACE CYBER</strong> PORTAL</span>

      </div><!-- End Logo -->
  
      <!-- <div class="search-bar">
        <form @submit.prevent="performSearch" class="search-form d-flex align-items-center">
          <input
            v-model="query"
            type="text"
            name="query"
            placeholder="Search"
            title="Enter search keyword"
          />
          <button type="submit" title="Search">
            <i class="bi bi-search"></i>
          </button>
        </form>
      </div> -->



      <!-- End Search Bar -->
  
      <nav class="header-nav ms-auto">
        <ul class="d-flex align-items-center">
  

          <!-- <li class="nav-item dropdown">
            <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
              <i class="bi bi-bell"></i>
              <span class="badge bg-danger badge-number">{{ notificationCount }}</span>
            </a>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications">
              <li class="dropdown-header">
                You have {{ notificationCount }} new notifications
              </li>
              <li><hr class="dropdown-divider"></li>

              <li class="notification-item" v-for="(notification, index) in notifications" :key="index">
                <i class="bi bi-exclamation-circle text-warning"></i>
                <div>
                  <h4>{{ notification.title }}</h4>
                  <p>{{ notification.message }}</p>
                  <p>{{ notification.time }}</p>
                </div>
              </li>

              <li><hr class="dropdown-divider"></li>
              <li class="dropdown-footer">
                <a href="#">Show all notifications</a>
              </li>
            </ul>
          </li> -->


  
          <li class="nav-item dropdown pe-3">
  
            <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
              <!-- <img src="assets/img/profile-img.jpg" alt="Profile" class="rounded-circle"> -->
              <i class="bi bi-person-fill"></i>
              <span class="d-none d-md-block dropdown-toggle ps-2">{{current_user.name}}</span>
            </a><!-- End Profile Image Icon -->
  
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
              <li class="dropdown-header">
                <h6>{{current_user.name}}</h6>
                <span v-if="current_user.role == 'admin'">Admin</span>
                <span v-else-if="current_user.role == 'landlord'">Landlord</span>
                <span v-else-if="current_user.role == 'caretaker'">Caretaker</span>
                <span v-else-if="current_user.role == 'tenant'">Tenant</span>
                <span v-else-if="current_user.role == 'service_provider'">Service Provider</span>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
  
              <li>
                <router-link to="/profile" custom v-slot="{ href, navigate, isActive }">
                  <a 
                  class="dropdown-item d-flex align-items-center"
                  :href="href"
                  :class="{ active: isActive }" 
                  @click="navigate"
                  >
                  <i class="bi bi-person"></i>
                  <span>My Profile</span>
                </a>
                </router-link>
              </li>
              <li>
                <hr class="dropdown-divider">
              </li>
  
              <li>
                <hr class="dropdown-divider">
              </li>
  
              <li>
                <a @click.prevent="logout" class="dropdown-item d-flex align-items-center" href="#">
                  <i class="bi bi-box-arrow-right"></i>
                  <span>Sign Out</span>
                </a>
              </li>
  
            </ul><!-- End Profile Dropdown Items -->
          </li><!-- End Profile Nav -->
  
        </ul>
      </nav>
      <!-- End Icons Navigation -->
  
    </header><!-- End Header -->

    <!-- Search Results Modal -->
    <div
      class="modal fade"
      id="searchResultsModal"
      tabindex="-1"
      aria-labelledby="searchResultsModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="searchResultsModalLabel">Search Results</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div v-if="searchResults.length > 0">
              <ul>
                <li v-for="(item, index) in searchResults" :key="index">
                  <strong>{{ item.table }}</strong>:
                  <ul>
                    <li v-for="(value, key) in item.result" :key="key">
                      <span v-if="value">{{ key }}: {{ value }}</span>
                    </li>
                  </ul>
                </li>
              </ul>
            </div>
            <div v-else>
              <p>No results found for "{{ query }}".</p>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
          </div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
import axios from 'axios';

  export default {
    name: 'TheHeader',
    data(){
      return {
        current_user: [],
        query: "",
        searchResults: [],
        idleTimeout: null,
        idleTime: 600000, // 10 minutes in milliseconds
        notificationCount: 2
      }
    },
      methods: {
      handleSidebar() {
        if (document.body.classList.contains("toggle-sidebar")) {
          document.body.classList.remove("toggle-sidebar");
        } else {
          document.body.classList.add("toggle-sidebar");
        }
      },
      async performSearch() {
      if (!this.query.trim()) {
        this.searchResults = [];
        return;
      }

      try {
        const response = await axios.post('/api/universalsearch', { query: this.query });
        this.searchResults = response.data || [];
        
        // Show the modal when results are fetched
        const modal = new bootstrap.Modal(document.getElementById('searchResultsModal'));
        modal.show();
      } catch (error) {
        console.error("Search error:", error);
        this.searchResults = [];
      }
    },
async logout() {
  try {
    await axios.post('/api/logout', {}, {
      headers: {
        Accept: 'application/json',
        Authorization: `Bearer ${localStorage.getItem('token')}`
      }
    });

    // Clear auth data
    localStorage.removeItem('token');
    localStorage.removeItem('user');

    // Redirect to login
    this.$router.replace('/login');

  } catch (error) {
    console.error('Logout error:', error);

    // Even if API fails, force logout locally
    localStorage.removeItem('token');
    this.$router.push('/login');
  }
}

  
      // loadLists(){
      //   axios.get('api/lists').then((response) => {
      //       this.displaymessages = response.data.lists.displaymessages
      //       console.log("messages",this.displaymessages)
      //   }).catch((error) => {
      //       console.log(error)
      //   })
      // },
    },
    mounted(){ 
      // this.loadLists();
      this.user = localStorage.getItem('user');
      this.user = JSON.parse(this.user);
      this.current_user = this.user;

    }
    
  }
  </script>
  
  
  