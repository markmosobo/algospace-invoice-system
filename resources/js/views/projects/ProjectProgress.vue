<template>
  <Master>
    <section class="section dashboard">

      <!-- PROJECT HEADER -->
      <div class="card mb-3">
        <div class="card-body d-flex align-items-center gap-3">

          <img
            v-if="project.cover_image"
            :src="'/storage/' + project.cover_image"
            class="project-cover"
          />

          <div>
            <h5 class="mb-1">{{ project.title }}</h5>
            <small class="text-muted">{{ project.description }}</small>

            <div class="mt-2">

              <div class="d-flex justify-content-between">
                <small class="text-muted">
                  Stage: {{ project.current_stage }}
                </small>

                <small class="text-muted">
                  {{ project.progress || 0 }}%
                </small>
              </div>

              <div class="progress project-progress">
                <div
                  class="progress-bar"
                  :style="{ width: (project.progress || 0) + '%' }"
                ></div>
              </div>

            </div>
          </div>

          <div class="ms-auto">
            <button
              class="btn btn-sm btn-success rounded-pill"
              @click="openModal"
            >
              Add Progress
            </button>
          </div>

        </div>
      </div>

      <!-- PROGRESS BY STAGE -->
      <div v-for="stage in STAGES" :key="stage.key" class="mb-4">

        <h6 class="text-uppercase text-muted mb-2">
          {{ stage.label }}
        </h6>

        <div class="row">
          <div
            class="col-md-4"
            v-for="p in groupedProgress[stage.key]"
            :key="p.id"
          >
            <div class="card mb-3">

              <img
                v-if="p.file_path"
                :src="'/storage/' + p.file_path"
                class="progress-img"
                @click="openImagePreview(p)"
              />

              <div class="card-body">
                <p class="mb-1">{{ p.notes }}</p>
                <small class="text-muted">
                  <span class="text-success">
                    ● {{ p.stage }}
                  </span>
                  {{ formatDate(p.created_at) }}
                </small>
              </div>

            </div>
          </div>
        </div>

      </div>

      <!-- 🔥 MODAL -->
      <div v-if="showModal" class="modal-overlay">

        <div class="modal-box">

          <div class="modal-header">
            <h5>Add Progress Update</h5>
            <button class="btn-close" @click="closeModal">×</button>
          </div>

          <div class="modal-body">

            <!-- STAGE SELECT -->
            <select
              class="form-control mb-2"
              v-model="form.stage"
            >
              <option disabled value="">Select stage</option>
              <option
                v-for="s in STAGES"
                :key="s.key"
                :value="s.key"
              >
                {{ s.label }}
              </option>
            </select>

            <textarea
              v-model="form.note"
              class="form-control mb-2"
              placeholder="Describe progress..."
            ></textarea>

            <input
              type="file"
              class="form-control mb-2"
              multiple
              @change="handleFiles"
            />

          </div>

          <div class="modal-footer">
            <button class="btn btn-secondary" @click="closeModal">
              Cancel
            </button>

            <button class="btn btn-success" @click="submitProgress">
              Save Progress
            </button>
          </div>

        </div>

      </div>

    <!-- IMAGE PREVIEW MODAL -->
    <div
      class="modal fade"
      id="ImagePreviewModal"
      tabindex="-1"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">Project Image</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
            ></button>
          </div>

          <div class="modal-body">

            <!-- IMAGE -->
            <div class="text-center mb-3">
              <img
                :src="previewImage"
                class="img-fluid rounded"
                style="max-height: 65vh;"
              />
            </div>

            <!-- META INFO -->
            <div v-if="previewProgress" class="px-2">

              <p class="mb-1">
                <strong>Notes:</strong><br>
                {{ previewProgress.notes || '—' }}
              </p>

              <div class="d-flex justify-content-between align-items-center mt-2">
                <span class="badge bg-success">
                  {{ previewProgress.stage }}
                </span>

                <small class="text-muted">
                  {{ formatDate(previewProgress.created_at) }}
                </small>
              </div>

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
  components: { Master },

  data() {
    return {
      project: {},
      progress: [],
      showModal: false,
      previewImage: null,
      previewProgress: null,

      STAGES: [
        { key: "ideation", label: "Ideation" },
        { key: "planning", label: "Planning" },
        { key: "setup", label: "Setup / Mobilization" },
        { key: "execution", label: "Execution" },
        { key: "refinement", label: "Refinement" },
        { key: "completion", label: "Completion" },
        { key: "maintenance", label: "Review / Maintenance" }
      ],

      form: {
        note: "",
        progress_increment: null,
        images: [],
        stage: "",
      }
    };
  },

  computed: {
    groupedProgress() {
      const groups = {};

      // Initialize empty arrays
      this.STAGES.forEach(s => {
        groups[s.key] = [];
      });

      // Group progress entries
      this.progress.forEach(p => {
        const stage = p.stage || "ideation";
        if (!groups[stage]) groups[stage] = [];
        groups[stage].push(p);
      });

      return groups;
    }
  },

  methods: {
    openImagePreview(p) {
      this.previewProgress = p;
      this.previewImage = p.file_path
        ? '/storage/' + p.file_path
        : null;

      const modal = new bootstrap.Modal(
        document.getElementById('ImagePreviewModal')
      );

      modal.show();
    },
    load() {
      axios
        .get(`/api/projects/${this.$route.params.id}/progress`)
        .then(res => {
          this.project = res.data.project;
          this.progress = res.data.progress;
        });
    },

    openModal() {
      this.showModal = true;
    },

    closeModal() {
      this.showModal = false;
    },

    handleFiles(e) {
      this.form.images = e.target.files;
    },

    submitProgress() {
      const fd = new FormData();

      fd.append("notes", this.form.note || "");
      fd.append("progress_increment", this.form.progress_increment || 0);
      fd.append("stage", this.form.stage);

      for (let f of this.form.images) {
        fd.append("images[]", f);
      }

      axios
        .post(`/api/projects/${this.$route.params.id}/progress`, fd)
        .then(() => {
          this.form = {
            note: "",
            progress_increment: null,
            stage: "",
            images: []
          };

          this.closeModal();
          this.load();
        });
    },

    formatDate(d) {
      return new Intl.DateTimeFormat('en-US', {
        weekday: 'short',
        year: 'numeric',
        month: 'short',
        day: 'numeric'
      }).format(new Date(d));
    }
  },

  mounted() {
    this.load();
  }
};
</script>

<style scoped>
.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0,0,0,0.5);
  display: flex;
  align-items: center;
  justify-content: center;
  z-index: 9999;
}

.modal-box {
  background: #fff;
  width: 500px;
  border-radius: 12px;
  overflow: hidden;
}

.modal-header,
.modal-footer {
  padding: 12px;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.modal-body {
  padding: 12px;
}

.btn-close {
  background: none;
  border: none;
  font-size: 20px;
  cursor: pointer;
}

.progress-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.project-cover {
  width: 70px;
  height: 70px;
object-fit: cover;
  border-radius: 12px;
}
</style>