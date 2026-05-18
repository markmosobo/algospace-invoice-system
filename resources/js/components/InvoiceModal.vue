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
      <div class="modal-body bg-light" v-if="invoice">

        <!-- CAPTURE AREA -->
        <div ref="invoiceArea" class="bg-white p-4 rounded shadow-sm">

          <!-- BRAND -->
          <div class="d-flex justify-content-between align-items-center mb-4 pb-3 border-bottom">

            <div class="d-flex align-items-center gap-3">
              <img src="@/assets/algospacelogo.png" style="height:55px;" />

              <div>
                <div class="fw-bold">AlgoSpace Cyber</div>
                <div class="text-muted small">Professional Digital Services</div>
              </div>
            </div>

            <div class="text-end">
              <div class="text-muted small">Invoice</div>
              <div class="fw-bold text-primary">
                #{{ invoice.invoice_number }}
              </div>
            </div>

          </div>

          <!-- CUSTOMER -->
          <div class="row mb-4">
            <div class="col-md-6">
              <h6 class="text-muted">Bill To</h6>
              <div class="fw-semibold">{{ invoice.customer.name }}</div>
              <div class="small text-muted">{{ invoice.customer.email }}</div>
              <div class="small text-muted">{{ invoice.customer.phone }}</div>
            </div>

            <div class="col-md-6 text-md-end">
              <h6 class="text-muted">Service</h6>
              <div class="fw-bold">{{ invoice.service?.name }}</div>
              <div class="text-muted small">
                KES {{ invoice.service?.price }}
              </div>
            </div>
          </div>

          <!-- ITEMS -->
          <table class="table table-borderless">
            <thead>
              <tr class="text-muted small">
                <th>Description</th>
                <th class="text-center">Qty</th>
                <th class="text-end">Unit</th>
                <th class="text-end">Total</th>
              </tr>
            </thead>

            <tbody>
              <tr v-for="item in invoice.items" :key="item.id">
                <td>
                  <div class="fw-semibold">{{ invoice.service?.name }}</div>
                  <div class="text-muted small">{{ item.description }}</div>
                </td>
                <td class="text-center">{{ item.quantity }}</td>
                <td class="text-end">{{ item.unit_price }}</td>
                <td class="text-end fw-bold">{{ item.line_total }}</td>
              </tr>
            </tbody>
          </table>

          <!-- TOTAL -->
          <div class="d-flex justify-content-end border-top pt-3">
            <h4 class="text-success">
              KES {{ invoice.total_amount }}
            </h4>
          </div>

          <!-- PAYMENT -->
          <div class="mt-3 p-3 border rounded bg-light">
            <div class="fw-bold mb-2">PAYMENT DETAILS</div>

            <div class="d-flex justify-content-between">
              <span>Paybill</span>
              <strong>542542</strong>
            </div>

            <div class="d-flex justify-content-between">
              <span>Account</span>
              <strong>608755</strong>
            </div>

            <div class="d-flex justify-content-between text-success">
              <span>Amount</span>
              <strong>KES {{ invoice.total_amount }}</strong>
            </div>
          </div>

        </div>
      </div>

      <!-- FOOTER ACTIONS -->
      <div class="modal-footer d-flex justify-content-between">

        <div class="d-flex gap-2">

          <button class="btn btn-primary" @click="downloadImage">
            📸 Image
          </button>

          <button class="btn btn-dark" @click="downloadPDF">
            📄 PDF
          </button>

          <button class="btn btn-success" @click="shareWhatsApp">
            WhatsApp
          </button>

          <button
            class="btn btn-outline-primary"
            @click="sendEmail"
            :disabled="sendingEmail"
          >
            📧 Email
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
import jsPDF from "jspdf";

export default {
  data() {
    return {
      invoice: null,
      sendingEmail: false
    };
  },

  methods: {

    // =========================
    // IMAGE DOWNLOAD
    // =========================
    async downloadImage() {
      const el = this.$refs.invoiceArea;

      const canvas = await html2canvas(el, {
        scale: 2,
        useCORS: true,
        backgroundColor: "#fff"
      });

      const image = canvas.toDataURL("image/png");

      const a = document.createElement("a");
      a.href = image;
      a.download = `invoice-${this.invoice.invoice_number}.png`;
      a.click();
    },

    // =========================
    // PDF DOWNLOAD
    // =========================
    async downloadPDF() {
      const el = this.$refs.invoiceArea;

      const canvas = await html2canvas(el, {
        scale: 2,
        useCORS: true,
        backgroundColor: "#fff"
      });

      const imgData = canvas.toDataURL("image/png");

      const pdf = new jsPDF("p", "mm", "a4");

      const pageWidth = 210;
      const pageHeight = 297;

      const imgWidth = pageWidth;
      const imgHeight = (canvas.height * imgWidth) / canvas.width;

      pdf.addImage(imgData, "PNG", 0, 0, imgWidth, imgHeight);
      pdf.save(`invoice-${this.invoice.invoice_number}.pdf`);
    },

    // =========================
    // WHATSAPP SHARE
    // =========================
    shareWhatsApp() {

      let phone = this.invoice.customer.phone;

      if (phone.startsWith("0")) {
        phone = "254" + phone.slice(1);
      }

      const message =
`Hello ${this.invoice.customer.name},

Invoice #: ${this.invoice.invoice_number}
Service: ${this.invoice.service?.name}
Amount: KES ${this.invoice.total_amount}

Paybill: 542542
Account: 608755

— AlgoSpace Cyber`;

      window.open(
        `https://wa.me/${phone}?text=${encodeURIComponent(message)}`,
        "_blank"
      );
    },

    async sendEmail() {
      if (!this.invoice?.id) return;

      this.sendingEmail = true;

      try {
        await axios.post("/api/invoice/send-pdf", {
          invoice_id: this.invoice.id
        });

        alert("Invoice emailed successfully ✅");

      } catch (err) {
        console.error(err);
        alert("Failed to send invoice ❌");
      } finally {
        this.sendingEmail = false;
      }
    },    

    // =========================
    // OPEN MODAL
    // =========================
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