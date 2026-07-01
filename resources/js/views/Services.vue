<template>
    <Master>
        <section class="section dashboard">
          <div class="row">
    
                <!-- Top Selling -->
                <div class="col-12">
                  <div class="card top-selling overflow-auto">
    
                    <div class="card-body pb-0">
                      <h5 class="card-title">Services <span>| Services offered at AlgoSpace Cyber</span></h5>
                      <p class="card-text">
                        <div class="row">
                          <!-- LEFT SIDE: SEARCH + ACTION BUTTONS -->
                          <div class="d-flex flex-column">

                            <!-- SEARCH + CLEAR ROW -->
                            <div class="d-flex align-items-center flex-wrap gap-2 mb-1">

                              <!-- SEARCH -->
                              <div class="d-flex flex-column me-2">
                                <input
                                  type="text"
                                  v-model="searchQuery"
                                  class="form-control form-control-sm"
                                  placeholder="Search services..."
                                  style="max-width: 220px;"
                                />

                                <small class="text-muted mt-1">
                                  Tip: type <b>remote</b> for online + in-store, or <b>walkin</b> for in-store only
                                </small>
                              </div>

                              <!-- CLEAR -->
                              <button
                                v-if="searchQuery"
                                class="btn btn-sm btn-outline-secondary"
                                @click="clearFilters"
                              >
                                Clear
                              </button>

                            </div>

                            <!-- ACTION BUTTONS ROW (BELOW SEARCH) -->
                            <div class="d-flex flex-wrap gap-2">

                              <a
                                class="btn btn-sm btn-primary rounded-pill"
                                style="background-color: darkgreen; border-color: darkgreen;"
                                @click="addService()"
                              >
                                Add Service
                              </a>

                              <button
                                class="btn btn-sm btn-info rounded-pill"
                                @click="exportToPDF('all')"
                              >
                                Export Full PDF
                              </button>

                              <button
                                class="btn btn-sm btn-warning rounded-pill"
                                @click="exportToPDF('filtered')"
                              >
                                Print Search Results
                              </button>

                            </div>

                          </div>

                        </div>   
            
                      </p>
    
                      <table id="ServicesTable" class="table table-borderless">
                        <thead>
                          <tr>
                            <th scope="col">Name</th>
                            <th scope="col">Category</th>
                            <th scope="col">Price</th>
                            <th scope="col">Unit</th>
                            <th scope="col">Status</th>
                            <th scope="col">Action</th>
                          </tr>
                        </thead>
                        <!-- Spinner shown while data is initializing -->
                        <tbody v-if="initializing">
                          <tr>
                            <td colspan="6" class="text-center">
                              <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                        <tbody v-else>
                          <tr v-for="item in filteredServicesList" :key="item.id">
                            <td>{{item.name}}</td>
                            <td>{{item.category ?? "N/A"}}</td>
                            <td>{{item.price ?? "N/A"}}</td>
                            <td>{{item.unit ?? "N/A"}}</td>
                            <td>
                              <span class="badge" :class="item.is_active ? 'bg-success' : 'bg-secondary'">
                                  {{ item.is_active ? 'In-Store + Remote' : 'In-Store Only' }}
                              </span>
                            </td>
                           
                            <td>
                              <div class="btn-group" role="group">
                                  <button id="btnGroupDrop1" type="button" style="background-color: darkgreen; border-color: darkgreen;" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-toggle="dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                  Action
                                  </button>
                                  <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                  <a @click="viewService(item)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> 
                                  <a @click="editService(item)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                  <a @click="toggleService(item)" class="dropdown-item" href="#">
                                      <i class="ri-toggle-line mr-2"></i>

                                      {{ item.is_active ? 'Move to In-Store Only' : 'Enable Remote + In-Store' }}
                                  </a>
                                  <a @click="deleteService(item.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-line mr-2"></i>Delete</a>
                                  </div>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
   
                    </div>
    
                  </div>
                </div><!-- End Top Selling -->

              <!-- View Service Modal -->
              <div class="modal fade" id="viewServiceModal" tabindex="-1" aria-labelledby="viewServiceModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Service Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedService">

                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedService.name">
                          <strong>Name:</strong> <br> {{ selectedService.name }}
                        </div>

                        <div class="col-md-6" v-if="selectedService.category">
                          <strong>Category:</strong> <br> {{ selectedService.category }}
                        </div>

                        <div class="col-md-6" v-if="selectedService.price">
                          <strong>Price:</strong> <br> {{ selectedService.price }}
                        </div>

                        <div class="col-md-6" v-if="selectedService.unit">
                          <strong>Unit:</strong> <br> {{ selectedService.unit }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>


                <!-- Add Service Modal -->
                <div class="modal fade" id="AddServiceModal" tabindex="-1" aria-labelledby="AddServiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title" id="AddServiceModalLabel">Add Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3 needs-validation" novalidate>

                          <!-- First & Last Name -->
                          <div class="col-md-12">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name" class="form-control" v-model="data.name" required>
                          </div>

                          <!-- Category -->
                          <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" v-model="data.category">
                              <option value="">Select</option>
                              <option value="Printing & Copying">Printing & Copying</option>
                              <option value="Typing & Documents">Typing & Documents</option>
                              <option value="Online Applications">Online Applications</option>
                              <option value="Internet & Computer Use">Internet & Computer Use</option>
                              <option value="Training">Training</option>
                              <option value="Internet">Internet</option>
                              <option value="Other Services">Other Services</option>
                              <option value="Bundles">Bundles</option>
                            </select>
                          </div>

                          <!-- price -->
                          <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" v-model="data.price">
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" v-model="data.unit">
                          </div>

                          <!-- TYPE (only for Training) -->
                          <div class="col-md-6" v-if="data.category === 'Training'">
                            <label class="form-label">Type</label>
                            <select class="form-select" v-model="data.type">
                              <option value="course">Course</option>
                              <option value="service">Service</option>
                            </select>
                          </div>

                          <!-- TIER (only for Training) -->
                          <div class="col-md-6" v-if="data.category === 'Training'">
                            <label class="form-label">Tier</label>
                            <select class="form-select" v-model="data.tier">
                              <option value="basic">Basic – Computer Fundamentals</option>
                              <option value="practical">Practical – Office & Cyber Skills</option>
                              <option value="coding">Coding – Programming & Logic</option>
                              <option value="refresher">Refresher – Skills Update</option>
                            </select>
                          </div>

                          <div class="col-md-6" v-if="data.category === 'Training'">
                            <label class="form-label">Schedule</label>
                            <select class="form-select" v-model="data.schedule_type">
                              <option value="saturday">Saturday Only</option>
                              <option value="weekday">Weekday</option>
                              <option value="custom">Custom</option>
                            </select>
                          </div>

                          <div class="col-md-6" v-if="data.category === 'Training'">
                            <label class="form-label">Duration (Saturdays)</label>
                            <input
                              type="number"
                              step="0.5"
                              min="0.5"
                              class="form-control"
                              v-model="data.duration_units"
                              placeholder="e.g. 0.5, 1, 3, 4"
                            >
                          </div>

                          <div class="col-md-6" v-if="data.category === 'Training'">
                            <label class="form-label">Session Hours</label>
                            <input
                              type="number"
                              step="0.5"
                              min="0.5"
                              class="form-control"
                              v-model="data.session_hours"
                              placeholder="Default: 1.5"
                            >
                          </div>


                        </form>
                      </div>

                      <!-- Footer -->
                      <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button class="btn btn-success" @click="submit" style="background: darkgreen; border-color: darkgreen;">
                          Save
                        </button>
                      </div>

                    </div>
                  </div>
                </div>


                <!-- EDIT Service MODAL -->
                <div class="modal fade" id="EditServiceModal" tabindex="-1" aria-labelledby="EditServiceModalLabel" aria-hidden="true">
                  <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                      <div class="modal-header">
                        <h5 class="modal-title">Edit Service</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                      </div>

                      <div class="modal-body">
                        <form class="row g-3">

                          <!-- Name -->
                          <div class="col-md-12">
                            <label class="form-label">Name*</label>
                            <input type="text" id="name_edit" class="form-control" v-model="form.name" required>
                          </div>

                          <!-- Category -->
                          <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <select class="form-select" v-model="form.category">
                              <option value="">Select</option>
                              <option value="Printing & Copying">Printing & Copying</option>
                              <option value="Typing & Documents">Typing & Documents</option>
                              <option value="Online Applications">Online Applications</option>
                              <option value="Internet">Internet</option>
                              <option value="Training">Training</option>
                              <option value="Internet & Computer Use">Internet & Computer Use</option>
                              <option value="Other Services">Other Services</option>
                              <option value="Bundles">Bundles</option>
                            </select>
                          </div>

                          <!-- price -->
                          <div class="col-md-6">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" v-model="form.price">
                          </div>

                          <!-- Unit -->
                          <div class="col-md-6">
                            <label class="form-label">Unit</label>
                            <input type="text" class="form-control" v-model="form.unit">
                          </div>

                          <!-- TYPE -->
                          <div class="col-md-6" v-if="form.category === 'Training'">
                            <label class="form-label">Type</label>
                            <select class="form-select" v-model="form.type">
                              <option value="course">Course</option>
                              <option value="service">Service</option>
                            </select>
                          </div>

                          <!-- TIER -->
                          <div class="col-md-6" v-if="form.category === 'Training'">
                            <label class="form-label">Tier</label>
                            <select class="form-select" v-model="form.tier">
                              <option value="basic">Basic – Computer Fundamentals</option>
                              <option value="practical">Practical – Office & Cyber Skills</option>
                              <option value="refresher">Refresher – Skills Update</option>
                              <option value="coding">Coding – Programming & Logic</option>
                             </select>
                          </div>

                          <div class="col-md-6" v-if="form.category === 'Training'">
                            <label class="form-label">Schedule</label>
                            <select class="form-select" v-model="form.schedule_type">
                              <option value="saturday">Saturday Only</option>
                              <option value="weekday">Weekday</option>
                              <option value="custom">Custom</option>
                            </select>
                          </div>

                          <div class="col-md-6" v-if="form.category === 'Training'">
                            <label class="form-label">Duration (Saturdays)</label>
                            <input
                              type="number"
                              step="0.5"
                              min="0.5"
                              class="form-control"
                              v-model="form.duration_units"
                              placeholder="e.g. 0.5, 1, 3, 4"
                            >
                          </div>

                          <div class="col-md-6" v-if="form.category === 'Training'">
                            <label class="form-label">Session Hours</label>
                            <input
                              type="number"
                              step="0.5"
                              min="0.5"
                              class="form-control"
                              v-model="form.session_hours"
                              placeholder="Default: 1.5"
                            >
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

    import html2canvas from "html2canvas";
    import jsPDF from "jspdf";

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
            services: [],
            selectedService: {},
            errors: {},
            initializing: true,
            submitting: false,
            logoUrl: window.location.origin + "/algospacelogo.png",
            printDate: new Date().toLocaleDateString(),

            data: {        // ADD service
                id: "",
                name: "",
                category: "",
                price: "",
                unit: "",
                is_bundle: false,
                type: 'service',
                tier: '',
                duration_units: null,
                session_hours: null,
                schedule_type: 'saturday',
            },

            form: {        // EDIT service
                id: "",
                name: "",
                category: "",
                price: "",
                unit: "",
                is_bundle: false,
                type: '',
                tier: '',
                duration_units: null,
                session_hours: null,
                schedule_type: 'saturday',
            },
            searchQuery: "",
        }
      },  
      computed: {
        filteredServicesList() {
          let list = this.services;

          const q = (this.searchQuery || "").toLowerCase().trim();

          // no search
          if (!q) return list;

          // STATUS FILTER (robust)
        if (q.includes("remote")) {
          return list.filter(s => Number(s.is_active) === 1);
        }

        if (q.includes("walkin")) {
          return list.filter(s => Number(s.is_active) === 0);
        }

          // normal search
          return list.filter(s =>
            (s.name || "").toLowerCase().includes(q) ||
            (s.category || "").toLowerCase().includes(q) ||
            String(s.price || "").includes(q) ||
            (s.unit || "").toLowerCase().includes(q)
          );
        },       
        groupedServices() {
          const groups = {};

          this.services.forEach(service => {
            const category = service.category || "Uncategorized";

            if (!groups[category]) {
              groups[category] = [];
            }

            groups[category].push(service);
          });

          return groups;
        },
        pdfGroups() {
          const groups = [];

          for (const [category, items] of Object.entries(this.groupedServices)) {
            groups.push({
              type: "category",
              category,
              items
            });
          }

          return groups;
        }        
      },   
      watch: {
        'data.category'(val) {
          if (val !== 'Training') {
            this.data.type = 'service'
            this.data.tier = ''
            this.data.duration_days = null
          }
        },

        'form.category'(val) {
          if (val !== 'Training') {
            this.form.type = 'service'
            this.form.tier = ''
            this.form.duration_days = null
          }
        }
      },             
      methods: { 
        clearFilters() {
          this.searchQuery = "";
        },        
        getPdfGroups(data) {
          const grouped = {};

          data.forEach(service => {
            const category = service.category || "Uncategorized";

            if (!grouped[category]) grouped[category] = [];

            grouped[category].push(service);
          });

          return Object.entries(grouped).map(([category, items]) => ({
            category,
            items
          }));
        },        
        getChunks(array, size) {
          const chunks = [];
          for (let i = 0; i < array.length; i += size) {
            chunks.push(array.slice(i, i + size));
          }
          return chunks;
        },         
        async exportToPDF(type = "all") {
          const pdf = new jsPDF("p", "mm", "a4");

          const pageWidth = pdf.internal.pageSize.getWidth();
          const pageHeight = pdf.internal.pageSize.getHeight();

          let y = 10;

          // ===== DATA SOURCE (THIS IS THE KEY FIX) =====
          const data =
            type === "filtered"
              ? this.filteredServicesList   // 👈 NOW MATCHES TABLE SEARCH
              : this.services;

          const groups = this.getPdfGroups(data);

          // ===== FORMAT DATE dd/mm/yyyy =====
          const formatDate = (date) => {
            const d = new Date(date);

            return `${String(d.getDate()).padStart(2, "0")}/${
              String(d.getMonth() + 1).padStart(2, "0")
            }/${d.getFullYear()}`;
          };

          const printDate = formatDate(new Date());

          // ===== HEADER =====
          pdf.setFontSize(18);
          pdf.setFont("helvetica", "bold");
          pdf.text("ALGOSPACE CYBER", pageWidth / 2, y, { align: "center" });

          y += 8;

          pdf.setFontSize(12);
          pdf.text("SERVICES & PRICE LIST", pageWidth / 2, y, { align: "center" });

          y += 6;

          pdf.setFontSize(10);
          pdf.setFont("helvetica", "normal");
          pdf.text(`PRICE LIST AS OF: ${printDate}`, pageWidth / 2, y, {
            align: "center"
          });

          y += 10;

          // ===== LEGEND =====
          pdf.setFontSize(10);
          pdf.setFont("helvetica", "bold");
          pdf.text("LEGEND:", 12, y);

          y += 5;

          pdf.setFontSize(9);
          pdf.setFont("helvetica", "normal");
          pdf.text("• ORDER ONLINE / IN-SHOP = You can send requests online or visit us", 12, y);
          y += 5;
          pdf.text("• IN-SHOP ONLY = You must visit the shop to get this service", 12, y);

          y += 8;

          pdf.setDrawColor(200);
          pdf.line(12, y, pageWidth - 12, y);

          y += 8;

          // ===== CONTACT =====
          pdf.setFontSize(9);
          pdf.setFont("helvetica", "normal");
          pdf.text("Phone: +254112514440", 12, y); y += 5;
          pdf.text("Website: algospacecyber.co.ke", 12, y); y += 5;
          pdf.text("Email: info@algospacecyber.co.ke", 12, y);

          y += 10;

          const totalPagesExp = "{total_pages_count_string}";

          const addFooter = () => {
            const pageCurrent = pdf.internal.getCurrentPageInfo().pageNumber;
            const footerY = pageHeight - 10;

            pdf.setFontSize(8);
            pdf.setTextColor(120);

            pdf.text(
              "AlgoSpace Cyber, Villa Nova Building, Shop 1, Kapsokwony, Mt. Elgon, Kenya",
              pageWidth / 2,
              footerY,
              { align: "center" }
            );

            pdf.text(
              `Page ${pageCurrent} of ${totalPagesExp}`,
              pageWidth - 12,
              footerY - 6,
              { align: "right" }
            );

            pdf.setTextColor(0);
          };

          // ===== CONTENT =====
          for (const group of groups) {

            if (y > pageHeight - 30) {
              addFooter();
              pdf.addPage();
              y = 15;
            }

            pdf.setFillColor(220, 220, 220);
            pdf.rect(10, y - 5, pageWidth - 20, 8, "F");

            pdf.setFontSize(11);
            pdf.setFont("helvetica", "bold");
            pdf.text(group.category.toUpperCase(), 12, y);

            y += 10;

            pdf.setFontSize(9);
            pdf.setFont("helvetica", "bold");

            pdf.text("SERVICE", 12, y);
            pdf.text("PRICE", 80, y);
            pdf.text("UNIT", 110, y);
            pdf.text("STATUS", 140, y);

            y += 6;

            pdf.setFont("helvetica", "normal");

            group.items.forEach(item => {

              if (y > pageHeight - 20) {
                addFooter();
                pdf.addPage();
                y = 15;
              }

              pdf.text(String(item.name || "").toUpperCase(), 12, y);
              pdf.text(String(item.price || "").toUpperCase(), 80, y);
              pdf.text(String(item.unit || "").toUpperCase(), 110, y);

              const status = item.is_active
                ? "ORDER ONLINE / IN-SHOP"
                : "IN-SHOP ONLY";

              pdf.text(status, 140, y);

              y += 6;
            });

            y += 5;
          }

          addFooter();
          pdf.putTotalPages(totalPagesExp);

          pdf.save(
            type === "filtered"
              ? "ALGOSPACE_FILTERED_SERVICES.pdf"
              : "ALGOSPACE_SERVICES.pdf"
          );
        },               
        viewService(item)
        {
          console.log(this.selectedService)
          this.selectedService = item;
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('viewServiceModal'));
          modal.show();
        },
        editService(item) {
        this.form = {
            id: item.id,
            name: item.name,
            category: item.category,
            price: item.price,
            unit: item.unit,
            is_bundle: item.is_bundle
        };

        const modal = new bootstrap.Modal(
            document.getElementById('EditServiceModal')
        );
        modal.show();
        },

        validateEditForm() {
        let isValid = true;

        if (!this.form.name) {
            document.getElementById('name_edit').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name_edit').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submitChanges() {
        if (!this.validateEditForm()) return;

        this.submitting = true;

        try {
            await axios.put(`/api/services/${this.form.id}`, this.form);

            toast.fire('Success!', 'Service updated successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('EditServiceModal')
            );
            modal.hide();

            this.loadLists();

        } catch (error) {
            console.error(error);
            toast.fire(
            'Error!',
            error.response?.data?.message || 'Failed to update service',
            'error'
            );
        } finally {
            this.submitting = false;
        }
        },
        async toggleService(item) {

            try {
                const res = await axios.patch(`/api/services/${item.id}/toggle`);

                toast.fire(
                    'Success!',
                    res.data.is_active ? 'Service activated' : 'Service deactivated',
                    'success'
                );

                // update locally (no reload needed)
                item.is_active = res.data.is_active;

            } catch (error) {

                console.error(error);

                toast.fire(
                    'Error!',
                    'Failed to update service status',
                    'error'
                );
            }
        },        

        addService()
        {
          // Show the modal after fetching data
          const modal = new bootstrap.Modal(document.getElementById('AddServiceModal'));
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

        if (!this.data.name) {
            document.getElementById('name').classList.add('is-invalid');
            isValid = false;
        } else {
            document.getElementById('name').classList.remove('is-invalid');
        }

        return isValid;
        },
        async submit() {
        if (!this.validateForm()) return;

        this.submitting = true;

        try {
            await axios.post('/api/services', this.data);

            toast.fire('Success!', 'Service added successfully', 'success');

            const modal = bootstrap.Modal.getInstance(
            document.getElementById('AddServiceModal')
            );
            modal.hide();

            // Reset form
            this.data = {
                id: "",
                name: "",
                category: "",
                price: "",
                unit: "",
                is_bundle: false
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
        deleteService(id){
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
                  axios.delete('/api/services/'+id).then(() => {
                  toast.fire(
                    'Deleted!',
                    'Service has been deleted.',
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
          axios.get('/api/services')
            .then((response) => {
              this.services = response.data;
              console.log(response)


            })
            .catch((error) => {
              console.error('Error fetching services list:', error);
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
    
    