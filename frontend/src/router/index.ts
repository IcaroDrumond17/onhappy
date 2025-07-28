import { createRouter, createWebHistory, type RouteRecordRaw } from 'vue-router'
import { useLoading } from '@/composables/useLoading'

const { show, hide } = useLoading()

import LoginRouter from './login'
import HomeRouter from './home'
import OrderRouter from './order'
import { ROUTE_NAMES } from './names'

const routes: RouteRecordRaw[] = [...LoginRouter, ...HomeRouter, ...OrderRouter]

export const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

router.beforeEach(async (to, from, next) => {
  show()
  const { useAuthStore } = await import('@/stores/authStore')
  const auth = useAuthStore()

  const isPublic = to.meta.public

  if (!isPublic) {
    // Se não está logado ou token expirou
    if (!auth.isLoggedIn) {
      await auth.logout() // limpa estado e token
      return next({ name: ROUTE_NAMES.LOGIN })
    }
  }

  next()
})

router.afterEach(() => {
  hide()
})

export default router
