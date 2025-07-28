import http from './http'
import { useAuthStore } from '@/stores/authStore'

let controller: AbortController | null = null

function isAdmin() {
  const store = useAuthStore()
  return store.user.type_user === 'admin'
}

export async function fetchOrdersWithCancel(filters: Record<string, any> = {}) {
  if (controller) controller.abort()
  controller = new AbortController()

  const endpoint = isAdmin() ? '/orders' : '/orders/user'

  try {
    const response = await http.get(endpoint, {
      params: filters,
      signal: controller.signal,
    })

    return response.data
  } catch (error: any) {
    if (error.name === 'AbortError' || error.code === 'ERR_CANCELED') {
      return null
    }

    throw error
  }
}

export async function fetchOrders(filters: Record<string, any> = {}) {
  const endpoint = isAdmin() ? '/orders' : '/orders/user'

  const response = await http.get(endpoint, { params: filters })
  return response.data
}

export async function createOrder(payload: Record<string, any>) {
  const response = await http.post('/orders', payload)
  return response.data
}

export async function updateOrderStatus(id: number, status: string) {
  const response = await http.patch(`/orders/${id}/status`, { status })
  return response.data
}

export async function deleteOrder(orderId: number) {
  const response = await http.delete(`/orders/${orderId}`)
  return response.data
}
