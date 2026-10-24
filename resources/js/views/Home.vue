
<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <!-- ================= PERSONAL FINANCIAL SUMMARY ================= -->
        <div v-if="userRole === 'personal'" class="col-12 mb-4">
          <div class="p-3 rounded bg-light border shadow-sm">
            <div class="text-end mb-2">
              <button
                class="btn btn-sm btn-outline-secondary"
                @click="toggleAmounts"
              >
                <i
                  class="bi"
                  :class="showAmounts ? 'bi-eye-slash' : 'bi-eye'"
                ></i>
                {{ showAmounts ? 'Hide Amounts' : 'Show Amounts' }}
              </button>
            </div>

            <div class="text-center mb-3">
              <div class="text-muted small">Total Worth</div>
              <div class="fs-4 fw-bold">
                KES {{ maskedAmount(accountTotal) }}
              </div>
            </div>

            <div class="row g-2 text-center">
              <div class="col-6">
                <div class="p-2 rounded bg-success bg-opacity-10 border h-100">
                  <div class="small text-muted">
                    <i class="bi bi-cash-coin me-1"></i>
                    Liquid (Spend Now)
                  </div>
                  <div class="fw-semibold text-success">
                    KES {{ maskedAmount(liquidTotal) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Safe to use today
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="p-2 rounded bg-warning bg-opacity-10 border h-100">
                  <div class="small text-muted">
                    <i class="bi bi-wallet2 me-1"></i>
                    Semi-Liquid (Buffer)
                  </div>
                  <div class="fw-semibold text-warning">
                    KES {{ maskedAmount(semiLiquidTotal) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Emergency cushion
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="p-2 rounded bg-primary bg-opacity-10 border">
                  <div class="small text-muted">
                    <i class="bi bi-piggy-bank me-1"></i>
                    Savings &amp; Shares
                  </div>
                  <div class="fw-semibold text-primary">
                    KES {{ maskedAmount(savingsTotal) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Do not spend
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- ================= END PERSONAL SUMMARY ================= -->


        <!-- ================= OFFICE FINANCIAL SUMMARY ================= -->
        <div v-if="userRole === 'office'" class="col-12 mb-4">
          <div class="p-3 rounded bg-light border shadow-sm">
            <div class="text-end mb-2">
              <button
                class="btn btn-sm btn-outline-secondary"
                @click="toggleAmounts"
              >
                <i
                  class="bi"
                  :class="showAmounts ? 'bi-eye-slash' : 'bi-eye'"
                ></i>
                {{ showAmounts ? 'Hide Amounts' : 'Show Amounts' }}
              </button>
            </div>

            <div class="text-center mb-3">
              <div class="text-muted small">Total Office Funds</div>
              <div class="fs-4 fw-bold">
                KES {{ maskedAmount(officeTotal) }}
              </div>
            </div>

            <div class="row g-2 text-center">
              <div class="col-6">
                <div class="p-2 rounded bg-success bg-opacity-10 border h-100">
                  <div class="small text-muted">
                    <i class="bi bi-cash-coin me-1"></i>
                    Cash on Hand
                  </div>
                  <div class="fw-semibold text-success">
                    KES {{ maskedAmount(officeCash) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Immediate liquidity
                  </div>
                </div>
              </div>

              <div class="col-6">
                <div class="p-2 rounded bg-info bg-opacity-10 border h-100">
                  <div class="small text-muted">
                    <i class="bi bi-bank me-1"></i>
                    Bank Accounts
                  </div>
                  <div class="fw-semibold text-info">
                    KES {{ maskedAmount(officeBank) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Accessible funds
                  </div>
                </div>
              </div>

              <div class="col-12">
                <div class="p-2 rounded bg-warning bg-opacity-10 border">
                  <div class="small text-muted">
                    <i class="bi bi-wallet2 me-1"></i>
                    Accounts Receivable
                  </div>
                  <div class="fw-semibold text-warning">
                    KES {{ maskedAmount(officeReceivables) }}
                  </div>
                  <div class="small text-muted fst-italic">
                    Pending collections
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- ================= END OFFICE SUMMARY ================= -->


        <!-- ================= EXISTING DASHBOARD CARDS ================= -->
        <div
          v-for="(card, index) in dashboardCards"
          :key="card.title + index"
          class="col-xxl-2 col-md-3 col-sm-4 col-6 mb-3"
        >
          <div class="card info-card shadow-sm h-100">
            <div class="card-body">
              <h5 class="card-title" :class="`text-${card.color}`">
                {{ card.title }}
              </h5>

              <div class="d-flex align-items-center">
                <div
                  class="card-icon rounded-circle d-flex align-items-center
                         justify-content-center bg-light"
                >
                  <i :class="`bi ${card.icon} text-${card.color}`"></i>
                </div>

                <div class="ps-3 min-width-0">
                  <h6
                    class="text-truncate mb-0"
                    :style="{
                      maxWidth: '120px',
                      fontSize: Number(card.value) > 999999
                        ? '0.9rem'
                        : '1rem'
                    }"
                  >
                    {{ card.value ?? 0 }}
                  </h6>
                </div>
              </div>
            </div>
          </div>
        </div>
        <!-- ================= END DASHBOARD CARDS ================= -->


        <!-- ================= TO-DO DASHBOARD ================= -->
        <div class="col-12 mt-3">
          <div class="card shadow-sm todo-dashboard-card">
            <div class="card-body">

              <div
                class="d-flex justify-content-between align-items-center
                       flex-wrap gap-2 mb-4"
              >
                <div>
                  <h5 class="card-title mb-1">
                    <i class="bi bi-check2-square text-success me-2"></i>
                    To-Do Dashboard
                  </h5>
                  <small class="text-muted">
                    Keep track of tasks, priorities and delegated work.
                  </small>
                </div>

                <div class="d-flex gap-2">
                  <button
                    class="btn btn-sm btn-outline-secondary"
                    :disabled="todosLoading"
                    @click="fetchTodos"
                  >
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh
                  </button>

                  <button
                    class="btn btn-sm btn-success"
                    @click="navigateTo('/to-do-tasks')"
                  >
                    <i class="bi bi-list-task me-1"></i>
                    Manage Tasks
                  </button>
                </div>
              </div>


              <!-- Task status cards -->
              <div class="row g-3 mb-4">

                <div class="col-6 col-lg-3">
                  <div class="todo-stat border rounded p-3 h-100">
                    <div class="d-flex justify-content-between">
                      <span class="small text-muted">Pending</span>
                      <i class="bi bi-hourglass-split text-warning"></i>
                    </div>
                    <h3 class="mb-0 mt-2 text-warning">
                      {{ todoSummary.pending }}
                    </h3>
                    <small class="text-muted">Awaiting action</small>
                  </div>
                </div>

                <div class="col-6 col-lg-3">
                  <div class="todo-stat border rounded p-3 h-100">
                    <div class="d-flex justify-content-between">
                      <span class="small text-muted">Deferred</span>
                      <i class="bi bi-pause-circle text-secondary"></i>
                    </div>
                    <h3 class="mb-0 mt-2 text-secondary">
                      {{ todoSummary.deferred }}
                    </h3>
                    <small class="text-muted">Temporarily postponed</small>
                  </div>
                </div>

                <div class="col-6 col-lg-3">
                  <div class="todo-stat border rounded p-3 h-100">
                    <div class="d-flex justify-content-between">
                      <span class="small text-muted">Completed</span>
                      <i class="bi bi-check-circle text-success"></i>
                    </div>
                    <h3 class="mb-0 mt-2 text-success">
                      {{ todoSummary.completed }}
                    </h3>
                    <small class="text-muted">Finished tasks</small>
                  </div>
                </div>

                <div class="col-6 col-lg-3">
                  <div class="todo-stat border rounded p-3 h-100">
                    <div class="d-flex justify-content-between">
                      <span class="small text-muted">Delegated</span>
                      <i class="bi bi-people text-primary"></i>
                    </div>
                    <h3 class="mb-0 mt-2 text-primary">
                      {{ todoSummary.delegated }}
                    </h3>
                    <small class="text-muted">Assigned to staff</small>
                  </div>
                </div>

              </div>


              <!-- Filters -->
              <div
                class="d-flex justify-content-between align-items-center
                       flex-wrap gap-2 mb-3"
              >
                <div class="d-flex flex-wrap gap-2">
                  <button
                    v-for="filter in todoFilters"
                    :key="filter.value"
                    class="btn btn-sm"
                    :class="todoFilter === filter.value
                      ? 'btn-success'
                      : 'btn-outline-secondary'"
                    @click="todoFilter = filter.value"
                  >
                    {{ filter.label }}
                  </button>
                </div>

                <small class="text-muted">
                  {{ filteredTodos.length }} task(s)
                </small>
              </div>


              <!-- Loading -->
              <div v-if="todosLoading" class="text-center py-5">
                <div
                  class="spinner-border spinner-border-sm text-success"
                  role="status"
                ></div>
                <span class="ms-2 text-muted">Loading tasks...</span>
              </div>


              <!-- Error -->
              <div
                v-else-if="todoError"
                class="alert alert-warning d-flex justify-content-between
                       align-items-center gap-2"
              >
                <span>{{ todoError }}</span>
                <button
                  class="btn btn-sm btn-outline-dark"
                  @click="fetchTodos"
                >
                  Retry
                </button>
              </div>


              <!-- Tasks -->
              <div
                v-else-if="filteredTodos.length"
                class="list-group list-group-flush"
              >
                <div
                  v-for="todo in filteredTodos"
                  :key="todo.id"
                  class="list-group-item px-0 py-3"
                >
                  <div
                    class="d-flex align-items-start gap-3"
                  >

                    <!-- Complete action -->
                    <button
                      v-if="todo.status !== 'completed'"
                      class="btn btn-sm btn-outline-success mt-1"
                      :disabled="todoBusy === todo.id"
                      title="Mark as completed"
                      @click="markTodoDone(todo)"
                    >
                      <span
                        v-if="todoBusy === todo.id"
                        class="spinner-border spinner-border-sm"
                      ></span>
                      <i v-else class="bi bi-check-lg"></i>
                    </button>

                    <div
                      v-else
                      class="text-success mt-1 px-2"
                      title="Completed"
                    >
                      <i class="bi bi-check-circle-fill fs-5"></i>
                    </div>


                    <!-- Task information -->
                    <div class="flex-grow-1 min-width-0">

                      <div
                        class="fw-semibold text-break"
                        :class="{
                          'text-decoration-line-through text-muted':
                            todo.status === 'completed'
                        }"
                      >
                        {{ todo.title || 'Untitled task' }}
                      </div>

                      <p
                        v-if="todo.description"
                        class="small text-muted mb-2 mt-1 text-break"
                      >
                        {{ todo.description }}
                      </p>

                      <div class="d-flex flex-wrap gap-2 mt-2">

                        <span
                          class="badge"
                          :class="statusClass(todo.status)"
                        >
                          {{ formatStatus(todo.status) }}
                        </span>

                        <span
                          v-if="todo.category"
                          class="badge bg-light text-dark border"
                        >
                          <i class="bi bi-folder2 me-1"></i>
                          {{ formatLabel(todo.category) }}
                        </span>

                        <span
                          v-if="todo.priority"
                          class="badge"
                          :class="priorityClass(todo.priority)"
                        >
                          <i class="bi bi-flag me-1"></i>
                          {{ formatLabel(todo.priority) }}
                        </span>

                      </div>

                      <!-- Delegated staff member -->
                      <div
                        v-if="todo.status === 'delegated'"
                        class="small text-muted mt-2"
                      >
                        <i class="bi bi-person-check me-1"></i>
                        Assigned to:
                        <strong>
                          {{ staffName(todo.delegated_user) }}
                        </strong>
                      </div>

                      <div
                        v-if="todo.checked_at"
                        class="small text-success mt-2"
                      >
                        <i class="bi bi-check2-all me-1"></i>
                        Checked: {{ formatDate(todo.checked_at) }}
                      </div>

                    </div>


                    <!-- Task actions -->
                    <div class="dropdown flex-shrink-0">
                      <button
                        class="btn btn-sm btn-light border"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                        aria-label="Task actions"
                        :disabled="todoBusy === todo.id"
                      >
                        <i class="bi bi-three-dots-vertical"></i>
                      </button>

                      <ul class="dropdown-menu dropdown-menu-end">

                        <li v-if="todo.status === 'deferred'">
                          <button
                            class="dropdown-item"
                            @click="resumeTodo(todo)"
                          >
                            <i class="bi bi-play-circle me-2"></i>
                            Resume task
                          </button>
                        </li>

                        <li
                          v-else-if="
                            todo.status !== 'completed' &&
                            todo.status !== 'deferred'
                          "
                        >
                          <button
                            class="dropdown-item"
                            @click="deferTodo(todo)"
                          >
                            <i class="bi bi-pause-circle me-2"></i>
                            Defer task
                          </button>
                        </li>

                        <li>
                          <button
                            class="dropdown-item"
                            @click="navigateTo('/to-do-tasks')"
                          >
                            <i class="bi bi-pencil-square me-2"></i>
                            Manage task
                          </button>
                        </li>

                      </ul>
                    </div>

                  </div>
                </div>
              </div>


              <!-- Empty state -->
              <div v-else class="text-center py-5">
                <i class="bi bi-clipboard-check fs-1 text-success"></i>
                <h6 class="mt-3">
                  {{ todoFilter === 'all'
                    ? 'No tasks found'
                    : 'No tasks in this category' }}
                </h6>
                <p class="text-muted small mb-3">
                  Create a task or change your filter to see other tasks.
                </p>
                <button
                  class="btn btn-sm btn-outline-success"
                  @click="navigateTo('/to-do-tasks')"
                >
                  <i class="bi bi-plus-lg me-1"></i>
                  Open To-Do Manager
                </button>
              </div>

            </div>
          </div>
        </div>
        <!-- ================= END TO-DO DASHBOARD ================= -->


        <!-- ================= STAFF OVERVIEW ================= -->
        <div
          v-if="userRole === 'office' || userRole === 'personal'"
          class="col-12 mt-3"
        >
          <div class="card shadow-sm">
            <div class="card-body">

              <div
                class="d-flex justify-content-between align-items-center
                       flex-wrap gap-2 mb-3"
              >
                <div>
                  <h5 class="card-title mb-1">
                    <i class="bi bi-people text-primary me-2"></i>
                    Staff &amp; Delegated Work
                  </h5>
                  <small class="text-muted">
                    Review tasks assigned to staff members.
                  </small>
                </div>

                <button
                  class="btn btn-sm btn-outline-primary"
                  @click="navigateTo('/to-do-tasks')"
                >
                  <i class="bi bi-list-task me-1"></i>
                  Manage Delegations
                </button>
              </div>


              <div v-if="delegatedTodos.length" class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead class="table-light">
                    <tr>
                      <th>Staff Member</th>
                      <th>Task</th>
                      <th>Priority</th>
                      <th>Status</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody>
                    <tr
                      v-for="todo in delegatedTodos"
                      :key="todo.id"
                    >
                      <td>
                        <div class="d-flex align-items-center gap-2">
                          <div class="staff-avatar">
                            {{ staffInitials(todo.delegated_user) }}
                          </div>

                          <div>
                            <div class="fw-semibold">
                              {{ staffName(todo.delegated_user) }}
                            </div>
                            <small class="text-muted">
                              Staff assignment
                            </small>
                          </div>
                        </div>
                      </td>

                      <td>
                        <div class="fw-semibold">
                          {{ todo.title }}
                        </div>
                        <small class="text-muted">
                          {{ formatLabel(todo.category) }}
                        </small>
                      </td>

                      <td>
                        <span
                          class="badge"
                          :class="priorityClass(todo.priority)"
                        >
                          {{ formatLabel(todo.priority || 'normal') }}
                        </span>
                      </td>

                      <td>
                        <span
                          class="badge"
                          :class="statusClass(todo.status)"
                        >
                          {{ formatStatus(todo.status) }}
                        </span>
                      </td>

                      <td>
                        <button
                          v-if="todo.status === 'delegated'"
                          class="btn btn-sm btn-outline-success"
                          :disabled="todoBusy === todo.id"
                          @click="markTodoDone(todo)"
                        >
                          <i class="bi bi-check-lg me-1"></i>
                          Complete
                        </button>

                        <span v-else class="text-muted small">
                          No action needed
                        </span>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>


              <div v-else class="text-center py-4">
                <i class="bi bi-person-check fs-2 text-muted"></i>
                <p class="mt-2 mb-1 fw-semibold">
                  No delegated tasks yet
                </p>
                <small class="text-muted">
                  Tasks assigned to staff will appear here.
                </small>
              </div>

            </div>
          </div>
        </div>
        <!-- ================= END STAFF OVERVIEW ================= -->

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
  timerProgressBar: true,
});

export default {
  name: 'Home',

  components: {
    Master,
  },

  data() {
    return {
      // Financial privacy
      showAmounts: false,
      currentYear: '',
      accountTotal: null,
      semiLiquidTotal: null,
      liquidTotal: null,
      savingsTotal: null,

      officeTotal: 0,
      officeCash: 0,
      officeBank: 0,
      officeReceivables: 0,

      // User and dashboard statistics
      user: {},
      currentUser: {},
      userRole: null,
      current_user_id: null,
      current_user: '',
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

      // To-do dashboard
      todos: [],
      statusCounts: {},
      todosLoading: false,
      todoError: '',
      todoBusy: null,
      todoFilter: 'all',

      todoFilters: [
        { label: 'All', value: 'all' },
        { label: 'Pending', value: 'pending' },
        { label: 'Deferred', value: 'deferred' },
        { label: 'Delegated', value: 'delegated' },
        { label: 'Completed', value: 'completed' },
      ],
    };
  },

  computed: {
    maskedAmount() {
      return (amount) => {
        if (!this.showAmounts) {
          return '••••••';
        }

        return Number(amount || 0).toLocaleString();
      };
    },

    dashboardCards() {
      const cards = {
        office: [
          {
            title: 'Services Offered',
            value: this.stats.services,
            icon: 'bi-people',
            color: 'primary',
          },
          {
            title: 'Suppliers',
            value: this.stats.suppliers,
            icon: 'bi-person-lines-fill',
            color: 'info',
          },
          {
            title: 'Customers',
            value: this.stats.customers,
            icon: 'bi-people',
            color: 'secondary',
          },
          {
            title: 'Supplies',
            value: this.stats.supplies,
            icon: 'bi-building',
            color: 'success',
          },
          {
            title: 'Payments',
            value: this.stats.payments,
            icon: 'bi-circle',
            color: 'warning',
          },
          {
            title: 'Invoices',
            value: this.stats.invoices,
            icon: 'bi-hourglass-split',
            color: 'info',
          },
        ],

        personal: [
          {
            title: 'Accounts',
            value: this.stats.personalAccounts,
            icon: 'bi-building',
            color: 'success',
          },
          {
            title: 'Categories',
            value: this.stats.personalCategories,
            icon: 'bi-house-door',
            color: 'warning',
          },
          {
            title: 'Transactions',
            value: this.stats.personalTransactions,
            icon: 'bi-box-arrow-right',
            color: 'danger',
          },
        ],
      };

      return (cards[this.userRole] || []).filter((card) => {
        return card.value !== null &&
          card.value !== undefined &&
          card.value !== 0 &&
          card.value !== 'N/A';
      });
    },

    todoSummary() {
      const counts = this.statusCounts || {};

      return {
        pending: Number(counts.pending || 0),
        deferred: Number(counts.deferred || 0),
        completed: Number(counts.completed || 0),
        delegated: Number(counts.delegated || 0),
      };
    },

    filteredTodos() {
      let result = [...this.todos];

      if (this.todoFilter !== 'all') {
        result = result.filter(
          (todo) => todo.status === this.todoFilter
        );
      }

      // Show outstanding work first, then completed tasks.
      const order = {
        pending: 0,
        in_progress: 1,
        delegated: 2,
        deferred: 3,
        completed: 4,
      };

      result.sort((a, b) => {
        const statusDifference =
          (order[a.status] ?? 5) - (order[b.status] ?? 5);

        if (statusDifference !== 0) {
          return statusDifference;
        }

        const priorityOrder = {
          high: 0,
          medium: 1,
          low: 2,
        };

        return (priorityOrder[a.priority] ?? 3) -
          (priorityOrder[b.priority] ?? 3);
      });

      return result;
    },

    delegatedTodos() {
      return this.todos.filter(
        (todo) =>
          todo.status === 'delegated' &&
          todo.delegated_to !== null &&
          todo.delegated_to !== undefined
      );
    },
  },

  methods: {
    toggleAmounts() {
      this.showAmounts = !this.showAmounts;
    },

    navigateTo(location) {
      this.$router.push(location);
    },

    // ================= DASHBOARD STATISTICS =================

    async fetchDashboardStats() {
      try {
        const response = await axios.get('/api/dashboard/stats');
        const stats = response.data.stats || {};

        this.stats = stats;

        this.accountTotal = stats.accountTotal ?? 0;
        this.liquidTotal = stats.liquidTotal ?? 0;
        this.semiLiquidTotal = stats.semiLiquidTotal ?? 0;
        this.savingsTotal = stats.savingsTotal ?? 0;

        this.officeTotal = stats.officeTotal ?? 0;
        this.officeCash = stats.officeCash ?? 0;
        this.officeBank = stats.officeBank ?? 0;
        this.officeReceivables = stats.officeReceivables ?? 0;
      } catch (error) {
        console.error('Dashboard stats error:', error);
      }
    },

    // ================= TO-DO DASHBOARD =================

    async fetchTodos() {
      this.todosLoading = true;
      this.todoError = '';

      try {
        const response = await axios.get('/api/todos-dashboard');
        const data = response.data || {};

        // Matches the actual ToDoController::dashboard response.
        this.todos = Array.isArray(data.todos)
          ? data.todos
          : [];

        this.statusCounts = data.statusCounts || {};
      } catch (error) {
        console.error('To-do dashboard error:', error);

        this.todoError =
          error.response?.data?.message ||
          'Unable to load tasks. Please try again.';
      } finally {
        this.todosLoading = false;
      }
    },

    async markTodoDone(todo) {
      const confirmed = await Swal.fire({
        title: 'Complete this task?',
        text: todo.title,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, complete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#198754',
      });

      if (!confirmed.isConfirmed) {
        return;
      }

      await this.performTodoAction(
        todo,
        `/api/todos/${todo.id}/done`,
        'PATCH',
        'Task marked as completed'
      );
    },

    async deferTodo(todo) {
      await this.performTodoAction(
        todo,
        `/api/todos/${todo.id}/defer`,
        'PATCH',
        'Task deferred successfully'
      );
    },

    async resumeTodo(todo) {
      await this.performTodoAction(
        todo,
        `/api/todos/${todo.id}/resume`,
        'PATCH',
        'Task resumed successfully'
      );
    },

    async performTodoAction(todo, url, method, successMessage) {
      if (this.todoBusy !== null) {
        return;
      }

      this.todoBusy = todo.id;

      try {
        await axios({
          method,
          url,
        });

        toast.fire({
          icon: 'success',
          title: successMessage,
        });

        await this.fetchTodos();
      } catch (error) {
        console.error('To-do action error:', error);

        const message =
          error.response?.data?.message ||
          'Unable to update the task. Please try again.';

        await Swal.fire({
          icon: 'error',
          title: 'Task update failed',
          text: message,
        });
      } finally {
        this.todoBusy = null;
      }
    },

    // ================= DISPLAY HELPERS =================

    formatLabel(value) {
      if (!value) {
        return '—';
      }

      return String(value)
        .replace(/_/g, ' ')
        .replace(/\b\w/g, (character) => character.toUpperCase());
    },

    formatStatus(status) {
      return this.formatLabel(status || 'pending');
    },

    statusClass(status) {
      const classes = {
        pending: 'bg-warning text-dark',
        in_progress: 'bg-info text-dark',
        completed: 'bg-success',
        deferred: 'bg-secondary',
        delegated: 'bg-primary',
      };

      return classes[status] || 'bg-light text-dark border';
    },

    priorityClass(priority) {
      const classes = {
        high: 'bg-danger',
        medium: 'bg-warning text-dark',
        low: 'bg-success',
      };

      return classes[priority] || 'bg-light text-dark border';
    },

    staffName(staff) {
      if (!staff) {
        return 'Staff member';
      }

      const fullName = [
        staff.first_name,
        staff.last_name,
      ].filter(Boolean).join(' ').trim();

      return fullName ||
        staff.name ||
        staff.username ||
        'Staff member';
    },

    staffInitials(staff) {
      const name = this.staffName(staff);

      if (name === 'Staff member') {
        return '?';
      }

      return name
        .split(/\s+/)
        .filter(Boolean)
        .slice(0, 2)
        .map((part) => part.charAt(0).toUpperCase())
        .join('');
    },

    formatDate(value) {
      if (!value) {
        return '—';
      }

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) {
        return value;
      }

      return date.toLocaleString();
    },
  },

  mounted() {
    let storedUser = {};

    try {
      storedUser = JSON.parse(
        localStorage.getItem('user') || '{}'
      ) || {};
    } catch (error) {
      console.error('Unable to read stored user:', error);
    }

    this.user = storedUser;
    this.currentUser = storedUser;
    this.userRole = storedUser.role || null;
    this.current_user_id = storedUser.id || null;

    this.current_user = [
      storedUser.first_name,
      storedUser.last_name,
    ].filter(Boolean).join(' ');

    this.fetchDashboardStats();
    this.fetchTodos();
  },
};
</script>


<style scoped>
.card {
  transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
  transform: translateY(-2px);
}

.bg-light {
  background-color: rgba(255, 255, 255, 0.8);
}

.min-width-0 {
  min-width: 0;
}

.todo-dashboard-card {
  overflow: visible;
}

.todo-stat {
  background: rgba(248, 249, 250, 0.7);
  transition: background-color 0.2s ease;
}

.todo-stat:hover {
  background: rgba(233, 236, 239, 0.8);
}

.staff-avatar {
  width: 36px;
  height: 36px;
  min-width: 36px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  background: #e8f0fe;
  color: #245dc1;
  font-size: 0.8rem;
  font-weight: 700;
}

.todo-dashboard-card .list-group-item {
  background: transparent;
}

.todo-dashboard-card .dropdown-menu {
  z-index: 1050;
}

@media (max-width: 576px) {
  .todo-dashboard-card .card-body {
    padding: 1rem;
  }

  .todo-dashboard-card .card-title {
    font-size: 1rem;
  }
}
</style>
