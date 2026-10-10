<template>
  <Master>
    <div class="container-fluid py-4">
      <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
          <h4 class="mb-1">System Logs</h4>
          <p class="text-muted mb-0">
            Application errors, warnings and diagnostic messages.
          </p>
        </div>

        <button
          class="btn btn-outline-success"
          :disabled="loading"
          @click="fetchLogs"
        >
          <i class="bi bi-arrow-clockwise me-1"></i>
          Refresh
        </button>
      </div>

      <!-- Summary -->
      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <small class="text-muted">Matching entries</small>
              <h3 class="mb-0">{{ total }}</h3>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <small class="text-muted">Current page</small>
              <h3 class="mb-0">{{ currentPage }} / {{ lastPage }}</h3>
            </div>
          </div>
        </div>

        <div class="col-md-4">
          <div class="card border-0 shadow-sm">
            <div class="card-body">
              <small class="text-muted">Log retention</small>
              <h3 class="mb-0">14 days</h3>
            </div>
          </div>
        </div>
      </div>

      <!-- Filters -->
      <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
          <div class="row g-3">
            <div class="col-md-8">
              <label class="form-label">Search logs</label>
              <input
                v-model="search"
                type="search"
                class="form-control"
                placeholder="Search message, context or exception..."
                @keyup.enter="applyFilters"
              />
            </div>

            <div class="col-md-4">
              <label class="form-label">Severity</label>
              <select v-model="level" class="form-select">
                <option value="">All levels</option>
                <option value="EMERGENCY">Emergency</option>
                <option value="ALERT">Alert</option>
                <option value="CRITICAL">Critical</option>
                <option value="ERROR">Error</option>
                <option value="WARNING">Warning</option>
                <option value="NOTICE">Notice</option>
                <option value="INFO">Info</option>
                <option value="DEBUG">Debug</option>
              </select>
            </div>

            <div class="col-12 d-flex gap-2">
              <button
                class="btn btn-success"
                :disabled="loading"
                @click="applyFilters"
              >
                Search
              </button>

              <button
                class="btn btn-outline-secondary"
                @click="clearFilters"
              >
                Clear
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Error message -->
      <div v-if="error" class="alert alert-danger">
        {{ error }}
      </div>

      <!-- Logs table -->
      <div class="card border-0 shadow-sm">
        <div class="card-body">
          <div v-if="loading" class="text-center py-5">
            <div class="spinner-border text-success" role="status"></div>
            <p class="mt-2 text-muted">Loading system logs...</p>
          </div>

          <div v-else-if="logs.length === 0" class="text-center py-5">
            <i class="bi bi-journal-check fs-1 text-muted"></i>
            <p class="mt-2 mb-0">No matching log entries found.</p>
          </div>

          <div v-else class="table-responsive">
            <table class="table table-hover align-middle">
              <thead>
                <tr>
                  <th>Timestamp</th>
                  <th>Severity</th>
                  <th>Message</th>
                  <th>Log file</th>
                  <th class="text-end">Details</th>
                </tr>
              </thead>

              <tbody>
                <template v-for="(log, index) in logs" :key="`${log.file}-${log.timestamp}-${index}`">
                  <tr>
                    <td class="text-nowrap">
                      <small>{{ log.timestamp }}</small>
                    </td>

                    <td>
                      <span :class="levelClass(log.level)">
                        {{ log.level }}
                      </span>
                    </td>

                    <td class="log-message">
                      {{ log.message }}
                    </td>

                    <td>
                      <small class="text-muted">{{ log.file }}</small>
                    </td>

                    <td class="text-end">
                      <button
                        class="btn btn-sm btn-outline-secondary"
                        @click="toggleDetails(index)"
                      >
                        {{ expandedIndex === index ? 'Hide' : 'View' }}
                      </button>
                    </td>
                  </tr>

                  <tr v-if="expandedIndex === index">
                    <td colspan="5" class="bg-body-tertiary">
                      <div class="p-2">
                        <p class="mb-2">
                          <strong>Environment:</strong>
                          {{ log.environment }}
                        </p>

                        <div v-if="log.context" class="mb-3">
                          <strong>Context</strong>
                          <pre class="log-details mt-2">{{ log.context }}</pre>
                        </div>

                        <div v-if="log.details" class="mb-3">
                          <strong>Exception / stack trace</strong>
                          <pre class="log-details mt-2">{{ log.details }}</pre>
                        </div>

                        <div v-if="!log.context && !log.details" class="text-muted">
                          No additional details were parsed from this entry.
                        </div>
                      </div>
                    </td>
                  </tr>
                </template>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div
            v-if="!loading && total > 0"
            class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-3"
          >
            <small class="text-muted">
              {{ total }} matching entries
            </small>

            <div class="d-flex align-items-center gap-2">
              <button
                class="btn btn-sm btn-outline-success"
                :disabled="currentPage <= 1"
                @click="changePage(currentPage - 1)"
              >
                Previous
              </button>

              <span class="small">
                {{ currentPage }} / {{ lastPage }}
              </span>

              <button
                class="btn btn-sm btn-outline-success"
                :disabled="currentPage >= lastPage"
                @click="changePage(currentPage + 1)"
              >
                Next
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Master>
</template>

<script>
import axios from 'axios';
import Master from '@/components/Master.vue';

export default {
  name: 'SystemLogs',

  components: {
    Master,
  },

  data() {
    return {
      logs: [],
      search: '',
      level: '',
      currentPage: 1,
      lastPage: 1,
      total: 0,
      perPage: 20,
      loading: false,
      error: '',
      expandedIndex: null,
    };
  },

  mounted() {
    this.fetchLogs();
  },

  methods: {
    async fetchLogs() {
      this.loading = true;
      this.error = '';
      this.expandedIndex = null;

      try {
        const response = await axios.get('/api/system-logs', {
          params: {
            search: this.search.trim() || undefined,
            level: this.level || undefined,
            page: this.currentPage,
            per_page: this.perPage,
          },
        });

        const result = response.data;

        this.logs = result.data || [];
        this.currentPage = result.current_page || 1;
        this.lastPage = result.last_page || 1;
        this.total = result.total || 0;
      } catch (error) {
        console.error('Failed to load system logs:', error);

        if (error.response?.status === 401) {
          this.error = 'Your session has expired. Please sign in again.';
        } else if (error.response?.status === 403) {
          this.error = 'You are not authorized to view system logs.';
        } else {
          this.error = 'Unable to load system logs. Check the API route and server logs.';
        }
      } finally {
        this.loading = false;
      }
    },

    applyFilters() {
      this.currentPage = 1;
      this.fetchLogs();
    },

    clearFilters() {
      this.search = '';
      this.level = '';
      this.currentPage = 1;
      this.fetchLogs();
    },

    changePage(page) {
      if (page < 1 || page > this.lastPage) return;

      this.currentPage = page;
      this.fetchLogs();
    },

    toggleDetails(index) {
      this.expandedIndex = this.expandedIndex === index ? null : index;
    },

    levelClass(level) {
      const classes = {
        EMERGENCY: 'badge bg-danger',
        ALERT: 'badge bg-danger',
        CRITICAL: 'badge bg-danger',
        ERROR: 'badge bg-danger',
        WARNING: 'badge bg-warning text-dark',
        NOTICE: 'badge bg-info text-dark',
        INFO: 'badge bg-success',
        DEBUG: 'badge bg-secondary',
      };

      return classes[level] || 'badge bg-secondary';
    },
  },
};
</script>

<style scoped>
.log-message {
  min-width: 220px;
  max-width: 460px;
  overflow-wrap: anywhere;
}

.log-details {
  max-height: 350px;
  overflow: auto;
  white-space: pre-wrap;
  overflow-wrap: anywhere;
  padding: 12px;
  border-radius: 6px;
  background: var(--bs-tertiary-bg);
  font-size: 12px;
}
</style>