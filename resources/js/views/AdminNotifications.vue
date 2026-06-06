<template>
  <Master>
    <section class="section dashboard">

      <div class="card">
        <div class="card-body">

          <!-- HEADER -->
          <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="card-title mb-0">
              Notifications
              <span class="text-muted">| Contact Messages</span>
            </h5>

            <div class="btn-group btn-group-sm">
              <button class="btn btn-outline-primary" @click="filter = 'all'">
                All
              </button>
              <button class="btn btn-outline-warning" @click="filter = 'unread'">
                Unread
              </button>
              <button class="btn btn-outline-success" @click="filter = 'read'">
                Read
              </button>
            </div>
          </div>

          <!-- TABLE -->
          <table class="table table-borderless align-middle">
            <thead>
              <tr>
                <th>Sender</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th width="140">Action</th>
              </tr>
            </thead>

            <!-- LOADING -->
            <tbody v-if="loading">
              <tr>
                <td colspan="5" class="text-center py-5">
                  <div class="spinner-border text-primary"></div>
                </td>
              </tr>
            </tbody>

            <!-- EMPTY -->
            <tbody v-else-if="!filteredNotifications.length">
              <tr>
                <td colspan="5" class="text-center py-5 text-muted">
                  <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                  {{ emptyMessage }}
                </td>
              </tr>
            </tbody>

            <!-- DATA -->
            <tbody v-else>
              <tr
                v-for="n in filteredNotifications"
                :key="n.id"
                @click="open(n)"
                style="cursor:pointer"
              >

                <!-- Sender -->
                <td>
                  <strong>{{ n.name }}</strong>
                </td>

                <!-- Message -->
                <td>
                  {{ truncate(n.message, 80) }}
                </td>

                <!-- STATUS BADGE -->
                <td>
                  <span
                    class="badge"
                    :class="{
                      'bg-warning': !n.read_at,
                      'bg-success': n.read_at && !n.replied_at,
                      'bg-primary': n.replied_at
                    }"
                  >
                    {{
                      !n.read_at
                        ? 'Unread'
                        : n.replied_at
                          ? 'Replied'
                          : 'Read'
                    }}
                  </span>
                </td>

                <!-- DATE -->
                <td>
                  {{ formatDate(n.created_at) }}
                </td>

                <!-- ACTION -->
                <td class="d-flex gap-2 align-items-center">

                    <button
                        class="btn btn-sm btn-outline-secondary"
                        @click.stop="open(n)"
                    >
                        Open
                    </button>

                  <button
                    v-if="!n.read_at"
                    class="btn btn-sm btn-success"
                    @click.stop="markAsRead(n.id)"
                  >
                    Mark Read
                  </button>

                </td>

              </tr>
            </tbody>
          </table>

        </div>
      </div>

      <!-- ===================== MODAL ===================== -->
      <div v-if="selected" class="modal d-block bg-dark bg-opacity-50">

        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <!-- HEADER -->
            <div class="modal-header">
              <div>
                <h5 class="modal-title mb-0">
                  {{ selected.title }}
                </h5>
                <small class="text-muted">
                  {{ selected.email }}
                </small>
              </div>

              <button class="btn-close" @click="selected = null"></button>
            </div>

            <!-- BODY -->
            <div class="modal-body">

              <!-- STATUS -->
              <div class="mb-3">
                <span
                  class="badge"
                  :class="{
                    'bg-warning': !selected.read_at,
                    'bg-success': selected.read_at && !selected.replied_at,
                    'bg-primary': selected.replied_at
                  }"
                >
                  {{
                    !selected.read_at
                      ? 'Unread'
                      : selected.replied_at
                        ? 'Replied'
                        : 'Read'
                  }}
                </span>
              </div>

              <!-- MESSAGE -->
              <div class="p-3 bg-light rounded mb-4">
                {{ selected.message }}
              </div>

              <hr>

              <!-- REPLY SECTION (ONLY IF NOT REPLIED) -->
              <div v-if="!selected.replied_at">

                <h6 class="mb-2">Reply</h6>

                <input
                  v-model="reply.subject"
                  class="form-control mb-2"
                  placeholder="Subject (Re: ...)"
                />

                <textarea
                  v-model="reply.message"
                  rows="5"
                  class="form-control mb-3"
                  placeholder="Type your reply..."
                ></textarea>

                <button
                  class="btn btn-primary"
                  @click="sendReply(selected)"
                >
                  Send Reply
                </button>

              </div>

              <!-- ALREADY REPLIED -->
              <div v-else class="alert alert-primary">
                <strong>Already Replied</strong>
                <div class="mt-2">
                  This message was handled by {{ selected.replied_by }} on:
                  <br>
                  <small>{{ formatDate(selected.replied_at) }}</small>
                </div>
              </div>

            </div>

          </div>
        </div>

      </div>

    </section>
  </Master>
</template>

<script>
import axios from "axios";
import Master from "@/components/Master.vue";

export default {
  components: { Master },

  data() {
    return {
      notifications: [],
      loading: true,
      filter: "all",
      selected: null,
      reply: {
        subject: "",
        message: ""
      }
    };
  },

  computed: {
    filteredNotifications() {
      if (this.filter === "unread") {
        return this.notifications.filter(n => !n.read_at);
      }
      if (this.filter === "read") {
        return this.notifications.filter(n => n.read_at);
      }
      return this.notifications;
    },

    emptyMessage() {
      if (this.filter === "unread") return "No unread messages 🎉";
      if (this.filter === "read") return "No read messages yet";
      return "No messages available";
    }
  },

  methods: {

    open(n) {
      this.selected = n;

      if (!n.read_at) {
        this.markAsRead(n.id);
      }

      this.reply.subject = `Re: ${n.title}`;
      this.reply.message = "";
    },

    async sendReply(n) {
      await axios.post(`/api/notifications/${n.id}/reply`, {
        subject: this.reply.subject,
        message: this.reply.message
      });

      this.reply.subject = "";
      this.reply.message = "";

      this.load();
      this.selected = null;
    },

    async load() {
      this.loading = true;

      try {
        const res = await axios.get("/api/admin/notifications");
        this.notifications = res.data ?? [];
      } catch (e) {
        console.error(e);
      } finally {
        this.loading = false;
      }
    },

    async markAsRead(id) {
      await axios.post(`/api/notifications/${id}/read`);
      this.load();
    },

    truncate(text, len) {
      return text.length > len ? text.slice(0, len) + "…" : text;
    },

    formatDate(date) {
      return new Date(date).toLocaleString();
    }
  },

  mounted() {
    this.load();
  }
};
</script>

<style scoped>
.modal {
  display: flex;
  align-items: center;
  justify-content: center;
}
</style>