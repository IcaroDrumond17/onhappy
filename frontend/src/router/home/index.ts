import type { RouteRecordRaw } from 'vue-router'
import { ROUTE_NAMES } from '../names'

const HomeRouter: RouteRecordRaw[] = [
  {
    name: ROUTE_NAMES.HOME_DEFAULT,
    path: '/',
    component: () => import('@/views/home/index.vue'),
  },
  {
    name: ROUTE_NAMES.HOME,
    path: '/home',
    component: () => import('@/views/home/index.vue'),
  },
]

export default HomeRouter
