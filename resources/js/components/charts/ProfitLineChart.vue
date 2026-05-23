<script>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
)

export default {
  name: 'ProfitLineChart',
  components: { Line },

  props: {
    monthlyProfit: {
      type: Array,
      default: () => []
    }
  },

  methods: {
    // ✅ Converts "2026-01" → "Jan 26"
    formatMonth(monthStr) {
      if (!monthStr) return ''

      const [year, month] = monthStr.split('-')
      const date = new Date(year, month - 1)

      return date.toLocaleString('en-US', {
        month: 'short',
        year: '2-digit'
      })
    }
  },

  computed: {
    chartData() {
      return {
        // ✅ FORMATTED LABELS HERE
        labels: this.monthlyProfit.map(m => this.formatMonth(m.month)),

        datasets: [
          // 🟦 SALES
          {
            label: 'Sales',
            data: this.monthlyProfit.map(m => Number(m.sales)),
            borderColor: '#0d6efd',
            backgroundColor: 'rgba(13,110,253,0.1)',
            tension: 0.4,
            pointRadius: 3,
            fill: false
          },

          // 🟥 EXPENSES
          {
            label: 'Expenses',
            data: this.monthlyProfit.map(m => Number(m.expenses)),
            borderColor: '#dc3545',
            backgroundColor: 'rgba(220,53,69,0.1)',
            tension: 0.4,
            pointRadius: 3,
            fill: false
          },

          // 🟩 PROFIT (highlighted)
          {
            label: 'Profit',
            data: this.monthlyProfit.map(m => Number(m.profit)),
            borderColor: '#198754',
            backgroundColor: 'rgba(25,135,84,0.15)',
            tension: 0.4,
            pointRadius: 4,
            borderWidth: 3,
            fill: true
          }
        ]
      }
    }
  },

  data() {
    return {
      chartOptions: {
        responsive: true,
        maintainAspectRatio: false,

        animation: {
          duration: 1200,
          easing: 'easeInOutQuart'
        },

        interaction: {
          mode: 'index',
          intersect: false
        },

        plugins: {
          legend: {
            position: 'top'
          },
          tooltip: {
            mode: 'index',
            intersect: false
          }
        },

        scales: {
          x: {
            grid: {
              display: false
            }
          },
          y: {
            beginAtZero: true,
            title: {
              display: true,
              text: 'Amount (KES)'
            }
          }
        }
      }
    }
  }
}
</script>

<template>
  <div style="height: 380px;">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>