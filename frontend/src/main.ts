import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import VueTippy from 'vue-tippy'
import 'tippy.js/dist/tippy.css'
// @ts-ignore
import Toasted from '@hoppscotch/vue-toasted'
import 'vue-toasted/dist/vue-toasted.min.css'

import App from './App.vue'
import router from './router'
import { useAuthStore } from './stores/authStore'
import http from './services/http'

const pinia = createPinia()
pinia.use(piniaPluginPersistedstate)

const app = createApp(App)
app.use(pinia)
app.use(router)


app.use(VueTippy, {
  directive: 'tippy', // => v-tippy
  component: 'tippy', // => <tippy/>
  componentSingleton: 'tippy-singleton',
  defaultProps: {
    placement: 'top',
    allowHTML: true,
  },
})

app.use(Toasted, {
  position: 'bottom-right',
  iconPack: 'fontawesome',
  duration: 3000,
  singleton: true,
})

// pinia persist
router.isReady().then(() => {
  const auth = useAuthStore()

  if (auth.user.token) {
    http.defaults.headers.common['Authorization'] = `Bearer ${auth.user.token}`
  }

  app.mount('#app')
})
