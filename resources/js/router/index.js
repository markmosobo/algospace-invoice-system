import { createRouter, createWebHistory } from 'vue-router';

import Login from '../views/Login.vue';
import Register from '../views/Register.vue';
import Profile from '../views/Profile.vue';
import Home from '../views/Home.vue';
import Supplies from '../views/Supplies.vue';
import QuickSale from '../views/QuickSale.vue';
import Invoices from '../views/Invoices.vue';
import Payments from '../views/Payments.vue';
import PendingInvoices from '../views/PendingInvoices.vue';
import Customers from '../views/Customers.vue';
import SystemLogs from '../views/SystemLogs.vue';
import Restocks from '../views/Restocks.vue';

const routes = [
  // Public routes
  { path: '/', name: 'index', component: Login },
  { path: '/login', name: 'login', component: Login },
  { path: '/register', name: 'register', component: Register },

  // Protected routes
  { path: '/dashboard', name: 'dashboard', component: Home, meta: { requiresAuth: true } },
  { path: '/supplies', name: 'supplies', component: Supplies, meta: { requiresAuth: true } },
  { path: '/restocks', name: 'restocks', component: Restocks, meta: { requiresAuth: true } },
  { path: '/quick-sale', name: 'quick-sale', component: QuickSale, meta: { requiresAuth: true } },
  { path: '/pending-invoices', name: 'pending-invoices', component: PendingInvoices, meta: { requiresAuth: true } },
  { path: '/invoices', name: 'invoices', component: Invoices, meta: { requiresAuth: true } },
  { path: '/payments', name: 'payments', component: Payments, meta: { requiresAuth: true } },
  { path: '/customers', name: 'customers', component: Customers, meta: { requiresAuth: true } },
  { path: '/system-logs', name: 'system-logs', component: SystemLogs, meta: { requiresAuth: true } },

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
