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

    <!-- Floating AI Chat Button -->
    <button
      class="ai-chat-btn"
      @click="chatOpen = !chatOpen"
    >
      🤖
    </button>

    <!-- Side AI Chat Panel -->
    <div class="ai-chat-panel" :class="{ open: chatOpen }">
    <div class="ai-chat-header">
        <strong>AI Insights</strong>
        <button class="btn-close" @click="chatOpen = false"></button>
    </div>

    <div class="ai-chat-body">
        <div
        v-for="(msg, i) in aiMessages"
        :key="i"
        class="mb-2"
        :class="msg.role === 'user' ? 'text-end' : 'text-start'"
        >
        <span
            class="d-inline-block p-2 rounded"
            :class="msg.role === 'user'
            ? 'bg-primary text-white'
            : 'bg-light text-dark'"
            style="max-width: 80%;"
        >
            {{ msg.text }}
        </span>
        </div>

        <div v-if="aiLoading" class="text-muted small">
        AI is thinking…
        </div>
    </div>

    <form class="ai-chat-input" @submit.prevent="sendAIMessage">
        <input
        v-model="aiInput"
        class="form-control"
        placeholder="Ask about profits, risks…"
        />
        <button class="btn btn-dark" :disabled="aiLoading">
        Send
        </button>
    </form>
    </div>    

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
        //ai
        chatOpen: false,
        aiInput: '',
        aiLoading: false,
        aiMessages: [
        {
            role: 'ai',
            text: 'I can explain your numbers, trends, and risks. Ask me anything.'
        }
        ],        
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
        async sendAIMessage() {
        if (!this.aiInput.trim()) return

        const message = this.aiInput

        this.aiMessages.push({
            role: 'user',
            text: message
        })

        this.aiInput = ''
        this.aiLoading = true

        try {
            const res = await axios.post('/api/ai/chat', {
            message,
            context: {
                start_date: this.filters.start_date,
                end_date: this.filters.end_date,
                summary: this.summary
            }
            })

            this.aiMessages.push({
            role: 'ai',
            text: res.data.reply
            })
        } catch (e) {
            this.aiMessages.push({
            role: 'ai',
            text: 'Something went wrong. Try again.'
            })
        } finally {
            this.aiLoading = false
        }
        },        
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

<style>
.ai-chat-btn {
  position: fixed;
  bottom: 24px;
  right: 24px;
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: #0d6efd;
  color: #fff;
  border: none;
  font-size: 22px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.2);
  z-index: 1050;
}

.ai-chat-panel {
  position: fixed;
  top: 0;
  right: -380px;
  width: 360px;
  height: 100vh;
  background: #fff;
  box-shadow: -4px 0 20px rgba(0,0,0,0.15);
  display: flex;
  flex-direction: column;
  transition: right 0.3s ease;
  z-index: 1049;
}

.ai-chat-panel.open {
  right: 0;
}

.ai-chat-header {
  padding: 12px 16px;
  border-bottom: 1px solid #eee;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.ai-chat-body {
  flex: 1;
  padding: 16px;
  overflow-y: auto;
}

.ai-chat-input {
  display: flex;
  gap: 8px;
  padding: 12px;
  border-top: 1px solid #eee;
}
</style>

    
    
    