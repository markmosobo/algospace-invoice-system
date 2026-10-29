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
                        <th>Copy Inventory</th>
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
                          <span v-if="Number(book.available_copies ?? 0) > 0" class="badge bg-success">
                            {{ book.available_copies }} Available
                          </span>
                          <span v-else class="badge bg-secondary">Out of Stock</span>
                          <div class="small text-muted mt-1">Total: {{ book.total_copies ?? 1 }}</div>
                          <div class="small text-muted">Borrowed: {{ book.borrowed_copies ?? 0 }}</div>
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

                                        <span class="badge bg-success" v-if="Number(selectedBook.available_copies ?? 0) > 0">
                                            {{ selectedBook.available_copies }} Available
                                        </span>
                                        <span class="badge bg-secondary" v-else>Out of Stock</span>

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


                                    <!-- Copy Inventory -->
                                    <div class="card mb-3">
                                        <div class="card-header bg-light"><strong>Copy Inventory</strong></div>
                                        <div class="card-body">
                                            <div class="row text-center">
                                                <div class="col-md-4 mb-2">
                                                    <h4>{{ selectedBook.total_copies ?? 1 }}</h4>
                                                    <small class="text-muted">Total Copies</small>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <h4 class="text-success">{{ selectedBook.available_copies ?? 0 }}</h4>
                                                    <small class="text-muted">Available</small>
                                                </div>
                                                <div class="col-md-4 mb-2">
                                                    <h4 class="text-warning">{{ selectedBook.borrowed_copies ?? 0 }}</h4>
                                                    <small class="text-muted">Borrowed</small>
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
                                    <label class="form-label">Total Copies *</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model.number="bookData.total_copies"
                                        min="1"
                                        max="100000"
                                        step="1"
                                        required>
                                    <small class="text-muted">Total physical copies owned for this title.</small>
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
                                    <label class="form-label">Total Copies *</label>
                                    <input
                                        type="number"
                                        class="form-control"
                                        v-model.number="bookData.total_copies"
                                        min="1"
                                        max="100000"
                                        step="1"
                                        required>
                                    <small class="text-muted">Cannot be less than copies currently borrowed.</small>
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
import Swal from "sweetalert2";

const toast = Swal.mixin({
    toast: true,
    position: "top-end",
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
            ebookName: "",
            bookData: this.emptyBook(),
            selectedBook: {},
            defaultCover: "https://upload.wikimedia.org/wikipedia/commons/6/65/No-Image-Placeholder.svg"
        };
    },

    methods: {
        emptyBook() {
            return {
                id: null,
                title: "",
                author: "",
                genre: "",
                book_type: "physical",
                pages: "",
                language: "English",
                barcode: "",
                shelf_location: "",
                condition: "",
                description: "",
                partner_id: "",
                total_copies: 1,
                cover_image: null,
                ebook_file: null
            };
        },

        formatDate(date) {
            if (!date) return "N/A";
            return new Date(date).toLocaleDateString("en-KE", {
                month: "short",
                day: "numeric",
                year: "numeric"
            });
        },

        getErrorMessage(error, fallback) {
            const errors = error?.response?.data?.errors;
            return errors
                ? Object.values(errors).flat().join("\n")
                : (error?.response?.data?.message || fallback);
        },

        showApiError(error, fallback) {
            Swal.fire({
                title: "Unable to complete request",
                text: this.getErrorMessage(error, fallback),
                icon: "error"
            });
        },

        handleEbookUpload(event) {
            const file = event.target.files?.[0];
            if (file) {
                this.bookData.ebook_file = file;
                this.ebookName = file.name;
            }
        },

        handleCoverUpload(event) {
            const file = event.target.files?.[0];
            if (!file) return;

            this.bookData.cover_image = file;
            if (this.coverPreview?.startsWith("blob:")) {
                URL.revokeObjectURL(this.coverPreview);
            }
            this.coverPreview = URL.createObjectURL(file);
        },

        viewBook(book) {
            this.selectedBook = {
                total_copies: 1,
                available_copies: book.status === "borrowed" ? 0 : 1,
                borrowed_copies: book.status === "borrowed" ? 1 : 0,
                ...book
            };
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById("ViewBookModal")
            ).show();
        },

        async loadBooks() {
            this.initializing = true;

            try {
                if ($.fn.DataTable.isDataTable("#BooksTable")) {
                    $("#BooksTable").DataTable().destroy();
                }

                const response = await axios.get("/api/books");
                this.books = (Array.isArray(response.data) ? response.data : []).map(book => ({
                    total_copies: 1,
                    available_copies: book.status === "borrowed" ? 0 : 1,
                    borrowed_copies: book.status === "borrowed" ? 1 : 0,
                    ...book
                }));

                // Render the real rows before initializing DataTables.
                this.initializing = false;
                await this.$nextTick();

                if (this.books.length && document.querySelector("#BooksTable")) {
                    $("#BooksTable").DataTable({
                        destroy: true,
                        pageLength: 10,
                        order: []
                    });
                }
            } catch (error) {
                this.showApiError(error, "Failed to load books.");
            } finally {
                this.initializing = false;
            }
        },

        async loadPartners() {
            try {
                const response = await axios.get("/api/partners");
                this.partners = response.data;
            } catch (error) {
                console.error("Failed to load partners:", error);
            }
        },

        resetBookForm() {
            if (this.coverPreview?.startsWith("blob:")) {
                URL.revokeObjectURL(this.coverPreview);
            }
            this.bookData = this.emptyBook();
            this.coverPreview = null;
            this.ebookName = "";
        },

        openAddBookModal() {
            this.resetBookForm();
            bootstrap.Modal.getOrCreateInstance(
                document.getElementById("AddBookModal")
            ).show();
        },

        editBook(book) {
            if (this.coverPreview?.startsWith("blob:")) {
                URL.revokeObjectURL(this.coverPreview);
            }

            this.bookData = {
                id: book.id,
                title: book.title ?? "",
                author: book.author ?? "",
                genre: book.genre ?? "",
                book_type: book.book_type ?? "physical",
                pages: book.pages ?? "",
                language: book.language ?? "English",
                description: book.description ?? "",
                shelf_location: book.shelf_location ?? "",
                condition: book.condition ?? "",
                barcode: book.barcode ?? "",
                partner_id: book.partner_id ?? "",
                total_copies: Number(book.total_copies ?? 1),
                cover_image: null,
                ebook_file: null,
                cover_url: book.cover_url ?? (book.cover_image ? `/storage/${book.cover_image}` : null),
                ebook_url: book.ebook_url ?? null
            };

            this.coverPreview = this.bookData.cover_url;
            this.ebookName = "";

            bootstrap.Modal.getOrCreateInstance(
                document.getElementById("EditBookModal")
            ).show();
        },

        buildFormData() {
            const formData = new FormData();

            Object.keys(this.bookData).forEach(key => {
                if (["cover_url", "ebook_url"].includes(key)) return;
                if (key === "id" && !this.bookData[key]) return;

                const value = this.bookData[key];
                if (value !== "" && value !== null && value !== undefined) {
                    formData.append(key, value);
                }
            });

            return formData;
        },

        async submitBook() {
            if (!this.bookData.title?.trim()) {
                Swal.fire("Title required", "Please enter a book title.", "warning");
                return;
            }

            if (!Number.isInteger(Number(this.bookData.total_copies)) ||
                Number(this.bookData.total_copies) < 1) {
                Swal.fire("Invalid quantity", "Total copies must be a whole number of at least 1.", "warning");
                return;
            }

            try {
                await axios.post("/api/books", this.buildFormData(), {
                    headers: { "Content-Type": "multipart/form-data" }
                });

                await this.loadBooks();
                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById("AddBookModal")
                ).hide();

                toast.fire("Success", "Book added successfully.", "success");
                this.resetBookForm();
            } catch (error) {
                this.showApiError(error, "Failed to add book.");
            }
        },

        async updateBook() {
            if (!this.bookData.id) {
                Swal.fire("Missing book", "The book ID is missing.", "error");
                return;
            }

            if (!this.bookData.title?.trim()) {
                Swal.fire("Title required", "Please enter a book title.", "warning");
                return;
            }

            if (!Number.isInteger(Number(this.bookData.total_copies)) ||
                Number(this.bookData.total_copies) < 1) {
                Swal.fire("Invalid quantity", "Total copies must be a whole number of at least 1.", "warning");
                return;
            }

            try {
                await axios.post(
                    `/api/books/${this.bookData.id}?_method=PUT`,
                    this.buildFormData(),
                    { headers: { "Content-Type": "multipart/form-data" } }
                );

                await this.loadBooks();
                bootstrap.Modal.getOrCreateInstance(
                    document.getElementById("EditBookModal")
                ).hide();

                toast.fire("Success", "Book updated successfully.", "success");
            } catch (error) {
                this.showApiError(error, "Failed to update book.");
            }
        },

        async deleteBook(id) {
            const result = await Swal.fire({
                title: "Delete this book?",
                text: "This action cannot be undone.",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#006400",
                cancelButtonColor: "#FFA500",
                confirmButtonText: "Yes, delete it!"
            });

            if (!result.isConfirmed) return;

            try {
                await axios.delete(`/api/books/${id}`);
                toast.fire("Deleted", "Book deleted successfully.", "success");
                await this.loadBooks();
            } catch (error) {
                this.showApiError(error, "Unable to delete this book.");
            }
        },

        navigateTo(path) {
            this.$router.push(path);
        }
    },

    mounted() {
        this.loadBooks();
        this.loadPartners();
    },

    beforeUnmount() {
        if (this.coverPreview?.startsWith("blob:")) {
            URL.revokeObjectURL(this.coverPreview);
        }
        if ($.fn.DataTable.isDataTable("#BooksTable")) {
            $("#BooksTable").DataTable().destroy();
        }
    }
};
</script>
