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

        <!-- Manage To-Dos Modal -->
        <div
        class="modal fade"
        id="toDoModal"
        tabindex="-1"
        aria-labelledby="toDoModalLabel"
        aria-hidden="true"
        >
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content">

            <!-- Header -->
            <div class="modal-header">
                <h5 class="modal-title" id="toDoModalLabel">To-Do List</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <!-- Body -->
            <div class="modal-body">

                <!-- Add To Do -->
                <div class="card mb-4">
                <div class="card-header fw-bold">Add To-Do</div>

                <div class="card-body row g-3">

                    <div class="col-md-6">
                    <label class="form-label">Title</label>
                    <input
                        v-model="newToDo.title"
                        type="text"
                        class="form-control"
                        placeholder="Enter task title"
                    >
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
                    <textarea
                        v-model="newToDo.description"
                        class="form-control"
                        rows="2"
                        placeholder="Optional description"
                    ></textarea>
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
                    <button
                        class="btn btn-success w-100"
                        @click="createToDo"
                    >
                        Save To-Do
                    </button>
                    </div>

                </div>
                </div>

                <!-- To-Do Table -->
                <table class="table table-bordered table-striped align-middle">
                <thead class="table-dark">
                    <tr>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Priority</th>
                    <th>Status</th>
                    <th width="150">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    <tr v-if="todos.length === 0">
                    <td colspan="5" class="text-center text-muted">
                        No to-dos added yet
                    </td>
                    </tr>

                    <tr v-for="item in todos" :key="item.id">
                    <td>{{ item.title }}</td>

                    <td class="text-capitalize">
                        {{ item.category }}
                    </td>

                    <td>
                        <span
                        class="badge"
                        :class="{
                            'bg-danger': item.priority === 'high',
                            'bg-warning': item.priority === 'medium',
                            'bg-secondary': item.priority === 'low'
                        }"
                        >
                        {{ item.priority }}
                        </span>
                    </td>

                    <td>
                        <span
                        class="badge"
                        :class="{
                            'bg-secondary': item.status === 'pending',
                            'bg-info': item.status === 'in_progress',
                            'bg-warning text-dark': item.status === 'deferred',
                            'bg-primary': item.status === 'delegated',
                            'bg-success': item.status === 'completed'
                        }"
                        >
                        {{ item.status}}
                        </span>
                    </td>

                        <td class="d-flex gap-1">

                        <button
                            v-if="item.status !== 'completed'"
                            class="btn btn-sm btn-outline-success"
                            @click="markDone(item)"
                        >
                            Done
                        </button>

                        <button
                            v-if="item.status !== 'completed' && item.status !== 'deferred'"
                            class="btn btn-sm btn-outline-warning"
                            @click="defer(item)"
                        >
                            Defer
                        </button>

                        <button
                            v-if="item.status === 'deferred'"
                            class="btn btn-sm btn-outline-info"
                            @click="resume(item)"
                        >
                            Resume
                        </button>

                        <button
                            class="btn btn-sm btn-outline-danger"
                            @click="deleteToDo(item)"
                        >
                            Delete
                        </button>

                        </td>
                    </tr>
                </tbody>
                </table>

            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                Close
                </button>
            </div>

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
      showToDoModal: false,
      newToDo: {
        title: '',
        description: '',
        category: 'cyber',
        priority: 'medium',
      },
      todos: [], // all current to-dos        
      todoList: [],
      statusCounts: {},
      totalTodos: 0,
      loading: true,
    };
  },
  methods: {
    openToDoModal() {
        this.todos = []
        this.resetToDoForm()
        this.fetchToDos()

        const modal = new bootstrap.Modal(
        document.getElementById('toDoModal')
        )
        modal.show()
    },

    fetchToDos() {
    axios.get('/api/todos/active')
        .then(res => {
        this.todos = res.data;
        })
        .catch(() => {
        toast.fire({
            icon: 'error',
            title: 'Failed to load to-dos'
        });
        });
    },
    resetToDoForm() {
        this.newToDo = {
        title: '',
        description: '',
        category: '',
        priority: '',
        status: '',
        }
    },
    createToDo() {
        axios.post(
        `/api/to-dos`,
        this.newToDo
        ).then(res => {
        this.todos.unshift(res.data)
        this.resetToDoForm()
        })
    },
    markDone(item) {
    axios.patch(`/api/todos/${item.id}/done`)
        .then(res => {
        // update UI from backend response
        item.status = res.data.todo.status;
        })
        .catch(() => {
        toast.fire({
            icon: 'error',
            title: 'Failed to mark to-do as done'
        });
        });
    },
    defer(item) {
    axios.patch(`/api/todos/${item.id}/defer`)
        .then(res => {
        // update UI from backend response
        item.status = res.data.todo.status;
        })
        .catch(() => {
        toast.fire({
            icon: 'error',
            title: 'Failed to defer to-do'
        });
        });
    },
    resume(item) {
    axios.patch(`/api/todos/${item.id}/resume`)
        .then(res => {
        // update UI from backend response
        item.status = res.data.todo.status;
        })
        .catch(() => {
        toast.fire({
            icon: 'error',
            title: 'Failed to resume to-do'
        });
        });
    },
    deleteToDo(item) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This to-do will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel'
    }).then((result) => {
        if (result.isConfirmed) {
        axios.delete(`/api/to-dos/${item.id}`)
            .then(() => {
            this.todos = this.todos.filter(t => t.id !== item.id);

            toast.fire(
                'Deleted!',
                'The to-do has been deleted.',
                'success'
            );
            })
            .catch(() => {
            toast.fire(
                'Error',
                'Failed to delete to-do',
                'error'
            );
            });
        }
    });
    },    
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