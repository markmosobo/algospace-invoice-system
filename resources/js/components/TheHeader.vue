<template>
<header id="header" class="header fixed-top d-flex align-items-center">

  <!-- LEFT -->
  <div class="d-flex align-items-center gap-3">

    <a href="/" class="logo d-flex align-items-center gap-2">
      <img src="@/assets/img/algospacelogo.png" />
      <span>Back to website</span>
    </a>

    <i class="bi bi-list toggle-sidebar-btn" @click="handleSidebar"></i>

    <strong class="d-none d-lg-block text-success">
      ALGOSPACE CYBER PORTAL
    </strong>
  </div>

  <!-- RIGHT -->
  <nav class="header-nav ms-auto">
    <ul class="d-flex align-items-center">

      <!-- NOTIFICATIONS -->
      <li class="nav-item dropdown">
        <a class="nav-link nav-icon" data-bs-toggle="dropdown">
          <i class="bi bi-bell"></i>
          <span v-if="notificationCount" class="badge bg-danger">
            {{ notificationCount }}
          </span>
        </a>
      </li>

      <!-- ACCOUNT SWITCH -->
      <li class="nav-item dropdown pe-3">

        <a class="nav-link d-flex align-items-center" data-bs-toggle="dropdown">
          <i class="bi bi-person-circle fs-5"></i>
          <span class="ms-2 d-none d-md-block">
            {{ currentUser.name }} ({{ currentUser.role }})
          </span>
        </a>

        <ul class="dropdown-menu dropdown-menu-end">

          <li class="dropdown-header">
            <strong>{{ currentUser.name }}</strong><br>
            <small>{{ currentUser.role }}</small>
          </li>

          <li><hr class="dropdown-divider"></li>

          <li class="dropdown-header small text-muted">Switch Account</li>

          <li v-for="role in roles" :key="role">
            <a class="dropdown-item" href="#" @click.prevent="switchAccount(role)">
              <i class="bi bi-arrow-repeat me-2"></i>
              Switch to {{ role }}
            </a>
          </li>

          <li><hr class="dropdown-divider"></li>

          <li>
            <router-link to="/profile" class="dropdown-item">
              Profile
            </router-link>
          </li>

          <li>
            <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
              Logout
            </a>
          </li>

        </ul>
      </li>

    </ul>
  </nav>

</header>
</template>

<script>
import axios from "axios";

export default {
  name: "TheHeader",

  data() {
    return {
      currentUser: {},
      roles: ["office", "farm", "personal"],
      notificationCount: 0
    };
  },

  methods: {

    handleSidebar() {
      document.body.classList.toggle("toggle-sidebar");
    },

    loadUser() {
      this.currentUser = JSON.parse(localStorage.getItem("user")) || {};
    },

    async switchAccount(role) {
      const presets = {
        office: { email: "office@algospace.co.ke", password: "password123" },
        farm: { email: "farm@algospace.co.ke", password: "password123" },
        personal: { email: "personal@algospace.co.ke", password: "password123" }
      };

      try {
        const res = await axios.post("/api/login", presets[role]);

        localStorage.setItem("token", res.data.token);
        localStorage.setItem("user", JSON.stringify(res.data.user));

        axios.defaults.headers.common["Authorization"] =
          `Bearer ${res.data.token}`;

        this.loadUser();

        // 🔥 CRITICAL: notify sidebar instantly
        window.dispatchEvent(new Event("auth-changed"));

        this.$router.push("/dashboard");

      } catch (err) {
        console.error("Switch failed", err);
      }
    },

    async logout() {
      localStorage.removeItem("token");
      localStorage.removeItem("user");

      window.dispatchEvent(new Event("auth-changed"));

      this.$router.push("/login2");
    }
  },

  mounted() {
    this.loadUser();
  }
};
</script>

<style>
.hover-shadow:hover {
  background: #f8f9fa;
}
</style>