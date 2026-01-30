import { createRouter, createWebHistory } from 'vue-router';

import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Profile from '../views/Profile.vue';
import Home from '../views/Home.vue';
import Supplies from '../views/Supplies.vue';
import Suppliers from '../views/Suppliers.vue';
import QuickSale from '../views/QuickSale.vue';
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

const routes = [
  // Public routes
  { path: '/', name: 'index', component: Login },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },

  // Protected routes
  { path: '/dashboard', name: 'dashboard', component: Home, meta: { requiresAuth: true } },
  { path: '/supplies', name: 'supplies', component: Supplies, meta: { requiresAuth: true } },
  { path: '/suppliers', name: 'suppliers', component: Suppliers, meta: { requiresAuth: true } },
  { path: '/restocks', name: 'restocks', component: Restocks, meta: { requiresAuth: true } },
  { path: '/quick-sale', name: 'quick-sale', component: QuickSale, meta: { requiresAuth: true } },
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

  //personal routes
  { path: '/personal-accounts', name: 'personal-accounts', component: PersonalAccounts, meta: { requiresAuth: true } },
  { path: '/personal-categories', name: 'personal-categories', component: PersonalCategory, meta: { requiresAuth: true } },
  { path: '/personal-transactions', name: 'personal-transactions', component: PersonalTransactions, meta: { requiresAuth: true } },

  { path: '/diary', name: 'diary', component: Diary, meta: { requiresAuth: true } },
  { path: '/profile', name: 'profile', component: Profile, meta: { requiresAuth: true } },
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
