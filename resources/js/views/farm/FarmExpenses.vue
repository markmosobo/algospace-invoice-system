
<template>
  <Master>
    <section class="section dashboard farm-expenses-page">

      <!-- PAGE HEADER -->
      <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4">
        <div>
          <h3 class="fw-bold mb-1">Farm Expenses</h3>
          <p class="text-muted mb-0">
            Monitor and manage expenditure across your farm ventures.
          </p>
        </div>

        <div class="d-flex gap-2 flex-wrap">
          <button
            class="btn btn-outline-success"
            @click="exportCsv"
            :disabled="filteredExpenses.length === 0"
          >
            <i class="bi bi-download me-1"></i>
            Export CSV
          </button>

          <button
            class="btn btn-success"
            @click="addExpense"
          >
            <i class="bi bi-plus-circle me-1"></i>
            Add Farm Expense
          </button>

          <div class="btn-group">
            <button
              type="button"
              class="btn btn-success dropdown-toggle"
              data-bs-toggle="dropdown"
              aria-expanded="false"
            >
              <i class="bi bi-three-dots"></i>
            </button>

            <ul class="dropdown-menu dropdown-menu-end">
              <li>
                <a
                  @click.prevent="navigateTo('/clients')"
                  class="dropdown-item"
                  href="#"
                >
                  <i class="ri-user-fill me-2"></i> Clients
                </a>
              </li>

              <li>
                <a
                  @click.prevent="navigateTo('/savings')"
                  class="dropdown-item"
                  href="#"
                >
                  <i class="ri-wallet-fill me-2"></i> Savings
                </a>
              </li>

              <li>
                <a
                  @click.prevent="navigateTo('/loans')"
                  class="dropdown-item"
                  href="#"
                >
                  <i class="ri-bank-card-fill me-2"></i> Loans
                </a>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
          <div class="card summary-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">Total Expenditure</small>
                  <h4 class="fw-bold text-danger mt-2 mb-0">
                    KSh {{ money(expenseSummary.totalAmount) }}
                  </h4>
                </div>

                <div class="summary-icon expense-icon">
                  <i class="bi bi-cash-stack"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-xl-3">
          <div class="card summary-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">Transactions</small>
                  <h4 class="fw-bold mt-2 mb-0">
                    {{ expenseSummary.transactions }}
                  </h4>
                </div>

                <div class="summary-icon transaction-icon">
                  <i class="bi bi-receipt"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-xl-3">
          <div class="card summary-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">Average Expense</small>
                  <h4 class="fw-bold mt-2 mb-0">
                    KSh {{ money(expenseSummary.averageAmount) }}
                  </h4>
                </div>

                <div class="summary-icon average-icon">
                  <i class="bi bi-calculator"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 col-xl-3">
          <div class="card summary-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">Largest Expense</small>
                  <h4 class="fw-bold mt-2 mb-0">
                    KSh {{ money(expenseSummary.largestAmount) }}
                  </h4>
                  <small class="text-muted">
                    {{ expenseSummary.largestCategory }}
                  </small>
                </div>

                <div class="summary-icon largest-icon">
                  <i class="bi bi-graph-up-arrow"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- EXPENSE TREND -->
      <div class="card mb-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <h5 class="card-title fw-bold mb-0">
                Expenditure Trend
              </h5>
              <small class="text-muted">
                Daily farm expenditure based on selected filters
              </small>
            </div>

            <span class="badge bg-light text-dark">
              {{ expenseTrend.length }} days
            </span>
          </div>
        </div>

        <div class="card-body">
          <div v-if="expenseTrend.length" class="trend-container">

            <div
              v-for="item in expenseTrend"
              :key="item.date"
              class="trend-column"
              :title="`${item.date}: KSh ${money(item.amount)}`"
            >
              <div class="trend-value">
                {{ compactMoney(item.amount) }}
              </div>

              <div class="trend-bar-wrapper">
                <div
                  class="trend-bar"
                  :style="{ height: trendHeight(item.amount) + '%' }"
                ></div>
              </div>

              <small class="trend-date">
                {{ shortDate(item.date) }}
              </small>
            </div>

          </div>

          <div v-else class="text-center text-muted py-4">
            No expenditure data available for the selected period.
          </div>
        </div>
      </div>

      <!-- EXPENSE FILTERS -->
      <div class="card mb-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0">
              <i class="bi bi-funnel me-2"></i>
              Filter Expenses
            </h5>

            <button
              class="btn btn-sm btn-outline-secondary"
              @click="resetFilters"
            >
              <i class="bi bi-arrow-counterclockwise me-1"></i>
              Reset
            </button>
          </div>
        </div>

        <div class="card-body">
          <div class="row g-3">

            <div class="col-md-4 col-lg-3">
              <label class="form-label">Farm Venture</label>

              <select
                v-model="filters.venture_id"
                class="form-select"
                @change="applyFilters"
              >
                <option value="">All Ventures</option>

                <option
                  v-for="venture in farmventures"
                  :key="venture.id"
                  :value="String(venture.id)"
                >
                  {{ venture.venture_name }}
                </option>
              </select>
            </div>

            <div class="col-md-4 col-lg-3">
              <label class="form-label">Expense Category</label>

              <select
                v-model="filters.category"
                class="form-select"
                @change="applyFilters"
              >
                <option value="">All Categories</option>

                <option
                  v-for="category in categories"
                  :key="category.value"
                  :value="category.value"
                >
                  {{ category.label }}
                </option>
              </select>
            </div>

            <div class="col-md-4 col-lg-3">
              <label class="form-label">Date Period</label>

              <select
                v-model="filters.period"
                class="form-select"
                @change="applyPeriod"
              >
                <option value="all">All Time</option>
                <option value="today">Today</option>
                <option value="week">This Week</option>
                <option value="month">This Month</option>
                <option value="year">This Year</option>
                <option value="custom">Custom Range</option>
              </select>
            </div>

            <div class="col-md-4 col-lg-3">
              <label class="form-label">Paid By / Method</label>

              <input
                type="text"
                v-model="filters.paid_by"
                class="form-control"
                placeholder="Cash, M-Pesa, Bank..."
                @input="applyFilters"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">From Date</label>

              <input
                type="date"
                v-model="filters.date_from"
                class="form-control"
                @change="applyCustomDate"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">To Date</label>

              <input
                type="date"
                v-model="filters.date_to"
                class="form-control"
                @change="applyCustomDate"
              >
            </div>

            <div class="col-12">
              <label class="form-label">Search Description</label>

              <input
                type="text"
                v-model="filters.search"
                class="form-control"
                placeholder="Search expense descriptions..."
                @input="applyFilters"
              >
            </div>

          </div>

          <div class="d-flex justify-content-between flex-wrap gap-2 mt-3">
            <small class="text-muted">
              {{ filteredExpenses.length }} matching expense records.
            </small>

            <small
              class="text-muted"
              v-if="hasActiveFilters"
            >
              Filters are active.
            </small>
          </div>
        </div>
      </div>

      <!-- EXPENSE TABLE -->
      <div class="card top-selling overflow-auto">

        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="card-title fw-bold mb-0">
              Farm Expense Records
            </h5>

            <div class="d-flex align-items-center gap-2">
              <label class="small text-muted">Rows:</label>

              <select
                v-model.number="pageSize"
                class="form-select form-select-sm"
                style="width: 80px"
                @change="currentPage = 1"
              >
                <option :value="10">10</option>
                <option :value="25">25</option>
                <option :value="50">50</option>
                <option :value="100">100</option>
              </select>
            </div>
          </div>
        </div>

        <div class="card-body p-0">

          <div v-if="initializing" class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="text-muted mt-2 mb-0">
              Loading farm expenses...
            </p>
          </div>

          <div v-else class="table-responsive">

            <table class="table table-hover align-middle mb-0">

              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Category</th>
                  <th>Description</th>
                  <th>Venture</th>
                  <th>Amount (KES)</th>
                  <th>Expense Date</th>
                  <th>Paid By</th>
                  <th class="text-center">Action</th>
                </tr>
              </thead>

              <tbody>

                <tr
                  v-for="(expense, index) in paginatedExpenses"
                  :key="expense.id"
                >
                  <td>{{ pageStart + index }}</td>

                  <td>
                    <span class="category-label">
                      {{ categoryLabel(expense.expense_category) }}
                    </span>
                  </td>

                  <td>
                    {{ expense.description || 'N/A' }}
                  </td>

                  <td>
                    {{ expense.venture?.venture_name || ventureName(expense.venture_id) }}
                  </td>

                  <td class="fw-bold text-danger">
                    KSh {{ money(expense.amount) }}
                  </td>

                  <td>
                    {{ formatDate(expense.expense_date) }}
                  </td>

                  <td>
                    {{ expense.paid_by || 'N/A' }}
                  </td>

                  <td class="text-center">

                    <div class="btn-group btn-group-sm">

                      <button
                        class="btn btn-outline-info"
                        title="View expense"
                        @click="viewExpense(expense)"
                      >
                        <i class="bi bi-eye"></i>
                      </button>

                      <button
                        class="btn btn-outline-primary"
                        title="Edit expense"
                        @click="editExpense(expense)"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>

                      <button
                        class="btn btn-outline-danger"
                        title="Delete expense"
                        @click="deleteExpense(expense.id)"
                      >
                        <i class="bi bi-trash"></i>
                      </button>

                    </div>

                  </td>
                </tr>

                <tr v-if="paginatedExpenses.length === 0">
                  <td colspan="8" class="text-center py-5">
                    <i class="bi bi-inbox display-5 text-muted"></i>

                    <p class="text-muted mt-2 mb-0">
                      No farm expenses found.
                    </p>

                    <small class="text-muted">
                      Adjust your filters or record a new expense.
                    </small>
                  </td>
                </tr>

              </tbody>

              <tfoot v-if="filteredExpenses.length">
                <tr class="table-light">
                  <th colspan="4" class="text-end">
                    Filtered Expenditure:
                  </th>

                  <th class="text-danger">
                    KSh {{ money(expenseSummary.totalAmount) }}
                  </th>

                  <th colspan="3"></th>
                </tr>
              </tfoot>

            </table>

          </div>
        </div>

        <!-- PAGINATION -->
        <div
          v-if="!initializing && filteredExpenses.length"
          class="card-footer bg-white"
        >
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <small class="text-muted">
              Showing {{ pageStart }}–{{ pageEnd }}
              of {{ filteredExpenses.length }} records
            </small>

            <div class="btn-group btn-group-sm">

              <button
                class="btn btn-outline-secondary"
                :disabled="currentPage === 1"
                @click="currentPage--"
              >
                Previous
              </button>

              <button class="btn btn-success" disabled>
                {{ currentPage }} / {{ totalPages }}
              </button>

              <button
                class="btn btn-outline-secondary"
                :disabled="currentPage >= totalPages"
                @click="currentPage++"
              >
                Next
              </button>

            </div>
          </div>
        </div>

      </div>

      <!-- VIEW EXPENSE MODAL -->
      <div
        class="modal fade"
        id="viewExpenseModal"
        tabindex="-1"
        aria-labelledby="viewExpenseModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered">

          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="viewExpenseModalLabel">
                Expense Details
              </h5>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>

            <div class="modal-body" v-if="selectedExpense">

              <div class="expense-detail-header mb-3">
                <small class="text-muted">Expense Amount</small>

                <h3 class="fw-bold text-danger mb-0">
                  KSh {{ money(selectedExpense.amount) }}
                </h3>
              </div>

              <div class="row g-3">

                <div class="col-md-6">
                  <small class="text-muted">Expense Category</small>
                  <h6 class="fw-semibold">
                    {{ categoryLabel(selectedExpense.expense_category) }}
                  </h6>
                </div>

                <div class="col-md-6">
                  <small class="text-muted">Farm Venture</small>
                  <h6 class="fw-semibold">
                    {{ selectedExpense.venture?.venture_name || ventureName(selectedExpense.venture_id) }}
                  </h6>
                </div>

                <div class="col-md-6">
                  <small class="text-muted">Expense Date</small>
                  <h6 class="fw-semibold">
                    {{ formatDate(selectedExpense.expense_date) }}
                  </h6>
                </div>

                <div class="col-md-6">
                  <small class="text-muted">Paid By</small>
                  <h6 class="fw-semibold">
                    {{ selectedExpense.paid_by || 'N/A' }}
                  </h6>
                </div>

                <div class="col-md-6">
                  <small class="text-muted">Receipt Number</small>
                  <h6 class="fw-semibold">
                    {{ selectedExpense.receipt_no || 'N/A' }}
                  </h6>
                </div>

                <div class="col-md-12">
                  <small class="text-muted">Description</small>
                  <p class="mb-0">
                    {{ selectedExpense.description || 'No description provided.' }}
                  </p>
                </div>

              </div>
            </div>

            <div class="modal-footer">

              <button
                class="btn btn-light"
                data-bs-dismiss="modal"
              >
                Close
              </button>

              <button
                v-if="selectedExpense"
                class="btn btn-primary"
                data-bs-dismiss="modal"
                @click="editExpense(selectedExpense)"
              >
                <i class="bi bi-pencil me-1"></i>
                Edit Expense
              </button>

            </div>

          </div>
        </div>
      </div>

      <!-- ADD / EDIT EXPENSE MODAL -->
      <div
        class="modal fade"
        id="AddExpenseModal"
        tabindex="-1"
        aria-labelledby="AddExpenseModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered">

          <div class="modal-content">

            <div class="modal-header">
              <div>
                <h5 class="modal-title fw-bold" id="AddExpenseModalLabel">
                  {{ editingExpense ? 'Edit Farm Expense' : 'Record Farm Expense' }}
                </h5>

                <small class="text-muted">
                  Capture expenditure details for your farm.
                </small>
              </div>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>

            <form @submit.prevent="submit">

              <div class="modal-body">

                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label">
                      Farm Venture <span class="text-danger">*</span>
                    </label>

                    <select
                      id="venture"
                      class="form-select"
                      v-model="data.venture_id"
                      :class="{ 'is-invalid': errors.venture_id }"
                      required
                    >
                      <option value="">Select Venture</option>

                      <option
                        v-for="venture in farmventures"
                        :key="venture.id"
                        :value="venture.id"
                      >
                        {{ venture.venture_name }}
                      </option>
                    </select>

                    <div class="invalid-feedback">
                      Please select a farm venture.
                    </div>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Expense Category <span class="text-danger">*</span>
                    </label>

                    <select
                      v-model="data.expense_category"
                      class="form-select"
                      required
                    >
                      <option value="">Select category</option>

                      <option
                        v-for="category in categories"
                        :key="category.value"
                        :value="category.value"
                      >
                        {{ category.label }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Amount (KES) <span class="text-danger">*</span>
                    </label>

                    <input
                      type="number"
                      min="0.01"
                      step="0.01"
                      class="form-control"
                      v-model.number="data.amount"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Expense Date <span class="text-danger">*</span>
                    </label>

                    <input
                      type="date"
                      class="form-control"
                      v-model="data.expense_date"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Paid By</label>

                    <input
                      type="text"
                      class="form-control"
                      v-model="data.paid_by"
                      placeholder="e.g. Cash, M-Pesa, Bank"
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Receipt Number</label>

                    <input
                      type="text"
                      class="form-control"
                      v-model="data.receipt_no"
                      placeholder="Optional receipt/reference number"
                    >
                  </div>

                  <div class="col-md-12">
                    <label class="form-label">Description</label>

                    <textarea
                      class="form-control"
                      rows="3"
                      v-model="data.description"
                      placeholder="Describe what the expense was for..."
                    ></textarea>
                  </div>

                </div>

              </div>

              <div class="modal-footer">

                <button
                  type="button"
                  class="btn btn-light"
                  data-bs-dismiss="modal"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  class="btn btn-success"
                  :disabled="submitting"
                >
                  <span
                    v-if="submitting"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>

                  {{ submitting ? 'Saving...' : (editingExpense ? 'Update Expense' : 'Save Expense') }}
                </button>

              </div>

            </form>

          </div>
        </div>
      </div>

    </section>
  </Master>
</template>

<script>
import Master from "@/components/Master.vue";
import axios from "axios";
import Swal from "sweetalert2";

const toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});

export default {
  name: "FarmExpenses",

  components: {
    Master
  },

  data() {
    return {
      farmExpenses: [],
      farmventures: [],

      selectedExpense: null,

      initializing: true,
      submitting: false,
      editingExpense: false,

      currentPage: 1,
      pageSize: 10,

      errors: {},

      categories: [
        { value: "seeds", label: "Seeds" },
        { value: "fertilizer", label: "Fertilizer" },
        { value: "labor", label: "Labor" },
        { value: "transport", label: "Transport" },
        { value: "pesticide", label: "Pesticide" },
        { value: "equipment", label: "Equipment" },
        { value: "utilities", label: "Utilities" },
        { value: "other", label: "Other" }
      ],

      filters: {
        venture_id: "",
        category: "",
        period: "all",
        date_from: "",
        date_to: "",
        paid_by: "",
        search: ""
      },

      data: {
        id: "",
        venture_id: "",
        expense_category: "",
        description: "",
        amount: "",
        expense_date: "",
        paid_by: "",
        receipt_no: ""
      }
    };
  },

  computed: {
    filteredExpenses() {
      return this.farmExpenses
        .filter(expense => {

          const ventureMatch =
            !this.filters.venture_id ||
            String(expense.venture_id) ===
            String(this.filters.venture_id);

          const categoryMatch =
            !this.filters.category ||
            String(expense.expense_category || "").toLowerCase() ===
            this.filters.category.toLowerCase();

          const paidByMatch =
            !this.filters.paid_by ||
            String(expense.paid_by || "")
              .toLowerCase()
              .includes(this.filters.paid_by.toLowerCase());

          const descriptionMatch =
            !this.filters.search ||
            String(expense.description || "")
              .toLowerCase()
              .includes(this.filters.search.toLowerCase()) ||
            String(expense.expense_category || "")
              .toLowerCase()
              .includes(this.filters.search.toLowerCase());

          const expenseDate = String(
            expense.expense_date || ""
          ).slice(0, 10);

          const fromMatch =
            !this.filters.date_from ||
            expenseDate >= this.filters.date_from;

          const toMatch =
            !this.filters.date_to ||
            expenseDate <= this.filters.date_to;

          return (
            ventureMatch &&
            categoryMatch &&
            paidByMatch &&
            descriptionMatch &&
            fromMatch &&
            toMatch
          );
        })
        .sort((a, b) =>
          String(b.expense_date || "").localeCompare(
            String(a.expense_date || "")
          )
        );
    },

    expenseSummary() {
      const totalAmount = this.filteredExpenses.reduce(
        (sum, expense) =>
          sum + (Number(expense.amount) || 0),
        0
      );

      const transactions = this.filteredExpenses.length;

      const largestExpense = this.filteredExpenses.reduce(
        (largest, expense) =>
          Number(expense.amount) > Number(largest?.amount || 0)
            ? expense
            : largest,
        null
      );

      return {
        totalAmount,
        transactions,

        averageAmount: transactions
          ? totalAmount / transactions
          : 0,

        largestAmount: Number(largestExpense?.amount) || 0,

        largestCategory: largestExpense
          ? this.categoryLabel(largestExpense.expense_category)
          : "No expenses"
      };
    },

    expenseTrend() {
      const grouped = {};

      this.filteredExpenses.forEach(expense => {
        const date = String(
          expense.expense_date || ""
        ).slice(0, 10);

        if (!date) return;

        grouped[date] =
          (grouped[date] || 0) +
          (Number(expense.amount) || 0);
      });

      return Object.keys(grouped)
        .sort()
        .map(date => ({
          date,
          amount: grouped[date]
        }));
    },

    totalPages() {
      return Math.max(
        1,
        Math.ceil(this.filteredExpenses.length / this.pageSize)
      );
    },

    paginatedExpenses() {
      const start =
        (this.currentPage - 1) * this.pageSize;

      return this.filteredExpenses.slice(
        start,
        start + this.pageSize
      );
    },

    pageStart() {
      return this.filteredExpenses.length
        ? (this.currentPage - 1) * this.pageSize + 1
        : 0;
    },

    pageEnd() {
      return Math.min(
        this.currentPage * this.pageSize,
        this.filteredExpenses.length
      );
    },

    hasActiveFilters() {
      return Boolean(
        this.filters.venture_id ||
        this.filters.category ||
        this.filters.date_from ||
        this.filters.date_to ||
        this.filters.paid_by ||
        this.filters.search
      );
    }
  },

  mounted() {
    this.loadLists();
  },

  methods: {

    // -------------------------------------------------
    // LOAD DATA
    // -------------------------------------------------

    async loadLists() {
      this.initializing = true;

      try {
        const response = await axios.get("/api/farm-expenses");

        this.farmExpenses = Array.isArray(
          response.data.farmexpenses
        )
          ? response.data.farmexpenses
          : [];

        this.farmventures = Array.isArray(
          response.data.farmventures
        )
          ? response.data.farmventures
          : [];

        this.currentPage = Math.min(
          this.currentPage,
          Math.max(
            1,
            Math.ceil(
              this.filteredExpenses.length / this.pageSize
            )
          )
        );

      } catch (error) {
        console.error("Error fetching farm expenses:", error);

        toast.fire({
          icon: "error",
          title: "Unable to load farm expenses"
        });

      } finally {
        this.initializing = false;
      }
    },

    // -------------------------------------------------
    // FORM MANAGEMENT
    // -------------------------------------------------

    emptyForm() {
      return {
        id: "",
        venture_id: "",
        expense_category: "",
        description: "",
        amount: "",
        expense_date: this.todayDate(),
        paid_by: "",
        receipt_no: ""
      };
    },

    addExpense() {
      this.editingExpense = false;
      this.errors = {};
      this.data = this.emptyForm();

      this.$nextTick(() => {
        this.showModal("AddExpenseModal");
      });
    },

    editExpense(item) {
      this.editingExpense = true;
      this.errors = {};

      this.data = {
        id: item.id,
        venture_id: item.venture_id,
        expense_category: item.expense_category || "",
        description: item.description || "",
        amount: Number(item.amount) || "",
        expense_date: String(
          item.expense_date || ""
        ).slice(0, 10),
        paid_by: item.paid_by || "",
        receipt_no: item.receipt_no || ""
      };

      this.$nextTick(() => {
        this.showModal("AddExpenseModal");
      });
    },

    validateForm() {
      this.errors = {};

      if (!this.data.venture_id) {
        this.errors.venture_id = "Please select a farm venture.";
      }

      if (!this.data.expense_category) {
        this.errors.expense_category = "Please select a category.";
      }

      if (!this.data.expense_date) {
        this.errors.expense_date = "Please select an expense date.";
      }

      if (
        this.data.amount === "" ||
        Number(this.data.amount) <= 0
      ) {
        this.errors.amount = "Enter an amount greater than zero.";
      }

      return Object.keys(this.errors).length === 0;
    },

    // -------------------------------------------------
    // SAVE / UPDATE EXPENSE
    // -------------------------------------------------

    async submit() {
      if (this.submitting) return;

      if (!this.validateForm()) {
        Swal.fire({
          icon: "warning",
          title: "Incomplete form",
          text: "Please check the required fields and try again."
        });

        return;
      }

      this.submitting = true;

      const payload = {
        venture_id: this.data.venture_id,
        expense_category: this.data.expense_category,
        description: this.data.description,
        amount: this.data.amount,
        expense_date: this.data.expense_date,
        paid_by: this.data.paid_by,
        receipt_no: this.data.receipt_no
      };

      try {
        if (this.editingExpense) {

          await axios.put(
            `/api/farm-expenses/${this.data.id}`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Expense updated successfully"
          });

        } else {

          await axios.post(
            "/api/farm-expenses",
            payload
          );

          toast.fire({
            icon: "success",
            title: "Expense recorded successfully"
          });
        }

        this.closeModal("AddExpenseModal");

        await this.loadLists();

        this.data = this.emptyForm();
        this.editingExpense = false;

      } catch (error) {
        console.error("Error saving expense:", error);

        const validationErrors = error.response?.data?.errors;

        const firstError = validationErrors
          ? Object.values(validationErrors).flat()[0]
          : null;

        Swal.fire({
          icon: "error",
          title: "Unable to save expense",
          text:
            firstError ||
            error.response?.data?.message ||
            "An unexpected error occurred."
        });

      } finally {
        this.submitting = false;
      }
    },

    // -------------------------------------------------
    // VIEW EXPENSE
    // -------------------------------------------------

    viewExpense(item) {
      this.selectedExpense = { ...item };

      this.$nextTick(() => {
        this.showModal("viewExpenseModal");
      });
    },

    // -------------------------------------------------
    // DELETE EXPENSE
    // -------------------------------------------------

    async deleteExpense(id) {
      const result = await Swal.fire({
        title: "Delete this expense?",
        text: "This action cannot be undone.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#006400",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it!",
        cancelButtonText: "Cancel"
      });

      if (!result.isConfirmed) return;

      try {
        await axios.delete(`/api/farm-expenses/${id}`);

        toast.fire({
          icon: "success",
          title: "Farm expense deleted"
        });

        await this.loadLists();

      } catch (error) {
        console.error("Error deleting expense:", error);

        Swal.fire({
          icon: "error",
          title: "Delete failed",
          text:
            error.response?.data?.message ||
            "Unable to delete this expense."
        });
      }
    },

    // -------------------------------------------------
    // FILTERING
    // -------------------------------------------------

    applyFilters() {
      this.currentPage = 1;
    },

    applyCustomDate() {
      this.filters.period = "custom";
      this.applyFilters();
    },

    applyPeriod() {
      const today = new Date();

      const formatDate = date => {
        const year = date.getFullYear();
        const month = String(
          date.getMonth() + 1
        ).padStart(2, "0");

        const day = String(
          date.getDate()
        ).padStart(2, "0");

        return `${year}-${month}-${day}`;
      };

      const todayString = formatDate(today);

      if (this.filters.period === "all") {
        this.filters.date_from = "";
        this.filters.date_to = "";
      }

      if (this.filters.period === "today") {
        this.filters.date_from = todayString;
        this.filters.date_to = todayString;
      }

      if (this.filters.period === "week") {
        const start = new Date(today);

        // Monday is the first day of the week.
        const day = (start.getDay() + 6) % 7;

        start.setDate(start.getDate() - day);

        this.filters.date_from = formatDate(start);
        this.filters.date_to = todayString;
      }

      if (this.filters.period === "month") {
        this.filters.date_from = formatDate(
          new Date(
            today.getFullYear(),
            today.getMonth(),
            1
          )
        );

        this.filters.date_to = todayString;
      }

      if (this.filters.period === "year") {
        this.filters.date_from =
          `${today.getFullYear()}-01-01`;

        this.filters.date_to = todayString;
      }

      this.currentPage = 1;
    },

    resetFilters() {
      this.filters = {
        venture_id: "",
        category: "",
        period: "all",
        date_from: "",
        date_to: "",
        paid_by: "",
        search: ""
      };

      this.currentPage = 1;
    },

    // -------------------------------------------------
    // DISPLAY HELPERS
    // -------------------------------------------------

    categoryLabel(category) {
      const item = this.categories.find(
        categoryItem =>
          categoryItem.value === String(category || "").toLowerCase()
      );

      if (item) return item.label;

      return category
        ? String(category).charAt(0).toUpperCase() +
          String(category).slice(1)
        : "Unspecified";
    },

    ventureName(ventureId) {
      const venture = this.farmventures.find(
        item =>
          String(item.id) === String(ventureId)
      );

      return venture?.venture_name || "Unknown venture";
    },

    todayDate() {
      const today = new Date();

      const year = today.getFullYear();

      const month = String(
        today.getMonth() + 1
      ).padStart(2, "0");

      const day = String(
        today.getDate()
      ).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },

    money(value) {
      return (Number(value) || 0).toLocaleString("en-KE", {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
      });
    },

    number(value) {
      return (Number(value) || 0).toLocaleString("en-KE", {
        maximumFractionDigits: 2
      });
    },

    compactMoney(value) {
      const amount = Number(value) || 0;

      if (amount >= 1000000) {
        return `${(amount / 1000000).toFixed(1)}M`;
      }

      if (amount >= 1000) {
        return `${(amount / 1000).toFixed(1)}K`;
      }

      return amount.toFixed(0);
    },

    formatDate(date) {
      if (!date) return "—";

      const parts = String(date).slice(0, 10).split("-");

      return parts.length === 3
        ? `${parts[2]}/${parts[1]}/${parts[0]}`
        : date;
    },

    shortDate(date) {
      if (!date) return "";

      const parts = String(date).slice(0, 10).split("-");

      return parts.length === 3
        ? `${parts[2]}/${parts[1]}`
        : date;
    },

    trendHeight(amount) {
      const maximum = Math.max(
        0,
        ...this.expenseTrend.map(item => item.amount)
      );

      return maximum > 0
        ? Math.max(2, (Number(amount) / maximum) * 100)
        : 0;
    },

    // -------------------------------------------------
    // MODAL HELPERS
    // -------------------------------------------------

    showModal(id) {
      const element = document.getElementById(id);

      if (!element || !window.bootstrap?.Modal) return;

      window.bootstrap.Modal
        .getOrCreateInstance(element)
        .show();
    },

    closeModal(id) {
      const element = document.getElementById(id);

      if (!element || !window.bootstrap?.Modal) return;

      const modal = window.bootstrap.Modal.getInstance(element);

      if (modal) {
        modal.hide();
      }
    },

    // -------------------------------------------------
    // NAVIGATION
    // -------------------------------------------------

    navigateTo(location) {
      this.$router.push(location);
    },

    // -------------------------------------------------
    // CSV EXPORT
    // -------------------------------------------------

    exportCsv() {
      if (!this.filteredExpenses.length) return;

      const columns = [
        ["Expense Date", "expense_date"],
        ["Venture", "venture"],
        ["Category", "expense_category"],
        ["Description", "description"],
        ["Amount", "amount"],
        ["Paid By", "paid_by"],
        ["Receipt Number", "receipt_no"]
      ];

      const escapeCsv = value => {
        let text = String(value ?? "");

        // Reduce spreadsheet formula injection risk.
        if (/^[\s]*[=+\-@]/.test(text)) {
          text = "'" + text;
        }

        return `"${text.replace(/"/g, '""')}"`;
      };

      const rows = [
        columns
          .map(([label]) => escapeCsv(label))
          .join(","),

        ...this.filteredExpenses.map(expense => {
          const row = {
            ...expense,
            venture:
              expense.venture?.venture_name ||
              this.ventureName(expense.venture_id)
          };

          return columns
            .map(([, key]) => escapeCsv(row[key]))
            .join(",");
        })
      ];

      const blob = new Blob(
        ["\uFEFF" + rows.join("\r\n")],
        { type: "text/csv;charset=utf-8;" }
      );

      const url = URL.createObjectURL(blob);

      const link = document.createElement("a");

      link.href = url;
      link.download =
        `farm-expenses-${this.todayDate()}.csv`;

      document.body.appendChild(link);

      link.click();

      document.body.removeChild(link);

      URL.revokeObjectURL(url);
    }

  }
};
</script>

<style scoped>
.farm-expenses-page {
  color: #27352d;
}

.summary-card {
  border: 1px solid #e9eee9;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.025);
}

.card {
  border: 1px solid #e9eee9;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.025);
}

.card-header {
  border-bottom: 1px solid #edf0ed;
  border-radius: 10px 10px 0 0 !important;
}

.summary-icon {
  width: 46px;
  height: 46px;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 12px;
  font-size: 21px;
}

.expense-icon {
  background: #fcebea;
  color: #dc3545;
}

.transaction-icon {
  background: #e8f1ff;
  color: #0d6efd;
}

.average-icon {
  background: #fff3df;
  color: #d58b13;
}

.largest-icon {
  background: #f0eaff;
  color: #7952b3;
}

.form-label {
  font-weight: 600;
  font-size: 13px;
  color: #47574b;
}

.form-control,
.form-select {
  border-color: #dfe6df;
  font-size: 14px;
}

.form-control:focus,
.form-select:focus {
  border-color: #198754;
  box-shadow: 0 0 0 0.15rem rgba(25, 135, 84, 0.12);
}

.btn-success {
  background-color: #176b43;
  border-color: #176b43;
}

.btn-success:hover {
  background-color: #105536;
  border-color: #105536;
}

.table {
  font-size: 13px;
}

.table thead th {
  font-size: 12px;
  font-weight: 700;
  color: #536257;
  white-space: nowrap;
  padding: 13px 12px;
}

.table tbody td {
  padding: 12px;
}

.category-label {
  display: inline-block;
  padding: 4px 8px;
  background: #edf5ef;
  color: #176b43;
  border-radius: 5px;
  font-size: 12px;
  text-transform: capitalize;
}

.expense-detail-header {
  padding: 18px;
  background: #fff4f3;
  border-radius: 8px;
  border: 1px solid #f8e2df;
}

/* Expenditure trend */

.trend-container {
  min-height: 190px;
  display: flex;
  align-items: flex-end;
  gap: 12px;
  overflow-x: auto;
  padding: 12px 4px 0;
}

.trend-column {
  min-width: 65px;
  flex: 1;
  max-width: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  height: 175px;
}

.trend-value {
  font-size: 10px;
  color: #68756c;
  margin-bottom: 6px;
  white-space: nowrap;
}

.trend-bar-wrapper {
  width: 28px;
  flex: 1;
  display: flex;
  align-items: flex-end;
  justify-content: center;
  background: #f1f5f1;
  border-radius: 5px 5px 0 0;
  overflow: hidden;
}

.trend-bar {
  width: 100%;
  min-height: 2px;
  background: #dc6b62;
  border-radius: 5px 5px 0 0;
  transition: height 0.3s ease;
}

.trend-date {
  margin-top: 7px;
  color: #78837b;
  font-size: 10px;
  white-space: nowrap;
}

.modal-content {
  border: none;
  border-radius: 12px;
  overflow: hidden;
}

.modal-header,
.modal-footer {
  border-color: #edf0ed;
}

@media (max-width: 576px) {
  .farm-expenses-page {
    padding-left: 8px;
    padding-right: 8px;
  }

  .trend-container {
    gap: 8px;
  }

  .summary-card h4 {
    font-size: 18px;
  }
}
</style>
