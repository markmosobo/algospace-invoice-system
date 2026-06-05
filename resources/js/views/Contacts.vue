<template>
  <Master>
    <section class="section dashboard">
      <div class="row">

        <div class="col-12">
          <div class="card top-selling overflow-auto">

            <div class="card-body pb-0">

              <h5 class="card-title">
                Inbox <span>| Customer Messages</span>
              </h5>

              <!-- SEARCH -->
              <div class="mb-3">
                <input
                  v-model="searchQuery"
                  class="form-control form-control-sm"
                  placeholder="Search messages... type 'read' or 'unread'"
                />
              </div>

              <!-- TABLE -->
              <table class="table table-borderless">

                <thead>
                  <tr>
                    <th>Status</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Phone</th>
                    <th>Message</th>
                    <th>Action</th>
                  </tr>
                </thead>

                <!-- LOADING -->
                <tbody v-if="initializing">
                  <tr>
                    <td colspan="6" class="text-center">
                      <div class="spinner-border text-primary"></div>
                    </td>
                  </tr>
                </tbody>

                <!-- DATA -->
                <tbody v-else>
                  <tr
                    v-for="item in filteredContacts"
                    :key="item.id"
                    :class="item.is_read ? '' : 'table-warning'"
                  >

                    <td>
                      <span class="badge" :class="item.is_read ? 'bg-secondary' : 'bg-success'">
                        {{ item.is_read ? 'Read' : 'Unread' }}
                      </span>
                    </td>

                    <td><b>{{ item.name }}</b></td>
                    <td>{{ item.email }}</td>
                    <td>{{ item.phone }}</td>

                    <td>
                      {{ item.message.substring(0, 40) }}...
                    </td>

                    <td>
                      <button class="btn btn-sm btn-primary" @click="viewContact(item)">
                        View
                      </button>

                      <button class="btn btn-sm btn-danger ms-1" @click="deleteContact(item.id)">
                        Delete
                      </button>
                    </td>

                  </tr>
                </tbody>

              </table>

            </div>
          </div>
        </div>

      </div>
    </section>

    <!-- VIEW MODAL -->
    <div class="modal fade" id="viewContactModal">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">

          <div class="modal-header">
            <h5 class="modal-title">
              Message from {{ selectedContact.name }}
            </h5>
            <button class="btn-close" data-bs-dismiss="modal"></button>
          </div>

          <div class="modal-body">

            <p><b>Email:</b> {{ selectedContact.email }}</p>
            <p><b>Phone:</b> {{ selectedContact.phone }}</p>

            <hr>

            <p style="white-space: pre-line;">
              {{ selectedContact.message }}
            </p>

          </div>

        </div>
      </div>
    </div>

  </Master>
</template>

<script>
import Master from "@/components/Master.vue";
import axios from "axios";
import Swal from "sweetalert2";

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
      contacts: [],
      selectedContact: {},
      searchQuery: "",
      initializing: true,
    };
  },

  computed: {
    filteredContacts() {
      let list = this.contacts;
      const q = (this.searchQuery || "").toLowerCase().trim();

      if (!q) return list;

      // Gmail style filters
      if (q.includes("unread")) {
        return list.filter(c => !c.is_read);
      }

      if (q.includes("read")) {
        return list.filter(c => c.is_read);
      }

      return list.filter(c =>
        (c.name || "").toLowerCase().includes(q) ||
        (c.email || "").toLowerCase().includes(q) ||
        (c.phone || "").toLowerCase().includes(q) ||
        (c.message || "").toLowerCase().includes(q)
      );
    }
  },

  methods: {

    loadContacts() {
      this.initializing = true;

      axios.get("/api/contacts")
        .then(res => {
          this.contacts = res.data;
        })
        .finally(() => {
          this.initializing = false;
        });
    },

    viewContact(item) {
      this.selectedContact = item;

      // mark as read (Gmail behavior)
      if (!item.is_read) {
        axios.patch(`/api/contacts/${item.id}/read`)
          .then(() => {
            item.is_read = true;
          });
      }

      const modal = new bootstrap.Modal(
        document.getElementById("viewContactModal")
      );
      modal.show();
    },

    deleteContact(id) {
      Swal.fire({
        title: "Delete message?",
        text: "This cannot be undone",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#006400"
      }).then(result => {
        if (result.isConfirmed) {
          axios.delete(`/api/contacts/${id}`)
            .then(() => {
              toast.fire("Deleted", "Message removed", "success");
              this.loadContacts();
            });
        }
      });
    }
  },

  mounted() {
    this.loadContacts();
  }
};
</script>