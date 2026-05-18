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

        <!-- BODY (IMPORTANT: capture wrapper) -->
        <div class="modal-body bg-light" v-if="invoice">

          <!-- CAPTURE AREA START -->
          <div ref="invoiceArea" class="bg-white p-4 rounded shadow-sm">

            <!-- INVOICE HEADER -->
            <div class="d-flex justify-content-between border-bottom pb-3 mb-3">
              <div>
                <h4 class="text-primary fw-bold">INVOICE</h4>
                <div class="text-muted">
                  #{{ invoice.invoice_number }}
                </div>
              </div>

              <div>
                <span class="badge bg-warning text-dark">
                  {{ invoice.status }}
                </span>
              </div>
            </div>

            <!-- CUSTOMER + SERVICE -->
            <div class="row mb-4">
              <div class="col-md-6">
                <h6 class="text-muted">Customer</h6>
                <div class="fw-semibold">{{ invoice.customer.name }}</div>
                <div class="small text-muted">{{ invoice.customer.email }}</div>
                <div class="small text-muted">{{ invoice.customer.phone }}</div>
              </div>

              <div class="col-md-6 text-md-end">
                <h6 class="text-muted">Service</h6>
                <div class="fw-bold text-primary">
                  {{ invoice.service?.name }}
                </div>
                <div class="small text-muted">
                  KES {{ invoice.service?.price }}
                </div>
              </div>
            </div>

            <!-- ITEMS -->
            <table class="table table-borderless">
              <thead class="border-bottom">
                <tr class="text-muted small">
                  <th>Description</th>
                  <th class="text-center">Qty</th>
                  <th class="text-end">Unit Price</th>
                  <th class="text-end">Total</th>
                </tr>
              </thead>

              <tbody>
                <tr v-for="item in invoice.items" :key="item.id">
                  <td>
                    <div class="fw-semibold text-primary">
                      {{ invoice.service?.name }}
                    </div>
                    <div class="small text-muted">
                      {{ item.description }}
                    </div>
                  </td>
                  <td class="text-center">{{ item.quantity }}</td>
                  <td class="text-end">{{ item.unit_price }}</td>
                  <td class="text-end fw-bold">{{ item.line_total }}</td>
                </tr>
              </tbody>
            </table>

            <!-- TOTAL -->
            <div class="d-flex justify-content-end border-top pt-3">
              <div class="text-end">
                <div class="text-muted">Total</div>
                <h4 class="text-success fw-bold">
                  KES {{ invoice.total_amount }}
                </h4>
              </div>
            </div>

            <!-- PAYMENT DETAILS (IMPORTANT FOR IMAGE) -->
            <div class="mt-4 p-3 border rounded bg-light">
              <h6 class="fw-bold mb-2">PAYMENT DETAILS</h6>

              <div class="d-flex justify-content-between">
                <span>Paybill</span>
                <span class="fw-bold">542542</span>
              </div>

              <div class="d-flex justify-content-between">
                <span>Account</span>
                <span class="fw-bold">
                  608755
                </span>
              </div>

              <div class="d-flex justify-content-between text-success mt-2">
                <span>Amount</span>
                <span class="fw-bold">
                  KES {{ invoice.total_amount }}
                </span>
              </div>
            </div>

          </div>
          <!-- CAPTURE AREA END -->

        </div>

        <!-- FOOTER -->
        <div class="modal-footer d-flex justify-content-between">

          <div>
            <button class="btn btn-success" @click="shareWhatsApp">
              📸 Share on WhatsApp
            </button>

            <button class="btn btn-danger" @click="sendEmailPDF">
              📄 Send PDF via Email
            </button>
          </div>

          <button class="btn btn-secondary" data-bs-dismiss="modal">
            Close
          </button>

        </div>

      </div>
    </div>
  </div>
</template>

<script>
import html2canvas from "html2canvas";
export default {
  data() {
    return {
      invoice: null
    };
  },

  methods: {
    async shareWhatsApp() {
      const el = this.$refs.invoiceArea;

      if (!el) return;

      // prevent clipping from scroll containers
      const originalOverflow = el.style.overflow;
      el.style.overflow = "visible";

      const canvas = await html2canvas(el, {
        scale: 2,
        useCORS: true,
        backgroundColor: "#ffffff",
        scrollX: 0,
        scrollY: -window.scrollY
      });

      el.style.overflow = originalOverflow;

      // IMAGE
      const image = canvas.toDataURL("image/png");

      // AUTO DOWNLOAD (important for WhatsApp manual sharing)
      const link = document.createElement("a");
      link.href = image;
      link.download = `invoice-${this.invoice.invoice_number}.png`;
      link.click();

      // WHATSAPP MESSAGE
      const phone = this.invoice.customer.phone;

      const text =
  `Hello ${this.invoice.customer.name},

  Invoice #: ${this.invoice.invoice_number}
  Service: ${this.invoice.service?.name}
  Amount: KES ${this.invoice.total_amount}

  Payment Details:
  Paybill: 542542
  Account: 608755

  Kindly proceed with payment.`;

      window.open(
        `https://wa.me/${phone}?text=${encodeURIComponent(text)}`,
        "_blank"
      );
    }, 
    async sendEmailPDF() {
      await axios.post("/api/invoice/send-pdf", {
        invoice_id: this.invoice.id
      });

      alert("Invoice sent to email successfully!");
    },    

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