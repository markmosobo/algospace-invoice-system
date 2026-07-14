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

                  <table id="BooksTable" class="table table-borderless align-middle">
                    <thead>
                      <tr>
                        <th>Cover</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Action</th>
                      </tr>
                    </thead>

                    <!-- Spinner while loading -->
                    <tbody v-if="initializing">
                      <tr>
                        <td colspan="6" class="text-center">
                          <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                          </div>
                        </td>
                      </tr>
                    </tbody>

                    <tbody v-else>
                      <tr v-for="book in books" :key="book.id">

                        <!-- Cover Image -->
                        <td style="width: 70px;">
                          <img
                            :src="book.cover_url || defaultCover"
                            alt="Book cover"
                            class="img-thumbnail cursor-pointer"
                            style="width: 50px; height: 70px; object-fit: cover;"
                            @click="viewBook(book)"
                          />
                        </td>

                        <td>{{ book.title }}</td>
                        <td>{{ book.author }}</td>
                        <td>
                          <span
                            v-if="book.status === 'available'"
                            class="badge bg-success"
                          >
                            Available
                          </span>
                          <span
                            v-else
                            class="badge bg-secondary"
                          >
                            Rented
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
                              <a @click.prevent="viewBook(book)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>
                              <a @click.prevent="editBook(book)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                              <a @click.prevent="deleteBook(book.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                            </div>
                          </div>
                        </td>

                      </tr>
                    </tbody>
                  </table>

                </div>
              </div>
            </div><!-- End Books Section -->

            <!-- View Book Modal -->
            <div class="modal fade" id="ViewBookModal" tabindex="-1">
                <div class="modal-dialog modal-xl modal-dialog-centered">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-book-half me-2"></i>
                                Book Details
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="row">

                                <!-- =========================
                                    BOOK COVER
                                ========================== -->

                                <div class="col-lg-4 text-center">

                                    <img
                                        :src="selectedBook.cover_url || defaultCover"
                                        class="img-fluid rounded shadow border"
                                        style="max-height:500px;object-fit:cover;"
                                    >

                                    <div class="mt-3">

                                        <span
                                            class="badge bg-success"
                                            v-if="selectedBook.status=='available'">

                                            Available

                                        </span>

                                        <span
                                            class="badge bg-secondary"
                                            v-else>

                                            Borrowed

                                        </span>

                                        <span
                                            class="badge bg-primary ms-2"
                                            v-if="selectedBook.book_type=='ebook'">

                                            E-Book

                                        </span>

                                        <span
                                            class="badge bg-warning text-dark ms-2"
                                            v-else>

                                            Physical Book

                                        </span>

                                    </div>

                                </div>


                                <!-- =========================
                                    DETAILS
                                ========================== -->

                                <div class="col-lg-8">

                                    <h2 class="mb-1">
                                        {{ selectedBook.title }}
                                    </h2>

                                    <h5 class="text-muted mb-4">
                                        {{ selectedBook.author || 'Unknown Author' }}
                                    </h5>


                                    <!-- General Information -->

                                    <div class="card mb-3">

                                        <div class="card-header bg-light">
                                            <strong>General Information</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-md-6 mb-2">
                                                    <strong>Genre</strong><br>
                                                    {{ selectedBook.genre || '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Language</strong><br>
                                                    {{ selectedBook.language || 'English' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Pages</strong><br>
                                                    {{ selectedBook.pages || '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Condition</strong><br>
                                                    {{ selectedBook.condition || '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Shelf Location</strong><br>
                                                    {{ selectedBook.shelf_location || '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Barcode / ISBN</strong><br>
                                                    {{ selectedBook.barcode || '-' }}
                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    <!-- Ownership -->

                                    <div class="card mb-3">

                                        <div class="card-header bg-light">
                                            <strong>Ownership</strong>
                                        </div>

                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-md-6 mb-2">
                                                    <strong>Added By</strong><br>
                                                    {{ selectedBook.added_by?.name || selectedBook.addedBy?.name || 'System' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Partner</strong><br>
                                                    {{ selectedBook.partner?.name || 'AlgoSpace Library' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Date Added</strong><br>
                                                    {{ formatDate(selectedBook.created_at) }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Last Updated</strong><br>
                                                    {{ formatDate(selectedBook.updated_at) }}
                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    <!-- EBOOK INFORMATION -->

                                    <div
                                        class="card mb-3"
                                        v-if="selectedBook.book_type=='ebook'">

                                        <div class="card-header bg-light">

                                            <strong>E-Book Information</strong>

                                        </div>

                                        <div class="card-body">

                                            <div class="row">

                                                <div class="col-md-6 mb-2">
                                                    <strong>Downloads</strong><br>
                                                    {{ selectedBook.download_count }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>File Size</strong><br>
                                                    {{ selectedBook.file_size_human || '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Format</strong><br>
                                                    {{ selectedBook.ebook_file ? selectedBook.ebook_file.split('.').pop().toUpperCase() : '-' }}
                                                </div>

                                                <div class="col-md-6 mb-2">
                                                    <strong>Download Link</strong><br>

                                                    <a
                                                        :href="selectedBook.ebook_url"
                                                        target="_blank"
                                                        v-if="selectedBook.ebook_url">

                                                        Open File

                                                    </a>

                                                    <span v-else>
                                                        None
                                                    </span>

                                                </div>

                                            </div>

                                        </div>

                                    </div>


                                    <!-- Description -->

                                    <div
                                        class="card"
                                        v-if="selectedBook.description">

                                        <div class="card-header bg-light">
                                            <strong>Description</strong>
                                        </div>

                                        <div class="card-body">

                                            {{ selectedBook.description }}

                                        </div>

                                    </div>

                                </div>

                            </div>

                        </div>

                        <div class="modal-footer">

                            <button
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                Close

                            </button>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Add Book Modal -->
            <div class="modal fade" id="AddBookModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-book me-2"></i>
                                Add New Book
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <form class="row g-3">

                                <!-- =======================
                                    BASIC INFORMATION
                                ======================== -->

                                <div class="col-12">
                                    <h6 class="text-success">
                                        <i class="bi bi-info-circle"></i>
                                        Basic Information
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Title *</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.title"
                                        required
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Author</label>
                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.author"
                                    >
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Genre</label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.genre">

                                        <option value="">Select Genre</option>
                                        <option>Fiction</option>
                                        <option>Non-fiction</option>
                                        <option>Mystery</option>
                                        <option>Science Fiction</option>
                                        <option>Fantasy</option>
                                        <option>Biography</option>
                                        <option>History</option>
                                        <option>Romance</option>
                                        <option>Children</option>
                                        <option>Poetry</option>
                                        <option>Self-help</option>
                                        <option>Other</option>

                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Book Type</label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.book_type">

                                        <option value="physical">
                                            Physical Book
                                        </option>

                                        <option value="ebook">
                                            E-Book
                                        </option>

                                    </select>
                                </div>


                                <!-- =======================
                                    BOOK DETAILS
                                ======================== -->

                                <div class="col-12 mt-4">
                                    <h6 class="text-success">
                                        <i class="bi bi-journal-bookmark"></i>
                                        Book Details
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Pages</label>

                                    <input
                                        type="number"
                                        min="1"
                                        class="form-control"
                                        v-model="bookData.pages">
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Language</label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.language">

                                        <option value="English">English</option>
                                        <option value="Swahili">Swahili</option>
                                        <option value="French">French</option>
                                        <option value="German">German</option>
                                        <option value="Chinese">Chinese</option>
                                        <option value="Other">Other</option>

                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">
                                        Barcode / ISBN
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.barcode">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">
                                        Shelf Location
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        placeholder="Example: Shelf A-2"
                                        v-model="bookData.shelf_location">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Condition</label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.condition">

                                        <option value="">Select</option>
                                        <option value="new">New</option>
                                        <option value="good">Good</option>
                                        <option value="worn">Worn</option>

                                    </select>
                                </div>


                                <!-- =======================
                                    DESCRIPTION
                                ======================== -->

                                <div class="col-12 mt-4">
                                    <label class="form-label">
                                        Description / Summary
                                    </label>

                                    <textarea
                                        rows="4"
                                        class="form-control"
                                        placeholder="Brief description of the book..."
                                        v-model="bookData.description">
                                    </textarea>
                                </div>


                                <!-- =======================
                                    OWNERSHIP
                                ======================== -->

                                <div class="col-12 mt-4">
                                    <h6 class="text-success">
                                        <i class="bi bi-people"></i>
                                        Ownership
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Partner / Owner
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.partner_id">

                                        <option value="">None</option>

                                        <option
                                            v-for="user in partners"
                                            :key="user.id"
                                            :value="user.id">

                                            {{ user.name }}

                                        </option>

                                    </select>

                                </div>


                                <!-- =======================
                                    FILES
                                ======================== -->

                                <div class="col-12 mt-4">
                                    <h6 class="text-success">
                                        <i class="bi bi-folder2-open"></i>
                                        Files
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Cover Image
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control"
                                        accept="image/*"
                                        @change="handleCoverUpload">

                                    <div
                                        v-if="coverPreview"
                                        class="mt-3">

                                        <img
                                            :src="coverPreview"
                                            class="img-thumbnail"
                                            style="max-height:220px;">

                                    </div>

                                </div>

                                <div
                                    class="col-md-6"
                                    v-if="bookData.book_type=='ebook'">

                                    <label class="form-label">
                                        Upload E-Book
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control"
                                        accept=".pdf,.epub"
                                        @change="handleEbookUpload">

                                    <small
                                        class="text-success"
                                        v-if="ebookName">

                                        Selected:
                                        {{ ebookName }}

                                    </small>

                                </div>

                            </form>

                        </div>

                        <div class="modal-footer">

                            <button
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button
                                class="btn btn-success"
                                style="background:darkgreen;border-color:darkgreen"
                                @click="submitBook">

                                <i class="bi bi-save me-1"></i>

                                Save Book

                            </button>

                        </div>

                    </div>
                </div>
            </div>

            <!-- Edit Book Modal -->
            <div class="modal fade" id="EditBookModal" tabindex="-1">
                <div class="modal-dialog modal-xl">
                    <div class="modal-content">

                        <div class="modal-header">
                            <h5 class="modal-title">
                                <i class="bi bi-pencil-square me-2"></i>
                                Edit Book
                            </h5>

                            <button
                                type="button"
                                class="btn-close"
                                data-bs-dismiss="modal">
                            </button>
                        </div>

                        <div class="modal-body">

                            <form class="row g-3">

                                <!-- BASIC INFORMATION -->

                                <div class="col-12">
                                    <h6 class="text-success">
                                        <i class="bi bi-info-circle"></i>
                                        Basic Information
                                    </h6>
                                    <hr>
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Title *</label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.title">
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label">Author</label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.author">
                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Genre
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.genre">

                                        <option>Fiction</option>
                                        <option>Non-fiction</option>
                                        <option>Mystery</option>
                                        <option>Science Fiction</option>
                                        <option>Fantasy</option>
                                        <option>Biography</option>
                                        <option>History</option>
                                        <option>Romance</option>
                                        <option>Children</option>
                                        <option>Poetry</option>
                                        <option>Self-help</option>
                                        <option>Other</option>

                                    </select>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Book Type
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.book_type">

                                        <option value="physical">
                                            Physical Book
                                        </option>

                                        <option value="ebook">
                                            E-Book
                                        </option>

                                    </select>

                                </div>

                                <!-- BOOK DETAILS -->

                                <div class="col-12 mt-4">

                                    <h6 class="text-success">
                                        <i class="bi bi-journal-bookmark"></i>
                                        Book Details
                                    </h6>

                                    <hr>

                                </div>

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Pages
                                    </label>

                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model="bookData.pages">

                                </div>

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Language
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.language">

                                        <option>English</option>
                                        <option>Swahili</option>
                                        <option>French</option>
                                        <option>German</option>
                                        <option>Chinese</option>
                                        <option>Other</option>

                                    </select>

                                </div>

                                <div class="col-md-4">

                                    <label class="form-label">
                                        Barcode / ISBN
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.barcode">

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Shelf Location
                                    </label>

                                    <input
                                        type="text"
                                        class="form-control"
                                        v-model="bookData.shelf_location">

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Condition
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.condition">

                                        <option value="">Select</option>
                                        <option value="new">New</option>
                                        <option value="good">Good</option>
                                        <option value="worn">Worn</option>

                                    </select>

                                </div>

                                <!-- DESCRIPTION -->

                                <div class="col-12 mt-4">

                                    <label class="form-label">
                                        Description
                                    </label>

                                    <textarea
                                        rows="4"
                                        class="form-control"
                                        v-model="bookData.description">
                                    </textarea>

                                </div>

                                <!-- OWNERSHIP -->

                                <div class="col-12 mt-4">

                                    <h6 class="text-success">
                                        <i class="bi bi-people"></i>
                                        Ownership
                                    </h6>

                                    <hr>

                                </div>

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Partner / Owner
                                    </label>

                                    <select
                                        class="form-select"
                                        v-model="bookData.partner_id">

                                        <option value="">
                                            None
                                        </option>

                                        <option
                                            v-for="user in partners"
                                            :key="user.id"
                                            :value="user.id">

                                            {{ user.name }}

                                        </option>

                                    </select>

                                </div>

                                <!-- FILES -->

                                <div class="col-12 mt-4">

                                    <h6 class="text-success">
                                        <i class="bi bi-folder2-open"></i>
                                        Files
                                    </h6>

                                    <hr>

                                </div>

                                <!-- Cover -->

                                <div class="col-md-6">

                                    <label class="form-label">
                                        Replace Cover Image
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control"
                                        accept="image/*"
                                        @change="handleCoverUpload">

                                    <div v-if="coverPreview || bookData.cover_url" class="mt-3">
                                        <img
                                            :src="coverPreview || bookData.cover_url"
                                            class="img-thumbnail"
                                            style="max-height:220px">
                                    </div>

                                    <small class="text-muted">
                                        Leave blank to keep the existing cover.
                                    </small>

                                </div>

                                <!-- Ebook -->

                                <div
                                    class="col-md-6"
                                    v-if="bookData.book_type=='ebook'">

                                    <label class="form-label">
                                        Replace E-Book File
                                    </label>

                                    <input
                                        type="file"
                                        class="form-control"
                                        accept=".pdf,.epub"
                                        @change="handleEbookUpload">

                                    <div
                                        v-if="bookData.ebook_url"
                                        class="mt-2">

                                        <small class="text-success">

                                            Current File Available

                                        </small>

                                    </div>

                                    <small
                                        v-if="ebookName"
                                        class="text-primary d-block mt-2">

                                        New File:
                                        {{ ebookName }}

                                    </small>

                                </div>

                            </form>

                        </div>

                        <div class="modal-footer">

                            <button
                                class="btn btn-secondary"
                                data-bs-dismiss="modal">

                                Cancel

                            </button>

                            <button
                                class="btn btn-success"
                                style="background:darkgreen;border-color:darkgreen"
                                @click="updateBook">

                                <i class="bi bi-check-circle me-1"></i>

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
import Swal from 'sweetalert2';

const toast = Swal.mixin({
    toast: true,
    position: 'top-end',
    showConfirmButton: false,
    timer: 3000
});

window.toast = toast;

export default {
  components: { Master },

  data() {
    return {
      books: [],
      partners: [],
      initializing: true,
      coverPreview: null,
      ebookName: '',
      bookData: this.emptyBook(),
      selectedBook: {},     // 👈 ADD THIS
      defaultCover: 'https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg'    };
  },

  methods: {
    emptyBook() {
      return {
        title: '',
        author: '',
        genre: '',
        book_type: 'physical',
        pages: '',
        language: 'English',
        barcode: '',
        shelf_location: '',
        condition: '',
        description: '',
        partner_id: '',
        cover_image: null,
        ebook_file: null
      };
    },
    formatDate(date) {
        if (!date) return 'N/A';

        return new Date(date).toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric'
        });
    },    
    handleEbookUpload(event) {
        const file = event.target.files[0];

        if (file) {
            this.bookData.ebook_file = file;
            this.ebookName = file.name;
        }
    },
    viewBook(book) {
      // Defensive copy (prevents reactive side-effects)
      this.selectedBook = { ...book };

      // Open modal
      const modalEl = document.getElementById("ViewBookModal");
      const modal = new bootstrap.Modal(modalEl);
      modal.show();
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

    deleteBook(id){
            Swal.fire({
              title: 'Are you sure?',
              text: "You won't be able to revert this!",
              icon: 'warning',
              showCancelButton: true,
              confirmButtonColor: '#006400',
              cancelButtonColor: '#FFA500',
              confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
              if (result.isConfirmed) { 
              //send request to the server
              axios.delete('/api/book/'+id).then(() => {
              toast.fire(
                'Deleted!',
                'Book has been deleted.',
                'success'
              )
              this.loadLists();
              }).catch(() => {
                Swal.fire(
                'Failed!',
                'There was something wrong.',
                'warning'
              )
              }); 
              }else if(result.isDenied) {
                console.log('cancelled')
              }
                                
            })
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