import { createRouter, createWebHistory } from 'vue-router';

import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Profile from '../views/Profile.vue';
import Home from '../views/Home.vue';
import Supplies from '../views/Supplies.vue';
import Suppliers from '../views/Suppliers.vue';
import QuickSale from '../views/QuickSale.vue';
import CyberRequests from '../views/CyberRequests.vue';
import Invoices from '../views/Invoices.vue';
import Payments from '../views/Payments.vue';
import PendingInvoices from '../views/PendingInvoices.vue';
import ServiceProviders from '../views/ServiceProviders.vue';
import Customers from '../views/Customers.vue';
import SystemLogs from '../views/SystemLogs.vue';
import Restocks from '../views/Restocks.vue';
import Expenses from '../views/Expenses.vue';
import Services from '../views/Services.vue';
import ProviderServices from '../views/ProviderServices.vue';
import Reports from '../views/Reports.vue';
import PersonalAccounts from '../views/personal/PersonalAccounts.vue';
import PersonalCategory from '../views/personal/PersonalCategory.vue';
import PersonalTransactions from '../views/personal/PersonalTransactions.vue';
import Diary from '../views/Diary.vue';
import Login2 from '../views/Login2.vue';
import LedgerReport from '../views/LedgerReport.vue';
import Farms from '../views/farm/Farms.vue';
import FarmVentures from '../views/farm/FarmVentures.vue';
import Crops from '../views/farm/Crops.vue';
import Harvests from '../views/farm/Harvests.vue';
import Seedlings from '../views/farm/Seedlings.vue';
import SeedlingSales from '../views/farm/SeedlingSales.vue';
import FarmInputs from '../views/farm/FarmInputs.vue';
import FarmExpenses from '../views/farm/FarmExpenses.vue';
import FarmSales from '../views/farm/FarmSales.vue';
import FarmWorkers from '../views/farm/FarmWorkers.vue';
import WorkerTasks from '../views/farm/WorkerTasks.vue';
import FarmAssets from '../views/farm/FarmAssets.vue';
import FootTraffic from '@/views/FootTraffic.vue';
import BookRentals from '@/views/BookRentals.vue';
import BooksCatalog from '@/views/BooksCatalog.vue';
import BookBorrowers from '@/views/BookBorrowers.vue';
import Partners from '@/views/Partners.vue';
import ToDos from '../views/ToDos.vue';
import Analytics from '../views/Analytics.vue';
import Projects from '../views/Projects.vue';

const routes = [
  // Public routes
  {
    path: '/login',
    name: 'login',
    component: Login,
    beforeEnter: (to, from, next) => {
      const token = localStorage.getItem('token')
      if (token) {
        next('/dashboard') // UX improvement happens here
      } else {
        next()
      }
    }
  },
  { path: '/register', name: 'register', component: Register },

  // Protected routes
  {
    path: '/login2',
    name: 'login2',
    component: Login2,
    meta: {
      requiresAuth: true,
      roles: ['office', 'personal', 'farm']
    }
  },
  { path: '/dashboard', name: 'dashboard', component: Home, meta: { requiresAuth: true } },
  { path: '/analytics', name: 'analytics', component: Analytics, meta: { requiresAuth: true } },
  { path: '/supplies', name: 'supplies', component: Supplies, meta: { requiresAuth: true } },
  { path: '/suppliers', name: 'suppliers', component: Suppliers, meta: { requiresAuth: true } },
  { path: '/restocks', name: 'restocks', component: Restocks, meta: { requiresAuth: true } },
  { path: '/quick-sale', name: 'quick-sale', component: QuickSale, meta: { requiresAuth: true } },
  { path: '/cyber-requests', name: 'cyber-requests', component: CyberRequests, meta: { requiresAuth: true } },
  { path: '/pending-invoices', name: 'pending-invoices', component: PendingInvoices, meta: { requiresAuth: true } },
  { path: '/invoices', name: 'invoices', component: Invoices, meta: { requiresAuth: true } },
  { path: '/payments', name: 'payments', component: Payments, meta: { requiresAuth: true } },
  { path: '/expenses', name: 'expenses', component: Expenses, meta: { requiresAuth: true } },
  { path: '/customers', name: 'customers', component: Customers, meta: { requiresAuth: true } },
  { path: '/service-providers', name: 'service-providers', component: ServiceProviders, meta: { requiresAuth: true } },
  { path: '/system-logs', name: 'system-logs', component: SystemLogs, meta: { requiresAuth: true } },
  { path: '/services', name: 'services', component: Services, meta: { requiresAuth: true } },
  { path: '/provider-services', name: 'provider-services', component: ProviderServices, meta: { requiresAuth: true } },
  { path: '/reports', name: 'reports', component: Reports, meta: { requiresAuth: true } },
  { path: '/ledger-report', name: 'ledger-report', component: LedgerReport, meta: { requiresAuth: true } },

  { path: '/farms', name: 'farms', component: Farms, meta: { requiresAuth: true } },
  { path: '/farm-ventures', name: 'farm-ventures', component: FarmVentures, meta: { requiresAuth: true } },
  { path: '/crops', name: 'crops', component: Crops, meta: { requiresAuth: true } },
  { path: '/harvests', name: 'harvests', component: Harvests, meta: { requiresAuth: true } },
  { path: '/seedlings', name: 'seedling', component: Seedlings, meta: { requiresAuth: true } },
  { path: '/seedling-sales', name: 'seedling-sales', component: SeedlingSales, meta: { requiresAuth: true } },
  { path: '/farm-expenses', name: 'farm-expenses', component: FarmExpenses, meta: { requiresAuth: true } },
  { path: '/farm-inputs', name: 'farm-inputs', component: FarmInputs, meta: { requiresAuth: true } },
  { path: '/farm-sales', name: 'farm-sales', component: FarmSales, meta: { requiresAuth: true } },
  { path: '/farm-workers', name: 'farm-workers', component: FarmWorkers, meta: { requiresAuth: true } },
  { path: '/worker-tasks', name: 'worker-tasks', component: WorkerTasks, meta: { requiresAuth: true } },
  { path: '/farm-assets', name: 'farm-assets', component: FarmAssets, meta: { requiresAuth: true } },

  //personal routes
  { path: '/personal-accounts', name: 'personal-accounts', component: PersonalAccounts, meta: { requiresAuth: true } },
  { path: '/personal-categories', name: 'personal-categories', component: PersonalCategory, meta: { requiresAuth: true } },
  { path: '/personal-transactions', name: 'personal-transactions', component: PersonalTransactions, meta: { requiresAuth: true } },

  { path: '/diary', name: 'diary', component: Diary, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: Profile, meta: { requiresAuth: true } },

  { path: '/foot-traffic', name: 'FootTraffic', component: FootTraffic, meta: { requiresAuth: true } },

  { path: '/to-do-tasks', name: 'ToDos', component: ToDos, meta: { requiresAuth: true } },

  { path: '/book-rentals', name: 'BookRentals', component: BookRentals, meta: { requiresAuth: true } },
  { path: '/books', name: 'BooksCatalog', component: BooksCatalog, meta: { requiresAuth: true } },
  { path: '/borrowers', name: 'BookBorrowers', component: BookBorrowers, meta: { requiresAuth: true } },

  { path: '/partners', name: 'Partners', component: Partners, meta: { requiresAuth: true } },

  { path: '/projects', name: 'Projects', component: Projects, meta: { requiresAuth: true } },
  {
    path: "/projects/create",
    name: "projects.create",
    component: () => import("@/views/projects/Create.vue"),
    meta: { requiresAuth: true } 
  },
  {
  path: "/projects/:id/progress",
    component: () => import("@/views/projects/ProjectProgress.vue"),
    meta: { requiresAuth: true } 

  }
];

const router = createRouter({
  history: createWebHistory('/'),
  routes,
});

// 🔐 Global Auth Guard
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');

  if (to.meta.requiresAuth && !token) {
    next({ path: '/login', replace: true });
  } else {
    next();
  }
});

export default router;
