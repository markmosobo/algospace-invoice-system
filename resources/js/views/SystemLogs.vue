<template>
    <Master>
        <section class="section dashboard">
          <div class="row">
    
                <!-- Top Selling -->
                <div class="col-12">
                  <div class="card top-selling overflow-auto">
    
                    <div class="filter">
                      <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                          <h6>Filter</h6>
                        </li>
    
                        <li>
                          <router-link to="/todayactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              Today
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/yesterdayactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              Yesterday
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/monthactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              This Month
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/lastmonthactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              Last Month
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/yearactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              This Year
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/lastyearactivities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              Last Year
                            </a>
                          </router-link>
                        </li>
                        <li>
                          <router-link to="/activities" custom v-slot="{ href, navigate, isActive }">
                            <a
                                :href="href"
                                :class="{ active: isActive }"
                                class="dropdown-item"
                                @click="navigate"
                            >
                              All Time
                            </a>
                          </router-link>
                        </li>
                      </ul>
                    </div>
    
                    <div class="card-body">
                      <h5 class="card-title">System Logs <span>| All Time </span></h5>

                      <!-- Tabs -->
                      <ul class="nav nav-pills mb-3" id="pills-tab" role="tablist">
                          <li class="nav-item" role="presentation">
                              <button class="nav-link active custom-active-btn" id="pills-profile-tab" 
                                      data-bs-toggle="pill" data-bs-target="#pills-profile" 
                                      type="button" role="tab" aria-controls="pills-profile" aria-selected="false">
                                  Tabular
                              </button>
                          </li>                        
                          <li class="nav-item" role="presentation">
                              <button class="nav-link custom-active-btn" id="pills-home-tab" 
                                      data-bs-toggle="pill" data-bs-target="#pills-home" 
                                      type="button" role="tab" aria-controls="pills-home" aria-selected="true">
                                  Sequential
                              </button>
                          </li>
                      </ul>

                      <div class="tab-content pt-2" id="myTabContent">
                        <div class="tab-pane fade show active" id="pills-home" role="tabpanel" aria-labelledby="home-tab">
                          <div class="card-body">

                            <div class="activity">

                             <div v-for="item in systemLogs" :key="item.id" class="activity-item d-flex">
                              <div class="activite-label">{{ getRelativeTime(item.created_at) }}</div>
                              <i
                                class="bi bi-circle-fill activity-badge align-self-start"
                                :class="getRandomBadgeClass()"
                              ></i>
                              <div class="activity-content">
                                {{ item.description }}
                              </div>
                            </div>

                            </div>

                          </div>
                        </div>
                        <div class="tab-pane fade" id="pills-profile" role="tabpanel" aria-labelledby="profile-tab">
                          <div class="card-body pb-0">

                            <table id="AllActivitiesTable" class="table table-borderless">
                              <thead>
                                <tr>
                                  <th scope="col">Timestamp</th>
                                  <th scope="col">Description</th>
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
                                <tr v-for="item in systemLogs" :key="item.id">
                                  <td>{{formatDateTime(item.created_at)}}</td>
                                  <td>{{item.description}}</td>
                                </tr>
                              </tbody>
                            </table>
          
                          </div>
                        </div>
                      </div>

                    </div>
    
                  </div>
                </div><!-- End Top Selling -->
    
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
    import moment from 'moment';
    
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
          systemLogs: [],
          initializing: true,
          badgeClasses: [
            'text-success',
            'text-danger',
            'text-primary',
            'text-info',
            'text-warning',
            'text-muted',
          ],

        }
      },
      methods: {
        format_date(value){
          if(value){
            return moment(String(value)).format('MMM Do YYYY')
          }
        },
        getRandomBadgeClass() {
          // Select a random class from the badgeClasses array
          const randomIndex = Math.floor(Math.random() * this.badgeClasses.length);
          return this.badgeClasses[randomIndex];
        },
        getRelativeTime(createdAt) {
          const currentTime = new Date();
          const activityTime = new Date(createdAt);
          const differenceInSeconds = (currentTime - activityTime) / 1000;

          if (differenceInSeconds < 60) return `${Math.floor(differenceInSeconds)} seconds ago`;
          const differenceInMinutes = differenceInSeconds / 60;
          if (differenceInMinutes < 60) return `${Math.floor(differenceInMinutes)} mins ago`;
          const differenceInHours = differenceInMinutes / 60;
          if (differenceInHours < 24) return `${Math.floor(differenceInHours)} hrs ago`;
          const differenceInDays = differenceInHours / 24;
          if (differenceInDays < 7) return `${Math.floor(differenceInDays)} days ago`;
          const differenceInWeeks = differenceInDays / 7;
          return `${Math.floor(differenceInWeeks)} weeks ago`;
        },        
        navigateTo(location){
            this.$router.push(location)
        },
        formatDateTime(dateTime) {
          const date = new Date(dateTime);

          // Format date components
          const day = String(date.getDate()).padStart(2, '0');
          const month = String(date.getMonth() + 1).padStart(2, '0'); // Months are zero-based
          const year = date.getFullYear();

          // Format time components
          const hours = String(date.getHours()).padStart(2, '0');
          const minutes = String(date.getMinutes()).padStart(2, '0');
          const seconds = String(date.getSeconds()).padStart(2, '0');

          // Combine to format 'DD/MM/YYYY HH:mm:ss'
          return `${day}/${month}/${year} ${hours}:${minutes}:${seconds}`;
        },
        deleteCategory(id){
                Swal.fire({
                  title: 'Are you sure?',
                  text: "You won't be able to revert this!",
                  icon: 'warning',
                  showCancelButton: true,
                  confirmButtonColor: '#3085d6',
                  cancelButtonColor: '#d33',
                  confirmButtonText: 'Yes, delete it!'
                }).then((result) => {
                  if (result.isConfirmed) { 
                  //send request to the server
                  axios.delete('/api/category/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Category has been deleted.',
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
          axios.get('/api/system-logs')
            .then((response) => {
              this.systemLogs = response.data;

              setTimeout(() => {
                $("#AllActivitiesTable").DataTable();
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
        this.currentUser = JSON.parse(localStorage.getItem('user')) || {};
        this.current_user_id = this.currentUser.id;
        this.current_user = this.currentUser.first_name + " " + this.currentUser.last_name;
      }
    }
    </script>
    
    
    <style>
      /* Custom active color for the buttons */
      .nav-pills .nav-link.custom-active-btn.active {
          background-color: #006400; /* Dark Green */
          color: #fff; /* White text */
      }

      /* Hover effect (optional) */
      .nav-pills .nav-link.custom-active-btn:hover {
          background-color: #004d00; /* Darker Green on hover */
      }

    </style>