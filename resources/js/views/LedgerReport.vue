<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

      <!-- ================= FINANCIAL SUMMARY (PERSONAL + PROFIT & LOSS) ================= -->
      <div v-if="userRole === 'personal'" class="col-12 mb-4">
        <div class="p-3 rounded bg-light border shadow-sm">

          <!-- ================= HEADER: FILTERS + TOTAL ================= -->
          <div class="row align-items-center mb-4">
            <div class="col-md-8">
              <div class="row g-2">
                <div class="col-md-4">
                  <input type="date"
                        v-model="filters.start_date"
                        class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                  <input type="date"
                        v-model="filters.end_date"
                        class="form-control form-control-sm">
                </div>
                <div class="col-md-4">
                  <button class="btn btn-sm btn-primary w-100"
                          @click="fetchReport">
                    Apply Filter
                  </button>
                </div>
              </div>
            </div>

            <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
              <div class="small text-muted">Total Worth</div>
              <div class="fs-4 fw-bold text-primary">
                KES {{ accountTotal }}
              </div>
            </div>
          </div>

          <!-- ================= SUMMARY CARDS ================= -->
          <div class="row g-2 text-center mb-4">

            <div class="col-6 col-md-3">
              <div class="p-2 rounded bg-success bg-opacity-10 border">
                <div class="small text-muted">
                  <i class="bi bi-cash-coin me-1"></i> Revenue
                </div>
                <div class="fw-semibold text-success">
                  KES {{ report.revenue }}
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="p-2 rounded bg-danger bg-opacity-10 border">
                <div class="small text-muted">
                  <i class="bi bi-wallet2 me-1"></i> Expenses
                </div>
                <div class="fw-semibold text-danger">
                  KES {{ report.expenses }}
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="p-2 rounded bg-warning bg-opacity-10 border">
                <div class="small text-muted">
                  <i class="bi bi-person-fill me-1"></i> Owner Draws
                </div>
                <div class="fw-semibold text-warning">
                  KES {{ report.owner_draws }}
                </div>
              </div>
            </div>

            <div class="col-6 col-md-3">
              <div class="p-2 rounded bg-primary bg-opacity-10 border">
                <div class="small text-muted">
                  <i class="bi bi-bar-chart-line me-1"></i> Profit
                </div>
                <div class="fw-semibold text-primary">
                  KES {{ report.profit }}
                </div>
              </div>
            </div>

          </div>

          <!-- ================= ACTION GRID ================= -->
          <div class="row g-3 mb-4">

            <!-- LEFT COLUMN -->
            <div class="col-md-6">

              <!-- TITHE -->
              <div v-if="!report.tithe_paid && report.tithe > 0"
                  class="p-3 rounded bg-info bg-opacity-10 border mb-3">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-heart me-1"></i> Tithe Due
                </div>
                <div class="mb-2 text-info">
                  KES {{ report.tithe }}
                </div>

                <select v-model="selectedAccount"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">Pay from account</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <button class="btn btn-sm btn-info w-100"
                        @click="payTithe">
                  Pay Tithe
                </button>
              </div>
              <!-- TITHE PAID -->
              <div v-if="report.tithe_paid"
                  class="p-3 rounded bg-info bg-opacity-10 border mb-3">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-check-circle me-1 text-success"></i> Tithe Paid
                </div>

                <div class="mb-2 text-success">
                  KES {{ report.tithe }}
                </div>

                <div class="small text-muted">
                  Thank you for fulfilling your tithe
                </div>
              </div>


              <!-- FIRST FRUITS -->
              <div class="p-3 rounded bg-secondary bg-opacity-10 border mb-3">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-gift me-1"></i> First Fruits
                </div>

                <!-- Amount input -->
                <input type="number"
                      v-model.number="firstFruitsAmount"
                      class="form-control form-control-sm mb-2"
                      placeholder="Enter First Fruits amount">

                <!-- Select account -->
                <select v-model="selectedAccount"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">Pay from account</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <button class="btn btn-sm btn-secondary w-100"
                        @click="payFirstFruits">
                  Pay First Fruits
                </button>
              </div>

              <!-- FUNDS IN -->
              <div class="p-3 rounded bg-success bg-opacity-10 border">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-box-arrow-in-down me-1"></i> Funds In
                </div>

                <!-- Amount -->
                <input type="number"
                      v-model.number="fundsIn.amount"
                      class="form-control form-control-sm mb-2"
                      placeholder="Amount">

                <!-- Destination Account -->
                <select v-model="fundsIn.account_id"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">Deposit to account</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <!-- Source Type -->
                <select v-model="fundsIn.source"
                        class="form-select form-select-sm mb-3">
                  <option disabled value="">Source of funds</option>
                  <option value="capital">New Capital</option>
                  <option value="owner_return">Owner Funds Returned</option>
                </select>

                <!-- Helper text -->
                <div class="small text-muted mb-2">
                  <span v-if="fundsIn.source === 'capital'">
                    Adds new capital into the business.
                  </span>
                  <span v-else-if="fundsIn.source === 'owner_return'">
                    Money previously withdrawn and now saved back.
                  </span>
                </div>

                <button class="btn btn-sm btn-success w-100"
                        :disabled="!fundsIn.amount || !fundsIn.account_id || !fundsIn.source"
                        @click="submitFundsIn">
                  Deposit Funds
                </button>
              </div>

            </div>

            <!-- RIGHT COLUMN -->
            <div class="col-md-6">

              <!-- CAPITAL INJECTION -->
              <div class="p-3 rounded bg-success bg-opacity-10 border mb-3">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-arrow-down-circle me-1"></i>
                  Capital Injection
                </div>

                <input type="number"
                      v-model.number="capital.amount"
                      class="form-control form-control-sm mb-2"
                      placeholder="Amount">

                <select v-model="capital.account_id"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">Receiving account</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <button class="btn btn-sm btn-success w-100"
                        @click="injectCapital">
                  Inject Capital
                </button>
              </div>

              <!-- TRANSFER -->
              <div class="p-3 rounded bg-primary bg-opacity-10 border">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-shuffle me-1"></i> Transfer Funds
                </div>

                <select v-model="transfer.from"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">From</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <select v-model="transfer.to"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">To</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }}
                  </option>
                </select>

                <input type="number"
                      v-model.number="transfer.amount"
                      class="form-control form-control-sm mb-2"
                      placeholder="Amount">

                <button class="btn btn-sm btn-primary w-100"
                        @click="transferFunds">
                  Transfer
                </button>
              </div>
            </div>
          </div>

          <!-- ================= LOAN + OWNER DRAW ================= -->
          <div class="row g-3">

            <!-- LOAN -->
            <div class="col-md-6">
              <div class="p-3 rounded bg-dark bg-opacity-10 border text-center">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-arrow-left-right me-1"></i> Loan
                </div>

                <div class="mb-2">
                  Outstanding: <strong>KES {{ report.loan_balance }}</strong>
                </div>

                <select v-model="selectedLoanAccount" class="form-select form-select-sm mb-2">
                  <option disabled value="">Select account</option>
                  <option v-for="acc in personalAccounts" :key="acc.id" :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <input type="number"
                      v-model.number="loanAmount"
                      class="form-control form-control-sm mb-2"
                      placeholder="Amount">

                <div class="d-flex gap-2">
                  <button class="btn btn-sm btn-success w-50" @click="loanIn">
                    Loan In
                  </button>

                  <button class="btn btn-sm btn-danger w-50"
                          :disabled="!loanAmount || !selectedLoanAccount || report.loan_balance <= 0"
                          @click="loanOut">
                    Repay
                  </button>
                </div>
              </div>
            </div>


              
            <!-- OWNER DRAW -->
            <div class="col-md-6"
                v-if="report.tithe_paid && report.profit > 0">
              <div class="p-3 rounded bg-warning bg-opacity-10 border text-center">
                <div class="fw-semibold mb-1">
                  <i class="bi bi-person-fill me-1"></i> Owner Draw
                </div>

                <div class="mb-2">
                  Withdrawable:
                  <strong>
                    KES {{ report.profit_after_tithe * 0.3 }}
                  </strong>
                </div>

                <select v-model="selectedAccount"
                        class="form-select form-select-sm mb-2">
                  <option disabled value="">Pay to account</option>
                  <option v-for="acc in personalAccounts"
                          :key="acc.id"
                          :value="acc.id">
                    {{ acc.name }} (KES {{ acc.balance }})
                  </option>
                </select>

                <button class="btn btn-sm btn-warning w-100"
                        @click="payOwnerDraw">
                  Withdraw Profit
                </button>
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
  timer: 3000,
  timerProgressBar: true
});

export default {
  name: 'Home',
  components: {
    Master,
  },
  data() {
    return {
      currentYear: '',
      filters: {
        start_date: null,
        end_date: null
      },
      accountTotal: 0,
      personalAccounts: [],
      selectedAccount: null,
      firstFruitsAmount: 0,
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
      /* Capital Injection */
      capital: {
        amount: null,
        account_id: null
      },

      /* Loans */
      loanAmount: null,

      /* Inter Account Transfer */
      transfer: {
        from: null,
        to: null,
        amount: null
      },   
      fundsIn: {
        amount: null,
        account_id: null,
        source: null, // capital | owner_return
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
    async payOwnerDraw() {
      // Validate account selection
      if (!this.selectedAccount) {
        toast.fire({
          icon: 'warning',
          title: 'Please select a payment account'
        });
        return;
      }

      try {
        const res = await axios.post('/api/ledger/owner-draw', {
          payment_account_id: this.selectedAccount,
          from: this.filters.start_date,
          to: this.filters.end_date
        });

        toast.fire({
          icon: 'success',
          title: `Owner draw successful: KES ${res.data.amount}`
        });

        this.fetchReport();

      } catch (error) {
        toast.fire({
          icon: 'error',
          title: error.response?.data?.message || 'Owner draw failed'
        });
      }
    },
    // =========================
    // First Fruits
    // =========================
    async payFirstFruits() {
      if (!this.selectedAccount) {
        toast.fire({
          icon: 'warning',
          title: 'Please select a payment account'
        });
        return;
      }

      if (!this.firstFruitsAmount || this.firstFruitsAmount <= 0) {
        toast.fire({
          icon: 'warning',
          title: 'Enter a valid amount'
        });
        return;
      }

      try {
        await axios.post('/api/ledger/first-fruits', {
          account_id: this.selectedAccount,
          amount: this.firstFruitsAmount
        });

        toast.fire({
          icon: 'success',
          title: `First Fruits paid: KES ${this.firstFruitsAmount}`
        });

        this.selectedAccount = null;
        this.firstFruitsAmount = null;
        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Payment failed'
        });
      }
    },


    // =========================
    // Capital Injection
    // =========================
    async injectCapital() {
      if (!this.capital.amount || !this.capital.account_id) {
        toast.fire({
          icon: 'warning',
          title: 'Enter amount and select account'
        });
        return;
      }

      try {
        await axios.post('/api/ledger/capital-injection', {
          amount: this.capital.amount,
          account_id: this.capital.account_id
        });

        toast.fire({
          icon: 'success',
          title: 'Capital injected successfully'
        });

        this.capital.amount = null;
        this.capital.account_id = null;
        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Capital injection failed'
        });
      }
    },


    async loanIn() {
      if (!this.loanAmount || !this.selectedLoanAccount) {
        toast.fire({
          icon: 'warning',
          title: 'Select account and enter amount'
        });
        return;
      }

      try {
        await axios.post('/api/ledger/loan/in', {
          account_id: this.selectedLoanAccount,
          amount: this.loanAmount
        });

        toast.fire({
          icon: 'success',
          title: 'Loan received successfully'
        });

        this.loanAmount = null;
        this.selectedLoanAccount = null;
        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Loan receipt failed'
        });
      }
    },


    async loanOut() {
      if (!this.loanAmount || !this.selectedLoanAccount) {
        toast.fire({
          icon: 'warning',
          title: 'Select account and enter amount'
        });
        return;
      }

      try {
        await axios.post('/api/ledger/loan/out', {
          account_id: this.selectedLoanAccount,
          amount: this.loanAmount
        });

        toast.fire({
          icon: 'success',
          title: 'Loan repayment successful'
        });

        this.loanAmount = null;
        this.selectedLoanAccount = null;
        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Loan repayment failed'
        });
      }
    },


    async submitFundsIn() {
      try {
        await axios.post('/api/ledger/funds-in', this.fundsIn);

        toast.fire({
          icon: 'success',
          title: 'Funds deposited successfully'
        });

        // Reset form
        this.fundsIn = {
          amount: null,
          account_id: null,
          source: null,
        };

        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Deposit failed'
        });
      }
    },


    // =========================
    // Inter Account Transfer
    // =========================
    async transferFunds() {
      if (!this.transfer.from || !this.transfer.to || !this.transfer.amount) {
        toast.fire({
          icon: 'warning',
          title: 'Select from, to, and amount'
        });
        return;
      }

      if (this.transfer.from === this.transfer.to) {
        toast.fire({
          icon: 'warning',
          title: 'Cannot transfer to the same account'
        });
        return;
      }

      try {
        await axios.post('/api/ledger/transfer', {
          from: this.transfer.from,
          to: this.transfer.to,
          amount: this.transfer.amount
        });

        toast.fire({
          icon: 'success',
          title: 'Transfer successful'
        });

        this.transfer.from = null;
        this.transfer.to = null;
        this.transfer.amount = null;
        this.fetchReport();

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Transfer failed'
        });
      }
    },

    async fetchReport() {
      try {
        const res = await axios.get('/api/ledger/profit-loss', {
          params: this.filters
        });

        this.report = res.data;
        this.accountTotal = res.data.accountTotal;
        this.personalAccounts = res.data.personalAccounts;

        // toast.fire({
        //   icon: 'success',
        //   title: 'Report loaded successfully'
        // });

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Failed to load report'
        });
      }
    },

    async payTithe() {
      if (!this.selectedAccount) {
        toast.fire({
          icon: 'warning',
          title: 'Select an account'
        });
        return;
      }

      try {
        const res = await axios.post('/api/ledger/tithe/pay', {
          payment_account_id: this.selectedAccount
        });

        toast.fire({
          icon: 'success',
          title: res.data.message
        });

        this.fetchReport(); // refresh profit/tithe

      } catch (err) {
        toast.fire({
          icon: 'error',
          title: err.response?.data?.message || 'Tithe payment failed'
        });
      }
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