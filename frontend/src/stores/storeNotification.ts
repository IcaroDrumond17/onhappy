import { defineStore } from 'pinia'
import { Notification} from "@/types/notification"
import { fetchNotifications } from '@/services/notifications'

export const useStoreNotification = defineStore('storeNotification', () => {
  
  const notifications = ref<Notification[]>([])

  async function loadNotifications() {
    try {
      const res = await fetchNotifications()
      notifications.value =  Array.isArray(res?.data) ? res.data : []
    } catch (error) {
      console.error('Erro ao carregar notificações:', error)
    }
  }

  return {
    notifications,
    loadNotifications,
  }
})
