<script lang="ts" setup>
import { textError } from '@/composables/error'
import { callToasted } from '@/composables/toasted'
import { isValidEmail } from '@/composables/validEmail'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()

const email = ref<string>('')
const emailErrorMessage = ref<string>('')
const password = ref<string>('')
const passwordErrorMessage = ref<string>('')
const errorMessageAfterLogin = ref<string>('')
const loading = ref<boolean>(false)

const valid = (): void => {
  emailErrorMessage.value = ''
  passwordErrorMessage.value = ''

  if (!email.value) {
    emailErrorMessage.value = 'E-mail é obrigatório!'
    throw emailErrorMessage.value
  }

  if (!isValidEmail(email.value)) {
    emailErrorMessage.value = 'Digite um E-mail válido!'
    throw emailErrorMessage.value
  }

  if (!password.value) {
    passwordErrorMessage.value = 'Senha é obrigatório!'
    throw passwordErrorMessage.value
  }
}

const login = async (): Promise<void> => {
  try {
    loading.value = true
    valid()

    await auth.login(email.value, password.value)
  } catch (e) {
    const message = textError(e)
    errorMessageAfterLogin.value = message
    callToasted({ text: message })
    loading.value = false
  }
}
</script>

<template>
  <div class="login">
    <Card>
      <template #default>
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
        <form class="form" @submit.prevent="login">
          <div class="group">
            <label for="input-email">E-mail <span>*</span></label>
            <Input
              id="input-email"
              v-model="email"
              type="email"
              :disabled="loading"
              :error="!!emailErrorMessage"
            />
            <ErrorMessage v-if="emailErrorMessage" :message="emailErrorMessage" />
          </div>
          <div class="group">
            <label for="input-pass">Senha <span>*</span></label>
            <Input
              id="input-pass"
              v-model="password"
              type="password"
              :disabled="loading"
              :error="!!passwordErrorMessage"
            />
            <ErrorMessage v-if="passwordErrorMessage" :message="passwordErrorMessage" />
          </div>
          <ErrorMessage v-if="errorMessageAfterLogin" :message="errorMessageAfterLogin" />
          <Button
            label="Entrar"
            variant="blue"
            type-button="submit"
            :loading="loading"
            full
            @click="login"
          />
        </form>
      </template>
    </Card>
  </div>
</template>

<style lang="less" scoped>
.login {
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;

  .card {
    min-width: 240px;

    @media (min-width: 768px) {
      min-width: 300px;
    }

    min-width: 240px;
    max-height: 500px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    gap: 30px;

    .logo {
      margin: 0 auto;
      cursor: pointer;

      a {
        img {
          width: 200px;
        }
      }
    }

    .form {
      width: 100%;

      label {
        font-size: 1rem; //16px
        line-height: 1.25rem; //20px
        font-weight: 400;
        color: #33475b;

        span {
          color: #fc4b6c;
        }
      }

      .group {
        margin-bottom: 18px;
        display: flex;
        flex-direction: column;
        gap: 6px;
      }

      button {
        margin: 15px auto 0;
      }
    }
  }
}
</style>
