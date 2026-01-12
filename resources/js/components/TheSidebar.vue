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

      <!-- Manage Listings -->
      <li v-show="['admin', 'landlord'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#listing-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-building"></i>
          <span>Manage Listings</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="listing-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li v-show="userRole === 'admin'">
            <router-link to="/property-listings" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>All Listings</span>
              </a>
            </router-link>
          </li>
          <li v-show="userRole === 'landlord'">
            <router-link to="/landlord-listings" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>My Listings</span>
              </a>
            </router-link>
          </li>          
        </ul>
      </li>

      <!-- Manage Users (Admin Only) -->
      <li v-show="userRole === 'admin'" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#users-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-people"></i>
          <span>Manage Users</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="users-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/users" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>All Users</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/landlords" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Landlords</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/caretakers" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Caretakers</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/service-providers" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Service Providers</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/tenants" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Tenants</span>
              </a>
            </router-link>
          </li>
        </ul>
      </li>

      <!-- Manage Properties -->
      <li v-show="['admin', 'landlord'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#build-nav" data-bs-toggle="collapse">
          <i class="bi bi-building"></i>
          <span>Manage Properties</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>

        <ul id="build-nav" class="nav-content collapse">
          <li v-show="userRole === 'admin'">
            <router-link to="/properties" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>All Properties</span>
              </a>
            </router-link>
          </li>

          <li v-show="userRole === 'landlord'">
            <router-link to="/landlord-properties" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>My Properties</span>
              </a>
            </router-link>
          </li>          

          <li v-show="userRole === 'landlord'">
            <router-link to="/caretakers" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i>
                <span>Caretakers</span>
              </a>
            </router-link>
          </li>
        </ul>
      </li>


      <!-- Manage Invoices -->
      <li v-show="['admin', 'tenant', 'service_provider'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#invoice-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-receipt"></i>
          <span>Manage Invoices</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="invoice-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li v-show="userRole === 'admin'">
            <router-link to="/awaitinginvoicing" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Unpaid Invoices</span>
              </a>
            </router-link>
          </li>
          <li v-show="userRole === 'admin'">
            <router-link to="/paid-invoices" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Paid Invoices</span>
              </a>
            </router-link>           
          </li>
          <li v-show="userRole === 'tenant'">
            <router-link to="/tenant-invoices" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>My Invoices</span>
              </a>
            </router-link>
          </li>
          <li v-show="userRole === 'service_provider'">
            <router-link to="/provider-invoices" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>My Invoices</span>
              </a>
            </router-link>
          </li>           
        </ul>
      </li>

      <!-- Manage Tickets -->
      <li v-show="['admin', 'landlord', 'tenant', 'caretaker', 'service_provider'].includes(userRole)" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#support-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-ticket"></i>
          <span>Manage Tickets</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="support-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/pendingtickets" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Pending Tickets</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/closedtickets" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Closed Tickets</span>
              </a>
            </router-link>
          </li>
        </ul>
      </li>

      <!-- System Logs -->
      <li v-show="userRole === 'admin'" class="nav-item">
        <a class="nav-link collapsed" data-bs-target="#logs-nav" data-bs-toggle="collapse" href="#">
          <i class="bi bi-activity"></i>
          <span>System Logs</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="logs-nav" class="nav-content collapse" data-bs-parent="#sidebar-nav">
          <li>
            <router-link to="/activitylogs" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>Activity Logs</span>
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/sysconfig" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i><span>System Config</span>
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
