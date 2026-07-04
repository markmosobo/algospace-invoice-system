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

              <button
                class="btn btn-sm btn-outline-danger"
                @click="downloadCoursesPdf"
              >
                <i class="bi bi-file-earmark-pdf"></i>
                Download PDF
              </button>

              <button
                class="btn btn-sm btn-outline-success ms-2"
                @click="sendCoursesWhatsapp"
              >
                <i class="bi bi-whatsapp"></i>
                Send via WhatsApp
              </button>

              <!-- TABLE -->
              <table id="CoursesTable" class="table table-borderless align-middle">
                <thead>
                  <tr>
                    <th>Course</th>
                    <th>Level</th>
                    <th>Schedule</th>
                    <th>Duration</th>
                    <th>Hours</th>
                    <th>Price (KES)</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <!-- LOADING -->
                  <tr v-if="loading">
                    <td colspan="8" class="text-center py-4">
                      <div class="spinner-border text-success"></div>
                    </td>
                  </tr>

                  <!-- NO DATA -->
                  <tr v-if="!loading && courses.length === 0">
                    <td colspan="8" class="text-center text-muted py-4">
                      No courses found
                    </td>
                  </tr>

                  <!-- DATA -->
                  <tr v-for="c in courses" :key="c.id">

                    <!-- COURSE NAME -->
                    <td>
                      <strong>{{ c.name }}</strong>
                      <br />
                      <small class="text-muted">{{ c.unit }}</small>
                    </td>

                    <!-- TIER -->
                    <td>
                      <span
                        class="badge text-capitalize"
                        :class="{
                          'bg-info': c.tier === 'basic',
                          'bg-warning': c.tier === 'intermediate',
                          'bg-danger': c.tier === 'advanced',
                          'bg-secondary': c.tier === 'refresher'
                        }"
                      >
                        {{ c.tier }}
                      </span>
                    </td>

                    <!-- SCHEDULE -->
                    <td class="text-capitalize">
                      {{ c.schedule_type }}
                    </td>

                    <!-- DURATION -->
                    <td>
                      {{ c.duration_units }} Saturdays
                    </td>

                    <!-- SESSION HOURS -->
                    <td>
                      {{ c.session_hours }} hrs
                    </td>

                    <!-- PRICE -->
                    <td>
                      {{ Number(c.price).toLocaleString() }}
                    </td>

                    <!-- STATUS -->
                    <td>
                      <span
                        class="badge"
                        :class="c.is_active ? 'bg-success' : 'bg-secondary'"
                      >
                        {{ c.is_active ? 'In-Store + Remote' : 'In-Store Only' }}
                      </span>
                    </td>

                    <!-- ACTION -->
                    <td>
                      <div class="btn-group">
                        <button
                          class="btn btn-sm btn-primary dropdown-toggle"
                          data-bs-toggle="dropdown"
                        >
                          Action
                        </button>

                        <div class="dropdown-menu">
                          <a
                            class="dropdown-item"
                            href="#"
                            @click.prevent="viewCourse(c)"
                          >
                            View
                          </a>
                          <a
                            class="dropdown-item"
                            href="#"
                            @click.prevent="editCourse(c)"
                          >
                            Edit
                          </a>

                          <div class="dropdown-divider"></div>

                          <a
                            class="dropdown-item text-danger"
                            href="#"
                            @click.prevent="removeCourse(c)"
                          >
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

        <div class="modal fade" id="viewCourseModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Course Details</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body" v-if="selectedCourse">

                <table class="table table-sm">
                  <tr><th>Name</th><td>{{ selectedCourse.name }}</td></tr>
                  <tr><th>Tier</th><td>{{ selectedCourse.tier }}</td></tr>
                  <tr><th>Schedule</th><td>{{ selectedCourse.schedule_type }}</td></tr>
                  <tr><th>Duration</th><td>{{ selectedCourse.duration_units }} Saturdays</td></tr>
                  <tr><th>Session Hours</th><td>{{ selectedCourse.session_hours }}</td></tr>
                  <tr><th>Price</th><td>KES {{ Number(selectedCourse.price).toLocaleString() }}</td></tr>
                  <tr><th>Status</th>
                    <td>
                      <span class="badge" :class="selectedCourse.is_active ? 'bg-success' : 'bg-secondary'">
                        {{ selectedCourse.is_active ? 'Active' : 'Inactive' }}
                      </span>
                    </td>
                  </tr>
                </table>

              </div>

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
      selectedCourse: null,

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
    viewCourse(course) {
      this.selectedCourse = course;

      new bootstrap.Modal(
        document.getElementById('viewCourseModal')
      ).show();
    },    
    downloadCoursesPdf() {
      window.open('/api/courses/pdf', '_blank');
    },
sendCoursesWhatsapp() {
  const pdfUrl = 'https://algospacecyber.co.ke/training-courses';

  const message = encodeURIComponent(
    `*Saturday Computer Classes – AlgoSpace Cyber*\n\n` +
    `View full course list here:\n` +
    `${pdfUrl}\n\n` +
    `*Timetable*\n` +
    `08:30 – 09:30  Session 1\n` +
    `09:30 – 10:30  Session 2\n` +
    `10:30 – 10:45  Break\n` +
    `10:45 – 11:45  Session 3\n` +
    `11:45 – 12:45  Session 4\n` +
    `12:45 – 13:00  Q&A / Payments\n\n` +
    `Location: AlgoSpace Cyber\n` +
    `Day: Every Saturday\n\n` +
    `Reply here to register or ask questions.`
  );

  window.open(`https://wa.me/?text=${message}`, '_blank');
},  
    async loadCourses() {
      this.loading = true;

      const res = await axios.get("/api/courses");
      console.log("Courses loaded:", res.data);
      this.courses = res.data.data || [];

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