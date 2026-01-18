<template>
  <!-- ======= Sidebar ======= -->
  <aside id="sidebar" class="sidebar">
    <ul class="sidebar-nav" id="sidebar-nav">

      <!-- Dashboard -->
      <li class="nav-item">
        <router-link to="/dashboard" custom v-slot="{ href, navigate, isActive }">
          <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
            <i class="bi bi-speedometer2"></i>
            <span>Dashboard</span>
          </a>
        </router-link>
      </li>

      <!-- Personal Transactions -->
      <li v-show="userRole === 'personal'" class="nav-item">
        <router-link to="/personal-transactions" custom v-slot="{ href, navigate, isActive }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-person-circle"></i>
            <span>Transactions</span>
          </a>
        </router-link>
      </li>

      <!-- Payments -->
      <li v-show="userRole === 'office'" class="nav-item">
        <router-link to="/personal-transactions" custom v-slot="{ href, navigate, isActive }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-person-circle"></i>
            <span>Payments</span>
          </a>
        </router-link>
      </li> 
      
      <!-- Supplies -->
      <li v-show="userRole === 'office'" class="nav-item">
        <router-link to="/supplies" custom v-slot="{ href, navigate, isActive }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-person-circle"></i>
            <span>Supplies</span>
          </a>
        </router-link>
      </li>       

      <!-- System Config -->
      <li v-show="['personal'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#listing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-building"></i>
          <span>Configurations</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="listing-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/personal-accounts" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>Accounts</span>
              </a>
            </router-link>
          </li> 
          <li>
            <router-link to="/personal-categories" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>Categories</span>
              </a>
            </router-link>
          </li>                    
        </ul>
      </li>

      <!-- Manage Resources (Office Only) -->
      <li v-show="userRole === 'office'" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i>
          <span>Manage Resources</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/customers" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Customers</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/suppliers" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Suppliers</span>
              </a>
            </router-link>
          </li>          
        </ul>
      </li>

      <!-- Manage Services -->
      <li v-show="['office'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#build-nav" data-bs-toggle="collapse">
          <i class="bi bi-building"></i>
          <span>Manage Services</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <ul id="build-nav" class="nav-content collapse">
          <li v-show="userRole === 'office'">
            <router-link to="/services" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>Services Offered</span>
              </a>
            </router-link>
          </li>
        </ul>
      </li>


      <!-- Manage Invoices -->
      <li v-show="['office'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#invoice-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-receipt"></i>
          <span>Manage Invoices</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="invoice-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li v-show="userRole === 'office'">
            <router-link to="/awaitinginvoicing" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Unpaid Invoices</span>
              </a>
            </router-link>
          </li>
          <li v-show="userRole === 'office'">
            <router-link to="/paid-invoices" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Paid Invoices</span>
              </a>
            </router-link>           
          </li>         
        </ul>
      </li>

      <!-- System Logs -->
      <li v-show="userRole === 'office'" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#logs-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-activity"></i>
          <span>System Logs</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="logs-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/system-logs" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>System Logs</span>
              </a>
            </router-link>
          </li>
        </ul>
      </li>      

      <!-- Profile -->
      <li class="nav-item">
        <router-link to="/profile" custom v-slot="{ href, navigate, isActive }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-person-circle"></i>
            <span>My Profile</span>
          </a>
        </router-link>
      </li>

    </ul>
  </aside>
</template>

<script>
export default {
  name: 'TheSidebar',
  data() {
    return {
      userRole: ''
    };
  },
  mounted() {
    const user = JSON.parse(localStorage.getItem('user'));
    this.userRole = user?.role || '';
  }
};
</script>

<style scoped>
.sidebar .nav-link i {
  font-size: 1.2rem;
  margin-right: 8px;
}
.sidebar .nav-link {
  display: flex;
  align-items: center;
  padding: 10px 15px;
  border-radius: 6px;
  transition: background 0.2s, color 0.2s;
}
.sidebar .nav-link:hover {
  background-color: #f1f1f1;
  color: #007bff;
}
.sidebar .nav-link.active {
  background-color: #007bff;
  color: #fff;
}
</style>
