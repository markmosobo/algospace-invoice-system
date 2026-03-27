<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <!-- Foot Traffic Card -->
        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="card-body pb-0">
              <h5 class="card-title">Foot Traffic <span>| Visitors at AlgoSpace Cyber</span></h5>

              <!-- Buttons -->
              <p class="card-text">
                <div class="row">
                  <div class="col d-flex">
                    <button
                        class="btn btn-sm btn-primary rounded-pill me-2"
                        style="background-color: darkgreen; border-color: darkgreen;"
                        @click="goToFootTraffic"
                    >
                    👥 {{ totalWalkIns }} Visitors 
                    </button>
                  </div>
                  <div class="col-auto d-flex justify-content-end">
                    <div class="btn-group" role="group">
                      <button
                        id="btnGroupDrop1"
                        type="button"
                        style="background-color: darkgreen; border-color: darkgreen;"
                        class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                        data-bs-toggle="dropdown"
                        aria-haspopup="true"
                        aria-expanded="false"
                      >
                        Quick Links
                      </button>
                      <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                        <a @click="navigateTo('/foot-traffic')" class="dropdown-item" href="#">
                          <i class="ri-user-line mr-2"></i> Foot Traffic Dashboard
                        </a>
                        <a @click="navigateTo('/quick-sale')" class="dropdown-item" href="#">
                          <i class="ri-shopping-cart-line mr-2"></i> Quick Sales
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </p>

              <!-- Cards showing visitor counts by service -->
              <div class="row mb-3">
                <div
                  v-for="(count, service) in serviceCounts"
                  :key="service"
                  class="col-md-3 col-sm-6 mb-2"
                >
                  <div class="card text-center bg-light p-2">
                    <div class="card-body">
                      <h6 class="card-subtitle mb-1 text-muted">{{ service }}</h6>
                      <h5 class="card-title">{{ count }}</h5>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Foot Traffic Table -->
              <table id="FootTrafficTable" class="table table-borderless">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Visitor</th>
                    <th>Service</th>
                    <th>Time In</th>
                    <th>Invoice</th>
                  </tr>
                </thead>

                <tbody v-if="initializing">
                  <tr>
                    <td colspan="5" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>

                <tbody v-else>
                  <tr v-for="(foot, index) in footTrafficList" :key="foot.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ foot.customer_name || 'Anonymous' }}</td>
                    <td>{{ foot.service_name }}</td>
                    <td>{{ formatTime(foot.time_in) }}</td>
                    <td>{{ foot.invoice_id || '-' }}</td>
                  </tr>
                </tbody>
              </table>

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
import "jquery/dist/jquery.min.js";
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";

export default {
  name: "FootTraffic",
  data() {
    return {
      footTrafficList: [],
      serviceCounts: {},
      totalWalkIns: 0,
      initializing: true,
    };
  },
  methods: {
    async loadFootTraffic() {
      this.initializing = true;
      try {
        const res = await axios.get("/api/foot-traffic-dashboard");
        this.footTrafficList = res.data.footTrafficList || [];
        this.totalWalkIns = this.footTrafficList.length;
        this.serviceCounts = res.data.serviceCounts || {};
      } catch (err) {
        console.error(err);
      } finally {
        this.initializing = false;
        // Initialize DataTable
        setTimeout(() => {
          $("#FootTrafficTable").DataTable();
        }, 10);
      }
    },
    formatTime(datetime) {
      if (!datetime) return "-";
      const date = new Date(datetime);
      return date.toLocaleTimeString([], { hour: "2-digit", minute: "2-digit" });
    },
    logWalkIn() {
      // Replace with your modal or API to log walk-in
      this.$router.push({ name: "LogWalkIn" });
    },
    navigateTo(location) {
      this.$router.push(location);
    },
  },
  mounted() {
    this.loadFootTraffic();
    // Optional auto-refresh
    setInterval(() => {
      this.loadFootTraffic();
    }, 60000);
  },
  components: {
    Master,
  },
};
</script>