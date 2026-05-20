<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="card-body pb-0">

              <!-- HEADER -->
              <h5 class="card-title">
                Projects
                <span>| Personal, Office & Asset Tracking</span>
              </h5>

              <!-- ACTIONS -->
              <div class="row mb-3">

                <div class="col">
                  <button
                    class="btn btn-sm btn-primary rounded-pill"
                    style="background: darkgreen; border-color: darkgreen;"
                    @click="addProject"
                  >
                    Add Project
                  </button>
                </div>

              </div>

              <!-- TABLE -->
              <table class="table table-borderless">

                <thead>
                  <tr>
                    <th>Project</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th>Progress</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody v-if="initializing">
                  <tr>
                    <td colspan="5" class="text-center">
                      <div class="spinner-border text-primary"></div>
                    </td>
                  </tr>
                </tbody>

                <tbody v-else>
                  <tr v-for="project in projects" :key="project.id">

                    <!-- PROJECT INFO -->
                    <td class="d-flex align-items-center gap-2">

                      <img
                        v-if="project.cover_image"
                        :src="'/storage/' + project.cover_image"
                        class="project-thumb"
                      />

                      <div v-else class="project-placeholder">
                        {{ getInitials(project.title) }}
                      </div>

                      <div>
                        <strong>{{ project.title }}</strong><br>
                        <small>{{ project.description }}</small>
                      </div>

                    </td>

                    <!-- TYPE -->
                    <td>
                      <span :class="typeClass(project.type)">
                        {{ project.type }}
                      </span>
                    </td>

                    <!-- STATUS -->
                    <td>
                      <span :class="statusClass(project.status)">
                        {{ project.status }}
                      </span>
                    </td>

                    <!-- PROGRESS -->
                    <td>
                      <div class="progress project-progress">
                        <div
                          class="progress-bar"
                          :style="{ width: (project.progress || 0) + '%' }"
                        ></div>
                      </div>
                      <small class="text-muted">
                        {{ project.progress || 0 }}%
                      </small>
                    </td>

                    <!-- ACTION -->
                    <td>
                      <div class="btn-group">
                        <button
                          class="btn btn-sm btn-primary dropdown-toggle"
                          data-bs-toggle="dropdown"
                          style="background: darkgreen; border-color: darkgreen;"
                        >
                          Action
                        </button>

                        <div class="dropdown-menu">
                          <a class="dropdown-item" @click="viewProject(project)">View</a>
                          <a class="dropdown-item" @click="editProject(project)">Edit</a>
                          <a
                            class="dropdown-item text-success"
                            @click="$router.push(`/projects/${project.id}/progress`)"
                            >
                            Add Progress
                          </a>
                          <a class="dropdown-item text-danger" @click="deleteProject(project.id)">Delete</a>
                        </div>
                      </div>
                    </td>

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
  components: { Master },

  data() {
    return {
      projects: [],
      initializing: true,
      filterStatus: "all"
    };
  },

  methods: {

    loadProjects() {
      this.initializing = true;

      axios.get("/api/projects", {
        params: {
          status: this.filterStatus === "all" ? null : this.filterStatus
        }
      })
      .then(res => {
        this.projects = res.data.data;
      })
      .finally(() => {
        this.initializing = false;
      });
    },

    addProject() {
      this.$router.push("/projects/create");
    },

    viewProject(p) {
      this.$router.push(`/projects/${p.id}`);
    },

    editProject(p) {
      this.$router.push(`/projects/${p.id}/edit`);
    },

    deleteProject(id) {
      axios.delete(`/api/projects/${id}`)
        .then(() => this.loadProjects());
    },

    getInitials(title) {
      return title?.split(" ").map(w => w[0]).join("").toUpperCase();
    },

    typeClass(type) {
      return {
        "badge bg-primary": type === "business",
        "badge bg-success": type === "personal",
        "badge bg-warning": type === "asset",
        "badge bg-info": type === "training"
      };
    },

    statusClass(status) {
      return {
        "badge bg-success": status === "completed",
        "badge bg-warning": status === "active",
        "badge bg-danger": status === "blocked",
        "badge bg-secondary": status === "draft"
      };
    }

  },

  mounted() {
    this.loadProjects();
  }
};
</script>

<style scoped>
/* =========================
   PROJECT TABLE STYLING
========================= */

.project-thumb {
  width: 45px;
  height: 45px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #e0e0e0;
}

.project-placeholder {
  width: 45px;
  height: 45px;
  border-radius: 10px;
  background: #e9ecef;
  display: flex;
  align-items: center;
  justify-content: center;
  font-weight: bold;
  color: #555;
}

.project-progress {
  height: 6px;
  border-radius: 10px;
  background: #e9ecef;
  overflow: hidden;
}

.project-progress .progress-bar {
  background: linear-gradient(90deg, #198754, #20c997);
  border-radius: 10px;
}
</style>