<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

  <!-- ================= FINANCIAL SUMMARY (PERSONAL + PROFIT & LOSS) ================= -->
  <div v-if="userRole === 'personal'" class="col-12 mb-4">
    <div class="p-3 rounded bg-light border shadow-sm">

      <!-- Primary total: Account Worth -->
      <div class="text-center mb-3">
        <div class="text-muted small">Total Worth</div>
        <div class="fs-4 fw-bold">KES {{ accountTotal }}</div>
      </div>

      <!-- Profit & Loss Breakdown -->
      <div class="row g-2 text-center mb-3">

        <!-- Revenue -->
        <div class="col-6 col-md-3">
          <div class="p-2 rounded bg-success bg-opacity-10 border">
            <div class="small text-muted">
              <i class="bi bi-cash-coin me-1"></i>
              Revenue
            </div>
            <div class="fw-semibold text-success">KES {{ report.revenue }}</div>
            <div class="small text-muted fst-italic">All sales income</div>
          </div>
        </div>

        <!-- Expenses -->
        <div class="col-6 col-md-3">
          <div class="p-2 rounded bg-danger bg-opacity-10 border">
            <div class="small text-muted">
              <i class="bi bi-wallet2 me-1"></i>
              Expenses
            </div>
            <div class="fw-semibold text-danger">KES {{ report.expenses }}</div>
            <div class="small text-muted fst-italic">Operational costs</div>
          </div>
        </div>

        <!-- Owner Draws -->
        <div class="col-6 col-md-3">
          <div class="p-2 rounded bg-warning bg-opacity-10 border">
            <div class="small text-muted">
              <i class="bi bi-person-fill me-1"></i>
              Owner Draws
            </div>
            <div class="fw-semibold text-warning">KES {{ report.owner_draws }}</div>
            <div class="small text-muted fst-italic">Withdrawn profit</div>
          </div>
        </div>

        <!-- Profit -->
        <div class="col-6 col-md-3">
          <div class="p-2 rounded bg-primary bg-opacity-10 border">
            <div class="small text-muted">
              <i class="bi bi-bar-chart-line me-1"></i>
              Profit
            </div>
            <div class="fw-semibold text-primary">KES {{ report.profit }}</div>
            <div class="small text-muted fst-italic">Revenue - Expenses - Draws</div>
          </div>
        </div>

      </div>

      <!-- Tithe Section -->
      <div class="row g-2 text-center mb-3">
        <div class="col-12 col-md-6 mx-auto">
          <div class="p-2 rounded bg-info bg-opacity-10 border">
            <div class="small text-muted">
              <i class="bi bi-heart me-1"></i>
              Tithe Due
            </div>
            <div class="fw-semibold text-info">KES {{ report.tithe }}</div>
            <div class="small text-muted fst-italic">10% of profit before owner draws</div>

            <div class="mt-2">
              <select v-model="selectedAccount" class="form-select form-select-sm mb-2">
                <option disabled value="">Select payment account</option>
                <option v-for="acc in personalAccounts" :key="acc.id" :value="acc.id">
                  {{ acc.name }} (KES {{ acc.balance }})
                </option>
              </select>
              <button class="btn btn-sm btn-info" @click="payTithe">
                Pay Tithe
              </button>
            </div>
          </div>
        </div>
      </div>

    </div>
  </div>
  <!-- ================= END FINANCIAL SUMMARY ================= -->

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
      personalAccounts: [],
      selectedAccount: null,
      accountTotal: 0,     // total worth
      liquidTotal: 0,      // spendable cash/bank/mpesa
      semiLiquidTotal: 0,  // buffer accounts
      savingsTotal: 0,      // savings/shares
      report: {
        revenue: 0,
        expenses: 0,
        owner_draws: 0,
        profit: 0,
        tithe: 0
      },          
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

  methods: {
    async fetchReport() {
      const res = await axios.get('/api/ledger/profit-loss');
      this.report = res.data;
      this.personalAccounts = res.data.personalAccounts
    },
    async payTithe() {
      if (!this.selectedAccount) return alert('Select an account');

      const res = await axios.post('/api/ledger/tithe/pay', {
        payment_account_id: this.selectedAccount
      });

      alert(res.data.message);
      this.fetchReport();       // refresh profit/tithe
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
    this.fetchReport();
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