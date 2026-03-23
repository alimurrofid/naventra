import { createRouter, createWebHistory } from 'vue-router';
import type { RouteRecordRaw } from 'vue-router';
import { permissionGuard } from './guards';

// Layouts
import AppLayout from '@/core/layouts/AppLayout.vue';
import AuthLayout from '@/core/layouts/AuthLayout.vue';

// Module Routes
import { dashboardRoutes } from '@/modules/dashboard/routes';
import { accountingRoutes } from '@/modules/accounting/accounting.routes';
import { inventoryRoutes } from '@/modules/inventory/routes';
import { salesRoutes } from '@/modules/sales/routes';
import { purchasingRoutes } from '@/modules/purchasing/routes';
import { setupRoutes } from '@/modules/setup/setup.routes';

const routes: Array<RouteRecordRaw> = [
  {
    path: '/login',
    component: AuthLayout,
    meta: { public: true },
    children: [
      {
        path: '',
        name: 'Login',
        component: () => import('@/modules/auth/views/Login.vue'),
      }
    ]
  },
  {
    path: '/',
    component: AppLayout,
    redirect: '/dashboard',
    children: [
      ...dashboardRoutes,
      ...accountingRoutes,
      ...inventoryRoutes,
      ...salesRoutes,
      ...purchasingRoutes,
      ...setupRoutes
    ]
  },
  {
    path: '/403',
    name: 'Forbidden',
    component: () => import('@/modules/auth/views/Forbidden.vue'),
    meta: { public: true }
  },
  {
    path: '/:pathMatch(.*)*',
    name: 'NotFound',
    component: () => import('@/modules/auth/views/NotFound.vue'),
    meta: { public: true }
  }
];

const router = createRouter({
  history: createWebHistory(),
  routes,
});

router.beforeEach(permissionGuard);

export default router;
