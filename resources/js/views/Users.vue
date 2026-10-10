
<template>
  <Master>
    <section class="section dashboard">
      <div class="row">
        <div class="col-12">
          <div class="card overflow-auto">
            <div class="card-body pb-0">
              <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                  <h5 class="card-title">
                    Users <span>| AlgoSpace Cyber</span>
                  </h5>
                  <p class="text-muted">
                    Manage borrowers, partners and staff accounts.
                  </p>
                </div>

                <button
                  class="btn btn-sm btn-primary rounded-pill green-btn"
                  @click="openAddModal"
                >
                  <i class="bi bi-person-plus me-1"></i> Add User
                </button>
              </div>

              <div class="row mb-3 g-2">
                <div class="col-md-5">
                  <input
                    v-model="search"
                    type="search"
                    class="form-control"
                    placeholder="Search name, email, phone..."
                  />
                </div>

                <div class="col-md-3">
                  <select v-model="roleFilter" class="form-select">
                    <option value="">All roles</option>
                    <option value="borrower">Borrower</option>
                    <option value="partner">Partner</option>
                    <option value="staff">Staff</option>
                  </select>
                </div>

                <div class="col-md-3">
                  <select v-model="statusFilter" class="form-select">
                    <option value="">All statuses</option>
                    <option value="active">Active</option>
                    <option value="pending">Pending</option>
                    <option value="suspended">Suspended</option>
                  </select>
                </div>

                <div class="col-md-1">
                  <button
                    class="btn btn-outline-secondary w-100"
                    title="Refresh users"
                    @click="loadUsers"
                    :disabled="initializing"
                  >
                    <i class="bi bi-arrow-clockwise"></i>
                  </button>
                </div>
              </div>

              <div v-if="errorMessage" class="alert alert-danger">
                {{ errorMessage }}
              </div>

              <div class="table-responsive">
                <table class="table table-hover align-middle">
                  <thead>
                    <tr>
                      <th>Name</th>
                      <th>Email</th>
                      <th>Phone</th>
                      <th>Role</th>
                      <th>Status</th>
                      <th>Membership</th>
                      <th>Action</th>
                    </tr>
                  </thead>

                  <tbody v-if="initializing">
                    <tr>
                      <td colspan="7" class="text-center py-4">
                        <div class="spinner-border text-success" role="status">
                          <span class="visually-hidden">Loading...</span>
                        </div>
                      </td>
                    </tr>
                  </tbody>

                  <tbody v-else-if="filteredUsers.length">
                    <tr v-for="user in filteredUsers" :key="user.id">
                      <td>{{ user.name }}</td>
                      <td>{{ user.email }}</td>
                      <td>{{ user.phone || 'N/A' }}</td>

                      <td>
                        <span class="badge text-bg-secondary">
                          {{ formatLabel(user.role) }}
                        </span>
                      </td>

                      <td>
                        <span
                          class="badge"
                          :class="statusClass(user.status)"
                        >
                          {{ formatLabel(user.status || 'active') }}
                        </span>
                      </td>

                      <td>
                        {{ formatLabel(user.membership_type || 'public') }}
                      </td>

                      <td>
                        <div class="dropdown">
                          <button
                            class="btn btn-sm btn-primary rounded-pill green-btn dropdown-toggle"
                            type="button"
                            data-bs-toggle="dropdown"
                            aria-expanded="false"
                          >
                            Action
                          </button>

                          <ul class="dropdown-menu dropdown-menu-end">
                            <li>
                              <button
                                class="dropdown-item"
                                type="button"
                                @click="viewUser(user)"
                              >
                                <i class="bi bi-eye me-2"></i>
                                View
                              </button>
                            </li>

                            <li>
                              <button
                                class="dropdown-item"
                                type="button"
                                @click="viewAuditLogs(user)"
                              >
                                <i class="bi bi-clock-history me-2"></i>
                                Audit History
                              </button>
                            </li>

                            <li>
                              <button
                                class="dropdown-item"
                                type="button"
                                @click="openEditModal(user)"
                              >
                                <i class="bi bi-pencil me-2"></i>
                                Edit
                              </button>
                            </li>

                            <li><hr class="dropdown-divider" /></li>

                            <li>
                              <button
                                class="dropdown-item text-danger"
                                type="button"
                                @click="deleteUser(user)"
                              >
                                <i class="bi bi-trash me-2"></i>
                                Delete
                              </button>
                            </li>
                          </ul>
                        </div>
                      </td>
                    </tr>
                  </tbody>

                  <tbody v-else>
                    <tr>
                      <td colspan="7" class="text-center py-4 text-muted">
                        No users found.
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>

              <p class="text-muted small pb-3">
                Showing {{ filteredUsers.length }} of {{ users.length }} users
              </p>
            </div>
          </div>
        </div>
      </div>

      <!-- View User Modal -->
      <div
        class="modal fade"
        id="viewUserModal"
        tabindex="-1"
        aria-labelledby="viewUserModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="viewUserModalLabel">
                User Details
              </h5>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>

            <div class="modal-body" v-if="selectedUser">
              <div class="row g-3">
                <div class="col-md-6">
                  <strong>Full Name</strong>
                  <div>{{ selectedUser.name || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Email</strong>
                  <div>{{ selectedUser.email || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Phone</strong>
                  <div>{{ selectedUser.phone || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Date of Birth</strong>
                  <div>{{ selectedUser.dob || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Role</strong>
                  <div>{{ formatLabel(selectedUser.role) }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Status</strong>
                  <div>{{ formatLabel(selectedUser.status || 'active') }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Membership Type</strong>
                  <div>
                    {{ formatLabel(selectedUser.membership_type || 'public') }}
                  </div>
                </div>

                <div class="col-md-6">
                  <strong>Borrow Limit</strong>
                  <div>{{ selectedUser.borrow_limit ?? 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>City</strong>
                  <div>{{ selectedUser.city || 'N/A' }}</div>
                </div>

                <div class="col-md-6">
                  <strong>Postal Code</strong>
                  <div>{{ selectedUser.postal_code || 'N/A' }}</div>
                </div>

                <div class="col-12">
                  <strong>Address</strong>
                  <div>{{ selectedUser.address || 'N/A' }}</div>
                </div>

                <div class="col-12">
                  <strong>Email Verified</strong>
                  <div>
                    {{ selectedUser.email_verified_at ? 'Yes' : 'No' }}
                  </div>
                </div>
              </div>
            </div>

            <div class="modal-footer">
              <button
                class="btn btn-secondary"
                type="button"
                data-bs-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Add / Edit User Modal -->
      <div
        class="modal fade"
        id="userFormModal"
        tabindex="-1"
        aria-labelledby="userFormModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="userFormModalLabel">
                {{ editing ? 'Edit User' : 'Add User' }}
              </h5>

              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>

            <form @submit.prevent="saveUser">
              <div class="modal-body">
                <div v-if="formError" class="alert alert-danger">
                  {{ formError }}
                </div>

                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label">Full Name *</label>
                    <input
                      v-model.trim="form.name"
                      type="text"
                      class="form-control"
                      required
                      maxlength="255"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Email *</label>
                    <input
                      v-model.trim="form.email"
                      type="email"
                      class="form-control"
                      required
                    />
                  </div>

                  <div class="col-md-6" v-if="!editing">
                    <label class="form-label">Initial Password *</label>
                    <input
                      v-model="form.password"
                      type="password"
                      class="form-control"
                      required
                      minlength="6"
                      autocomplete="new-password"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Phone</label>
                    <input
                      v-model.trim="form.phone"
                      type="text"
                      class="form-control"
                      maxlength="20"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Role *</label>
                    <select v-model="form.role" class="form-select" required>
                      <option value="borrower">Borrower</option>
                      <option value="partner">Partner</option>
                      <option value="staff">Staff</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Status</label>
                    <select v-model="form.status" class="form-select">
                      <option value="active">Active</option>
                      <option value="pending">Pending</option>
                      <option value="suspended">Suspended</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Membership Type</label>
                    <select
                      v-model="form.membership_type"
                      class="form-select"
                    >
                      <option value="public">Public</option>
                      <option value="student">Student</option>
                      <option value="staff">Staff</option>
                      <option value="premium">Premium</option>
                    </select>
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Borrow Limit</label>
                    <input
                      v-model.number="form.borrow_limit"
                      type="number"
                      class="form-control"
                      min="1"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Date of Birth</label>
                    <input
                      v-model="form.dob"
                      type="date"
                      class="form-control"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">City</label>
                    <input
                      v-model.trim="form.city"
                      type="text"
                      class="form-control"
                      maxlength="100"
                    />
                  </div>

                  <div class="col-md-6">
                    <label class="form-label">Postal Code</label>
                    <input
                      v-model.trim="form.postal_code"
                      type="text"
                      class="form-control"
                      maxlength="20"
                    />
                  </div>

                  <div class="col-12">
                    <label class="form-label">Address</label>
                    <input
                      v-model.trim="form.address"
                      type="text"
                      class="form-control"
                      maxlength="255"
                    />
                  </div>
                </div>
              </div>

              <div class="modal-footer">
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-bs-dismiss="modal"
                  :disabled="submitting"
                >
                  Cancel
                </button>

                <button
                  type="submit"
                  class="btn btn-success green-btn"
                  :disabled="submitting"
                >
                  <span
                    v-if="submitting"
                    class="spinner-border spinner-border-sm me-1"
                  ></span>

                  {{
                    submitting
                      ? 'Saving...'
                      : editing
                        ? 'Save Changes'
                        : 'Create User'
                  }}
                </button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- User Audit History Modal -->
      <div
        class="modal fade"
        id="userAuditModal"
        tabindex="-1"
        aria-labelledby="userAuditModalLabel"
        aria-hidden="true"
      >
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
          <div class="modal-content">
            <div class="modal-header bg-success text-white">
              <div>
                <h5 class="modal-title" id="userAuditModalLabel">
                  <i class="bi bi-clock-history me-2"></i>
                  User Audit History
                </h5>

                <small v-if="selectedAuditUser">
                  {{ selectedAuditUser.name }}
                  <span v-if="selectedAuditUser.email">
                    — {{ selectedAuditUser.email }}
                  </span>
                </small>
              </div>

              <button
                type="button"
                class="btn-close btn-close-white"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>

            <div class="modal-body">
              <div v-if="loadingAuditLogs" class="text-center py-5">
                <div
                  class="spinner-border text-success"
                  role="status"
                >
                  <span class="visually-hidden">Loading...</span>
                </div>

                <p class="text-muted mt-2 mb-0">
                  Loading audit history...
                </p>
              </div>

              <div
                v-else-if="auditError"
                class="alert alert-danger"
              >
                {{ auditError }}

                <button
                  type="button"
                  class="btn btn-sm btn-outline-danger ms-2"
                  @click="loadAuditPage(auditPagination.current_page || 1)"
                >
                  Retry
                </button>
              </div>

              <div
                v-else-if="auditLogs.length === 0"
                class="text-center py-5"
              >
                <i class="bi bi-journal-text display-5 text-muted"></i>

                <h6 class="mt-3">No audit history found</h6>

                <p class="text-muted mb-0">
                  No recorded events are available for this user.
                </p>
              </div>

              <template v-else>
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                  <span class="text-muted small">
                    {{ auditPagination.total }} record(s)
                  </span>

                  <button
                    class="btn btn-sm btn-outline-success"
                    type="button"
                    @click="loadAuditPage(auditPagination.current_page)"
                    :disabled="loadingAuditLogs"
                  >
                    <i class="bi bi-arrow-clockwise me-1"></i>
                    Refresh History
                  </button>
                </div>

                <div class="table-responsive">
                  <table class="table table-hover table-striped align-middle">
                    <thead class="table-light">
                      <tr>
                        <th>Date &amp; Time</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>Performed By</th>
                        <th>IP Address</th>
                      </tr>
                    </thead>

                    <tbody>
                      <tr
                        v-for="log in auditLogs"
                        :key="log.id"
                      >
                        <td class="text-nowrap">
                          {{ formatAuditDate(log.created_at) }}
                        </td>

                        <td>
                          <span class="badge bg-success-subtle text-success border">
                            {{ log.event || 'event' }}
                          </span>
                        </td>

                        <td class="audit-description">
                          {{ log.description || 'No description provided' }}
                        </td>

                        <td>
                          <template v-if="log.user">
                            <div>{{ log.user.name }}</div>
                            <small class="text-muted">
                              {{ log.user.email }}
                            </small>
                          </template>

                          <span v-else class="text-muted">
                            System / unavailable
                          </span>
                        </td>

                        <td>
                          {{ log.ip_address || '—' }}
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>

                <div
                  v-if="auditPagination.last_page > 1"
                  class="d-flex justify-content-between align-items-center flex-wrap gap-2 mt-3"
                >
                  <small class="text-muted">
                    Page {{ auditPagination.current_page }}
                    of {{ auditPagination.last_page }}
                  </small>

                  <div class="btn-group">
                    <button
                      class="btn btn-sm btn-outline-secondary"
                      type="button"
                      :disabled="
                        loadingAuditLogs ||
                        auditPagination.current_page <= 1
                      "
                      @click="loadAuditPage(auditPagination.current_page - 1)"
                    >
                      <i class="bi bi-chevron-left"></i>
                      Previous
                    </button>

                    <button
                      class="btn btn-sm btn-outline-secondary"
                      type="button"
                      :disabled="
                        loadingAuditLogs ||
                        auditPagination.current_page >= auditPagination.last_page
                      "
                      @click="loadAuditPage(auditPagination.current_page + 1)"
                    >
                      Next
                      <i class="bi bi-chevron-right"></i>
                    </button>
                  </div>
                </div>
              </template>
            </div>

            <div class="modal-footer">
              <button
                class="btn btn-secondary"
                type="button"
                data-bs-dismiss="modal"
              >
                Close
              </button>
            </div>
          </div>
        </div>
      </div>
    </section>
  </Master>
</template>

<script>
import Master from '@/components/Master.vue';
import axios from 'axios';
import Swal from 'sweetalert2';

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000,
});

export default {
  name: 'Users',

  components: {
    Master,
  },

  data() {
    return {
      users: [],
      selectedUser: null,
      selectedAuditUser: null,

      auditLogs: [],
      loadingAuditLogs: false,
      auditError: '',

      auditPagination: {
        current_page: 1,
        last_page: 1,
        total: 0,
      },

      initializing: false,
      submitting: false,
      editing: false,

      search: '',
      roleFilter: '',
      statusFilter: '',

      errorMessage: '',
      formError: '',

      form: this.emptyForm(),
    };
  },

  computed: {
    filteredUsers() {
      const query = this.search.trim().toLowerCase();

      return this.users.filter((user) => {
        const matchesSearch =
          !query ||
          [user.name, user.email, user.phone, user.city]
            .some((value) =>
              String(value || '').toLowerCase().includes(query)
            );

        const matchesRole =
          !this.roleFilter || user.role === this.roleFilter;

        const matchesStatus =
          !this.statusFilter ||
          (user.status || 'active') === this.statusFilter;

        return matchesSearch && matchesRole && matchesStatus;
      });
    },
  },

  methods: {
    emptyForm() {
      return {
        id: null,
        name: '',
        email: '',
        password: '',
        phone: '',
        dob: '',
        address: '',
        city: '',
        postal_code: '',
        role: 'borrower',
        status: 'active',
        membership_type: 'public',
        borrow_limit: 3,
      };
    },

    formatLabel(value) {
      if (!value) return 'N/A';

      return String(value)
        .replace(/[_-]/g, ' ')
        .replace(/\b\w/g, (letter) => letter.toUpperCase());
    },

    statusClass(status) {
      return {
        active: 'text-bg-success',
        pending: 'text-bg-warning',
        suspended: 'text-bg-danger',
      }[status] || 'text-bg-secondary';
    },

    showModal(id) {
      const element = document.getElementById(id);

      if (element && window.bootstrap) {
        window.bootstrap.Modal.getOrCreateInstance(element).show();
      }
    },

    hideModal(id) {
      const element = document.getElementById(id);

      if (element && window.bootstrap) {
        window.bootstrap.Modal.getInstance(element)?.hide();
      }
    },

    async loadUsers() {
      this.initializing = true;
      this.errorMessage = '';

      try {
        const response = await axios.get('/api/users');

        this.users = Array.isArray(response.data)
          ? response.data
          : response.data.data || [];
      } catch (error) {
        console.error('Failed to load users:', error);

        this.errorMessage =
          error.response?.data?.message || 'Unable to load users.';
      } finally {
        this.initializing = false;
      }
    },

    openAddModal() {
      this.editing = false;
      this.formError = '';
      this.form = this.emptyForm();

      this.showModal('userFormModal');
    },

    openEditModal(user) {
      this.editing = true;
      this.formError = '';

      this.form = {
        ...this.emptyForm(),
        id: user.id,
        name: user.name || '',
        email: user.email || '',
        phone: user.phone || '',
        dob: user.dob ? String(user.dob).substring(0, 10) : '',
        address: user.address || '',
        city: user.city || '',
        postal_code: user.postal_code || '',
        role: user.role || 'borrower',
        status: user.status || 'active',
        membership_type: user.membership_type || 'public',
        borrow_limit: user.borrow_limit ?? 3,
      };

      this.showModal('userFormModal');
    },

    viewUser(user) {
      this.selectedUser = user;
      this.showModal('viewUserModal');
    },

    async viewAuditLogs(user) {
      this.selectedAuditUser = user;
      this.auditLogs = [];
      this.auditError = '';

      this.auditPagination = {
        current_page: 1,
        last_page: 1,
        total: 0,
      };

      this.showModal('userAuditModal');

      await this.loadAuditPage(1);
    },

    async loadAuditPage(page = 1) {
      if (!this.selectedAuditUser) return;

      this.loadingAuditLogs = true;
      this.auditError = '';

      try {
        const response = await axios.get(
          `/api/users/${this.selectedAuditUser.id}/audit-logs`,
          {
            params: { page },
          }
        );

        const result = response.data;

        this.auditLogs = Array.isArray(result.data)
          ? result.data
          : [];

        this.auditPagination = {
          current_page: result.current_page || 1,
          last_page: result.last_page || 1,
          total: result.total || 0,
        };
      } catch (error) {
        console.error('Failed to load audit history:', error);

        this.auditLogs = [];

        this.auditError =
          error.response?.data?.message ||
          'Unable to load audit history. Check your access permissions and try again.';
      } finally {
        this.loadingAuditLogs = false;
      }
    },

    formatAuditDate(value) {
      if (!value) return '—';

      const date = new Date(value);

      if (Number.isNaN(date.getTime())) {
        return value;
      }

      return date.toLocaleString();
    },

    async saveUser() {
      this.submitting = true;
      this.formError = '';

      const payload = {
        name: this.form.name,
        email: this.form.email,
        phone: this.form.phone || null,
        dob: this.form.dob || null,
        address: this.form.address || null,
        city: this.form.city || null,
        postal_code: this.form.postal_code || null,
        role: this.form.role,
        status: this.form.status,
        membership_type: this.form.membership_type,
        borrow_limit: this.form.borrow_limit,
      };

      try {
        if (this.editing) {
          await axios.put(
            `/api/users/update-user/${this.form.id}`,
            payload
          );

          toast.fire({
            icon: 'success',
            title: 'User updated successfully',
          });
        } else {
          await axios.post('/api/users/create-user', {
            ...payload,
            password: this.form.password,
          });

          toast.fire({
            icon: 'success',
            title: 'User created successfully',
          });
        }

        this.hideModal('userFormModal');
        await this.loadUsers();
      } catch (error) {
        console.error('Failed to save user:', error);

        const errors = error.response?.data?.errors;

        this.formError = errors
          ? Object.values(errors).flat().join(' ')
          : error.response?.data?.message || 'Unable to save user.';
      } finally {
        this.submitting = false;
      }
    },

    async deleteUser(user) {
      const result = await Swal.fire({
        title: 'Delete user?',
        text: `This will delete ${user.name}'s account.`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#006400',
        cancelButtonColor: '#6c757d',
        confirmButtonText: 'Yes, delete',
      });

      if (!result.isConfirmed) return;

      try {
        await axios.delete(`/api/users/${user.id}`);

        toast.fire({
          icon: 'success',
          title: 'User deleted successfully',
        });

        await this.loadUsers();
      } catch (error) {
        console.error('Failed to delete user:', error);

        Swal.fire({
          icon: 'error',
          title: 'Delete failed',
          text:
            error.response?.data?.message ||
            'The user could not be deleted.',
        });
      }
    },
  },

  mounted() {
    this.loadUsers();
  },
};
</script>

<style scoped>
.green-btn {
  background-color: darkgreen;
  border-color: darkgreen;
}

.green-btn:hover,
.green-btn:focus {
  background-color: #004d00;
  border-color: #004d00;
}

.table th {
  white-space: nowrap;
}

.audit-description {
  min-width: 220px;
  max-width: 360px;
  overflow-wrap: anywhere;
}
</style>
