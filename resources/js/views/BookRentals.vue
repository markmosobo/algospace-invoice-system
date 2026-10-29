
<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <!-- Borrow Records Table -->
        <div class="col-12">
          <div class="card overflow-auto">
            <div class="card-body pb-0">
              <h5 class="card-title">
                Borrow Records <span>| Track Book Rentals</span>
              </h5>

              <div class="row mb-3">
                <div class="col d-flex">
                  <button
                    type="button"
                    class="btn btn-sm btn-primary rounded-pill"
                    style="background-color: darkgreen; border-color: darkgreen;"
                    @click="openBorrowModal"
                  >
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
                    <td>{{ record.book?.title || 'Book unavailable' }}</td>
                    <td>{{ record.user?.name || 'User unavailable' }}</td>
                    <td>{{ formatDate(record.borrow_date) }}</td>
                    <td>{{ formatDate(record.expected_return_date) }}</td>
                    <td>{{ formatDate(record.return_date) }}</td>
                    <td>{{ record.late_fee ?? 0 }}</td>
                    <td>
                      <span
                        v-if="record.status === 'borrowed' && isOverdue(record)"
                        class="badge bg-danger"
                      >
                        Overdue
                      </span>

                      <span
                        v-else-if="record.status === 'borrowed'"
                        class="badge bg-warning text-dark"
                      >
                        Borrowed
                      </span>

                      <span
                        v-else-if="record.status === 'returned'"
                        class="badge bg-success"
                      >
                        Returned
                      </span>

                      <span v-else class="badge bg-secondary">
                        {{ record.status }}
                      </span>
                    </td>

                    <td>
                      <div class="btn-group" role="group">
                        <button
                          type="button"
                          class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                          style="background-color: darkgreen; border-color: darkgreen;"
                          data-bs-toggle="dropdown"
                        >
                          Action
                        </button>

                        <div class="dropdown-menu">
                          <a
                            href="#"
                            class="dropdown-item"
                            @click.prevent="viewRecord(record)"
                          >
                            View
                          </a>

                          <a
                            v-if="record.status === 'borrowed'"
                            href="#"
                            class="dropdown-item"
                            @click.prevent="returnBook(record)"
                          >
                            Return
                          </a>

                          <a
                            href="#"
                            class="dropdown-item text-danger"
                            @click.prevent="deleteRecord(record.id)"
                          >
                            Delete
                          </a>
                        </div>
                      </div>
                    </td>
                  </tr>

                  <tr v-if="!borrowRecords.length">
                    <td colspan="8" class="text-center text-muted">
                      No borrowing records found.
                    </td>
                  </tr>
                </tbody>
              </table>

            </div>
          </div>
        </div>

        <!-- View Borrow Record Modal -->
        <div
          class="modal fade"
          id="viewBorrowModal"
          tabindex="-1"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-lg">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Borrow Record Details</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                ></button>
              </div>

              <div class="modal-body" v-if="selectedRecord">
                <p>
                  <strong>Book:</strong>
                  {{ selectedRecord.book?.title || 'Book unavailable' }}
                </p>

                <p>
                  <strong>Borrower:</strong>
                  {{ selectedRecord.user?.name || 'User unavailable' }}
                </p>

                <p>
                  <strong>Borrow Date:</strong>
                  {{ formatDate(selectedRecord.borrow_date) }}
                </p>

                <p>
                  <strong>Expected Return:</strong>
                  {{ formatDate(selectedRecord.expected_return_date) }}
                </p>

                <p>
                  <strong>Return Date:</strong>
                  {{ formatDate(selectedRecord.return_date) }}
                </p>

                <p>
                  <strong>Late Fee:</strong>
                  KSh {{ selectedRecord.late_fee ?? 0 }}
                </p>

                <p>
                  <strong>Status:</strong>
                  {{ selectedRecord.status }}
                </p>
              </div>

              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
                >
                  Close
                </button>
              </div>
            </div>
          </div>
        </div>

        <!-- Borrow Book Modal -->
        <div
          class="modal fade"
          id="BorrowBookModal"
          tabindex="-1"
          aria-hidden="true"
        >
          <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">

              <div class="modal-header">
                <h5 class="modal-title">Borrow Book</h5>
                <button
                  type="button"
                  class="btn-close"
                  data-bs-dismiss="modal"
                ></button>
              </div>

              <div class="modal-body">
                <form class="row g-3" @submit.prevent="submitBorrow">

                  <!-- Searchable Book -->
                  <div class="col-md-6 position-relative">
                    <label class="form-label">Search Book*</label>

                    <input
                      type="text"
                      class="form-control"
                      v-model="bookSearch"
                      placeholder="Type title, author or barcode..."
                      autocomplete="off"
                      required
                      @focus="showBookResults = true"
                      @input="onBookSearch"
                    />

                    <div
                      v-if="showBookResults && filteredAvailableBooks.length"
                      class="list-group position-absolute w-100 shadow"
                      style="z-index: 1056; max-height: 220px; overflow-y: auto;"
                    >
                      <button
                        v-for="book in filteredAvailableBooks"
                        :key="book.id"
                        type="button"
                        class="list-group-item list-group-item-action"
                        @click="selectBook(book)"
                      >
                        <strong>{{ book.title }}</strong>

                        <small class="d-block text-muted">
                          {{ book.author || 'Unknown author' }}
                          ·
                          {{ book.available_copies ?? (book.status === 'available' ? 1 : 0) }}
                          available
                        </small>
                      </button>
                    </div>

                    <div
                      v-if="borrowData.book_id"
                      class="form-text text-success"
                    >
                      <i class="bi bi-check-circle"></i>
                      Selected: {{ bookSearch }}
                      <button
                        type="button"
                        class="btn btn-link btn-sm p-0 ms-1"
                        @click="clearBookSelection"
                      >
                        Change
                      </button>
                    </div>

                    <small
                      v-else-if="showBookResults && bookSearch && !filteredAvailableBooks.length"
                      class="d-block text-muted mt-1"
                    >
                      No available books match your search.
                    </small>
                  </div>

                  <!-- Searchable Borrower -->
                  <div class="col-md-6 position-relative">
                    <label class="form-label">Search Borrower*</label>

                    <input
                      type="text"
                      class="form-control"
                      v-model="userSearch"
                      placeholder="Type borrower's name or email..."
                      autocomplete="off"
                      required
                      @focus="showUserResults = true"
                      @input="onUserSearch"
                    />

                    <div
                      v-if="showUserResults && filteredUsers.length"
                      class="list-group position-absolute w-100 shadow"
                      style="z-index: 1056; max-height: 220px; overflow-y: auto;"
                    >
                      <button
                        v-for="user in filteredUsers"
                        :key="user.id"
                        type="button"
                        class="list-group-item list-group-item-action"
                        @click="selectUser(user)"
                      >
                        <strong>{{ user.name }}</strong>

                        <small
                          v-if="user.email"
                          class="d-block text-muted"
                        >
                          {{ user.email }}
                        </small>
                      </button>
                    </div>

                    <div
                      v-if="borrowData.user_id"
                      class="form-text text-success"
                    >
                      <i class="bi bi-check-circle"></i>
                      Selected: {{ userSearch }}
                      <button
                        type="button"
                        class="btn btn-link btn-sm p-0 ms-1"
                        @click="clearUserSelection"
                      >
                        Change
                      </button>
                    </div>

                    <small
                      v-else-if="showUserResults && userSearch && !filteredUsers.length"
                      class="d-block text-muted mt-1"
                    >
                      No matching borrowers found.
                    </small>
                  </div>

                  <!-- Borrow Date -->
                  <div class="col-md-6">
                    <label class="form-label">Borrow Date*</label>

                    <input
                      type="date"
                      class="form-control"
                      v-model="borrowData.borrow_date"
                      :min="minimumBorrowDate"
                      required
                    />
                  </div>

                  <!-- Expected Return Date -->
                  <div class="col-md-6">
                    <label class="form-label">Expected Return Date</label>

                    <input
                      type="date"
                      class="form-control"
                      :value="borrowData.expected_return_date"
                      readonly
                    />

                    <small class="text-muted">
                      Automatically calculated as 14 days after the borrow date.
                    </small>
                  </div>

                  <div class="col-12">
                    <div class="alert alert-info mb-0">
                      <i class="bi bi-info-circle"></i>
                      Confirm the selected book and borrower before recording
                      this loan.
                    </div>
                  </div>

                </form>
              </div>

              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
                >
                  Close
                </button>

                <button
                  type="button"
                  class="btn btn-success"
                  style="background: darkgreen; border-color: darkgreen;"
                  :disabled="submittingBorrow"
                  @click="submitBorrow"
                >
                  <span
                    v-if="submittingBorrow"
                    class="spinner-border spinner-border-sm me-1"
                  ></span>
                  {{ submittingBorrow ? 'Processing...' : 'Borrow Book' }}
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
      submittingBorrow: false,

      borrowData: {
        book_id: "",
        user_id: "",
        borrow_date: "",
        expected_return_date: ""
      },

      availableBooks: [],
      users: [],

      bookSearch: "",
      userSearch: "",
      showBookResults: false,
      showUserResults: false,

      dataTable: null
    };
  },

  computed: {
    minimumBorrowDate() {
      return this.getLocalToday();
    },

    filteredAvailableBooks() {
      const query = this.bookSearch.trim().toLowerCase();

      return this.availableBooks.filter(book => {
        const available = Number(
          book.available_copies ??
          (book.status === "available" ? 1 : 0)
        );

        const matches = [
          book.title,
          book.author,
          book.barcode
        ].some(value =>
          String(value ?? "").toLowerCase().includes(query)
        );

        return available > 0 && matches;
      });
    },

    filteredUsers() {
      const query = this.userSearch.trim().toLowerCase();

      return this.users.filter(user =>
        [user.name, user.email].some(value =>
          String(value ?? "").toLowerCase().includes(query)
        )
      );
    }
  },

  watch: {
    "borrowData.borrow_date"(newDate) {
      this.borrowData.expected_return_date =
        this.calculateExpectedReturnDate(newDate);
    }
  },

  methods: {
    getLocalToday() {
      const today = new Date();

      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, "0");
      const day = String(today.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },

    calculateExpectedReturnDate(borrowDate) {
      if (!borrowDate) return "";

      // Use local calendar arithmetic to avoid UTC date shifts.
      const date = new Date(`${borrowDate}T00:00:00`);

      if (Number.isNaN(date.getTime())) return "";

      date.setDate(date.getDate() + 14);

      const year = date.getFullYear();
      const month = String(date.getMonth() + 1).padStart(2, "0");
      const day = String(date.getDate()).padStart(2, "0");

      return `${year}-${month}-${day}`;
    },

    formatDate(date) {
      if (!date) return "N/A";

      // Avoid timezone shifts when formatting date-only database values.
      const dateString = String(date).slice(0, 10);
      const parsed = new Date(`${dateString}T00:00:00`);

      if (Number.isNaN(parsed.getTime())) return "N/A";

      return parsed.toLocaleDateString("en-GB", {
        day: "2-digit",
        month: "short",
        year: "numeric"
      });
    },

    isOverdue(record) {
      if (
        record.status !== "borrowed" ||
        !record.expected_return_date
      ) {
        return false;
      }

      // Compare date-only values, avoiding timezone-related errors.
      return record.expected_return_date.slice(0, 10) < this.getLocalToday();
    },

    destroyDataTable() {
      if ($.fn.DataTable.isDataTable("#BorrowTable")) {
        $("#BorrowTable").DataTable().destroy();
      }

      this.dataTable = null;
    },

    async loadBorrowRecords() {
      this.initializing = true;
      this.destroyDataTable();

      try {
        const res = await axios.get("/api/borrow");
        this.borrowRecords = res.data;
      } catch (err) {
        console.error("Failed to load borrowing records:", err);

        toast.fire(
          "Error!",
          "Failed to load borrowing records.",
          "error"
        );
      } finally {
        this.initializing = false;

        this.$nextTick(() => {
          if (!this.dataTable && this.$el.querySelector("#BorrowTable")) {
            this.dataTable = $("#BorrowTable").DataTable({
              pageLength: 10,
              order: [[2, "desc"]],
              destroy: true
            });
          }
        });
      }
    },

    viewRecord(record) {
      this.selectedRecord = record;

      bootstrap.Modal
        .getOrCreateInstance(document.getElementById("viewBorrowModal"))
        .show();
    },

    async returnBook(record) {
      const confirmed = window.confirm(
        `Record the return of "${record.book?.title || "this book"}"?`
      );

      if (!confirmed) return;

      try {
        await axios.post(`/api/borrow/return/${record.id}`);

        toast.fire(
          "Success!",
          "Book returned successfully.",
          "success"
        );

        await this.loadBorrowRecords();
      } catch (err) {
        console.error("Failed to return book:", err);

        toast.fire(
          "Error!",
          err.response?.data?.error ||
            err.response?.data?.message ||
            "Failed to return book.",
          "error"
        );
      }
    },

    async deleteRecord(id) {
      if (!window.confirm("Delete this borrowing record?")) return;

      try {
        await axios.delete(`/api/borrow/${id}`);

        toast.fire(
          "Success!",
          "Borrowing record deleted.",
          "success"
        );

        await this.loadBorrowRecords();
      } catch (err) {
        console.error("Failed to delete borrowing record:", err);

        toast.fire(
          "Error!",
          err.response?.data?.error ||
            err.response?.data?.message ||
            "Failed to delete borrowing record.",
          "error"
        );
      }
    },

    async openBorrowModal() {
      const today = this.getLocalToday();

      this.borrowData = {
        book_id: "",
        user_id: "",
        borrow_date: today,
        expected_return_date: this.calculateExpectedReturnDate(today)
      };

      this.bookSearch = "";
      this.userSearch = "";
      this.showBookResults = false;
      this.showUserResults = false;

      try {
        const [booksResponse, usersResponse] = await Promise.all([
          axios.get("/api/books"),
          axios.get("/api/borrowers")
        ]);

        this.availableBooks = Array.isArray(booksResponse.data)
          ? booksResponse.data
          : [];

        this.users = Array.isArray(usersResponse.data)
          ? usersResponse.data
          : [];

        bootstrap.Modal
          .getOrCreateInstance(document.getElementById("BorrowBookModal"))
          .show();
      } catch (err) {
        console.error("Failed to load books or borrowers:", err);

        toast.fire(
          "Error!",
          "Could not load books or borrowers. Please try again.",
          "error"
        );
      }
    },

    onBookSearch() {
      // Clear the selected ID when the librarian changes the search text.
      this.borrowData.book_id = "";
      this.showBookResults = true;
    },

    onUserSearch() {
      // Clear the selected ID when the librarian changes the search text.
      this.borrowData.user_id = "";
      this.showUserResults = true;
    },

    selectBook(book) {
      this.borrowData.book_id = book.id;
      this.bookSearch = book.title;
      this.showBookResults = false;
    },

    selectUser(user) {
      this.borrowData.user_id = user.id;
      this.userSearch = user.name;
      this.showUserResults = false;
    },

    clearBookSelection() {
      this.borrowData.book_id = "";
      this.bookSearch = "";
      this.showBookResults = true;
    },

    clearUserSelection() {
      this.borrowData.user_id = "";
      this.userSearch = "";
      this.showUserResults = true;
    },

    async submitBorrow() {
      if (this.submittingBorrow) return;

      if (!this.borrowData.book_id || !this.borrowData.user_id) {
        toast.fire(
          "Required!",
          "Please select both a book and a borrower from the search results.",
          "warning"
        );
        return;
      }

      if (!this.borrowData.borrow_date) {
        toast.fire(
          "Required!",
          "Please select a borrow date.",
          "warning"
        );
        return;
      }

      // Recalculate immediately before submission.
      this.borrowData.expected_return_date =
        this.calculateExpectedReturnDate(this.borrowData.borrow_date);

      this.submittingBorrow = true;

      try {
        await axios.post("/api/borrow", {
          book_id: this.borrowData.book_id,
          user_id: this.borrowData.user_id,
          borrow_date: this.borrowData.borrow_date,
          expected_return_date: this.borrowData.expected_return_date
        });

        toast.fire(
          "Success!",
          "Book borrowed successfully.",
          "success"
        );

        bootstrap.Modal
          .getOrCreateInstance(document.getElementById("BorrowBookModal"))
          .hide();

        // Refresh inventory so the latest available quantities are used.
        await this.loadBorrowRecords();

        try {
          const booksResponse = await axios.get("/api/books");

          this.availableBooks = Array.isArray(booksResponse.data)
            ? booksResponse.data
            : [];
        } catch (refreshError) {
          console.error("Could not refresh book inventory:", refreshError);
        }
      } catch (err) {
        console.error("Failed to borrow book:", err);

        toast.fire(
          "Error!",
          err.response?.data?.error ||
            err.response?.data?.message ||
            "Failed to borrow book.",
          "error"
        );
      } finally {
        this.submittingBorrow = false;
      }
    }
  },

  mounted() {
    this.loadBorrowRecords();
  },

  beforeUnmount() {
    this.destroyDataTable();
  }
};
</script>
