import type { RouteRecordRaw } from 'vue-router';

export const accountingRoutes: Array<RouteRecordRaw> = [
  {
    path: '/accounting/m_coa',
    name: 'MCoaList',
    component: () => import('./master/m_coa/views/MCoaList.vue'),
    meta: { permission: 'accounting.m_coa.read' }
  },
  {
    path: '/accounting/t_kbk',
    name: 'TKbkList',
    component: () => import('./transaction/t_kbk/views/TKbkList.vue'),
    meta: { permission: 'accounting.t_kbk.read' }
  },
  {
    path: '/accounting/t_kbk/create',
    name: 'TKbkCreate',
    component: () => import('./transaction/t_kbk/views/TKbkForm.vue'),
    meta: { permission: 'accounting.t_kbk.create' }
  },
  {
    path: '/accounting/t_kbk/:id',
    name: 'TKbkView',
    component: () => import('./transaction/t_kbk/views/TKbkForm.vue'),
    meta: { permission: 'accounting.t_kbk.read' }
  }
];
