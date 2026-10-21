
<template>
  <Master>
    <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card top-selling overflow-auto">
            <div class="card-body pb-0">

              <h5 class="card-title">
                Audit Logs
                <span>| Application activity and accountability</span>
              </h5>

              <!-- SEARCH AND FILTERS -->
              <div class="row g-2 mb-3">
                <div class="col-md-4">
                  <input
                    v-model="searchQuery"
                    @input="onSearchInput"
                    type="text"
                    class="form-control form-control-sm"
                    placeholder="Search event, description or IP..."
                  />
                </div>

                <div class="col-md-3">
                  <select
                    v-model="filters.event"
                    @change="loadAuditLogs"
                    class="form-select form-select-sm"
                  >
                    <option value="">All Events</option>
                    <option
                      v-for="event in events"
                      :key="event"
                      :value="event"
                    >
                      {{ event }}
                    </option>
                  </select>
                </div>

                <div class="col-md-2">
                  <input
                    type="date"
                    v-model="filters.date_from"
                    class="form-control form-control-sm"
                    title="From date"
                  />
                </div>

                <div class="col-md-2">
                  <input
                    type="date"
                    v-model="filters.date_to"
                    class="form-control form-control-sm"
                    title="To date"
                  />
                </div>

                <div class="col-md-1">
                  <button
                    class="btn btn-sm btn-success w-100"
                    style="background: darkgreen"
                    @click="applyFilters"
                  >
                    Apply
                  </button>
                </div>

                <div class="col-12 d-flex align-items-center gap-2">
                  <button
                    class="btn btn-sm btn-outline-secondary"
                    @click="clearFilters"
                  >
                    Clear Filters
                  </button>

                  <span class="text-muted small ms-auto">
                    {{ meta.total }} records
                  </span>
                </div>
              </div>

              <!-- TABLE -->
              <div class="table-responsive">
                <table class="table table-borderless">
                  <thead>
                    <tr>
                      <th>Time</th>
                      <th>User</th>
                      <th>Event</th>
                      <th>Description</th>
                      <th>IP Address</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody v-if="initializing">
                    <tr>
                      <td colspan="6" class="text-center py-4">
                        <div
                          class="spinner-border text-success"
                          role="status"
                        >
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>

                  <tbody v-else-if="auditLogs.length">
                    <tr
                      v-for="log in auditLogs"
                      :key="log.id"
                    >
                      <td>{{ formatDate(log.created_at) }}</td>
                      <td>{{ log.user?.name || 'System / Unknown' }}</td>
                      <td>
                        <span class="badge bg-secondary">
                          {{ log.event }}
                        </span>
                      </td>
                      <td>{{ log.description }}</td>
                      <td>{{ log.ip_address || 'N/A' }}</td>
                      <td>
                        <button
                          class="btn btn-sm btn-outline-success"
                          @click="viewLog(log)"
                        >
                          <i class="ri-eye-line"></i> View
                        </button>
                      </td>
                    </tr>
                  </tbody>

                  <tbody v-else>
                    <tr>
                      <td colspan="6" class="text-center text-muted py-4">
                        No audit records found.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <!-- PAGINATION -->
              <div
                class="d-flex justify-content-between align-items-center flex-wrap gap-2 py-3"
              >
                <small class="text-muted">
                  Showing {{ meta.from ?? 0 }}–{{ meta.to ?? 0 }}
                  of {{ meta.total }}
                </small>

                <div class="d-flex gap-2">
                  <button
                    class="btn btn-sm btn-outline-success"
                    :disabled="meta.current_page <= 1 || initializing"
                    @click="changePage(meta.current_page - 1)"
                  >
                    Previous
                  </button>

                  <span class="small align-self-center">
                    Page {{ meta.current_page }} of {{ meta.last_page }}
                  </span>

                  <button
                    class="btn btn-sm btn-outline-success"
                    :disabled="
                      meta.current_page >= meta.last_page || initializing
                    "
                    @click="changePage(meta.current_page + 1)"
                  >
                    Next
                  </button>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <!-- DETAIL MODAL -->
      <div
        class="modal fade"
        id="auditLogModal"
        tabindex="-1"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title">Audit Record Details</h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
              ></button>
            </div>

            <div class="modal-body" v-if="selectedLog">
              <div class="row g-3">
                <div class="col-md-6">
                  <strong>Event</strong>
                  <div>{{ selectedLog.event }}</div>
                </div>

                <div class="col-md-6">
                  <strong>User</strong>
                  <div>
                    {{ selectedLog.user?.name || 'System / Unknown' }}
                  </div>
                </div>

                <div class="col-12">
                  <strong>Description</strong>
                  <div>{{ selectedLog.description }}</div>
                </div>

                <div class="col-md-6">
                  <strong>IP Address</strong>
                  <div>{{ selectedLog.ip_address || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Request ID</strong>
                  <div>{{ selectedLog.request_id || 'N/A' }}</div>
                </div>

                <div class="col-12">
                  <strong>User Agent</strong>
                  <div class="text-break">
                    {{ selectedLog.user_agent || 'N/A' }}
                  </div>
                </div>

                <div class="col-12">
                  <strong>Additional Properties</strong>
                  <pre class="bg-light p-3 rounded mt-2">{{ JSON.stringify(selectedLog.properties, null, 2) }}</pre>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button
                class="btn btn-secondary"
                data-bs-dismiss="modal"
              >
                Close
              </button>
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

const toast = Swal.mixin({
  toast: true,
  position: "top-end",
  showConfirmButton: false,
  timer: 3000,
});

export default {
  name: "AuditLogs",

  components: {
    Master,
  },

  data() {
    return {
      auditLogs: [],
      events: [],
      selectedLog: null,

      initializing: false,
      searchQuery: "",
      searchTimer: null,

      filters: {
        event: "",
        date_from: "",
        date_to: "",
      },

      meta: {
        current_page: 1,
        last_page: 1,
        per_page: 25,
        total: 0,
        from: null,
        to: null,
      },
    };
  },

  methods: {
    async loadAuditLogs(page = 1) {
      this.initializing = true;

      try {
        const response = await axios.get("/api/audit-logs", {
          params: {
            search: this.searchQuery.trim() || undefined,
            event: this.filters.event || undefined,
            date_from: this.filters.date_from || undefined,
            date_to: this.filters.date_to || undefined,
            page,
            per_page: this.meta.per_page,
          },
        });

        this.auditLogs = response.data.data || [];
        this.meta = {
          ...this.meta,
          ...response.data.meta,
        };
      } catch (error) {
        console.error("Failed to load audit logs:", error);

        toast.fire({
          icon: "error",
          title:
            error.response?.status === 403
              ? "You are not authorized to view audit logs."
              : "Failed to load audit logs.",
        });
      } finally {
        this.initializing = false;
      }
    },

    async loadEvents() {
      try {
        const response = await axios.get("/api/audit-logs/events");
        this.events = response.data.data || [];
      } catch (error) {
        console.error("Failed to load audit events:", error);
      }
    },

    onSearchInput() {
      clearTimeout(this.searchTimer);

      this.searchTimer = setTimeout(() => {
        this.loadAuditLogs(1);
      }, 350);
    },

    applyFilters() {
      if (
        this.filters.date_from &&
        this.filters.date_to &&
        this.filters.date_to < this.filters.date_from
      ) {
        toast.fire({
          icon: "warning",
          title: "End date cannot be earlier than start date.",
        });
        return;
      }

      this.loadAuditLogs(1);
    },

    clearFilters() {
      this.searchQuery = "";

      this.filters = {
        event: "",
        date_from: "",
        date_to: "",
      };

      clearTimeout(this.searchTimer);
      this.loadAuditLogs(1);
    },

    changePage(page) {
      if (
        page < 1 ||
        page > this.meta.last_page ||
        this.initializing
      ) {
        return;
      }

      this.loadAuditLogs(page);
    },

    async viewLog(log) {
      try {
        // Fetch the full record, including properties.
        const response = await axios.get(
          `/api/audit-logs/${log.id}`
        );

        this.selectedLog = response.data.data;

        const element = document.getElementById("auditLogModal");
        const modal = bootstrap.Modal.getOrCreateInstance(element);
        modal.show();
      } catch (error) {
        console.error("Failed to load audit record:", error);

        toast.fire({
          icon: "error",
          title: "Could not load audit record details.",
        });
      }
    },

    formatDate(value) {
      if (!value) return "N/A";

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) return "N/A";

      return date.toLocaleString();
    },
  },

  mounted() {
    this.loadEvents();
    this.loadAuditLogs();
  },

  beforeUnmount() {
    clearTimeout(this.searchTimer);
  },
};
</script>