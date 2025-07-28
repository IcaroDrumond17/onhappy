<script lang="ts" setup>
import { textError } from '@/composables/error'
import { callToasted } from '@/composables/toasted'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()

const loading = ref<boolean>(false)

const isLoggedIn = computed((): boolean => auth.isLoggedIn)

const userName = computed((): string => auth.user.name)

const logout = async (): Promise<void> => {
  try {
    loading.value = true

    await auth.logout()
  } catch (e) {
    callToasted({ text: textError(e) })
  } finally {
    loading.value = false
  }
}
</script>

<template>
  <nav>
    <div class="logo">
      <a href="https://onhappy.com.br" target="_blank">
        <img
          src="https://onhappy.com.br/wp-content/uploads/2024/07/Logo-completa-10-768x208.png"
          width="auto"
          height="auto"
          alt="onhappy"
        />
      </a>
    </div>
    <div v-if="isLoggedIn" class="profile">
      <p v-if="userName" aria-live="polite" class="welcome-message">Bem-vindo, {{ userName }}!</p>
      <div class="notifications">
        <NotificationDropdown />
      </div>
      <p class="logout" @click="logout">Sair</p>
    </div>
  </nav>
</template>

<style lang="less" scoped>
nav {
  position: relative;
  top: 0;
  left: 0;
  right: 0;
  padding: 24px 8px;
  height: 100px;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  gap: 4px;

  @media (min-width: 768px) {
    padding: 8px 24px;
    height: 50px;
    flex-direction: row;
    justify-content: space-between;
  }

  z-index: 99999;
  background-color: #ffffff;

  .logo {
    cursor: pointer;
    img {
      width: 100px;
    }
  }

  .profile {
    display: flex;
    align-items: baseline;
    gap: 24px;

    p {
      font-size: 0.875rem; //14px
      line-height: 1.125rem; //18px
      font-weight: 500;
      color: #333;
    }

    .logout {
      cursor: pointer;

      &:hover {
        font-weight: 700;
      }
    }
  }
}
</style>
