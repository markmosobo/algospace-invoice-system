<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="card-body pb-0">

              <h5 class="card-title">
                Enrollments <span>| Course Registrations</span>
              </h5>

              <div class="mb-3">
                <button
                  class="btn btn-sm btn-primary rounded-pill"
                  @click="openEnrollmentModal"
                >
                  New Enrollment
                </button>
              </div>

              <!-- TABLE -->
              <table id="EnrollmentTable" class="table table-borderless">
                <thead>
                  <tr>
                    <th>Student</th>
                    <th>Course</th>
                    <th>Tier</th>
                    <th>Status</th>
                    <th>Enrolled At</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <tbody>
                  <!-- LOADING ROW -->
                  <tr v-if="loading">
                    <td colspan="6" class="text-center">
                      <div class="spinner-border text-success"></div>
                    </td>
                  </tr>

                  <!-- DATA ROWS -->
                  <tr v-for="e in enrollments" :key="e.id">

                    <td>{{ e.customer?.name }}</td>

                    <td>{{ e.service?.name }}</td>

                    <td>
                      <span class="badge bg-info">
                        {{ e.service?.tier || 'basic' }}
                      </span>
                    </td>

                    <td>
                      <span
                        class="badge"
                        :class="{
                          'bg-warning': e.status === 'pending',
                          'bg-success': e.status === 'active',
                          'bg-secondary': e.status === 'completed'
                        }"
                      >
                        {{ e.status }}
                      </span>
                    </td>

                    <td>{{ formatDate(e.enrolled_at) }}</td>

                    <td>
                      <div class="btn-group">
                        <button
                          class="btn btn-sm btn-primary dropdown-toggle"
                          data-bs-toggle="dropdown"
                        >
                          Action
                        </button>

                        <div class="dropdown-menu">
                          <a class="dropdown-item" href="#" @click.prevent="activate(e)">
                            Activate
                          </a>

                          <a class="dropdown-item" href="#" @click.prevent="complete(e)">
                            Complete
                          </a>

                          <div class="dropdown-divider"></div>

                          <a class="dropdown-item text-danger" href="#" @click.prevent="remove(e)">
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

        <!-- MODAL -->
        <div class="modal fade" id="enrollmentModal" tabindex="-1">
          <div class="modal-dialog">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">New Enrollment</h5>
                <button class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">

                <label class="form-label">Student</label>
                <select v-model="enrollForm.customer_id" class="form-select">
                  <option value="">Select Student</option>
                  <option v-for="c in customers" :key="c.id" :value="c.id">
                    {{ c.name }} - {{ c.phone }}
                  </option>
                </select>

                <label class="form-label mt-2">Course</label>
                <select v-model="enrollForm.service_id" class="form-select">
                  <option value="">Select Course</option>
                  <option v-for="s in courses" :key="s.id" :value="s.id">
                    {{ s.name }} ({{ s.tier || 'basic' }}) - KES {{ s.price }}
                  </option>
                </select>

              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                  Close
                </button>

                <button class="btn btn-success" @click="createEnrollment">
                  Enroll Student
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
      enrollments: [],
      customers: [],
      courses: [],
      loading: true,

      enrollForm: {
        customer_id: null,
        service_id: null
      }
    };
  },

  methods: {

    async loadData() {
    this.loading = true;

    const res = await axios.get("/api/enrollments");

    this.enrollments = res.data.enrollments || [];
    this.customers = res.data.customers || [];
    this.courses = res.data.courses || [];

    this.loading = false;

    setTimeout(() => {
        if ($.fn.DataTable.isDataTable("#EnrollmentTable")) {
        $("#EnrollmentTable").DataTable().destroy();
        }

        $("#EnrollmentTable").DataTable();
    }, 300);
    },

    openEnrollmentModal() {
      this.enrollForm = { customer_id: null, service_id: null };

      new bootstrap.Modal(
        document.getElementById("enrollmentModal")
      ).show();
    },

    createEnrollment() {
      axios.post("/api/enrollments", this.enrollForm)
        .then(res => {

          this.enrollments.unshift(res.data.enrollment);

          toast.fire({
            icon: "success",
            title: "Student enrolled successfully"
          });

          bootstrap.Modal.getInstance(
            document.getElementById("enrollmentModal")
          ).hide();

          this.loadData();

        })
        .catch(() => {
          toast.fire({
            icon: "error",
            title: "Failed to enroll student"
          });
        });
    },

    activate(e) {
      axios.patch(`/api/enrollments/${e.id}/activate`)
        .then(() => {
          e.status = "active";
          toast.fire({ icon: "success", title: "Activated" });
        });
    },

    complete(e) {
      axios.patch(`/api/enrollments/${e.id}/complete`)
        .then(() => {
          e.status = "completed";
          toast.fire({ icon: "success", title: "Completed" });
        });
    },

    remove(e) {
      Swal.fire({
        title: "Delete enrollment?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Yes delete"
      }).then(result => {
        if (!result.isConfirmed) return;

        axios.delete(`/api/enrollments/${e.id}`)
          .then(() => {
            this.enrollments = this.enrollments.filter(x => x.id !== e.id);

            toast.fire({
              icon: "success",
              title: "Deleted"
            });
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
    this.loadData();
  }
};
</script>

<style scoped>
select.form-select {
  max-height: 160px;
}
</style>