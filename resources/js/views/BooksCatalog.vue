<template>
    <Master>
        <section class="section dashboard">
          <div class="row">

            <!-- Book Rentals / Catalog -->
            <div class="col-12">
              <div class="card top-selling overflow-auto">

                <div class="card-body pb-0">
                  <h5 class="card-title">Books <span>| Catalog & Rentals</span></h5>
                  
                  <div class="row mb-3">
                    <div class="col d-flex">
                      <button
                        class="btn btn-sm btn-primary rounded-pill"
                        style="background-color: darkgreen; border-color: darkgreen;"
                        @click="openAddBookModal()"
                      >
                        Add Book
                      </button>
                    </div>
                    <div class="col-auto d-flex justify-content-end">
                      <div class="btn-group" role="group">
                        <button type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-bs-toggle="dropdown">
                          Actions
                        </button>
                        <div class="dropdown-menu">
                          <a @click="navigateTo('/books')"
                             class="dropdown-item" href="#">All Books</a>
                          <a @click="navigateTo('/book-rentals')"
                             class="dropdown-item" href="#">Rentals</a>
                          <a @click="navigateTo('/book-users')"
                             class="dropdown-item" href="#">Users</a>
                        </div>
                      </div>
                    </div>
                  </div>

                  <table id="BooksTable" class="table table-borderless">
                    <thead>
                      <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>ISBN</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>

                    <!-- Spinner while loading -->
                    <tbody v-if="initializing">
                      <tr>
                        <td colspan="5" class="text-center">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </td>
                      </tr>
                    </tbody>

                    <tbody v-else>
                      <tr v-for="book in books" :key="book.id">
                        <td>{{ book.title }}</td>
                        <td>{{ book.author }}</td>
                        <td>{{ book.barcode ?? 'N/A' }}</td>
                        <td>
                          <span v-if="book.available" class="badge bg-success">Available</span>
                          <span v-else class="badge bg-secondary">Rented</span>
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <button type="button" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" style="background-color: darkgreen; border-color: darkgreen;" data-bs-toggle="dropdown">
                              Action
                            </button>
                            <div class="dropdown-menu">
                              <a @click="viewBook(book)" class="dropdown-item" href="#">View</a>
                              <a @click="editBook(book)" class="dropdown-item" href="#">Edit</a>
                              <a @click="deleteBook(book.id)" class="dropdown-item" href="#">Delete</a>
                            </div>
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>
            </div><!-- End Books Section -->

            <!-- Add Book Modal -->
            <div class="modal fade" id="AddBookModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form class="row g-3" novalidate>
                    <div class="col-md-6">
                        <label>Title*</label>
                        <input type="text" class="form-control" v-model="bookData.title" required>
                    </div>
                    <div class="col-md-6">
                        <label>Author</label>
                        <input type="text" class="form-control" v-model="bookData.author">
                    </div>
                    <div class="col-md-6">
                        <label>Genre</label>
                        <select class="form-control" v-model="bookData.genre">
                            <option value="" disabled>Select Genre</option>
                            <option value="Fiction">Fiction</option>
                            <option value="Non-fiction">Non-fiction</option>
                            <option value="Mystery">Mystery</option>
                            <option value="Science Fiction">Science Fiction</option>
                            <option value="Romance">Romance</option>
                            <option value="Biography">Biography</option>
                            <option value="Fantasy">Fantasy</option>
                            <option value="History">History</option>
                            <option value="Children">Children</option>
                            <option value="Self-help">Self-help</option>
                            <option value="Poetry">Poetry</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>Shelf Location</label>
                        <input type="text" class="form-control" v-model="bookData.shelf_location">
                    </div>
                    <div class="col-md-6">
                        <label>Condition</label>
                        <select class="form-select" v-model="bookData.condition">
                        <option value="">Select</option>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="worn">Worn</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label>ISBN</label>
                        <input type="text" class="form-control" v-model="bookData.barcode">
                    </div>
                    <div class="col-md-6">
                        <label>Partner</label>
                        <select class="form-select" v-model="bookData.partner_id">
                        <option value="">None</option>
                        <option v-for="user in partners" :key="user.id" :value="user.id">{{ user.name }}</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                    <label>Cover Image</label>
                    <input
                        type="file"
                        class="form-control"
                        accept="image/*"
                        @change="handleCoverUpload"
                    />

                    <!-- Image Preview -->
                    <div v-if="coverPreview" class="mt-2">
                        <img
                        :src="coverPreview"
                        alt="Cover Preview"
                        class="img-thumbnail"
                        style="max-height: 200px;"
                        >
                    </div>
                    </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button class="btn btn-success" @click="submitBook" style="background: darkgreen; border-color: darkgreen;">Save Book</button>
                </div>
                </div>
            </div>
            </div>

            <!-- Edit Book Modal -->
            <div class="modal fade" id="EditBookModal" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Book</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <form class="row g-3">
                    <div class="col-md-6">
                        <label>Title*</label>
                        <input type="text" class="form-control" v-model="bookData.title">
                    </div>

                    <div class="col-md-6">
                        <label>Author</label>
                        <input type="text" class="form-control" v-model="bookData.author">
                    </div>

                    <div class="col-md-6">
                        <label>Genre</label>
                        <select class="form-control" v-model="bookData.genre">
                        <option value="" disabled>Select Genre</option>
                        <option>Fiction</option>
                        <option>Non-fiction</option>
                        <option>Mystery</option>
                        <option>Science Fiction</option>
                        <option>Romance</option>
                        <option>Biography</option>
                        <option>Fantasy</option>
                        <option>History</option>
                        <option>Children</option>
                        <option>Self-help</option>
                        <option>Poetry</option>
                        <option>Other</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>Shelf Location</label>
                        <input type="text" class="form-control" v-model="bookData.shelf_location">
                    </div>

                    <div class="col-md-6">
                        <label>Condition</label>
                        <select class="form-select" v-model="bookData.condition">
                        <option value="">Select</option>
                        <option value="new">New</option>
                        <option value="good">Good</option>
                        <option value="worn">Worn</option>
                        </select>
                    </div>

                    <div class="col-md-6">
                        <label>ISBN</label>
                        <input type="text" class="form-control" v-model="bookData.isbn">
                    </div>

                    <div class="col-md-6">
                    <label>Cover Image</label>
                    <input type="file"
                            class="form-control"
                            accept="image/*"
                            @change="handleCoverUpload">

                    <!-- Preview -->
                    <div v-if="coverPreview" class="mt-2">
                        <img
                        :src="coverPreview"
                        alt="Cover Preview"
                        class="img-thumbnail"
                        style="max-height:200px"
                        >
                    </div>
                    <small class="text-muted">
                    Selecting a new image will replace the existing cover
                    </small>
                    </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button class="btn btn-success" style="background:darkgreen"
                            @click="updateBook">
                    Update Book
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
import "jquery/dist/jquery.min.js";
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";

export default {
  components: { Master },

  data() {
    return {
      books: [],
      partners: [],
      initializing: true,
      coverPreview: null,
      bookData: this.emptyBook(),
    };
  },

  methods: {
    emptyBook() {
      return {
        id: "",
        title: "",
        author: "",
        genre: "",
        shelf_location: "",
        condition: "",
        isbn: "",
        partner_id: "",
        cover_image: null, // 🔑 must match backend
      };
    },

    loadBooks() {
      this.initializing = true;
      axios.get("/api/books")
        .then(res => {
          this.books = res.data;
          setTimeout(() => {
            $("#BooksTable").DataTable();
          }, 10);
        })
        .finally(() => (this.initializing = false));
    },

    loadPartners() {
      axios.get("/api/partners")
        .then(res => (this.partners = res.data));
    },

    openAddBookModal() {
    this.bookData = this.emptyBook();
    this.coverPreview = null;
    this.coverPreview = null; // reset preview
    new bootstrap.Modal(document.getElementById("AddBookModal")).show();
    },

    editBook(book) {
    // Fill form fields
    this.bookData = {
        id: book.id,
        title: book.title,
        author: book.author,
        genre: book.genre,
        shelf_location: book.shelf_location,
        condition: book.condition,
        isbn: book.barcode,
        partner_id: book.partner_id,
    };

    // Show existing cover if available
    this.coverPreview = book.cover_image
        ? `/storage/book_covers/${book.cover_image}` // match your backend storage
        : null;

    new bootstrap.Modal(document.getElementById("EditBookModal")).show();
    },

    submitBook() {
    axios.post("/api/books", this.buildFormData(), {
        headers: { "Content-Type": "multipart/form-data" }
    }).then(() => {
        this.loadBooks();
        bootstrap.Modal.getInstance(
        document.getElementById("AddBookModal")
        ).hide();
    });
    },

    updateBook() {
    axios.post(
        `/api/books/${this.bookData.id}?_method=PUT`,
        this.buildFormData(),
        { headers: { "Content-Type": "multipart/form-data" } }
    ).then(() => {
        this.loadBooks();
        bootstrap.Modal.getInstance(
        document.getElementById("EditBookModal")
        ).hide();
    });
    },

    deleteBook(id) {
      if (!confirm("Delete this book?")) return;
      axios.delete(`/api/books/${id}`)
        .then(() => this.loadBooks());
    },

    handleCoverUpload(e) {
    const file = e.target.files[0];
    if (!file) return;

    // Set file for form submission
    this.bookData.cover_image = file;

    // Create preview for Add & Edit modals
    this.coverPreview = URL.createObjectURL(file);
    },

    buildFormData() {
    const fd = new FormData();

    Object.keys(this.bookData).forEach(key => {
        const value = this.bookData[key];
        if (value !== "" && value !== null && value !== undefined) {
        fd.append(key, value);
        }
    });

    return fd;
    },

    navigateTo(path) {
      this.$router.push(path);
    }
  },

  mounted() {
    this.loadBooks();
    this.loadPartners();
  }
};
</script>