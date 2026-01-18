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
                
                <!-- <a href="visitors.php" class="btn btn-primary" >Add Visitor</a> -->
                <router-link to="/add-product" custom v-slot="{ href, navigate, isActive }">
                    <a
                    :href="href"
                    :class="{ active: isActive }"
                    class="btn btn-primary me-2"
                    @click="navigate"
                    >
                    Add Product
                    </a>
                </router-link>
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
                                <a @click="navigateTo('/viewproduct/'+product.id )" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View</a>
                                <a @click="viewProductHistory(product.id)" class="dropdown-item" href="#"><i class="ri-eye-fill mr-2"></i>View History</a>   
                                <a @click="navigateTo('/editproduct/'+product.id )" class="dropdown-item" href="#"><i class="ri-pencil-fill mr-2"></i>Edit</a>
                                <a @click="restockProduct(product.id)" class="dropdown-item" href="#"><i class="ri-add-fill mr-2"></i>Restock</a>   
                                <!-- <a @click="deleteProduct(product.id)" class="dropdown-item" href="#"><i class="ri-delete-bin-6-fill mr-2"></i>Delete</a>    -->
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
      currentYear: '',
      user: {},
      currentUser: {},
      userRole: null,
      supplies: {},
      properties: [],
      openproperties: [],
      closedproperties: [],
      users: [],
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
    loadLists() {
      axios
        .get('/api/supplies')
        .then(response => {
          this.supplies = response.data;
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