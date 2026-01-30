<template>
    <Master>

    <section class="section dashboard">
    <div class="row">

        <!-- Summary Cards -->
        <div class="col-md-3" v-for="card in [
        { title: 'Total Sales', value: summary.total_sales },
        { title: 'Total Expenses', value: summary.total_expenses },
        { title: 'Gross Profit', value: summary.gross_profit },
        { title: 'Net Profit', value: summary.net_profit }
        ]" :key="card.title">
        <div class="card">
            <div class="card-body">
            <h5>{{ card.title }}</h5>
            <h3>{{ card.value }}</h3>
            </div>
        </div>
        </div>

        <!-- Filter -->
        <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                <label>Start Date</label>
                <input type="date" v-model="filters.start_date" class="form-control">
                </div>
                <div class="col-md-4">
                <label>End Date</label>
                <input type="date" v-model="filters.end_date" class="form-control">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                <button class="btn btn-success me-2" @click="applyFilter">
                    Apply
                </button>
                <button class="btn btn-secondary" @click="resetFilter">
                    Reset
                </button>
                </div>
            </div>
            </div>
        </div>
        </div>

        <!-- Chart -->
        <div class="col-12 mt-3">
        <div class="card">
            <div class="card-body">
            <h5>Profit Trend</h5>
            <apexchart
                type="line"
                height="350"
                :options="chartOptions"
                :series="chartSeries"
            />
            </div>
        </div>
        </div>

        <!-- Optional Table -->
<div class="col-12 mt-3" v-if="showTable">
  <div class="card">
    <div class="card-body">
      <h5>Details</h5>
      <table class="table table-bordered">
        <thead>
          <tr>
            <th>#</th>
            <th>Type</th>
            <th>Reference</th>
            <th>Amount</th>
            <th>Date</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(d, index) in paginatedDetails" :key="d.id">
            <td>{{ (currentPage - 1) * perPage + index + 1 }}</td>
            <td>{{ d.type }}</td>
            <td>{{ d.reference }}</td>
            <td>{{ Number(d.amount).toLocaleString() }}</td>
            <td>{{ formatDate(d.payment_date) }}</td>
          </tr>
          <tr v-if="details.length === 0">
            <td colspan="5" class="text-center">No details found.</td>
          </tr>
        </tbody>
      </table>

      <!-- Pagination Controls -->
      <nav v-if="totalPages > 1" class="mt-2">
        <ul class="pagination justify-content-center mb-0">
          <li class="page-item" :class="{ disabled: currentPage === 1 }">
            <button class="page-link" @click="currentPage--">Previous</button>
          </li>

          <li
            v-for="page in totalPages"
            :key="page"
            class="page-item"
            :class="{ active: currentPage === page }"
          >
            <button class="page-link" @click="currentPage = page">{{ page }}</button>
          </li>

          <li class="page-item" :class="{ disabled: currentPage === totalPages }">
            <button class="page-link" @click="currentPage++">Next</button>
          </li>
        </ul>
      </nav>
    </div>
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
    import VueApexCharts from "vue3-apexcharts";

    const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    });

    window.toast = toast;

    export default {
    components: {
        Master,
        VueApexCharts,
    },

    data() {
        return {
        initializing: true,
        submitting: false,
        currentPage: 1,
        perPage: 10, // rows per page
        // Summary cards
        summary: {
            total_sales: 0,
            total_expenses: 0,
            gross_profit: 0,
            net_profit: 0,
        },

        // Chart data
        monthlyProfit: [],

        // Table details (optional)
        details: [],

        // Date filter
        filters: {
            start_date: "",
            end_date: "",
        },

        // Chart options
        chartOptions: {
            chart: {
            type: "line",
            height: 350,
            },
            xaxis: {
            categories: [],
            },
            yaxis: {
            title: {
                text: "Profit (KES)",
            },
            },
            stroke: {
            curve: "smooth",
            },
            title: {
            text: "Profit Trend",
            align: "left",
            },
        },

        chartSeries: [],
        showTable: true,
        };
    },
    computed: {
        paginatedDetails() {
            const start = (this.currentPage - 1) * this.perPage;
            const end = start + this.perPage;
            return this.details.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.details.length / this.perPage);
        },
    },
    watch: {
        details() {
            this.currentPage = 1;
        }
    },

    methods: {
        // Format date as dd/mm/yyyy
        formatDate(date) {
          if (!date) return "N/A";
          const d = new Date(date);
          const day = String(d.getDate()).padStart(2, '0');
          const month = String(d.getMonth() + 1).padStart(2, '0'); // Months are 0-based
          const year = d.getFullYear();
          return `${day}/${month}/${year}`;
        },
        async loadReport() {
        this.initializing = true;

        try {
            const response = await axios.get("/api/reports/profit", {
            params: this.filters,
            });

            this.summary = response.data.summary;
            this.monthlyProfit = response.data.monthly;
            this.details = response.data.details;

            this.updateChart();
        } catch (error) {
            console.error(error);
            toast.fire("Error!", "Failed to load report", "error");
        } finally {
            this.initializing = false;
        }
        },

        updateChart() {
        const categories = this.monthlyProfit.map((m) => m.month);
        const profitData = this.monthlyProfit.map((m) => m.profit);

        this.chartOptions.xaxis.categories = categories;
        this.chartSeries = [
            {
            name: "Profit",
            data: profitData,
            },
        ];
        },

        applyFilter() {
        this.loadReport();
        },

        resetFilter() {
        this.filters.start_date = "";
        this.filters.end_date = "";
        this.loadReport();
        },
    },

    mounted() {
        this.loadReport();
    },
    };
    </script>



    
    
    