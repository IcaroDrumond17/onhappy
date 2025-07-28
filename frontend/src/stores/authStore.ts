import { defineStore } from 'pinia'
import type { PersistenceOptions } from 'pinia-plugin-persistedstate'
import { decodeToken } from '@/utils/jwtHelper'
import { User, UserType } from '@/types/user'
import http from '@/services/http'
import { useRouter } from 'vue-router'
import { ROUTE_NAMES } from '@/router/names'

export const useAuthStore = defineStore(
  'authStore',
  () => {
    const user = ref<User>({
      id: 0,
      name: '',
      email: '',
      token: '',
      type_user: UserType.Default,
    })

    const tokenExpiration = ref<number>(0)

    const router = useRouter()

    const validAdmin = computed((): boolean => user.value?.type_user === UserType.Admin)

    const isLoggedIn = computed(() => !!user.value?.token && Date.now() < tokenExpiration.value)

    async function login(email: string, password: string) {
      try {
        const response = await http.post('/login', { email, password })

        user.value = {
          ...response.data.user,
          token: response.data.access_token,
        }

        if (
          user.value.token &&
          typeof user.value.token === 'string' &&
          user.value.token.trim() !== ''
        ) {
          // Decodifica o token para pegar expiração
          const payload = decodeToken(user.value.token)
          tokenExpiration.value = payload.exp * 1000 // em ms
        } else {
          console.warn('Token inválido ou ausente para decodificação')
        }

        // definir o token no header
        http.defaults.headers.common['Authorization'] = `Bearer ${user.value.token}`

        router.push({ name: ROUTE_NAMES.HOME_DEFAULT })
      } catch (error) {
        throw error
      }
    }

    async function logout() {
      user.value = {
        id: 0,
        name: '',
        email: '',
        token: '',
        type_user: UserType.Default,
      }

      tokenExpiration.value = 0
      delete http.defaults.headers.common['Authorization']
      await router.push({ name: ROUTE_NAMES.LOGIN })
    }

    return {
      user,
      tokenExpiration,
      validAdmin,
      isLoggedIn,
      decodeToken,
      login,
      logout,
    }
  },
  {
    persist: {
      storage: localStorage,
      paths: ['user', 'tokenExpiration'],
    } as unknown as PersistenceOptions,
  },
)
