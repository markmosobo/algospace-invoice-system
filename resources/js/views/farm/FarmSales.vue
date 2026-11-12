
<template>
    <Master>
    <div class="container-fluid py-3 farm-sales-page">

      <!-- PAGE HEADER -->
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h3 class="fw-bold mb-1">Farm Sales</h3>
          <p class="text-muted mb-0">
            Track farm produce sales, buyers and revenue.
          </p>
        </div>

        <div class="d-flex flex-wrap gap-2">
          <button
            class="btn btn-outline-success"
            @click="exportCsv"
            :disabled="filteredSales.length === 0"
          >
            <i class="bi bi-download me-1"></i>
            Export CSV
          </button>

          <button
            class="btn btn-success"
            data-bs-toggle="modal"
            data-bs-target="#saleModal"
            @click="prepareNewSale"
          >
            <i class="bi bi-plus-circle me-1"></i>
            Record Sale
          </button>
        </div>
      </div>

      <!-- SUMMARY CARDS -->
      <div class="row g-3 mb-4">

        <div class="col-md-6 col-xl-3">
          <div class="card summary-card h-100">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <div>
                  <small class="text-muted">Total Revenue</small>
                  <h4 class="fw-bold text-success mt-2 mb-0">
                    KSh {{ money(salesSummary.totalRevenue) }}
                  </h4>
                </div>
                <div class="summary-icon revenue-icon">
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
                    {{ salesSummary.transactions }}
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
                  <small class="text-muted">Average Sale</small>
                  <h4 class="fw-bold mt-2 mb-0">
                    KSh {{ money(salesSummary.averageSale) }}
                  </h4>
                </div>
                <div class="summary-icon average-icon">
                  <i class="bi bi-graph-up-arrow"></i>
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
                  <small class="text-muted">Quantity Sold</small>
                  <h5 class="fw-bold mt-2 mb-0">
                    {{ salesSummary.quantityByUnit }}
                  </h5>
                  <small class="text-muted">Grouped by measurement unit</small>
                </div>
                <div class="summary-icon quantity-icon">
                  <i class="bi bi-box-seam"></i>
                </div>
              </div>
            </div>
          </div>
        </div>

      </div>

      <!-- REVENUE TREND -->
      <div class="card mb-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
              <h6 class="fw-bold mb-0">Sales Revenue Trend</h6>
              <small class="text-muted">
                Daily revenue based on the selected filters
              </small>
            </div>
            <span class="badge bg-light text-dark">
              {{ salesTrend.length }} days
            </span>
          </div>
        </div>

        <div class="card-body">
          <div v-if="salesTrend.length" class="trend-container">
            <div
              v-for="item in salesTrend"
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
            No sales data available for the selected filters.
          </div>
        </div>
      </div>

      <!-- FILTERS -->
      <div class="card mb-4">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">
              <i class="bi bi-funnel me-2"></i>
              Filter Sales
            </h6>

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
              <label class="form-label">From Date</label>
              <input
                type="date"
                v-model="filters.date_from"
                class="form-control"
                @change="applyCustomDate"
              >
            </div>

            <div class="col-md-4 col-lg-3">
              <label class="form-label">To Date</label>
              <input
                type="date"
                v-model="filters.date_to"
                class="form-control"
                @change="applyCustomDate"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Search Product</label>
              <input
                type="text"
                v-model="filters.product"
                class="form-control"
                placeholder="e.g. Coffee, Beans, Maize..."
                @input="applyFilters"
              >
            </div>

            <div class="col-md-6">
              <label class="form-label">Search Buyer</label>
              <input
                type="text"
                v-model="filters.buyer"
                class="form-control"
                placeholder="Enter buyer name..."
                @input="applyFilters"
              >
            </div>

          </div>

          <div class="mt-3 d-flex flex-wrap justify-content-between gap-2">
            <small class="text-muted">
              Showing {{ filteredSales.length }} matching sales.
            </small>

            <small class="text-muted" v-if="hasActiveFilters">
              Filters are active.
            </small>
          </div>
        </div>
      </div>

      <!-- SALES TABLE -->
      <div class="card">
        <div class="card-header bg-white py-3">
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h6 class="fw-bold mb-0">Sales Records</h6>

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
            <p class="text-muted mt-2 mb-0">Loading farm sales...</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle mb-0">
              <thead class="table-light">
                <tr>
                  <th>#</th>
                  <th>Date</th>
                  <th>Venture</th>
                  <th>Product</th>
                  <th>Quantity</th>
                  <th>Buyer</th>
                  <th>Unit Price</th>
                  <th class="text-end">Total</th>
                  <th class="text-center">Actions</th>
                </tr>
              </thead>

              <tbody>
                <tr
                  v-for="(sale, index) in paginatedSales"
                  :key="sale.id"
                >
                  <td>{{ pageStart + index }}</td>

                  <td>
                    {{ formatDate(sale.sale_date) }}
                  </td>

                  <td>
                    <span class="venture-label">
                      {{ sale.venture?.venture_name || ventureName(sale.venture_id) }}
                    </span>
                  </td>

                  <td class="fw-semibold">
                    {{ sale.product_name }}
                  </td>

                  <td>
                    {{ number(sale.quantity) }} {{ sale.unit }}
                  </td>

                  <td>
                    {{ sale.buyer || 'Walk-in Buyer' }}
                  </td>

                  <td>
                    KSh {{ money(sale.price_per_unit) }}
                  </td>

                  <td class="text-end fw-bold text-success">
                    KSh {{ money(sale.total_amount) }}
                  </td>

                  <td class="text-center">
                    <div class="btn-group btn-group-sm">

                      <button
                        class="btn btn-outline-info"
                        title="View sale"
                        @click="viewSale(sale)"
                      >
                        <i class="bi bi-eye"></i>
                      </button>

                      <button
                        class="btn btn-outline-primary"
                        title="Edit sale"
                        @click="editSale(sale)"
                      >
                        <i class="bi bi-pencil"></i>
                      </button>

                      <button
                        class="btn btn-outline-danger"
                        title="Delete sale"
                        @click="deleteSale(sale)"
                      >
                        <i class="bi bi-trash"></i>
                      </button>

                    </div>
                  </td>
                </tr>

                <tr v-if="paginatedSales.length === 0">
                  <td colspan="9" class="text-center py-5">
                    <i class="bi bi-inbox display-5 text-muted"></i>
                    <p class="text-muted mt-2 mb-0">
                      No farm sales found.
                    </p>
                    <small class="text-muted">
                      Try changing your filters or record a new sale.
                    </small>
                  </td>
                </tr>
              </tbody>

              <tfoot v-if="filteredSales.length">
                <tr class="table-light">
                  <th colspan="7" class="text-end">
                    Filtered Revenue:
                  </th>
                  <th class="text-end text-success">
                    KSh {{ money(salesSummary.totalRevenue) }}
                  </th>
                  <th></th>
                </tr>
              </tfoot>

            </table>
          </div>
        </div>

        <!-- PAGINATION -->
        <div
          v-if="!initializing && filteredSales.length"
          class="card-footer bg-white"
        >
          <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">

            <small class="text-muted">
              Showing {{ pageStart }}–{{ pageEnd }}
              of {{ filteredSales.length }} records
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

      <!-- ADD / EDIT SALE MODAL -->
      <div
        class="modal fade"
        id="saleModal"
        tabindex="-1"
        aria-labelledby="saleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-centered">
          <div class="modal-content">

            <div class="modal-header">
              <div>
                <h5 class="modal-title fw-bold" id="saleModalLabel">
                  {{ editingSale ? 'Edit Farm Sale' : 'Record Farm Sale' }}
                </h5>
                <small class="text-muted">
                  Enter the details of the produce sold.
                </small>
              </div>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>

            <form @submit.prevent="submitSale">
              <div class="modal-body">

                <div class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label">
                      Farm Venture <span class="text-danger">*</span>
                    </label>

                    <select
                      v-model="form.venture_id"
                      class="form-select"
                      required
                    >
                      <option value="">Select venture</option>
                      <option
                        v-for="venture in farmventures"
                        :key="venture.id"
                        :value="venture.id"
                      >
                        {{ venture.venture_name }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Sale Date <span class="text-danger">*</span>
                    </label>

                    <input
                      type="date"
                      v-model="form.sale_date"
                      class="form-control"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">
                      Product Name <span class="text-danger">*</span>
                    </label>

                    <input
                      type="text"
                      v-model="form.product_name"
                      class="form-control"
                      placeholder="e.g. Coffee, Beans, Maize"
                      required
                    >
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Buyer</label>

                    <input
                      type="text"
                      v-model="form.buyer"
                      class="form-control"
                      placeholder="Buyer name or organisation"
                    >
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">
                      Quantity <span class="text-danger">*</span>
                    </label>

                    <input
                      type="number"
                      v-model.number="form.quantity"
                      class="form-control"
                      min="0.01"
                      step="any"
                      required
                    >
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">
                      Unit <span class="text-danger">*</span>
                    </label>

                    <select
                      v-model="form.unit"
                      class="form-select"
                      required
                    >
                      <option value="">Select unit</option>
                      <option value="kg">Kilograms (kg)</option>
                      <option value="g">Grams (g)</option>
                      <option value="bag">Bags</option>
                      <option value="crate">Crates</option>
                      <option value="piece">Pieces</option>
                      <option value="litre">Litres</option>
                      <option value="tonne">Tonnes</option>
                      <option value="bunch">Bunches</option>
                      <option value="other">Other</option>
                    </select>
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">
                      Price Per Unit (KSh) <span class="text-danger">*</span>
                    </label>

                    <input
                      type="number"
                      v-model.number="form.price_per_unit"
                      class="form-control"
                      min="0"
                      step="0.01"
                      required
                    >
                  </div>

                  <div class="col-12">
                    <div class="calculation-box">
                      <div class="d-flex justify-content-between align-items-center">
                        <div>
                          <small class="text-muted">Calculated Sale Total</small>
                          <h4 class="fw-bold text-success mb-0">
                            KSh {{ money(form.total_amount) }}
                          </h4>
                        </div>

                        <i class="bi bi-calculator display-6 text-success"></i>
                      </div>

                      <small class="text-muted">
                        Quantity × Price per unit
                      </small>
                    </div>
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
                  :disabled="saving"
                >
                  <span
                    v-if="saving"
                    class="spinner-border spinner-border-sm me-2"
                  ></span>

                  {{ saving ? 'Saving...' : (editingSale ? 'Update Sale' : 'Save Sale') }}
                </button>
              </div>
            </form>

          </div>
        </div>
      </div>

      <!-- VIEW SALE MODAL -->
      <div
        class="modal fade"
        id="viewSaleModal"
        tabindex="-1"
        aria-labelledby="viewSaleModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-md modal-dialog-centered">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title fw-bold" id="viewSaleModalLabel">
                Sale Details
              </h5>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>

            <div class="modal-body" v-if="selectedSale">

              <div class="sale-detail-header mb-3">
                <small class="text-muted">Sale Total</small>
                <h3 class="fw-bold text-success mb-0">
                  KSh {{ money(selectedSale.total_amount) }}
                </h3>
              </div>

              <div class="detail-row">
                <span>Sale Date</span>
                <strong>{{ formatDate(selectedSale.sale_date) }}</strong>
              </div>

              <div class="detail-row">
                <span>Farm Venture</span>
                <strong>
                  {{ selectedSale.venture?.venture_name || ventureName(selectedSale.venture_id) }}
                </strong>
              </div>

              <div class="detail-row">
                <span>Product</span>
                <strong>{{ selectedSale.product_name }}</strong>
              </div>

              <div class="detail-row">
                <span>Quantity</span>
                <strong>
                  {{ number(selectedSale.quantity) }} {{ selectedSale.unit }}
                </strong>
              </div>

              <div class="detail-row">
                <span>Price Per Unit</span>
                <strong>KSh {{ money(selectedSale.price_per_unit) }}</strong>
              </div>

              <div class="detail-row">
                <span>Buyer</span>
                <strong>{{ selectedSale.buyer || 'Walk-in Buyer' }}</strong>
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
                v-if="selectedSale"
                class="btn btn-primary"
                data-bs-dismiss="modal"
                @click="editSale(selectedSale)"
              >
                <i class="bi bi-pencil me-1"></i>
                Edit Sale
              </button>
            </div>

          </div>
        </div>
      </div>

    </div>
  </Master>  
</template>

<script>
import axios from "axios";
import Swal from "sweetalert2";
import Master from "@/components/Master.vue";

const toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
  timerProgressBar: true
});

export default {
  name: "FarmSales",
  components: {
    Master,
  },
  
  data() {
    return {
      farmsales: [],
      farmventures: [],

      initializing: false,
      saving: false,
      editingSale: false,

      selectedSale: null,

      currentPage: 1,
      pageSize: 10,

      filters: {
        venture_id: "",
        period: "all",
        date_from: "",
        date_to: "",
        product: "",
        buyer: ""
      },

      form: {
        id: null,
        venture_id: "",
        product_name: "",
        quantity: "",
        unit: "",
        price_per_unit: "",
        buyer: "",
        sale_date: "",
        total_amount: 0
      }
    };
  },

  computed: {
    filteredSales() {
      return this.farmsales
        .filter(item => {
          const ventureMatch =
            !this.filters.venture_id ||
            String(item.venture_id) === String(this.filters.venture_id);

          const productMatch =
            !this.filters.product ||
            String(item.product_name || "")
              .toLowerCase()
              .includes(this.filters.product.toLowerCase());

          const buyerMatch =
            !this.filters.buyer ||
            String(item.buyer || "")
              .toLowerCase()
              .includes(this.filters.buyer.toLowerCase());

          const saleDate = String(item.sale_date || "").slice(0, 10);

          const fromMatch =
            !this.filters.date_from ||
            saleDate >= this.filters.date_from;

          const toMatch =
            !this.filters.date_to ||
            saleDate <= this.filters.date_to;

          return (
            ventureMatch &&
            productMatch &&
            buyerMatch &&
            fromMatch &&
            toMatch
          );
        })
        .sort((a, b) =>
          String(b.sale_date || "").localeCompare(
            String(a.sale_date || "")
          )
        );
    },

    salesSummary() {
      const totalRevenue = this.filteredSales.reduce(
        (sum, item) =>
          sum + (Number(item.total_amount) || 0),
        0
      );

      const transactions = this.filteredSales.length;

      const quantityByUnit = {};

      this.filteredSales.forEach(item => {
        const unit = String(item.unit || "Unspecified").trim();

        quantityByUnit[unit] =
          (quantityByUnit[unit] || 0) +
          (Number(item.quantity) || 0);
      });

      return {
        totalRevenue,
        transactions,
        averageSale: transactions
          ? totalRevenue / transactions
          : 0,

        quantityByUnit:
          Object.entries(quantityByUnit)
            .map(([unit, quantity]) =>
              `${this.number(quantity)} ${unit}`
            )
            .join(" · ") || "0"
      };
    },

    salesTrend() {
      const grouped = {};

      this.filteredSales.forEach(item => {
        const date = String(item.sale_date || "").slice(0, 10);

        if (!date) return;

        grouped[date] =
          (grouped[date] || 0) +
          (Number(item.total_amount) || 0);
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
        Math.ceil(this.filteredSales.length / this.pageSize)
      );
    },

    paginatedSales() {
      const start = (this.currentPage - 1) * this.pageSize;

      return this.filteredSales.slice(
        start,
        start + this.pageSize
      );
    },

    pageStart() {
      return this.filteredSales.length
        ? (this.currentPage - 1) * this.pageSize + 1
        : 0;
    },

    pageEnd() {
      return Math.min(
        this.currentPage * this.pageSize,
        this.filteredSales.length
      );
    },

    hasActiveFilters() {
      return Boolean(
        this.filters.venture_id ||
        this.filters.date_from ||
        this.filters.date_to ||
        this.filters.product ||
        this.filters.buyer
      );
    }
  },

  watch: {
    "form.quantity"() {
      this.calculateSaleTotal();
    },

    "form.price_per_unit"() {
      this.calculateSaleTotal();
    }
  },

  mounted() {
    this.loadLists();
  },

  methods: {
    async loadLists() {
      this.initializing = true;

      try {
        const response = await axios.get("/api/farm-sales");

        this.farmsales = Array.isArray(response.data.farmsales)
          ? response.data.farmsales
          : [];

        this.farmventures = Array.isArray(response.data.farmventures)
          ? response.data.farmventures
          : [];

        this.currentPage = Math.min(
          this.currentPage,
          Math.max(
            1,
            Math.ceil(this.filteredSales.length / this.pageSize)
          )
        );

      } catch (error) {
        console.error("Error fetching farm sales:", error);

        toast.fire({
          icon: "error",
          title: "Could not load farm sales",
          text:
            error.response?.data?.message ||
            "Please check your API connection."
        });

      } finally {
        this.initializing = false;
      }
    },

    emptyForm() {
      return {
        id: null,
        venture_id: "",
        product_name: "",
        quantity: "",
        unit: "",
        price_per_unit: "",
        buyer: "",
        sale_date: this.todayDate(),
        total_amount: 0
      };
    },

    prepareNewSale() {
      this.editingSale = false;
      this.form = this.emptyForm();
    },

    calculateSaleTotal() {
      const quantity = Number(this.form.quantity) || 0;
      const price = Number(this.form.price_per_unit) || 0;

      this.form.total_amount = Number(
        (quantity * price).toFixed(2)
      );
    },

    async submitSale() {
      if (this.saving) return;

      this.calculateSaleTotal();

      if (!this.form.venture_id) {
        toast.fire({
          icon: "warning",
          title: "Please select a farm venture"
        });
        return;
      }

      if (
        !this.form.product_name ||
        !this.form.sale_date ||
        !this.form.unit ||
        Number(this.form.quantity) <= 0 ||
        Number(this.form.price_per_unit) < 0
      ) {
        toast.fire({
          icon: "warning",
          title: "Please complete all required fields"
        });
        return;
      }

      this.saving = true;

      const payload = {
        venture_id: this.form.venture_id,
        product_name: this.form.product_name,
        quantity: this.form.quantity,
        unit: this.form.unit,
        price_per_unit: this.form.price_per_unit,
        buyer: this.form.buyer,
        sale_date: this.form.sale_date,
        total_amount: this.form.total_amount
      };

      try {
        if (this.editingSale) {
          await axios.put(
            `/api/farm-sales/${this.form.id}`,
            payload
          );

          toast.fire({
            icon: "success",
            title: "Farm sale updated successfully"
          });

        } else {
          await axios.post("/api/farm-sales", payload);

          toast.fire({
            icon: "success",
            title: "Farm sale recorded successfully"
          });
        }

        this.closeModal("saleModal");
        await this.loadLists();

      } catch (error) {
        console.error("Error saving farm sale:", error);

        const errors = error.response?.data?.errors;
        const firstError = errors
          ? Object.values(errors).flat()[0]
          : null;

        Swal.fire({
          icon: "error",
          title: "Unable to save sale",
          text:
            firstError ||
            error.response?.data?.message ||
            "An unexpected error occurred."
        });

      } finally {
        this.saving = false;
      }
    },

    editSale(sale) {
      this.editingSale = true;

      this.form = {
        id: sale.id,
        venture_id: sale.venture_id,
        product_name: sale.product_name || "",
        quantity: Number(sale.quantity) || "",
        unit: sale.unit || "",
        price_per_unit: Number(sale.price_per_unit) || "",
        buyer: sale.buyer || "",
        sale_date: String(sale.sale_date || "").slice(0, 10),
        total_amount: Number(sale.total_amount) || 0
      };

      this.calculateSaleTotal();

      this.$nextTick(() => {
        this.showModal("saleModal");
      });
    },

    viewSale(sale) {
      this.selectedSale = { ...sale };
      this.showModal("viewSaleModal");
    },

    async deleteSale(sale) {
      const result = await Swal.fire({
        title: "Delete this sale?",
        text: `This will permanently delete the sale for ${sale.product_name}.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#dc3545",
        cancelButtonColor: "#6c757d",
        confirmButtonText: "Yes, delete it",
        cancelButtonText: "Cancel"
      });

      if (!result.isConfirmed) return;

      try {
        await axios.delete(`/api/farm-sales/${sale.id}`);

        toast.fire({
          icon: "success",
          title: "Farm sale deleted"
        });

        await this.loadLists();

      } catch (error) {
        console.error("Error deleting farm sale:", error);

        Swal.fire({
          icon: "error",
          title: "Delete failed",
          text:
            error.response?.data?.message ||
            "Unable to delete this sale."
        });
      }
    },

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
        const month = String(date.getMonth() + 1).padStart(2, "0");
        const day = String(date.getDate()).padStart(2, "0");

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
        const day = (start.getDay() + 6) % 7;

        start.setDate(start.getDate() - day);

        this.filters.date_from = formatDate(start);
        this.filters.date_to = todayString;
      }

      if (this.filters.period === "month") {
        this.filters.date_from = formatDate(
          new Date(today.getFullYear(), today.getMonth(), 1)
        );

        this.filters.date_to = todayString;
      }

      if (this.filters.period === "year") {
        this.filters.date_from = `${today.getFullYear()}-01-01`;
        this.filters.date_to = todayString;
      }

      this.currentPage = 1;
    },

    resetFilters() {
      this.filters = {
        venture_id: "",
        period: "all",
        date_from: "",
        date_to: "",
        product: "",
        buyer: ""
      };

      this.currentPage = 1;
    },

    ventureName(ventureId) {
      const venture = this.farmventures.find(
        item => String(item.id) === String(ventureId)
      );

      return venture?.venture_name || "Unknown venture";
    },

    todayDate() {
      const today = new Date();
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");

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

      const value = String(date).slice(0, 10);
      const parts = value.split("-");

      if (parts.length !== 3) return value;

      return `${parts[2]}/${parts[1]}/${parts[0]}`;
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
        ...this.salesTrend.map(item => item.amount)
      );

      return maximum > 0
        ? Math.max(2, (Number(amount) / maximum) * 100)
        : 0;
    },

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

    exportCsv() {
      if (!this.filteredSales.length) return;

      const columns = [
        ["Sale Date", "sale_date"],
        ["Venture", "venture"],
        ["Product", "product_name"],
        ["Quantity", "quantity"],
        ["Unit", "unit"],
        ["Buyer", "buyer"],
        ["Price Per Unit", "price_per_unit"],
        ["Total Amount", "total_amount"]
      ];

      const escapeCsv = value => {
        let text = String(value ?? "");

        // Reduce CSV formula injection risk when opened in spreadsheet apps.
        if (/^[\s]*[=+\-@]/.test(text)) {
          text = "'" + text;
        }

        return `"${text.replace(/"/g, '""')}"`;
      };

      const rows = [
        columns
          .map(([label]) => escapeCsv(label))
          .join(","),

        ...this.filteredSales.map(item => {
          const row = {
            ...item,
            venture:
              item.venture?.venture_name ||
              this.ventureName(item.venture_id)
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
        `farm-sales-${this.todayDate()}.csv`;

      document.body.appendChild(link);
      link.click();
      document.body.removeChild(link);

      URL.revokeObjectURL(url);
    }
  }
};
</script>

<style scoped>
.farm-sales-page {
  color: #27352d;
}

.summary-card {
  border: 1px solid #e9eee9;
  border-radius: 10px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.025);
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

.revenue-icon {
  background: #e7f5eb;
  color: #198754;
}

.transaction-icon {
  background: #e8f1ff;
  color: #0d6efd;
}

.average-icon {
  background: #fff3df;
  color: #d58b13;
}

.quantity-icon {
  background: #f0eaff;
  color: #7952b3;
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

.venture-label {
  display: inline-block;
  padding: 4px 8px;
  background: #edf5ef;
  color: #176b43;
  border-radius: 5px;
  font-size: 12px;
}

.calculation-box {
  padding: 16px;
  border-radius: 8px;
  background: #f0f8f2;
  border: 1px solid #d9ebdd;
}

.sale-detail-header {
  padding: 18px;
  background: #f0f8f2;
  border-radius: 8px;
}

.detail-row {
  display: flex;
  justify-content: space-between;
  gap: 15px;
  padding: 12px 0;
  border-bottom: 1px solid #edf0ed;
  font-size: 14px;
}

.detail-row:last-child {
  border-bottom: none;
}

.detail-row span {
  color: #758078;
}

.detail-row strong {
  text-align: right;
  color: #344239;
}

/* Revenue trend */
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
  background: #198754;
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
  .farm-sales-page {
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
