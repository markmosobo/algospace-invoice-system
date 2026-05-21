<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card">

            <div class="card-body">

              <h5 class="card-title">
                Create Project
                <span>| Add new personal / office / asset project</span>
              </h5>

              <form @submit.prevent="submit">

                <div class="row g-3">

                  <!-- TITLE -->
                  <div class="col-md-6">
                    <label class="form-label">Title *</label>
                    <input
                      v-model="form.title"
                      type="text"
                      class="form-control"
                      placeholder="e.g House Construction"
                    />
                  </div>

                  <!-- TYPE -->
                  <div class="col-md-6">
                    <label class="form-label">Type *</label>
                    <select v-model="form.type" class="form-select">
                      <option value="personal">Personal</option>
                      <option value="business">Business</option>
                      <option value="asset">Asset</option>
                      <option value="training">Training</option>
                    </select>
                  </div>

                  <!-- STATUS -->
                  <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-select">
                      <option value="draft">Draft</option>
                      <option value="active">Active</option>
                      <option value="blocked">Blocked</option>
                      <option value="milestone">Milestone</option>
                    </select>
                  </div>


                  <!-- DESCRIPTION -->
                  <div class="col-12">
                    <label class="form-label">Description</label>
                    <textarea
                      v-model="form.description"
                      class="form-control"
                      rows="3"
                    ></textarea>
                  </div>

                  <!-- DATES -->
                  <div class="col-md-4">
                    <label class="form-label">Start Date</label>
                    <input v-model="form.start_date" type="date" class="form-control" />
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">End Date</label>
                    <input v-model="form.end_date" type="date" class="form-control" />
                  </div>

                  <div class="col-md-4">
                    <label class="form-label">Due Date</label>
                    <input v-model="form.due_date" type="date" class="form-control" />
                  </div>

                  <!-- PRIORITY -->
                  <div class="col-md-6">
                    <label class="form-label">Priority (1-5)</label>
                    <input
                      v-model="form.priority"
                      type="number"
                      min="1"
                      max="5"
                      class="form-control"
                    />
                  </div>

                  <!-- BLOCKER -->
                  <div class="col-md-6">
                    <label class="form-label">Blocker</label>
                    <input
                      v-model="form.blocker"
                      type="text"
                      class="form-control"
                      placeholder="e.g Waiting driving course enrollment"
                    />
                  </div>

                  <!-- COVER IMAGE -->
                  <div class="col-12">
                    <label class="form-label">Cover Image</label>
                    <input
                      type="file"
                      class="form-control"
                      @change="handleImage"
                    />
                  </div>

                  <!-- PREVIEW -->
                  <div class="col-12" v-if="preview">
                    <img :src="preview" class="preview-img" />
                  </div>

                </div>

                <!-- BUTTONS -->
                <div class="mt-4 d-flex gap-2">

                  <button
                    type="submit"
                    class="btn btn-success"
                    style="background: darkgreen; border-color: darkgreen;"
                  >
                    Save Project
                  </button>

                  <button
                    type="button"
                    class="btn btn-secondary"
                    @click="$router.push('/projects')"
                  >
                    Cancel
                  </button>

                </div>

              </form>

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
  timer: 2500,
  timerProgressBar: true
});

export default {
  components: { Master },

  data() {
    return {
      form: {
        title: "",
        description: "",
        type: "personal",
        status: "draft",
        progress: 0,
        start_date: "",
        end_date: "",
        due_date: "",
        priority: 3,
        blocker: ""
      },

      image: null,
      preview: null
    };
  },

  methods: {

    handleImage(e) {
      const file = e.target.files[0];
      this.image = file;
      this.preview = URL.createObjectURL(file);
    },

    async submit() {
      let formData = new FormData();

      Object.keys(this.form).forEach(key => {
        formData.append(key, this.form[key]);
      });

      if (this.image) {
        formData.append("cover_image", this.image);
      }

      try {
        await axios.post("/api/projects", formData, {
          headers: {
            "Content-Type": "multipart/form-data"
          }
        });

        toast.fire({
          icon: "success",
          title: "Project created successfully"
        });

        // small delay so user sees toast
        setTimeout(() => {
          this.$router.push("/projects");
        }, 800);

      } catch (error) {
        toast.fire({
          icon: "error",
          title: "Failed to create project"
        });
      }
    }

  }
};
</script>

<style scoped>
.preview-img {
  width: 200px;
  height: 140px;
  object-fit: cover;
  border-radius: 10px;
  border: 1px solid #ddd;
  margin-top: 10px;
}
</style>