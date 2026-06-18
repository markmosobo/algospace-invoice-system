<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="card-body pb-0">

              <h5 class="card-title">
                Courses <span>| Training Programs</span>
              </h5>

              <!-- <div class="mb-3">
                <button
                  class="btn btn-sm btn-primary rounded-pill"
                  @click="openCourseModal"
                >
                  New Course
                </button>
              </div> -->

              <!-- TABLE -->
              <table id="CoursesTable" class="table table-borderless">
                <thead>
                  <tr>
                    <th>Course Name</th>
                    <th>Tier</th>
                    <th>Price (KES)</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <!-- LOADING -->
                  <tr v-if="loading">
                    <td colspan="6" class="text-center">
                      <div class="spinner-border text-success"></div>
                    </td>
                  </tr>

                  <!-- DATA -->
                  <tr v-for="c in courses" :key="c.id">
                    <td>{{ c.name }}</td>

                    <td>
                      <span class="badge bg-info">
                        {{ c.tier || 'basic' }}
                      </span>
                    </td>

                    <td>{{ c.price.toLocaleString() }}</td>

                    <td>
                      <span
                        class="badge"
                        :class="{
                          'bg-success': c.status === 'active',
                          'bg-secondary': c.status === 'inactive'
                        }"
                      >
                        {{ c.status }}
                      </span>
                    </td>

                    <td>{{ formatDate(c.created_at) }}</td>

                    <td>
                      <div class="btn-group">
                        <button
                          class="btn btn-sm btn-primary dropdown-toggle"
                          data-bs-toggle="dropdown"
                        >
                          Action
                        </button>

                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#" @click.prevent="editCourse(c)">
                            Edit
                          </a>

                          <div class="dropdown-divider"></div>

                          <a class="dropdown-item text-danger" href="#" @click.prevent="removeCourse(c)">
                            Delete
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>

                </tbody>
              </table>

            </div>
          </div>
        </div>

        <!-- COURSE MODAL -->
        <div class="modal fade" id="courseModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">
                  {{ courseForm.id ? 'Edit Course' : 'New Course' }}
                </h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">

                <label class="form-label">Course Name</label>
                <input v-model="courseForm.name" class="form-control" />

                <label class="form-label mt-2">Tier</label>
                <select v-model="courseForm.tier" class="form-select">
                  <option value="basic">Basic</option>
                  <option value="intermediate">Intermediate</option>
                  <option value="advanced">Advanced</option>
                </select>

                <label class="form-label mt-2">Price (KES)</label>
                <input
                  type="number"
                  v-model="courseForm.price"
                  class="form-control"
                />

                <label class="form-label mt-2">Status</label>
                <select v-model="courseForm.status" class="form-select">
                  <option value="active">Active</option>
                  <option value="inactive">Inactive</option>
                </select>

              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>

                <button class="btn btn-success" @click="saveCourse">
                  Save Course
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
import Swal from "sweetalert2";
import $ from "jquery";
import "datatables.net-dt";

const toast = Swal.mixin({
  toast: true,
  position: "top-end",
  timer: 3000,
  showConfirmButton: false
});

export default {
  components: { Master },

  data() {
    return {
      courses: [],
      loading: true,

      courseForm: {
        id: null,
        name: "",
        tier: "basic",
        price: "",
        status: "active"
      }
    };
  },

  methods: {

    async loadCourses() {
      this.loading = true;

      const res = await axios.get("/api/services/courses");
      console.log("Courses loaded:", res);
      this.courses = res.data.courses || [];

      this.loading = false;

      setTimeout(() => {
        if ($.fn.DataTable.isDataTable("#CoursesTable")) {
          $("#CoursesTable").DataTable().destroy();
        }
        $("#CoursesTable").DataTable();
      }, 300);
    },

    openCourseModal() {
      this.courseForm = {
        id: null,
        name: "",
        tier: "basic",
        price: "",
        status: "active"
      };

      new bootstrap.Modal(
        document.getElementById("courseModal")
      ).show();
    },

    editCourse(course) {
      this.courseForm = { ...course };

      new bootstrap.Modal(
        document.getElementById("courseModal")
      ).show();
    },

    saveCourse() {
      const req = this.courseForm.id
        ? axios.put(`/api/courses/${this.courseForm.id}`, this.courseForm)
        : axios.post("/api/courses", this.courseForm);

      req.then(() => {
        toast.fire({ icon: "success", title: "Course saved" });
        bootstrap.Modal.getInstance(
          document.getElementById("courseModal")
        ).hide();
        this.loadCourses();
      }).catch(() => {
        toast.fire({ icon: "error", title: "Failed to save course" });
      });
    },

    removeCourse(course) {
      Swal.fire({
        title: "Delete this course?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes delete"
      }).then(result => {
        if (!result.isConfirmed) return;

        axios.delete(`/api/courses/${course.id}`)
          .then(() => {
            this.courses = this.courses.filter(c => c.id !== course.id);
            toast.fire({ icon: "success", title: "Deleted" });
          });
      });
    },

    formatDate(date) {
      if (!date) return "N/A";
      const d = new Date(date);
      return `${d.getDate()}/${d.getMonth() + 1}/${d.getFullYear()}`;
    }

  },

  mounted() {
    this.loadCourses();
  }
};
</script>

<style scoped>
select.form-select {
  max-height: 160px;
}
</style>