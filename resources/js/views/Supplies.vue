<template>
<Master>
        <section class="section dashboard">
        <div class="row">
            <!-- Checked in visitors -->
            <div class="col-12">
                <div class="card top-selling overflow-auto">

                <div class="card-body pb-0">

                <h5 class="card-title">Supplies <span>| Products in the business premise</span></h5>
                <p class="card-text">
                
                    <a
                    :href="href"
                    :class="{ active: isActive }"
                    class="btn btn-primary me-2"
                    @click="addProduct"
                    >
                    Add Product
                    </a>
                <router-link to="#" custom v-slot="{ href, navigate, isActive }">
                    <a
                    :href="href"
                    :class="{ active: isActive }"
                    class="btn btn-secondary"
                    @click.prevent="exportToExcel"
                    >
                    Export
                    </a>
                </router-link>
                </p>
                <table id="SuppliesTable" class="table table-borderless datatable">
                <thead>
                    <tr>
                    <!-- <th scope="col">Preview</th> -->
                    <th scope="col">Name</th>
                    <th scope="col">Unit Price</th>                
                    <th scope="col">Quantity</th>                
                    <th scope="col">Total</th>                
                    <th scope="col">Paid For On</th>
                    <th scope="col">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <tr  v-for="product in supplies" :key="product.id" >
                    <!-- <th scope="col">{{visit.id}}</th> -->
           <!--          <th scope="row"><a href="#">
                        <img :src="getPhoto() + product.image" />
                    </a></th> -->
                    <td scope="col">{{product.item}}</td>
                    <td scope="col">{{formatPrice(product.unit_price)}}</td>
                    <td scope="col">{{product.quantity}}</td>
                    <td scope="col">{{formatPrice(product.total)}}</td>
                    <td scope="col">{{format_date(product.payment_date)}}</td>
                    <td>
                        <div class="btn-group" role="group">
                            <button id="btnGroupDrop1" type="button" class="btn btn-sm btn-primary rounded-pill dropdown-toggle" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            Action
                            </button>
                            <div class="dropdown-menu" aria-labelledby="btnGroupDrop1" style="">
                                <!-- <a @click="navigateTo('/viewproduct/'+product.id )" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a> -->
                                <a @click="viewProduct(product)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>   
                                <a @click="editProduct(product)" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                <a @click="restockProduct(product.id)" class="dropdown-item" href="#"><i class="ri-add-fill mr-2"></i>Restock</a>   
                                <a @click="deleteProduct(product.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-6-fill mr-2"></i>Delete</a>   
                            </div>
                        </div>
                    </td>
                    </tr>
                </tbody>
                </table>
                </div>
                </div>
            </div>
            <!--End Products visitors -->

              <!-- View Supply Modal -->
              <div class="modal fade" id="viewSupplyModal" tabindex="-1" aria-labelledby="viewSupplyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                  <div class="modal-content">

                    <div class="modal-header">
                      <h5 class="modal-title">View Supply Details</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body" v-if="selectedSupply">

                        <!-- Image Gallery -->
                        <div v-if="selectedSupply.images?.length" class="mt-3">
                          <strong>Gallery Images:</strong>
                          <div class="d-flex flex-wrap mt-2">
                            <div v-for="(img, i) in selectedSupply.images" :key="i" class="me-2 mb-2">
                              <img 
                                :src="'/storage/supplies/' + img.name"
                                style="width:120px; height:100px; object-fit:cover; border-radius:4px;"
                              >
                            </div>
                          </div>
                        </div>


                      <div class="row g-3">

                        <!-- BASIC INFO -->
                        <div class="col-md-6" v-if="selectedSupply.item">
                          <strong>Product Name:</strong> <br> {{ selectedSupply.item }}
                        </div>

                        <div class="col-md-6" v-if="selectedSupply.unit_price">
                          <strong>Unit Price:</strong> <br> {{ selectedSupply.unit_price }}
                        </div>

                        <div class="col-md-6" v-if="selectedSupply.quantity">
                          <strong>Quantity:</strong> <br> {{ selectedSupply.quantity }}
                        </div>

                        <!-- Supplier -->
                        <div class="col-md-6" v-if="selectedSupply.supplier_id">
                          <strong>Supplier:</strong> <br> {{ selectedSupply.supplier.name }}
                        </div>

                        <!-- Status -->
                        <div class="col-md-6" v-if="selectedSupply.status">
                          <strong>Status:</strong> <br> {{ selectedSupply.status }}
                        </div>
                        
                        <!-- Payment Method -->
                        <div class="col-md-6" v-if="selectedSupply.payment_method">
                          <strong>Payment Method:</strong> <br> {{ selectedSupply.payment_method }}
                        </div>

                      </div>
                    </div>

                    <div class="modal-footer">
                      <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>

                  </div>
                </div>
              </div>

            <!-- Add Supply Modal -->
            <div class="modal fade" id="AddSupplyModal" tabindex="-1" aria-labelledby="AddSupplyModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                <div class="modal-content">

                    <div class="modal-header">
                    <h5 class="modal-title" id="AddTicketModalLabel">Add Supply</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                    <form class="row g-3 needs-validation" novalidate>

                        <!-- Status -->
                        <div class="col-md-6">
                        <label class="form-label">Status*</label>
                        <select name="role" v-model="form.status" class="form-select" id="status">
                            <option value="" selected disabled>Select status</option>
                            <option value="replenisheble">Replenisheble</option>
                            <option value="onetime">Onetime</option>
                            <option value="other">Other</option>
                        </select>

                        </div>

                        <div class="col-md-6">
                        <label class="form-label">Payment methods</label>
                        <select class="form-select" v-model="form.payment_method" id="payment_method">
                            <option value="" disabled selected>Select payment method</option>
                            <option value="cash">Cash</option>
                            <option value="mpesa">MPESA</option>
                            <option value="card">Card</option>
                            <option value="other">Other</option>
                        </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="item" v-model="form.item">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Unit Price</label>
                            <input type="number" class="form-control" id="unit_price" v-model="form.unit_price" placeholder="KES">
                        </div>
                        
                        <div class="col-md-6">
                            <label class="form-label">Quantity</label>
                            <input type="number" min="1" class="form-control" id="quantity" v-model="form.quantity">
                        </div> 
                        
                        <div class="col-md-6">
                            <label class="form-label">Supplier</label>
                            <select name="supplier" v-model="form.supplier_id" class="form-select" id="supplier_id">
                                <option value="0" selected disabled>Select Supplier</option>
                                <option v-for="supplier in suppliers" :value="supplier.id"
                                :selected="supplier.id == form.supplier_id" :key="supplier.id">{{ supplier.name}} </option>
    
                            </select>
                        </div>                         

                    <!-- Media Upload -->
                    <div class="col-md-6">
                        <label class="form-label">Upload Images</label>
                        <input
                        type="file"
                        class="form-control"
                        multiple
                        accept="image/*"
                        @change="handleImages"
                        />
                    </div>                          

                    <!-- Image Previews in a NEW full-width row -->
                    <div class="col-12 mt-3" v-if="images.length > 0">
                        <label class="form-label fw-bold">Preview Images</label>

                        <div class="image-preview-container">
                            <div class="preview-box" v-for="(img, index) in images" :key="index">
                                <img :src="img.preview" class="preview-img">
                                <button class="remove-btn" @click="removeImage(index)">×</button>
                            </div>
                        </div>
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
        </div>
        </section> 
</Master>
 

</template>

<script>
import Master from '@/components/Master.vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import "jquery/dist/jquery.min.js";
import "datatables.net-dt/js/dataTables.dataTables";
import "datatables.net-dt/css/jquery.dataTables.min.css";
import $ from "jquery";
import numeral from 'numeral';
import moment from 'moment';

const toast = Swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

export default {
  name: 'Home',
  components: {
    Master,
  },
  data() {
    return {
      selectedSupply: [],
      user: {},
      currentUser: {},
      userRole: null,
      supplies: {},
      errors: {},
      suppliers: [],
        form: {
            supplier_id: "",
            unit_price: "",
            quantity: "",
            item: "",
            total: "",
            payment_date: "",
            payment_method: "cash",
            status: "replenisheble",

        },      
        images: [],
        existingImages: [],
        newImages: [],
        badgeClasses: [
        'text-success',
        'text-danger',
        'text-primary',
        'text-info',
        'text-warning',
        'text-muted',
      ],
    };
  },
  computed: {
    dashboardCards() {
      const cards = {
        office: [
          { title: 'Services Offered', value: this.stats.services, icon: 'bi-people', color: 'primary' },
          { title: 'Suppliers', value: this.stats.suppliers, icon: 'bi-person-lines-fill', color: 'info' },
          { title: 'Customers', value: this.stats.customers, icon: 'bi-people', color: 'secondary' },
          { title: 'Supplies', value: this.stats.supplies, icon: 'bi-building', color: 'success' },
          { title: 'Payments', value: this.stats.payments, icon: 'bi-circle', color: 'warning' },
          { title: 'Invoices', value: this.stats.invoices, icon: 'bi-hourglass-split', color: 'info' },
        ],

        personal: [
          { title: 'My Accounts', value: this.stats.personalAccounts, icon: 'bi-building', color: 'success' },
          { title: 'Categories', value: this.stats.personalCategories, icon: 'bi-house-door', color: 'warning' },
          { title: 'Transactions', value: this.stats.personalTransactions, icon: 'bi-box-arrow-right', color: 'danger' },
        ],

      };

      // Remove cards with null, 0, or 'N/A' values for cleaner UI
      return (cards[this.userRole] || []).filter(card => {
        return card.value !== null && card.value !== 0 && card.value !== 'N/A';
      });
    }
  },
  methods: {
    viewProduct(product)
    {
        console.log(product)
        this.selectedSupply = product;
        // Show the modal after fetching data
        const modal = new bootstrap.Modal(document.getElementById('viewSupplyModal'));
        modal.show();
    },
    addProduct()
    {
        // Show the modal after fetching data
        const modal = new bootstrap.Modal(document.getElementById('AddSupplyModal'));
        modal.show();
    }, 
    async submit() {
    if (!this.validateForm()) return;

    this.submitting = true;

    try {
        await this.submitForm();
        this.submitted = true;
    } catch (error) {
        console.error('Submission error:', error);
    } finally {
        this.submitting = false;
    }
    },
    validateForm() {
    let isValid = true;

    const fields = [
        { id: 'item', value: this.form.item },
        { id: 'unit_price', value: this.form.unit_price },
        { id: 'quantity', value: this.form.quantity },
        { id: 'supplier_id', value: this.form.supplier_id }
    ];

    fields.forEach(({ id, value }) => {
        const el = document.getElementById(id);
        if (!el) return;

        if (!value) {
        el.classList.add('is-invalid');
        isValid = false;
        } else {
        el.classList.remove('is-invalid');
        }
    });

    return isValid;
    },
    async submitForm() {
    try {
        const formData = new FormData();

        // append normal fields
        Object.keys(this.form).forEach(key => {
        formData.append(key, this.form[key]);
        });

        // append images[]
        this.images.forEach(img => {
        formData.append('images[]', img.file);
        });

        const response = await axios.post('/api/supplies', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
        });

        toast.fire('Success!', 'Supply added successfully!', 'success');

        // close modal
        const modal = bootstrap.Modal.getInstance(
        document.getElementById('AddSupplyModal')
        );
        modal.hide();

        // reset form
        this.form = {
        status: "",
        payment_method: "",
        item: "",
        unit_price: "",
        quantity: 1,
        supplier_id: 0
        };

        this.images = [];

        this.loadLists();

    } catch (error) {
        console.error(error);
        toast.fire(
        'Error!',
        error.response?.data?.message || 'Failed to add supply',
        'error'
        );
    }
    },
        
    handleImages(e) {
        const files = e.target.files;

        for (let i = 0; i < files.length; i++) {
            const file = files[i];

            this.images.push({
                file: file,                                   // ← real file!
                preview: URL.createObjectURL(file)           // ← preview
            });
        }
    },
    removeImage(index) {
        this.images.splice(index, 1);
    },
    handleNewImages(event) {
        const files = event.target.files;
        for (let i = 0; i < files.length; i++) {
        this.newImages.push({
            file: files[i],
            preview: URL.createObjectURL(files[i])
        });
        }
    },
    async removeExistingImage(ticketId, imageId, index) {
        try {
            await axios.delete(`/api/support-tickets/${ticketId}/images/${imageId}`);
            this.form.images.splice(index, 1); // remove from array
            toast.fire('Success!', 'Image removed!', 'success');
        } catch (error) {
            console.error(error);
            toast.fire('Error!', 'Could not remove image.', 'error');
        }
    },     
    loadLists() {
      axios
        .get('/api/supplies')
        .then(response => {
          this.supplies = response.data.supplies;
          this.suppliers = response.data.suppliers;
          console.log("new", response)
            setTimeout(() => {
                $("#SuppliesTable").DataTable();
            }, 10);
        })
        .catch(error => {
          console.error('Dashboard supplies error:', error);
        });
    },
        navigateTo(location){
            this.$router.push(location)
        },
        format_date(value){
          if(value){
            return moment(String(value)).format('DD/MM/YYYY')
          }
        },
        formatDateTime(value) {
            return moment(value).format('h:mm A');
        },
        formatPrice(value) {
          return numeral(value).format('0,0.00');
        },
        exportToExcel() {
          const productsData = this.supplies.map(product => ({
            "Item Name": product.name,
            "Item type": "",
            "Purchase Price (KES)": product.buying_price,
            "Beginning Stock": product.pieces,
            "Origin": "Kenya",
            "PKG Unit": "",
            "Sale Price (KES)": product.selling_price,
            "Qty unit": "",
            "Tax type": "",
            // "Added By": `${product.user.first_name} ${product.user.last_name}`,
            // "Date In": this.format_date(product.created_at)
          }));

          const worksheet = XLSX.utils.json_to_sheet(productsData);
          const workbook = XLSX.utils.book_new();
          XLSX.utils.book_append_sheet(workbook, worksheet, "Products");

          XLSX.writeFile(workbook, "products.xlsx");
        }, 
        restockProduct(id){
            this.$router.push('/restockproduct/'+id)
        },
        viewProductHistory(id){
            this.$router.push('/viewproducthistory/'+id)
        },
        deleteProduct(id){
            Swal.fire({
                title: 'Are you sure?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete!'
            }).then((result) => {
                if (result.isConfirmed) {
                axios.delete('/api/supplies/' + id).then(() => {
                    this.loadLists();
                })
                //api goes here
                toast.fire(
                    'Deleted!',
                    'Product has been deleted.',
                    'success'
                )
                setTimeout(() => {
                    this.loadLists();
                    $("#SuppliesTable").DataTable();
                }, 2000)
                }
            })
        }               
  },
  mounted() {
    const storedUser = JSON.parse(localStorage.getItem('user')) || {};
    this.user = storedUser;
    this.currentUser = storedUser;
    this.userRole = this.user.role;
    this.current_user_id = storedUser.id;
    this.current_user = `${storedUser.first_name || ''} ${storedUser.last_name || ''}`.trim();
    this.loadLists();
  }
};
</script>