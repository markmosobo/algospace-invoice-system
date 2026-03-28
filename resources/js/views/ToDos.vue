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
                    📝 {{ totalTodos }} Active Tasks
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

              <!-- Status summary cards -->
              <div class="row mb-3">
                <div
                  v-for="(count, status) in statusCounts"
                  :key="status"
                  class="col-md-3 col-sm-6 mb-2"
                >
                  <div class="card text-center bg-light p-2">
                    <div class="card-body">
                      <h6 class="card-subtitle text-muted">
                        {{ status.replace('_', ' ') }}
                      </h6>
                      <h5 class="card-title">{{ count }}</h5>
                    </div>
                  </div>
                </div>
              </div>

              <!-- To-Do Table -->
              <table class="table table-borderless">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                  </tr>
                </thead>

                <tbody v-if="loading">
                  <tr>
                    <td colspan="5" class="text-center">
                      <div class="spinner-border text-warning"></div>
                    </td>
                  </tr>
                </tbody>

                <tbody v-else>
                  <tr v-for="(todo, index) in todoList" :key="todo.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ todo.title }}</td>
                    <td>{{ todo.category }}</td>
                    <td>{{ todo.priority }}</td>
                    <td>{{ todo.status }}</td>
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

export default {
  name: "TodoDashboard",
  data() {
    return {
      todoList: [],
      statusCounts: {},
      totalTodos: 0,
      loading: true,
    };
  },
  methods: {
    async loadTodos() {
      this.loading = true;
      try {
        const res = await axios.get("/api/todos-dashboard");

        this.todoList = res.data.todos || [];
        this.statusCounts = res.data.statusCounts || {};
        this.totalTodos = this.todoList.length;

      } catch (err) {
        console.error(err);
      } finally {
        this.loading = false;
      }
    },
    openToDoModal() {
      // open modal or route
    }
  },
  mounted() {
    this.loadTodos();
  },
  components: {
    Master
  }
};
</script>