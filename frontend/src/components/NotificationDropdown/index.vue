<script setup lang="ts">
import { markNotificationViewed } from '@/services/notifications'
import { Notification } from '@/types/notification'
import { useStoreNotification } from '@/stores/storeNotification'
const storeNotification = useStoreNotification()

const isOpen = ref(false)
const loading = ref(false)
const error = ref<string | null>(null)

const el: Ref<HTMLElement | null> = ref(null)

const pollingInterval = 60000 // 10 segundos
let timer: number | undefined = undefined

const toggleDropdown = (): void => {
  isOpen.value = !isOpen.value
  if (isOpen.value && notifications.value.length === 0) {
    loadNotifications()
  }
}

const unreadCount = computed(() => {
  return notifications.value.filter((n) => !n.viewed).length
})

const notifications = computed((): Notification[] => storeNotification.notifications)

const loadNotifications = async (): Promise<void> => {
  try {
    loading.value = true
    error.value = null

    await storeNotification.loadNotifications()

  } catch (e) {
    error.value = 'Erro ao carregar notificações.'
  } finally {
    loading.value = false
  }
}

const handleNotificationClick = async (notification: Notification): Promise<void> => {
  if (!notification.viewed) {
    try {
      await markNotificationViewed(notification.id)
      notification.viewed = true
    } catch (e) {
      console.error('Erro ao marcar notificação como vista:', e)
    }
  }
}

const clickOutside = (el: Ref<HTMLElement | null>, callback: () => void) => {
  const handler = (event: MouseEvent) => {
    if (el.value && !el.value.contains(event.target as Node)) {
      callback()
    }
  }

  document.addEventListener('click', handler)

  onBeforeUnmount(() => {
    document.removeEventListener('click', handler)
  })
}

const translateNotificationMessage = (message: string): string => {
  const translations: Record<string, string> = {
    approved: 'Aprovado',
    canceled: 'Cancelado',
    requested: 'Solicitado',
  }

  return message.replace(/approved|canceled|requested/gi, (match) => {
    const key = match.toLowerCase()
    return translations[key] || match
  })
}

onMounted(() => {
  loadNotifications()

  timer = setInterval(loadNotifications, pollingInterval)

  clickOutside(el, () => {
    isOpen.value = false
  })
})

onUnmounted(() => {
  if (timer) clearInterval(timer)
})
</script>

<template>
  <div class="notification-dropdown" ref="el">
    <Button :label="`(${unreadCount})`" padding="0" @click="toggleDropdown">
      <template #prefix-icon>
        <i class="fa fa-bell" aria-hidden="true"></i>
      </template>
    </Button>

    <div v-if="isOpen" class="dropdown-menu">
      <ul class="notifications" v-for="notification in notifications" :key="notification.id">
        <li
          :style="{
            backgroundColor: notification.viewed ? '#f9f9f9' : '#e6f7ff',
          }"
          @click="handleNotificationClick(notification)"
        >
          {{ translateNotificationMessage(notification.notification_message) }}
        </li>
      </ul>
      <p v-if="notifications.length === 0" class="default">Nenhuma notificação</p>
      <p v-if="loading" class="loading">Carregando... <i class="fa fa-spin fa-spinner"></i></p>
      <p v-if="error" class="error">{{ error }}</p>
    </div>
  </div>
</template>

<style lang="less" scoped>
.notification-dropdown {
  position: relative;
  display: inline-block;

  .dropdown-menu {
    position: absolute;
    right: -50px;
    background: #ffffff;
    border: 1px solid #b3b3b3;
    border-radius: 8px;
    width: 300px;
    max-height: 400px;
    overflow-y: auto;
    z-index: 100;

    li:hover {
      font-weight: 500;
    }

    p,
    li {
      font-size: 0.875rem; //14px
      line-height: 1.125rem; //18px
      font-weight: 400;
    }

    li,
    .default,
    .loading,
    .error {
      padding: 10px;
    }

    .default {
      padding: 10px;
      text-align: center;
      color: #777;
    }

    .loading {
      text-align: center;
      color: #555;
    }

    .error {
      text-align: center;
      color: red;
    }

    .notifications {
      list-style: none;
      padding: 0;
      margin: 0;
      border-bottom: 1px solid #b3b3b3;
      cursor: pointer;
    }
  }
}
</style>
