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
              <div class="progress project-progress">
                <div
                  class="progress-bar"
                  :style="{ width: (project.progress || 0) + '%' }"
                ></div>
              </div>
              <small>{{ project.progress || 0 }}% completed</small>
            </div>
          </div>

          <div class="ms-auto">
            <button
              class="btn btn-sm btn-success rounded-pill"
              @click="showForm = !showForm"
            >
              Add Progress
            </button>
          </div>

        </div>
      </div>

      <!-- ADD PROGRESS FORM -->
      <div v-if="showForm" class="card mb-3">
        <div class="card-body">

          <textarea
            v-model="form.note"
            class="form-control mb-2"
            placeholder="Describe progress made..."
          ></textarea>

          <input
            type="number"
            class="form-control mb-2"
            placeholder="Progress increment (%)"
            v-model="form.progress_increment"
          />

          <input
            type="file"
            class="form-control mb-2"
            multiple
            @change="handleFiles"
          />

          <button
            class="btn btn-success"
            @click="submitProgress"
          >
            Save Progress
          </button>

        </div>
      </div>

      <!-- PROGRESS HISTORY -->
      <div class="row">
        <div
          class="col-md-4"
          v-for="p in progress"
          :key="p.id"
        >
          <div class="card mb-3">
            <img
              v-if="p.image_path"
              :src="'/storage/' + p.image_path"
              class="progress-img"
            />

            <div class="card-body">
              <p class="mb-1">{{ p.note }}</p>
              <small class="text-muted">
                +{{ p.progress_increment || 0 }}% •
                {{ formatDate(p.created_at) }}
              </small>
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
      showForm: false,
      form: {
        note: "",
        progress_increment: null,
        images: []
      }
    };
  },

  methods: {
    load() {
      axios.get(`/api/projects/${this.$route.params.id}/progress`)
        .then(res => {
          this.project = res.data.project;
          this.progress = res.data.progress;
        });
    },

    handleFiles(e) {
      this.form.images = e.target.files;
    },

    submitProgress() {
      const fd = new FormData();
      fd.append("note", this.form.note);
      fd.append("progress_increment", this.form.progress_increment);

      for (let f of this.form.images) {
        fd.append("images[]", f);
      }

      axios.post(
        `/api/projects/${this.$route.params.id}/progress`,
        fd
      ).then(() => {
        this.form = { note: "", progress_increment: null, images: [] };
        this.showForm = false;
        this.load();
      });
    },

    formatDate(d) {
      return new Date(d).toLocaleDateString();
    }
  },

  mounted() {
    this.load();
  }
};
</script>

<style scoped>
.project-cover {
  width: 70px;
  height: 70px;
  object-fit: cover;
  border-radius: 12px;
}

.progress-img {
  width: 100%;
  height: 180px;
  object-fit: cover;
}

.project-progress {
  height: 6px;
  margin-top: 5px;
}
</style>