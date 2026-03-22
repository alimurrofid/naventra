import type { RouteRecordRaw } from 'vue-router';

export const dashboardRoutes: Array<RouteRecordRaw> = [
  {
    path: '/dashboard',
    name: 'Dashboard',
    component: () => import('./views/Dashboard.vue'),
    meta: { permission: 'dashboard.view' }
  }
];
