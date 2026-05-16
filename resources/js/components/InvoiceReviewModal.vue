<template>
  <div class="modal fade" id="invoiceReviewModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header">
          <div>
            <h5 class="modal-title">Invoice Review</h5>
            <small class="text-muted">
              Smart billing adapts to request type
            </small>
          </div>

          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body" v-if="draft">

          <!-- MODE DETECTION -->
          <div class="alert alert-secondary">
            <strong>Billing Mode:</strong>

            <span v-if="hasFiles">
              Document-based (Pages detected)
            </span>

            <span v-else>
              Service-based (No attachments)
            </span>
          </div>

          <!-- INTELLIGENCE -->
          <div class="card mb-3 border-0 bg-light">
            <div class="card-body py-2 d-flex justify-content-between align-items-center">

              <div>
                <strong>Invoice Intelligence</strong><br>
                <small class="text-muted">
                  Adaptive pricing system
                </small>
              </div>

              <div class="text-end">
                <span class="badge bg-info">
                  Suggested Pages: {{ draft.system_pages }}
                </span>
              </div>

            </div>
          </div>

          <!-- CLIENT INFO -->
          <div class="mb-3">
            <strong>Client:</strong> {{ draft.client_name }} <br>
            <strong>Request ID:</strong> #{{ draft.request_id }} <br>
            <strong>Service:</strong> {{ draft.service_name || '—' }}
          </div>

          <!-- FILE SECTION (ONLY IF EXISTS) -->
          <div v-if="hasFiles" class="mb-3">
            <h6 class="text-muted">Attachments</h6>

            <div class="alert alert-light">
              This request includes documents. Pricing is page-based.
            </div>
          </div>

          <!-- SERVICE MODE NOTICE -->
          <div v-else class="alert alert-warning">
            No attachments found.
            You can still create an invoice manually for this service.
          </div>

          <!-- ITEMS TABLE -->
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Description</th>
                <th width="140">Pages</th>
                <th width="160">Unit Price</th>
                <th width="140">Total</th>
                <th width="60"></th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, i) in draft.items" :key="i">

                <td>
                  <input v-model="item.description" class="form-control">
                </td>

                <!-- PAGES ONLY MEANINGFUL IF FILES EXIST -->
                <td>
                  <input type="number"
                         v-model.number="item.pages"
                         class="form-control"
                         :disabled="!hasFiles">

                  <small class="text-muted">
                    <span v-if="hasFiles">
                      Suggested: {{ item.pages }} pages
                    </span>
                    <span v-else>
                      Flat service item
                    </span>
                  </small>
                </td>

                <td>
                  <input type="number"
                         v-model.number="item.unit_price"
                         class="form-control">
                </td>

                <td class="fw-bold">
                  {{ item.pages * item.unit_price }}
                </td>

                <td>
                  <button class="btn btn-sm btn-danger"
                          @click="removeItem(i)">
                    ×
                  </button>
                </td>

              </tr>
            </tbody>
          </table>

          <button class="btn btn-sm btn-outline-primary"
                  @click="addItem">
            + Add Item
          </button>

          <!-- SUMMARY -->
          <div class="mt-4 p-3 bg-light rounded">

            <div class="d-flex justify-content-between">
              <span>Subtotal</span>
              <strong>{{ subtotal }}</strong>
            </div>

            <hr>

            <div class="d-flex justify-content-between fs-5">
              <span>Total</span>
              <strong>{{ total }}</strong>
            </div>

          </div>

          <!-- NOTES -->
          <textarea v-model="notes"
                    class="form-control mt-3"
                    placeholder="Internal notes (optional)"></textarea>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button class="btn btn-secondary"
                  data-bs-dismiss="modal">
            Cancel
          </button>

          <button class="btn btn-success"
                  @click="confirmInvoice"
                  :disabled="saving">
            {{ saving ? 'Saving...' : 'Confirm Invoice' }}
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  data() {
    return {
      draft: null,
      notes: "",
      saving: false
    };
  },

  computed: {
    hasFiles() {
      return this.draft?.files?.length > 0;
    },

    subtotal() {
      if (!this.draft?.items?.length) return 0;

      return this.draft.items.reduce((sum, i) => {
        const qty = this.hasFiles ? (i.pages || 1) : 1;
        const price = Number(i.unit_price || 0);
        return sum + qty * price;
      }, 0);
    },

    total() {
      return this.subtotal;
    }
  },

  methods: {

    openDraft(requestId) {
      axios.get(`/api/cyber-requests/${requestId}/invoice-draft`)
        .then(res => {

          this.draft = res.data.draft;
          this.notes = "";

          // ✅ FIX: ensure items ALWAYS valid
          if (!Array.isArray(this.draft.items) || !this.draft.items.length) {
            this.draft.items = [{
              description: this.draft.service_name || "Cyber Service",
              pages: 1,
              unit_price: this.draft.unit_price || 0
            }];
          }

          // ✅ FIX: normalize all items
          this.draft.items = this.draft.items.map(i => ({
            description: i.description || this.draft.service_name || "Service",
            pages: i.pages || 1,
            unit_price: Number(i.unit_price || 0)
          }));

          this.$nextTick(() => {
            new bootstrap.Modal(
              document.getElementById("invoiceReviewModal")
            ).show();
          });
        });
    },

    addItem() {
      this.draft.items.push({
        description: this.draft.service_name || "Service",
        pages: 1,
        unit_price: 0
      });
    },

    removeItem(i) {
      this.draft.items.splice(i, 1);
    },

    confirmInvoice() {
      this.saving = true;

      const payload = {
        items: this.draft.items.map(i => ({
          // ✅ IMPORTANT: backend-safe mapping
          description: i.description || this.draft.service_name || "Service",
          name: i.description || this.draft.service_name || "Service",

          pages: i.pages || 1,
          unit_price: Number(i.unit_price || 0),

          quantity: this.hasFiles ? (i.pages || 1) : 1
        })),
        notes: this.notes
      };

      axios.post(
        `/api/cyber-requests/${this.draft.request_id}/confirm-invoice`,
        payload
      )
      .then(() => {
        this.saving = false;

        bootstrap.Modal.getInstance(
          document.getElementById("invoiceReviewModal")
        ).hide();

        this.$emit("refresh");
      })
      .catch(() => {
        this.saving = false;
        alert("Failed to confirm invoice");
      });
    }

  }
};
</script>