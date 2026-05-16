<template>
  <div class="modal fade" id="invoiceModal">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">

        <!-- HEADER -->
        <div class="modal-header">
          <div>
            <h5 class="modal-title">Invoice</h5>
            <small class="text-muted">Full invoice details</small>
          </div>

          <button class="btn-close" data-bs-dismiss="modal"></button>
        </div>

        <!-- BODY -->
        <div class="modal-body" v-if="invoice">

          <!-- HEADER INFO -->
          <div class="mb-3">
            <strong>Invoice #:</strong> {{ invoice.invoice_number }} <br>
            <strong>Status:</strong>
            <span class="badge bg-primary">{{ invoice.status }}</span>
          </div>

          <!-- CUSTOMER -->
          <div class="mb-3" v-if="invoice.customer">
            <strong>Customer:</strong> {{ invoice.customer.name }} <br>
            <strong>Email:</strong> {{ invoice.customer.email }} <br>
            <strong>Phone:</strong> {{ invoice.customer.phone }}
          </div>

          <!-- ITEMS TABLE -->
          <table class="table table-bordered align-middle">
            <thead class="table-light">
              <tr>
                <th>Description</th>
                <th width="120">Qty</th>
                <th width="140">Unit Price</th>
                <th width="140">Total</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="item in invoice.items" :key="item.id">
                <td>{{ item.description }}</td>
                <td>{{ item.quantity }}</td>
                <td>{{ item.unit_price }}</td>
                <td class="fw-bold">
                  {{ item.line_total }}
                </td>
              </tr>
            </tbody>
          </table>

          <!-- TOTAL -->
          <div class="text-end fs-5 mt-3">
            <strong>Total: {{ invoice.total_amount }}</strong>
          </div>

        </div>

        <!-- FOOTER -->
        <div class="modal-footer">
          <button class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>
        </div>

      </div>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      invoice: null
    };
  },

  methods: {

    openInvoice(invoice) {
      this.invoice = invoice;

      this.$nextTick(() => {
        new bootstrap.Modal(
          document.getElementById("invoiceModal")
        ).show();
      });
    }

  }
};
</script>