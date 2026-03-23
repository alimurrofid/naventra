import type { RouteRecordRaw } from 'vue-router';

export const setupRoutes: Array<RouteRecordRaw> = [
  {
    path: '/setup',
    name: 'Setup',
    children: [
      {
        path: 'master/examples',
        name: 'SetupMasterExampleList',
        component: () => import('@/modules/setup/master/example/views/ExampleList.vue'),
      },
      {
        path: 'master/examples/create',
        name: 'SetupMasterExampleCreate',
        component: () => import('@/modules/setup/master/example/views/ExampleForm.vue'),
      }
    ]
  }
];
