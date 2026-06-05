<template>
  <!-- ======= HEADER ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <!-- LEFT SECTION -->
    <div class="d-flex align-items-center justify-content-between">

      <a href="/" class="logo d-flex align-items-center gap-2" title="Back to AlgoSpace Cyber Website">
        <img src="@/assets/img/algospacelogo.png" alt="AlgoSpace Cyber Logo" />
        <span class="logo-text fs-6 opacity-75">Back to website</span>
      </a>

      <i class="bi bi-list toggle-sidebar-btn" @click="handleSidebar"></i>

      <span class="d-none d-lg-block" style="color: darkgreen;">
        <strong>ALGOSPACE CYBER</strong> PORTAL
      </span>
    </div>

    <!-- RIGHT NAV -->
    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <!-- 🔔 NOTIFICATIONS -->
        <li class="nav-item dropdown">
          <a class="nav-link nav-icon" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-bell"></i>
            <span v-if="notificationCount > 0" class="badge bg-danger badge-number">
              {{ notificationCount }}
            </span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow notifications p-2" style="min-width: 320px;">

            <li v-if="overdue.length" class="dropdown-header d-flex justify-content-between">
              <span>Overdue</span>
              <span class="badge bg-danger">{{ overdue.length }}</span>
            </li>

            <li v-for="r in overdue" :key="r.id" class="notification-item d-flex p-2 mb-1">
              <i class="bi bi-exclamation-triangle text-danger me-2"></i>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ r.title }}</h6>
                <small class="text-muted">{{ r.date }}</small>
              </div>
              <button class="btn btn-sm btn-success" @click.stop="markAsDone(r.id)">Done</button>
            </li>

            <li v-if="today.length" class="dropdown-header mt-2">
              Today <span class="badge bg-warning">{{ today.length }}</span>
            </li>

            <li v-for="r in today" :key="r.id" class="notification-item d-flex p-2 mb-1">
              <i class="bi bi-alarm text-warning me-2"></i>
              <div class="flex-grow-1">
                <h6 class="mb-0">{{ r.title }}</h6>
                <small class="text-muted">{{ r.time }}</small>
              </div>
              <button class="btn btn-sm btn-success" @click.stop="markAsDone(r.id)">Done</button>
            </li>

            <li v-if="notificationCount === 0" class="text-center text-muted p-2">
              No reminders 🎉
            </li>

          </ul>
        </li>

        <!-- 👤 ACCOUNT SWITCHER (NEW) -->
        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <i class="bi bi-person-circle fs-5"></i>
            <span class="d-none d-md-block dropdown-toggle ps-2">
              {{ currentUser.name }}
            </span>
          </a>

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">

            <li class="dropdown-header text-center">
              <h6>{{ currentUser.name }}</h6>
              <small class="text-muted">{{ currentUser.role }}</small>
            </li>

            <li><hr class="dropdown-divider"></li>

            <!-- 🔁 SWITCH ACCOUNTS -->
            <li class="dropdown-header small text-muted">Switch Account</li>

            <li v-for="role in roles" :key="role">
              <a class="dropdown-item d-flex align-items-center" href="#" @click.prevent="autoLogin(role)">
                <i class="bi bi-box-arrow-in-right me-2"></i>
                {{ role }}
              </a>
            </li>

            <li><hr class="dropdown-divider"></li>

            <li>
              <router-link to="/profile" class="dropdown-item">
                <i class="bi bi-person me-2"></i> My Profile
              </router-link>
            </li>

            <li>
              <a class="dropdown-item text-danger" href="#" @click.prevent="logout">
                <i class="bi bi-box-arrow-right me-2"></i> Sign Out
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
      currentUser: {
        name: "Guest",
        role: "unknown"
      },

      roles: ["office", "farm", "personal"],

      notificationCount: 0,
      reminders: [],

      reminderInterval: null
    };
  },

  computed: {
    isLoggedIn() {
      return !!localStorage.getItem("token");
    },

    overdue() {
      return this.reminders.filter(r => r.status === "overdue");
    },

    today() {
      return this.reminders.filter(r => r.status === "today");
    }
  },

  methods: {

    handleSidebar() {
      document.body.classList.toggle("toggle-sidebar");
    },

    // 🔥 FIX: ALWAYS READ FRESH USER
    loadUser() {
      const user = JSON.parse(localStorage.getItem("user"));
      if (user) {
        this.currentUser = user;
      }
    },

    async loadReminders() {
      if (!this.isLoggedIn) return;

      try {
        const res = await axios.get("/api/reminders/overview", {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
          }
        });

        this.reminders = res.data || [];
        this.notificationCount = this.reminders.length;

      } catch (err) {
        this.reminders = [];
        this.notificationCount = 0;
      }
    },

    async markAsDone(id) {
      try {
        await axios.put(`/api/diary-entries/${id}/done`);
        this.loadReminders();
      } catch (err) {
        console.error(err);
      }
    },

    autoLogin(role) {
      const presets = {
        office: { email: "office@algospace.co.ke", password: "password123" },
        farm: { email: "farm@algospace.co.ke", password: "password123" },
        personal: { email: "personal@algospace.co.ke", password: "password123" }
      };

      const creds = presets[role];
      if (!creds) return;

      axios.post("/api/login", creds)
        .then(res => {

          localStorage.setItem("token", res.data.token);
          localStorage.setItem("user", JSON.stringify(res.data.user));

          axios.defaults.headers.common["Authorization"] =
            `Bearer ${res.data.token}`;

          this.loadUser();
          this.loadReminders();

          // 🔥 IMPORTANT: notify sidebar + other components
          window.dispatchEvent(new Event("auth-changed"));

          this.$router.push("/dashboard");
        })
        .catch(err => {
          console.error("Switch login failed", err);
        });
    },

    async logout() {
      try {
        await axios.post("/api/logout", {}, {
          headers: {
            Authorization: `Bearer ${localStorage.getItem("token")}`
          }
        });
      } catch (e) {}

      localStorage.removeItem("token");
      localStorage.removeItem("user");

      window.dispatchEvent(new Event("auth-changed"));

      this.$router.push("/login2");
    }
  },

  mounted() {
    this.loadUser();
    this.loadReminders();

    // 🔥 CRITICAL FIX: listen for account changes
    window.addEventListener("auth-changed", this.loadUser);
  },

  beforeUnmount() {
    window.removeEventListener("auth-changed", this.loadUser);

    if (this.reminderInterval) {
      clearInterval(this.reminderInterval);
    }
  }
};
</script>

<style>
.hover-shadow:hover {
  background: #f8f9fa;
}
</style>