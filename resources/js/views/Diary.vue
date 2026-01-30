<template>
    <Master>
        <section class="section dashboard">
          <div class="row">
    
                <!-- Top Selling -->
                <div class="col-12">
                  <div class="card top-selling overflow-auto">
    
                    <div class="filter">
                    <!--                       <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                      <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                        <li class="dropdown-header text-start">
                          <h6>Filter</h6>
                        </li>
    
                        <li><a class="dropdown-item" href="#">Today</a></li>
                        <li><a class="dropdown-item" href="#">This Month</a></li>
                        <li><a class="dropdown-item" href="#">This Year</a></li>
                      </ul> -->
                    </div>
    
                    <div class="card-body pb-0">
                      <h5 class="card-title">Diary entries <span>| Diary entries at AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <div class="col d-flex">
                   
                   
                                <a
                                  :href="href"
                                  :class="{ active: isActive }"
                                  class="btn btn-sm btn-primary rounded-pill"
                                  style="background-color: darkgreen; border-color: darkgreen;"
                                  @click="addEntry()"
                                >
                                  Add Entry
                                </a>
                          </div>
                          <div class="col-auto d-flex justify-content-end">
                          <div class="btn-group" role="group">
                              <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="ri-add-line"></i>
                              </button>
                              <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
                                     <a @click="navigateTo('/clients' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Clients</a>
                                    <a @click="navigateTo('/savings' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Savings</a>
                                    <a @click="navigateTo('/loans' )" class="dropdown-item" href="#"><i class="ri-user-fill mr-2"></i>Loans</a>
                                </div>
                              </div>
                            </div>
                        </div>   
            
                      </p>
    
<table id="EntriesTable" class="table table-borderless">
  <thead>
    <tr>
      <th scope="col">Title</th>
      <th scope="col">Date</th>
      <th scope="col">Description</th>
      <th scope="col">Action</th>
    </tr>
  </thead>
  <!-- Spinner while loading -->
  <tbody v-if="initializing">
    <tr>
      <td colspan="7" class="text-center">
        <div class="spinner-border text-primary" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </td>
    </tr>
  </tbody>
  <tbody v-else>
    <tr v-for="item in diaryEntries" :key="item.id">
      <td>
        <span class="fw-semibold">{{ item.title }}</span>

        <span
          class="badge ms-2 text-uppercase"
          :class="item.category === 'office' ? 'bg-primary' : 'bg-secondary'"
          style="font-size: 0.65rem; letter-spacing: 0.08em;"
        >
          {{ item.category }}
        </span>
      </td>

      <!-- Format date as dd/mm/yyyy -->
      <td>{{ formatDate(item.entry_date) }}</td>

      <!-- Truncate description to 30 characters -->
      <td>{{ truncateText(item.description, 30) }}</td>

      <td>
        <div class="btn-group" role="group">
          <button id="btnGroupDrop1" type="button"
                  style="background-color: darkgreen; border-color: darkgreen;"
                  class="btn btn-sm btn-primary rounded-pill dropdown-toggle"
                  data-toggle="dropdown" data-bs-toggle="dropdown"
                  aria-haspopup="true" aria-expanded="false">
            Action
          </button>
          <div class="dropdown-menu" aria-labelledby="btnGroupDrop1">
            <a @click="viewEntry(item)" class="dropdown-item" href="#">
              <i class="ri-eye-fill mr-2"></i>View
            </a> 
            <a @click="editEntry(item)" class="dropdown-item" href="#">
              <i class="ri-pencil-fill mr-2"></i>Edit
            </a>
            <a @click="deleteEntry(item.id)" class="dropdown-item" href="#">
              <i class="ri-delete-bin-line mr-2"></i>Delete
            </a>
          </div>
        </div>
      </td>
    </tr>
  </tbody>
</table>

    
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

                <!-- View Entry Modal -->
                <div class="modal fade" id="viewEntryModal" tabindex="-1" aria-labelledby="viewEntryModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">View Entry Details</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body" v-if="selectedEntry">
                        <div class="row g-3">

                          <!-- Title + Category Badge -->
                          <div class="col-md-6" v-if="selectedEntry.title">
                            <strong>Title:</strong><br>
                            {{ selectedEntry.title }}
                            <span
                              class="badge ms-2"
                              :class="selectedEntry.category === 'office' ? 'bg-primary' : 'bg-success'"
                              v-if="selectedEntry.category"
                            >
                              {{ selectedEntry.category }}
                            </span>
                          </div>


                          <!-- Entry Date -->
                          <div class="col-md-6" v-if="selectedEntry.entry_date">
                            <strong>Entry Date:</strong> <br> {{ formatDateTime(selectedEntry.entry_date) }}
                          </div>

                          <!-- Remind At (for reminders/events) -->
                          <div 
                            class="col-md-6" 
                            v-if="selectedEntry.remind_at && (selectedEntry.type === 'reminder' || selectedEntry.type === 'event')"
                          >
                            <strong>Remind At:</strong> <br> 
                            <span :class="remindAtClass(selectedEntry.remind_at)">
                              {{ formatDateTime(selectedEntry.remind_at) }}
                            </span>
                          </div>


                          <!-- Type -->
                          <div class="col-md-6" v-if="selectedEntry.type">
                            <strong>Type:</strong> <br> {{ selectedEntry.type }}
                          </div>

                          <!-- Amount (for credit/debit) -->
                          <div class="col-md-6" v-if="selectedEntry.type === 'credit' || selectedEntry.type === 'debit'">
                            <strong>Amount:</strong> <br> {{ selectedEntry.amount }}
                          </div>

                          <!-- Tags -->
                          <div class="col-md-6" v-if="selectedEntry.tags">
                            <strong>Tags:</strong> <br> {{ selectedEntry.tags }}
                          </div>

                          <!-- Description -->
                          <div class="col-12" v-if="selectedEntry.description">
                            <strong>Description:</strong> <br> {{ selectedEntry.description }}
                          </div>

                          <!-- Attachment -->
                          <div class="col-12" v-if="selectedEntry.attachment">
                            <strong>Attachment:</strong> <br>
                            <a :href="selectedEntry.attachment" target="_blank">View / Download</a>
                          </div>

                          <!-- Status (for reminder/event) -->
                          <div class="col-md-6" v-if="selectedEntry.type === 'reminder' || selectedEntry.type === 'event'">
                            <strong>Status:</strong> <br> {{ selectedEntry.status }}
                          </div>

                        </div>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                      </div>

                    </div>
                  </div>
                </div>

                <!-- Add Entry Modal -->
                <div class="modal fade" id="addEntryModal" tabindex="-1" aria-labelledby="addEntryModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="addEntryModalLabel">Add Diary Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- Hidden ID (for edit) -->
                          <input type="hidden" v-model="data.id" />

                          <!-- Title -->
                          <div class="col-md-6">
                            <label class="form-label">Title*</label>
                            <input type="text" class="form-control" id="title" v-model="data.title" required>
                          </div>

                          <!-- Category -->
                          <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" v-model="data.category">
                              <option value="">Select</option>
                              <option value="office">Office</option>
                              <option value="personal">Personal</option>
                            </select>
                          </div>

                          <!-- Type -->
                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" v-model="data.type">
                              <option value="">Select</option>
                              <option value="note">Note</option>
                              <option value="credit">Credit</option>
                              <option value="debit">Debit</option>
                              <option value="reminder">Reminder</option>
                              <option value="event">Event</option>
                            </select>
                          </div>

                          <!-- Amount (only for credit/debit) -->
                          <div class="col-md-6" v-if="data.type === 'credit' || data.type === 'debit'">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" v-model="data.amount">
                          </div>

                          <!-- Tags -->
                          <div class="col-md-6">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control" v-model="data.tags" placeholder="Comma separated">
                          </div>

                          <!-- Description -->
                          <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" v-model="data.description"></textarea>
                          </div>

                          <!-- Attachment -->
                          <div class="col-md-6">
                            <label class="form-label">Attachment</label>
                            <input type="file" class="form-control" @change="handleFileUpload">
                          </div>

                          <!-- Entry Date (not reminder) -->
                          <div class="col-md-6" v-if="data.type !== 'reminder'">
                            <label class="form-label">
                              {{ data.type === 'event' ? 'Event Date' : 'Entry Date' }}
                            </label>
                            <input type="datetime-local" class="form-control" v-model="data.entry_date">
                          </div>

                          <!-- Remind At (only for reminders) -->
                          <div class="col-md-6" v-if="data.type === 'reminder'">
                            <label class="form-label">Remind Me At</label>
                            <input type="datetime-local" class="form-control" v-model="data.remind_at">
                          </div>


                          <!-- Status (for reminder/event) -->
                          <div class="col-md-6" v-if="data.type === 'reminder' || data.type === 'event'">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="data.status">
                              <option value="pending">Pending</option>
                              <option value="done">Done</option>
                              <option value="overdue">Overdue</option>
                            </select>
                          </div>

                        </form>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submit" style="background: darkgreen; border-color: darkgreen;">
                          Save
                        </button>
                      </div>

                    </div>
                  </div>
                </div>


                <!-- EDIT Entry Modal -->
                <div class="modal fade" id="editEntryModal" tabindex="-1" aria-labelledby="editEntryModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Diary Entry</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Title -->
                          <div class="col-md-6">
                            <label class="form-label">Title*</label>
                            <input type="text" id="title_edit" class="form-control" v-model="form.title" required>
                          </div>

                          <!-- Category -->
                          <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" v-model="form.category">
                              <option value="">Select</option>
                              <option value="office">Office</option>
                              <option value="personal">Personal</option>
                            </select>
                          </div>

                          <!-- Type -->
                          <div class="col-md-6">
                            <label class="form-label">Type</label>
                            <select class="form-select" v-model="form.type">
                              <option value="">Select</option>
                              <option value="note">Note</option>
                              <option value="credit">Credit</option>
                              <option value="debit">Debit</option>
                              <option value="reminder">Reminder</option>
                              <option value="event">Event</option>
                            </select>
                          </div>

                          <!-- Amount (for credit/debit) -->
                          <div class="col-md-6" v-if="form.type === 'credit' || form.type === 'debit'">
                            <label class="form-label">Amount</label>
                            <input type="number" step="0.01" class="form-control" v-model="form.amount">
                          </div>

                          <!-- Tags -->
                          <div class="col-md-6">
                            <label class="form-label">Tags</label>
                            <input type="text" class="form-control" v-model="form.tags" placeholder="Comma separated">
                          </div>

                          <!-- Description -->
                          <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="3" v-model="form.description"></textarea>
                          </div>

                          <!-- Attachment -->
                          <div class="col-md-6">
                            <label class="form-label">Attachment</label>
                            <input type="file" class="form-control" @change="handleFileUpload">
                          </div>

                          <!-- Entry Date -->
                          <div class="col-md-6">
                            <label class="form-label">Entry Date</label>
                            <input type="datetime-local" class="form-control" v-model="form.entry_date">
                          </div>

                          <!-- Remind At (for reminder/event) -->
                          <div class="col-md-6" v-if="form.type === 'reminder' || form.type === 'event'">
                            <label class="form-label">Remind At</label>
                            <input type="datetime-local" class="form-control" v-model="form.remind_at">
                          </div>

                          <!-- Status (for reminder/event) -->
                          <div class="col-md-6" v-if="form.type === 'reminder' || form.type === 'event'">
                            <label class="form-label">Status</label>
                            <select class="form-select" v-model="form.status">
                              <option value="pending">Pending</option>
                              <option value="done">Done</option>
                              <option value="overdue">Overdue</option>
                            </select>
                          </div>

                        </form>
                      </div>

                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submitChanges" style="background: darkgreen; border-color: darkgreen;">
                          Save Changes
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
    import Swal from 'sweetalert2';
    import "jquery/dist/jquery.min.js";
    import "datatables.net-dt/js/dataTables.dataTables";
    import "datatables.net-dt/css/jquery.dataTables.min.css";
    import $ from "jquery";
    
    const toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000
    });
    
    window.toast = toast;
    
    export default {
      data() {
        return {
            customers: [],
            selectedEntry: {},
            errors: {},
            initializing: true,
            submitting: false,

            data: {
              id: null,
              title: '',
              type: 'note',
              category: 'office',
              amount: null,
              tags: '',
              description: '',
              attachment: null,
              entry_date: '',
              remind_at: '',
              status: 'pending'
            },

            form: {
              id: '',
              title: '',
              type: '',
              category: '',
              amount: '',
              tags: '',
              description: '',
              attachment: '',
              entry_date: '',
              remind_at: '',
              status: ''
            },
        }
      },      
      methods: {   
        handleFileUpload(event) {
          this.data.attachment = event.target.files[0];
        }, 
          // Format date as dd/mm/yyyy
        formatDate(date) {
          if (!date) return "N/A";
          const d = new Date(date);
          const day = String(d.getDate()).padStart(2, '0');
          const month = String(d.getMonth() + 1).padStart(2, '0'); // Months are 0-based
          const year = d.getFullYear();
          return `${day}/${month}/${year}`;
        },
        formatDateTime(dateTime) {
            if (!dateTime) return "N/A";
            const d = new Date(dateTime);
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0'); // months are 0-indexed
            const year = d.getFullYear();

            const hours = String(d.getHours()).padStart(2, '0');
            const minutes = String(d.getMinutes()).padStart(2, '0');

            return `${day}/${month}/${year} ${hours}:${minutes}`;
          },
          remindAtClass(remindAt) {
          const now = new Date();
          const r = new Date(remindAt);

          const startOfToday = new Date();
          startOfToday.setHours(0,0,0,0);

          const endOfToday = new Date();
          endOfToday.setHours(23,59,59,999);

          const startOfTomorrow = new Date(startOfToday);
          startOfTomorrow.setDate(startOfTomorrow.getDate() + 1);

          const endOfTomorrow = new Date(endOfToday);
          endOfTomorrow.setDate(endOfTomorrow.getDate() + 1);

          if (r < now) return 'text-danger';        // overdue → red
          if (r >= startOfToday && r <= endOfToday) return 'text-warning'; // today → orange
          if (r >= startOfTomorrow && r <= endOfTomorrow) return 'text-info'; // tomorrow → blue

          return 'text-secondary'; // default / later dates
        },
        // Truncate text to specified length
        truncateText(text, maxLength = 30) {
          if (!text) return "N/A";
          return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
        },            
        viewEntry(item)
        {
          console.log(this.selectedEntry)
          this.selectedEntry = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewEntryModal'));
          modal.show();
        },
        editEntry(item) {
        this.form = {
            id: item.id,
            title: item.title,
            type: item.type,
            amount: item.amount,
            category: item.category,
            tags: item.tags,
            description: item.description,
            attachment: item.attachment,
            entry_date: item.entry_date,
            status: item.status,
        };

        const modal = new bootstrap.Modal(
            document.getElementById('editEntryModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.title) {
            document.getElementById('title_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('title_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/diary-entries/${this.form.id}`, this.form);

            toast.fire('Success!', 'Diary entry updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('editEntryModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update entry',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },

        addEntry()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('addEntryModal'));
          modal.show();
        },
        async submit() {
            if (this.validateForm()) {

                // Start submitting process
                this.submitting = true;
                
                try {
                    // Simulate asynchronous submission process (you would replace this with your actual submission logic)
                    await this.submitForm();

                    // Submission successful
                    this.submitted = true;
                } catch (error) {
                    // Handle submission error
                    console.error("Submission error:", error);
                } finally {
                    // End submitting process
                    this.submitting = false;
                }
            }
        },
        validateForm() {
        let isValid = true;

        if (!this.data.title) {
            document.getElementById('title').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('title').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/diary-entries', this.data);

            toast.fire('Success!', 'Diary entry added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('addEntryModal')
            );
            modal.hide();

            // Reset form
            this.data = {
              id: '',
              title: '',
              type: '',
              category: '',
              amount: '',
              tags: '',
              description: '',
              attachment: '',
              entry_date: '',
              status: ''
            };

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Something went wrong',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },
        navigateTo(location){
            this.$router.push(location)
        },
        deleteEntry(id){
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
                  axios.delete('/api/diary-entries/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Diary entry has been deleted.',
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
        loadLists() {
          this.initializing = true; // Start spinner
          axios.get('/api/diary-entries')
            .then((response) => {
              this.diaryEntries = response.data;
              console.log(response)

              setTimeout(() => {
                $("#EntriesTable").DataTable();
              }, 10);
            })
            .catch((error) => {
              console.error('Error fetching user list:', error);
            })
            .finally(() => {
              this.initializing = false; // Stop spinner
            });
        },
      },
      components : {
          Master,
      },
      mounted(){
        this.loadLists();
        // this.user = localStorage.getItem('user');
        // this.user = JSON.parse(this.user);
        // this.userId = this.user.id;
        // this.currentUser = JSON.parse(localStorage.getItem('user')) || {};
        // this.current_user_id = this.currentUser.id;
        // this.current_user = this.currentUser.first_name + " " + this.currentUser.last_name;

      }
    }
    </script>
    
    
    