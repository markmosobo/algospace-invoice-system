<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card overflow-auto">
            <div class="card-body pb-0">

              <!-- Header -->
              <h5 class="card-title">
                To-Dos <span>| Active Tasks</span>
              </h5>

              <!-- Buttons -->
              <div class="row mb-3">
                <div class="col d-flex">
                  <button
                    class="btn btn-sm btn-warning rounded-pill me-2"
                    @click="openToDoModal"
                  >
                    📝 {{ totalTodos }} Tasks
                  </button>
                </div>

                <div class="col-auto d-flex justify-content-end">
                  <button
                    class="btn btn-sm btn-outline-warning rounded-pill"
                    @click="openToDoModal"
                  >
                    Add To-Do
                  </button>
                </div>
              </div>

              <!-- STATUS FILTER CARDS -->
              <div class="row mb-3">

                <!-- ALL -->
                <div class="col-md-3 col-sm-6 mb-2">
                  <div
                    class="card text-center p-2 cursor-pointer"
                    :class="{ 'border-dark shadow-sm bg-light': activeStatus === null }"
                    @click="activeStatus = null"
                  >
                    <div class="card-body">
                      <h6 class="text-muted">All Tasks</h6>
                      <h5>{{ totalTodos }}</h5>
                    </div>
                  </div>
                </div>

                <!-- PER STATUS -->
                <div
                  v-for="(count, status) in statusCounts"
                  :key="status"
                  class="col-md-3 col-sm-6 mb-2"
                >
                  <div
                    class="card text-center p-2 cursor-pointer"
                    :class="{
                      'border-warning shadow-sm bg-warning bg-opacity-10':
                        activeStatus === status
                    }"
                    @click="toggleStatus(status)"
                  >
                    <div class="card-body">
                      <h6 class="text-muted text-capitalize">
                        {{ status.replace('_', ' ') }}
                      </h6>
                      <h5>{{ count }}</h5>
                    </div>
                  </div>
                </div>
              </div>

              <!-- TO-DO TABLE -->
              <table id="ToDosTable" class="table table-borderless align-middle">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Last Action</th>
                    <th>Actions</th>
                  </tr>
                </thead>

                <!-- LOADING -->
                <tbody v-if="loading">
                  <tr>
                    <td colspan="7" class="text-center">
                      <div class="spinner-border text-warning"></div>
                    </td>
                  </tr>
                </tbody>

                <!-- DATA -->
                <tbody v-else>
                  <tr
                    v-for="(todo, index) in filteredTodos"
                    :key="todo.id"
                  >
                    <td>{{ index + 1 }}</td>

                    <td class="fw-semibold">
                      {{ todo.title }}
                    </td>

                    <td class="text-capitalize">
                      {{ todo.category }}
                    </td>

                    <td class="text-capitalize">
                      {{ todo.priority }}
                    </td>

                    <!-- STATUS -->
                    <td>
                      <span
                        class="badge"
                        :class="{
                          'bg-secondary': todo.status === 'pending',
                          'bg-info': todo.status === 'in_progress',
                          'bg-warning text-dark': todo.status === 'deferred',
                          'bg-primary': todo.status === 'delegated',
                          'bg-success': todo.status === 'completed'
                        }"
                      >
                        {{ todo.status }}
                      </span>
                    </td>

                    <!-- CREATED DATE -->
                    <td class="text-muted small">
                      {{ formatDate(todo.created_at) }}
                    </td>

                    <!-- LAST ACTION DATE -->
                    <td class="text-muted small">
                      <span v-if="todo.updated_at !== todo.created_at">
                        {{ formatDate(todo.updated_at) }}
                      </span>
                      <span v-else class="fst-italic text-muted">
                        —
                      </span>
                    </td>

                    <td>
                      <button
                        class="btn btn-sm btn-outline-primary"
                        @click="viewTodo(todo)"
                      >
                        View
                      </button>
                    </td>
                  </tr>

                  <!-- EMPTY STATE -->
                  <tr v-if="filteredTodos.length === 0">
                    <td colspan="7" class="text-center text-muted py-4">
                      No tasks found for this status
                    </td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <!-- MANAGE TODOS MODAL -->
        <div
          class="modal fade"
          id="toDoModal"
          tabindex="-1"
        >
          <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">To-Do List</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">

                <!-- ADD TODO -->
                <div class="card mb-4">
                  <div class="card-header fw-bold">Add To-Do</div>

                  <div class="card-body row g-3">
                    <div class="col-md-6">
                      <label class="form-label">Title</label>
                      <input v-model="newToDo.title" class="form-control">
                    </div>

                    <div class="col-md-6">
                      <label class="form-label">Category</label>
                      <select v-model="newToDo.category" class="form-select">
                        <option value="cyber">Cyber</option>
                        <option value="farm">Farm</option>
                        <option value="personal">Personal</option>
                        <option value="other">Other</option>
                      </select>
                    </div>

                    <div class="col-md-12">
                      <label class="form-label">Description</label>
                      <textarea v-model="newToDo.description" class="form-control"></textarea>
                    </div>

                    <div class="col-md-4">
                      <label class="form-label">Priority</label>
                      <select v-model="newToDo.priority" class="form-select">
                        <option value="high">High</option>
                        <option value="medium">Medium</option>
                        <option value="low">Low</option>
                      </select>
                    </div>

                    <div class="col-md-4 align-self-end">
                      <button class="btn btn-success w-100" @click="createToDo">
                        Save To-Do
                      </button>
                    </div>
                  </div>
                </div>

              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>
              </div>

            </div>
          </div>
        </div>

      </div>

      <!-- VIEW TODO MODAL -->
      <div
        class="modal fade"
        id="viewTodoModal"
        tabindex="-1"
      >
        <div class="modal-dialog modal-md modal-dialog-centered">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title">Task Details</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" v-if="selectedTodo">

              <h5 class="fw-bold mb-2">
                {{ selectedTodo.title }}
              </h5>

              <p class="text-muted mb-3">
                {{ selectedTodo.description || 'No description provided' }}
              </p>

              <hr>

              <p><strong>Category:</strong> {{ selectedTodo.category }}</p>
              <p><strong>Priority:</strong> {{ selectedTodo.priority }}</p>

              <p>
                <strong>Status:</strong>
                <span class="badge bg-secondary">
                  {{ selectedTodo.status }}
                </span>
              </p>

              <hr>

              <p class="text-muted small">
                Created: {{ formatDate(selectedTodo.created_at) }}
              </p>

              <p class="text-muted small">
                Last Updated: {{ formatDate(selectedTodo.updated_at) }}
              </p>

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
  name: "TodoDashboard",

  components: { Master },

  data() {
    return {
      newToDo: {
        title: "",
        description: "",
        category: "cyber",
        priority: "medium",
      },
      todoList: [],
      statusCounts: {},
      totalTodos: 0,
      activeStatus: null,
      loading: true,
      selectedTodo: null
    };
  },

  computed: {
    filteredTodos() {
      if (!this.activeStatus) return this.todoList;
      return this.todoList.filter(
        t => t.status === this.activeStatus
      );
    }
  },

  methods: {
    viewTodo(todo) {
      this.selectedTodo = todo;

      const modal = new bootstrap.Modal(
        document.getElementById('viewTodoModal')
      );

      modal.show();
    },
    formatDate(date) {
      if (!date) return '-';

      return new Date(date).toLocaleString('en-GB', {
        day: '2-digit',
        month: 'short',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
      });
    },    
    toggleStatus(status) {
      this.activeStatus =
        this.activeStatus === status ? null : status;
    },

    openToDoModal() {
      new bootstrap.Modal(
        document.getElementById("toDoModal")
      ).show();
    },

    async loadTodos() {
      this.loading = true;
      const res = await axios.get("/api/todos-dashboard");
      this.todoList = res.data.todos || [];
      this.statusCounts = res.data.statusCounts || {};
      this.totalTodos = this.todoList.length;
      this.loading = false;
      setTimeout(() => {
        $("#ToDosTable").DataTable();
      }, 10);
    },

    createToDo() {
      axios.post("/api/to-dos", this.newToDo)
        .then(() => this.loadTodos());
    }
  },

  mounted() {
    this.loadTodos();
  }
};
</script>

<style>
.cursor-pointer {
  cursor: pointer;
}
</style>