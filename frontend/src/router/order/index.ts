import type { RouteRecordRaw } from 'vue-router'
import { ROUTE_NAMES } from '../names'

const OrderRouter: RouteRecordRaw[] = [
  {
    name: ROUTE_NAMES.ORDER,
    path: '/order/:id?',
    component: () => import('@/views/order/index.vue'),
  },
]

export default OrderRouter
