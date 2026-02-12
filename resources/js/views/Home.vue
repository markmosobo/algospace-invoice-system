<template>
  <Master>
    <section class="section dashboard">
      <div class="row">
        <div
          v-for="(card, index) in dashboardCards"
          :key="index"
          class="col-xxl-2 col-md-3 col-sm-4 mb-3"
        >
          <div class="card info-card shadow-sm">
            <div class="card-body">
              <h5 class="card-title" :class="`text-${card.color}`">
                {{ card.title }}
              </h5>

              <div class="d-flex align-items-center">
                <div class="card-icon rounded-circle d-flex align-items-center justify-content-center bg-light">
                  <i :class="`bi ${card.icon} text-${card.color}`"></i>
                </div>
                <div class="ps-3">
                  <h6 
                    class="text-truncate" 
                    :style="{ maxWidth: '120px', fontSize: card.value > 999999 ? '0.9rem' : '1rem' }"
                  >
                    {{ card.value ?? 0 }}
                  </h6>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

    </section>

  </Master>
</template>

<script>
import Master from '@/components/Master.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

export default {
  name: 'Home',
  components: {
    Master,
  },
  data() {
    return {
      currentYear: '',
      user: {},
      currentUser: {},
      userRole: null,
      stats: {},
      properties: [],
      openproperties: [],
      closedproperties: [],
      users: [],
      badgeClasses: [
        'text-success',
        'text-danger',
        'text-primary',
        'text-info',
        'text-warning',
        'text-muted',
      ],
    };
  },
  computed: {
    dashboardCards() {
      const cards = {
        office: [
          { title: 'Services Offered', value: this.stats.services, icon: 'bi-people', color: 'primary' },
          { title: 'Suppliers', value: this.stats.suppliers, icon: 'bi-person-lines-fill', color: 'info' },
          { title: 'Customers', value: this.stats.customers, icon: 'bi-people', color: 'secondary' },
          { title: 'Supplies', value: this.stats.supplies, icon: 'bi-building', color: 'success' },
          { title: 'Payments', value: this.stats.payments, icon: 'bi-circle', color: 'warning' },
          { title: 'Invoices', value: this.stats.invoices, icon: 'bi-hourglass-split', color: 'info' },
        ],

        personal: [
          { title: 'Accounts', value: this.stats.personalAccounts, icon: 'bi-building', color: 'success' },
          { title: 'Grand Total', value: this.stats.grandTotal, icon: 'bi-cash-stack', color: 'primary' }, // <-- new
          { title: 'Categories', value: this.stats.personalCategories, icon: 'bi-house-door', color: 'warning' },
          { title: 'Transactions', value: this.stats.personalTransactions, icon: 'bi-box-arrow-right', color: 'danger' },
        ],

      };

      // Remove cards with null, 0, or 'N/A' values for cleaner UI
      return (cards[this.userRole] || []).filter(card => {
        return card.value !== null && card.value !== 0 && card.value !== 'N/A';
      });
    }
  },
  methods: {
    fetchDashboardStats() {
      axios
        .get('/api/dashboard/stats')
        .then(response => {
          this.stats = response.data.stats;
        })
        .catch(error => {
          console.error('Dashboard stats error:', error);
        });
    },
    navigateTo(location) {
      this.$router.push(location);
    },
  },
  mounted() {
    const storedUser = JSON.parse(localStorage.getItem('user')) || {};
    this.user = storedUser;
    this.currentUser = storedUser;
    this.userRole = this.user.role;
    this.current_user_id = storedUser.id;
    this.current_user = `${storedUser.first_name || ''} ${storedUser.last_name || ''}`.trim();
    // this.getCurrentYear();
    this.fetchDashboardStats();
  }
};
</script>



<style scoped>
.card {
  transition: transform 0.2s;
}

.card:hover {
  transform: scale(1.02);
}

.bg-light {
  background-color: rgba(255, 255, 255, 0.8);
}
</style>