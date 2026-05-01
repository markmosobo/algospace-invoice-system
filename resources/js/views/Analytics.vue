<template>
  <Master>
    <section class="section dashboard">

      <h4 class="mb-3">Website Analytics</h4>

      <!-- TOP STATS -->
      <div class="row mb-4">

        <div class="col-md-3">
          <div class="card p-3 shadow-sm">
            <h6>Total Visits</h6>
            <h3>{{ totalVisits }}</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card p-3 shadow-sm">
            <h6>Today Visits</h6>
            <h3>{{ todayVisits }}</h3>
          </div>
        </div>

        <div class="col-md-3">
          <div class="card p-3 shadow-sm">
            <h6>Unique Visitors</h6>
            <h3>{{ uniqueVisitors }}</h3>
          </div>
        </div>

      </div>

      <!-- TOP PAGES -->
      <div class="card p-3 shadow-sm mb-4">
        <h6 class="mb-3">Top Pages</h6>

        <table class="table table-sm">
          <thead>
            <tr>
              <th>Page</th>
              <th>Visits</th>
            </tr>
          </thead>

          <tbody>
            <tr v-for="(page, index) in topPages" :key="index">
              <td class="text-truncate" style="max-width: 300px;">
                {{ page.url }}
              </td>
              <td>{{ page.total }}</td>
            </tr>
          </tbody>
        </table>
      </div>

    </section>
  </Master>
</template>

<script>
import Master from '@/components/Master.vue'
import axios from 'axios'

export default {
  name: 'Analytics',
  components: { Master },

  data() {
    return {
      totalVisits: 0,
      todayVisits: 0,
      uniqueVisitors: 0,
      topPages: []
    }
  },

  methods: {
    async fetchAnalytics() {
      try {
        const [total, today, unique, pages] = await Promise.all([
          axios.get('/api/analytics/visits/total'),
          axios.get('/api/analytics/visits/today'),
          axios.get('/api/analytics/visits/unique'),
          axios.get('/api/analytics/visits/top-pages')
        ])

        this.totalVisits = total.data.total
        this.todayVisits = today.data.today
        this.uniqueVisitors = unique.data.unique_visitors
        this.topPages = pages.data

      } catch (error) {
        console.error('Analytics error:', error)
      }
    }
  },

  mounted() {
    this.fetchAnalytics()
  }
}
</script>

<style scoped>
.card {
  border-radius: 12px;
}

h3 {
  font-weight: bold;
}
</style>