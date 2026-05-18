  <template>
  <Master>
  <section class="section dashboard">
    <div class="row">

      <div class="col-12">
        <div class="card top-selling overflow-auto">

          <div class="card-body pb-0">

            <h5 class="card-title">
              Cyber Requests <span>| Incoming client requests</span>
            </h5>

            <table id="RequestsTable" class="table table-borderless">

              <thead>
                <tr>
                  <th>Name</th>
                  <th>Service</th>
                  <th>Urgency</th>
                  <th>Status</th>
                  <th>Contact</th>
                  <th>Payment</th>
                  <th>Created</th>
                  <th>Last Update</th>
                  <th>Action</th>
                </tr>
              </thead>

              <!-- LOADING -->
              <tbody v-if="initializing">
                <tr>
                  <td colspan="9" class="text-center">
                    <div class="spinner-border text-primary"></div>
                  </td>
                </tr>
              </tbody>

              <!-- DATA -->
              <tbody v-else>
                <tr v-for="item in requests" :key="item.id">

                  <td>{{ item.name }}</td>

                  <td>
                    <span class="badge bg-dark">
                      {{ item.service?.name ?? '—' }}
                    </span>
                  </td>

                  <!-- URGENCY -->
                  <td>
                    <span
                      class="badge"
                      :class="{
                        'bg-success': item.urgency === 'Normal (24–48 hrs)',
                        'bg-warning': item.urgency === 'Urgent (Same Day)',
                        'bg-danger': item.urgency === 'Express (2–4 hrs)'
                      }"
                    >
                      {{ item.urgency ?? 'Normal' }}
                    </span>
                  </td>

                  <!-- STATUS -->
                  <td>
                    <span
                      class="badge"
                      :class="{
                        'bg-warning': item.status === 'pending',
                        'bg-info': item.status === 'processing',
                        'bg-primary': item.status === 'billed',
                        'bg-success': item.status === 'completed',
                        'bg-danger': item.status === 'cancelled'
                      }"
                    >
                      {{ item.status }}
                    </span>
                  </td>

                  <!-- CONTACT -->
                  <td>
                    <div>{{ item.phone }}</div>
                    <small>{{ item.email }}</small>
                  </td>
                  <td>
                    <span
                      class="badge"
                      :class="{
                        'bg-danger': item.payment_status === 'unpaid',
                        'bg-warning': item.payment_status === 'pending',
                        'bg-success': item.payment_status === 'paid'
                      }"
                    >
                      {{ item.payment_status }}
                    </span>
                  </td>
                  <!-- CREATED -->
                  <td>{{ item.created_at }}</td>
                  <td>{{ item.updated_at }}</td>

                  <!-- ACTION -->
                  <td>
                    <div class="btn-group">

                      <button
                        class="btn btn-sm btn-primary dropdown-toggle"
                        data-bs-toggle="dropdown"
                        style="background-color: darkgreen; border-color: darkgreen;"
                      >
                        Action
                      </button>

                      <div class="dropdown-menu">

                        <!-- VIEW ALWAYS -->
                        <a class="dropdown-item" @click="viewRequest(item)">
                          <i class="bi bi-eye"></i> View
                        </a>

                        <!-- GENERATE INVOICE (ONLY IF NO INVOICE) -->
                        <a
                          v-if="!item.invoice_id"
                          class="dropdown-item"
                          @click="generateInvoice(item)"
                        >
                          <i class="bi bi-receipt"></i> Generate Invoice
                        </a>

                        <!-- VIEW INVOICE (ONLY IF EXISTS) -->
                        <a
                          v-else
                          class="dropdown-item"
                          @click="viewInvoice(item)"
                        >
                          <i class="bi bi-eye"></i> View Invoice
                        </a>

                        <!-- PENDING → PROCESSING -->
                        <a
                          v-if="!(item.payment_type === 'prepay' && item.payment_status !== 'paid') && item.status === 'pending'"
                          class="dropdown-item"
                          @click="updateStatus(item, 'processing')"
                        >
                          Start Processing
                        </a>

                        <!-- PROCESSING → COMPLETED -->
                        <a
                          v-if="item.status === 'processing'"
                          class="dropdown-item"
                          @click="updateStatus(item, 'completed')"
                        >
                          <i class="bi bi-check-circle"></i> Mark Completed
                        </a>

                        <!-- CANCEL ONLY IF NOT COMPLETED -->
                        <a
                          v-if="item.status !== 'completed' && item.status !== 'cancelled'"
                          class="dropdown-item text-danger"
                          @click="updateStatus(item, 'cancelled')"
                        >
                          <i class="bi bi-x-circle"></i> Cancel Request
                        </a>

                        <a
                          v-if="item.payment_status !== 'paid'"
                          class="dropdown-item"
                          @click="updatePayment(item, 'paid')"
                        >
                          <i class="bi bi-cash-coin"></i> Mark Paid
                        </a>
                        <a
                          v-if="item.payment_status === 'paid'"
                          class="dropdown-item text-warning"
                          @click="updatePayment(item, 'unpaid')"
                        >
                          <i class="bi bi-arrow-counterclockwise"></i> Revert Payment
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

      <!-- VIEW MODAL -->
      <div class="modal fade" id="viewRequestModal">
        <div class="modal-dialog modal-lg">
          <div class="modal-content">

            <div class="modal-header">
              <h5 class="modal-title">Cyber Request Details</h5>
              <button class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div class="modal-body" v-if="selectedRequest">

              <div class="row g-3">

                <div class="col-md-6">
                  <strong>Name:</strong><br>
                  {{ selectedRequest.name }}
                </div>

                <div class="col-md-6">
                  <strong>Service:</strong><br>
                  {{ selectedRequest.service?.name ?? '—' }}
                </div>

                <div class="col-md-6">
                  <strong>Email:</strong><br>
                  {{ selectedRequest.email }}
                </div>

                <div class="col-md-6">
                  <strong>Phone:</strong><br>
                  {{ selectedRequest.phone }}
                </div>

                <div class="col-md-12">
                  <strong>Message:</strong><br>
                  {{ selectedRequest.message }}
                </div>

                <div class="col-md-12" v-if="selectedRequest.files?.length">
                  <strong>Files:</strong>
                  <ul>
                    <li v-for="file in selectedRequest.files" :key="file.id">
                      <a :href="`/storage/${file.file_path}`" target="_blank">
                        {{ file.file_name }}
                      </a>
                    </li>
                  </ul>
                </div>

              </div>

            </div>

          </div>
        </div>
      </div>

      <InvoiceReviewModal
        ref="invoiceReviewModal"
        @refresh="loadLists"
      />   

      <InvoiceModal
        ref="invoiceModal"
        @refresh="loadLists"
      />
    </div>
  </section>
  </Master>
  </template>

  <script>
  import Master from "@/components/Master.vue";
  import axios from "axios";
  import Swal from "sweetalert2";
  import $ from "jquery";
  import InvoiceReviewModal from "@/components/InvoiceReviewModal.vue";
  import InvoiceModal from "@/components/InvoiceModal.vue";

  const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
  });

  export default {
    components: { Master, InvoiceReviewModal, InvoiceModal },

    data() {
      return {
        requests: [],
        selectedRequest: null,
        initializing: true,
        invoiceDraft: null,
      };
    },

  computed: {
    formattedTotal() {
      if (!this.invoiceDraft) return "0";

      const base =
        this.invoiceDraft.final_pages *
        this.invoiceDraft.unit_price;

      const urgencyMultiplier = {
        normal: 1,
        urgent: 1.2,
        express: 1.5,
      }[this.invoiceDraft.urgency] ?? 1;

      return (
        base * urgencyMultiplier +
        this.invoiceDraft.extra_fees
      ).toLocaleString();
    },
  },  

    methods: {

      loadLists() {
        this.initializing = true;

        axios.get("/api/cyber-requests")
          .then((res) => {
            this.requests = res.data;

            setTimeout(() => {
              if ($.fn.DataTable.isDataTable("#RequestsTable")) {
                $("#RequestsTable").DataTable().destroy();
              }
              $("#RequestsTable").DataTable();
            }, 50);
          })
          .finally(() => {
            this.initializing = false;
          });
      },

      viewRequest(item) {
        this.selectedRequest = item;
        new bootstrap.Modal(
          document.getElementById("viewRequestModal")
        ).show();
      },
      generateInvoice(item) {
        this.$refs.invoiceReviewModal.openDraft(item.id);
      },
      viewInvoice(item) {
        this.$refs.invoiceModal.openInvoice({
          ...item.invoice,
          service: item.service
        });
      },  

      updateStatus(item, status) {
        axios.put(`/api/cyber-requests/${item.id}`, { status })
          .then(() => {
            toast.fire("Updated", "Status changed", "success");
            this.loadLists();
          });
      },
      updatePayment(item, status) {
        axios.put(`/api/cyber-requests/${item.id}`, {
          payment_status: status
        }).then(() => {
          toast.fire("Updated", "Payment status changed", "success");
          this.loadLists();
        });
      }    
    },

    mounted() {
      this.loadLists();
    }
  };
  </script>