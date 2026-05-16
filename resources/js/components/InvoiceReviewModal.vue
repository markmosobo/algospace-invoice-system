<template>
  <div class="modal fade" id="invoiceReviewModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header">
          <div>
            <h5 class="modal-title">Invoice Review</h5>
            <small class="text-muted">
              Human review + system suggestion
            </small>
          </div>
          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body" v-if="draft">

          <!-- SMART SUMMARY -->
          <div class="card mb-3 bg-light border-0">
            <div class="card-body d-flex justify-content-between">

              <div>
                <strong>{{ draft.service_name }}</strong><br>
                <small class="text-muted">
                  Client: {{ draft.client_name }}
                </small>
              </div>

              <div class="text-end">
                <div class="badge bg-dark">
                  Base: KES {{ draft.service_price }}
                </div>

                <div class="badge bg-info mt-1">
                  × {{ draft.urgency_multiplier }} urgency
                </div>

                <div class="badge bg-success mt-1">
                  Suggested: KES {{ draft.suggested_total }}
                </div>
              </div>

            </div>
          </div>

          <!-- CONFIDENCE -->
          <div class="alert" :class="confidenceClass">
            Confidence: {{ Math.round(draft.confidence * 100) }}%
          </div>

          <!-- ITEMS -->
          <table class="table table-bordered">
            <thead>
              <tr>
                <th>Description</th>
                <th>Pages</th>
                <th>Unit Price</th>
                <th>Total</th>
                <th></th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="(item, i) in draft.items" :key="i">

                <td>
                  <input v-model="item.description" class="form-control" />
                </td>

                <td>
                  <input type="number"
                         v-model.number="item.pages"
                         class="form-control" />
                  <small class="text-muted">
                    System: {{ draft.system_pages }}
                  </small>
                </td>

                <td>
                  <input type="number"
                         v-model.number="item.unit_price"
                         class="form-control" />
                </td>

                <td>
                  {{ item.pages * item.unit_price }}
                </td>

                <td>
                  <button class="btn btn-danger btn-sm"
                          @click="removeItem(i)">
                    ×
                  </button>
                </td>

              </tr>
            </tbody>
          </table>

          <button class="btn btn-outline-primary btn-sm"
                  @click="addItem">
            + Add Item
          </button>

          <!-- SUMMARY -->
          <div class="mt-4 p-3 bg-light rounded">
            <div class="d-flex justify-content-between">
              <span>Subtotal</span>
              <strong>{{ subtotal }}</strong>
            </div>

            <div class="d-flex justify-content-between">
              <span>Tax</span>
              <strong>{{ tax }}</strong>
            </div>

            <hr />

            <div class="d-flex justify-content-between fs-5">
              <span>Total</span>
              <strong>{{ total }}</strong>
            </div>
          </div>

          <!-- NOTES -->
          <textarea v-model="notes"
                    class="form-control mt-3"
                    placeholder="Internal notes"></textarea>

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
            {{ saving ? "Saving..." : "Confirm Invoice" }}
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
    subtotal() {
      if (!this.draft) return 0;

      return this.draft.items.reduce((sum, i) => {
        return sum + (i.pages * i.unit_price);
      }, 0);
    },

    tax() {
      return 0;
    },

    total() {
      return this.subtotal + this.tax;
    },

    confidenceClass() {
      if (!this.draft) return "";
      if (this.draft.confidence >= 0.8) return "alert-success";
      if (this.draft.confidence >= 0.5) return "alert-warning";
      return "alert-danger";
    }
  },

  methods: {

    openDraft(requestId) {
      axios.get(`/api/cyber-requests/${requestId}/invoice-draft`)
        .then(res => {
          this.draft = res.data.draft;
          this.notes = "";

          this.$nextTick(() => {
            new bootstrap.Modal(
              document.getElementById("invoiceReviewModal")
            ).show();
          });
        });
    },

    addItem() {
      this.draft.items.push({
        description: "",
        pages: 1,
        unit_price: 0
      });
    },

    removeItem(i) {
      this.draft.items.splice(i, 1);
    },

    confirmInvoice() {
      this.saving = true;

      axios.post(
        `/api/cyber-requests/${this.draft.request_id}/confirm-invoice`,
        {
          items: this.draft.items,
          notes: this.notes
        }
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
      });
    }
  }
};
</script>