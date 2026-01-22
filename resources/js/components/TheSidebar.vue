<template>
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
        <router-link to="/personal-transactions" custom v-slot="{ href, navigate }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-wallet2"></i>
            <span>My Transactions</span>
          </a>
        </router-link>
      </li>

      <!-- Payments -->
      <li v-show="userRole === 'office'" class="nav-item">
        <router-link to="/personal-transactions" custom v-slot="{ href, navigate }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-cash-coin"></i>
            <span>Payments</span>
          </a>
        </router-link>
      </li>

      <!-- Supplies -->
      <li v-show="userRole === 'office'" class="nav-item">
        <router-link to="/supplies" custom v-slot="{ href, navigate }">
          <a :href="href" class="nav-link" @click="navigate">
            <i class="bi bi-box-seam"></i>
            <span>Supplies</span>
          </a>
        </router-link>
      </li>

      <!-- Configurations -->
      <li v-show="userRole === 'personal'" class="nav-item">
        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#listing-nav">
          <i class="bi bi-gear"></i>
          <span>Configurations</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="listing-nav" class="nav-content collapse">
          <li>
            <router-link to="/personal-accounts" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i> Accounts
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/personal-categories" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i> Categories
              </a>
            </router-link>
          </li>
        </ul>
      </li>

      <!-- Inventory -->
      <li v-show="userRole === 'office'" class="nav-item">
        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#supplies-nav">
          <i class="bi bi-boxes"></i>
          <span>Inventory</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="supplies-nav" class="nav-content collapse">
          <li>
            <router-link to="/supplies" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i> Supplies
              </a>
            </router-link>
          </li>
          <li>
            <router-link to="/restocks" custom v-slot="{ href, navigate, isActive }">
              <a :href="href" :class="{ active: isActive }" class="nav-link" @click="navigate">
                <i class="bi bi-circle"></i> Restocks
              </a>
            </router-link>
          </li>
        </ul>
      </li>

      <!-- Resources -->
      <li v-show="userRole === 'office'" class="nav-item">
        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#users-nav">
          <i class="bi bi-people"></i>
          <span>Resources</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="users-nav" class="nav-content collapse">
          <li><RouterLink to="/customers" class="nav-link"><i class="bi bi-circle"></i> Customers</RouterLink></li>
          <li><RouterLink to="/suppliers" class="nav-link"><i class="bi bi-circle"></i> Suppliers</RouterLink></li>
        </ul>
      </li>

      <!-- Invoices -->
      <li v-show="userRole === 'office'" class="nav-item">
        <a class="nav-link collapsed" data-bs-toggle="collapse" href="#invoice-nav">
          <i class="bi bi-receipt"></i>
          <span>Invoices</span>
          <i class="bi bi-chevron-down ms-auto"></i>
        </a>
        <ul id="invoice-nav" class="nav-content collapse">
          <li><RouterLink to="/draft-invoices" class="nav-link"><i class="bi bi-circle"></i> Draft</RouterLink></li>
          <li><RouterLink to="/invoices" class="nav-link"><i class="bi bi-circle"></i> Paid</RouterLink></li>
        </ul>
      </li>

      <!-- Logs -->
      <li v-show="userRole === 'office'" class="nav-item">
        <RouterLink to="/system-logs" class="nav-link">
          <i class="bi bi-shield-check"></i>
          <span>System Logs</span>
        </RouterLink>
      </li>

      <!-- Profile -->
      <li class="nav-item mt-3">
        <RouterLink to="/profile" class="nav-link">
          <i class="bi bi-person-circle"></i>
          <span>My Profile</span>
        </RouterLink>
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
.sidebar .nav-link {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 14px;
  border-radius: 8px;
  font-weight: 500;
  transition: all 0.2s ease;
}

.sidebar .nav-link i {
  font-size: 1.1rem;
}

.sidebar .nav-link:hover {
  background: #f4f6f9;
  color: #0d6efd;
}

.sidebar .nav-link.active {
  background: #0d6efd;
  color: #fff;
}

.nav-content .nav-link {
  padding-left: 42px;
  font-size: 0.95rem;
}

</style>
