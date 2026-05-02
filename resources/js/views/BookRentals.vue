<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <!-- Borrow Records Table -->
        <div class="col-12">
          <div class="card overflow-auto">

            <div class="card-body pb-0">
              <h5 class="card-title">Borrow Records <span>| Track Book Rentals</span></h5>

              <div class="row mb-3">
                <div class="col d-flex">
                  <button class="btn btn-sm btn-primary rounded-pill"
                          style="background-color: darkgreen; border-color: darkgreen;"
                          @click="openBorrowModal">
                    Borrow Book
                  </button>
                </div>
              </div>

              <table id="BorrowTable" class="table table-borderless align-middle">
                <thead>
                  <tr>
                    <th>Book</th>
                    <th>User</th>
                    <th>Borrow Date</th>
                    <th>Expected Return</th>
                    <th>Return Date</th>
                    <th>Late Fee</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <!-- Spinner while loading -->
                <tbody v-if="initializing">
                  <tr>
                    <td colspan="8" class="text-center">
                      <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                      </div>
                    </td>
                  </tr>
                </tbody>

                <tbody v-else>
                  <tr v-for="record in borrowRecords" :key="record.id">
                    <td>{{ record.book.title }}</td>
                    <td>{{ record.user.name }}</td>
                    <td>{{ formatDate(record.borrow_date )}}</td>
                    <td>{{ formatDate(record.expected_return_date) }}</td>
                    <td>{{ formatDate(record.return_date) }}</td>
                    <td>{{ record.late_fee }}</td>
                    <td>
                      <span v-if="record.status==='borrowed'" class="badge bg-warning">Borrowed</span>
                      <span v-else-if="record.status==='returned'" class="badge bg-success">Returned</span>
                      <span v-else class="badge bg-danger">Overdue</span>
                    </td>
                    <td>
                      <div class="btn-group" role="group">
                        <button class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                                style="background-color: darkgreen; border-color: darkgreen;"
                                data-bs-toggle="dropdown">
                          Action
                        </button>
                        <div class="dropdown-menu">
                          <a @click.prevent="viewRecord(record)" class="dropdown-item">View</a>
                          <a v-if="record.status==='borrowed'" 
                             @click.prevent="returnBook(record)" class="dropdown-item">
                             Return
                          </a>
                          <a @click.prevent="deleteRecord(record.id)" class="dropdown-item">Delete</a>
                        </div>
                      </div>
                    </td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <!-- View Borrow Record Modal -->
        <div class="modal fade" id="viewBorrowModal" tabindex="-1">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Borrow Record Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body" v-if="selectedRecord">
                <p><strong>Book:</strong> {{ selectedRecord.book.title }}</p>
                <p><strong>User:</strong> {{ selectedRecord.user.name }}</p>
                <p><strong>Borrow Date:</strong> {{ formatDate(selectedRecord.borrow_date) }}</p>
                <p><strong>Expected Return:</strong> {{ formatDate(selectedRecord.expected_return_date) }}</p>
                <p><strong>Return Date:</strong> {{ formatDate(selectedRecord.return_date) }}</p>
                <p><strong>Late Fee:</strong> {{ selectedRecord.late_fee }}</p>
                <p><strong>Status:</strong> {{ selectedRecord.status }}</p>
              </div>
              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
              </div>
            </div>
          </div>
        </div>

        <!-- Borrow Book Modal -->
        <div class="modal fade" id="BorrowBookModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-lg">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Borrow Book</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>

              <div class="modal-body">
                <form class="row g-3">

                  <div class="col-md-6">
                    <label class="form-label">Select Book*</label>
                    <select class="form-select" v-model="borrowData.book_id">
                      <option value="">-- Choose a Book --</option>
                      <option v-for="book in availableBooks" :key="book.id" :value="book.id">
                        {{ book.title }} ({{ book.status }})
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Select User*</label>
                    <select class="form-select" v-model="borrowData.user_id">
                      <option value="">-- Choose a User --</option>
                      <option v-for="user in users" :key="user.id" :value="user.id">
                        {{ user.name }}
                      </option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Borrow Date</label>
                    <input type="date" class="form-control" v-model="borrowData.borrow_date">
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Expected Return Date</label>
                    <input type="date" class="form-control" v-model="borrowData.expected_return_date">
                  </div>

                </form>
              </div>

              <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button class="btn btn-success" style="background: darkgreen; border-color: darkgreen;"
                        @click="submitBorrow">
                  Borrow Book
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
import $ from "jquery";
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";

export default {
  components: { Master },
  data() {
    return {
      borrowRecords: [],
      selectedRecord: null,
      initializing: true,
      borrowData: {
        book_id: "",
        user_id: "",
        borrow_date: "",
        expected_return_date: ""
      },
      availableBooks: [],
      users: []
    };
  },
  methods: {
    formatDate(date) {
        if (!date) return 'N/A';

        return new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    },
    loadBorrowRecords() {
      this.initializing = true;
      axios.get('/api/borrow')
        .then(res => {
          this.borrowRecords = res.data;
          setTimeout(() => { $("#BorrowTable").DataTable(); }, 10);
        })
        .finally(() => { this.initializing = false; });
    },

    viewRecord(record) {
      this.selectedRecord = record;
      new bootstrap.Modal(document.getElementById('viewBorrowModal')).show();
    },

    returnBook(record) {
      axios.post(`/api/borrow/return/${record.id}`)
        .then(() => this.loadBorrowRecords());
    },

    deleteRecord(id) {
      if (!confirm('Delete this record?')) return;
      axios.delete(`/api/borrow/${id}`).then(() => this.loadBorrowRecords());
    },

    // Open Borrow Book Modal
    openBorrowModal() {
      this.borrowData = {
        book_id: "",
        user_id: "",
        borrow_date: new Date().toISOString().split("T")[0], // today
        expected_return_date: "" // optional
      };

      // Load books that are available
      axios.get("/api/books?status=available").then(res => {
        this.availableBooks = res.data;
      });

      // Load all users
      axios.get("/api/borrowers").then(res => {
        this.users = res.data;
      });

      const modal = new bootstrap.Modal(document.getElementById("BorrowBookModal"));
      modal.show();
    },

    // Submit borrow record
    submitBorrow() {
      if (!this.borrowData.book_id || !this.borrowData.user_id) {
        alert("Please select both a book and a user.");
        return;
      }

      axios.post("/api/borrow", this.borrowData)
        .then(() => {
          toast.fire("Success!", "Book borrowed successfully", "success");
          this.loadBorrowRecords();

          // Hide modal
          const modal = bootstrap.Modal.getInstance(document.getElementById("BorrowBookModal"));
          modal.hide();
        })
        .catch(err => {
          console.error(err);
          toast.fire("Error!", err.response?.data?.message || "Failed to borrow book", "error");
        });
    }
  },
  mounted() {
    this.loadBorrowRecords();
  }
};
</script>