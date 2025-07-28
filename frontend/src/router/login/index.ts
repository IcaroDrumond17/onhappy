import type { RouteRecordRaw } from 'vue-router'
import { ROUTE_NAMES } from '../names'

const LoginRouter: RouteRecordRaw[] = [
  {
    name: ROUTE_NAMES.LOGIN,
    path: '/login',
    component: () => import('@/views/login/index.vue'),
    meta: { public: true },
  },
]

export default LoginRouter
