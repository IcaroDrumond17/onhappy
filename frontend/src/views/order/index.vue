<script lang="ts" setup>
import { OrderForm } from '@/types/orderForm'
import { StatisticalValues } from '@/types/statisticalValues'
const router = useRouter()
import { useStoreForm } from '@/stores/storeForm'
const storeForm = useStoreForm()
import { useStoreStatisticalValues } from '@/stores/storeStatisticalValues'
import { callToasted } from '@/composables/toasted'
import { textError } from '@/composables/error'
import { ROUTE_NAMES } from '@/router/names'
import { createOrder } from '@/services/orderService'
const storeStatisticalValues = useStoreStatisticalValues()

const saving = ref<boolean>(false)
const search = ref<string>('')

const form = computed((): OrderForm => storeForm.getOrderForm)

onBeforeMount(() => {
  storeForm.clearForm()
})

const name = computed({
  get(): string | null {
    return form.value.name
  },
  set(val: string | null) {
    storeForm.updateOrderForm('name', val)
  },
})

const departureDate = computed({
  get(): string | Date | null {
    return form.value.departureDate
  },
  set(val: string | Date | null) {
    storeForm.updateOrderForm('departureDate', val)
  },
})

const returnDate = computed({
  get(): string | Date | null {
    return form.value.returnDate
  },
  set(val: string | Date | null) {
    storeForm.updateOrderForm('returnDate', val)
  },
})

const destinationOptions = computed(
  (): StatisticalValues[] => storeStatisticalValues.getDestinationOptions,
)

const destination = computed({
  get(): StatisticalValues[] {
    return (
      destinationOptions.value.filter(
        (f: StatisticalValues) => f.name === form.value.destination,
      ) || ''
    )
  },
  set(val: StatisticalValues) {
    storeForm.updateOrderForm('destination', val ? val.name : '')
  },
})

const valid = (): void => {
  if (!name.value) throw 'Nome do solicitante é obrigatório!'
  if (!departureDate.value) throw 'Data de ida é obrigatório!'
  if (!returnDate.value) throw 'Data de volta é obrigatório!'
  if (!destination.value) throw 'Destino é obrigatório!'
}

const save = async (): Promise<void> => {
  try {
    saving.value = true
    valid()

    await createOrder({
      requestor_name: form.value.name,
      destination: form.value.destination,
      departure_date: form.value.departureDate,
      return_date: form.value.returnDate,
    })

    storeForm.clearForm()
    search.value = ''
    callToasted({ text: 'Pedido adicionado com sucesso!' })
  } catch (e) {
    callToasted({ text: textError(e) })
  } finally {
    saving.value = false
  }
}

const gotToList = (): void => {
  router.push({ name: ROUTE_NAMES.HOME })
}

const filteredOptions = computed(() => {
  if (!search.value) return []

  return destinationOptions.value.filter((o: StatisticalValues) =>
    o.name.toLowerCase().includes(search.value.toLowerCase()),
  )
})

function onSearch(val: string) {
  search.value = val
}
</script>

<template>
  <div class="order">
    <Card padding="15px 0">
      <template #default>
        <div>
          <div class="title">
            <h2>Novo pedido:</h2>
            <Button label="Ver Pedidos" variant="blue" min-width="100" @click="gotToList" />
          </div>
          <form class="form" @submit.prevent="save">
            <div class="group">
              <label for="input-name">Nome solicitante: <span>*</span></label>
              <Input id="input-name" v-model="name" :disabled="saving" />
            </div>
            <div class="flex">
              <div class="group">
                <label for="departure-date">Data Ida <span>*</span></label>
                <InputDate
                  id="departure-date"
                  v-model="departureDate"
                  placeholder="__/__/____"
                  full
                  :disabled="saving"
                />
              </div>
              <div class="group">
                <label for="return-date">Data Volta <span>*</span></label>
                <InputDate
                  id="return-date"
                  v-model="returnDate"
                  placeholder="__/__/____"
                  full
                  :disabled="saving"
                />
              </div>
            </div>
            <div class="group">
              <label for="destination">Destino <span>*</span></label>
              <InputSelect
                id="destination"
                v-model="destination"
                label="name"
                :options="filteredOptions"
                placeholder="Digite uma cidade..."
                noOptionsTitle="Nenhuma cidade encontrada..."
                max-width="400px"
                :disabled="saving"
                :filterable="false"
                :focus="false"
                @search="onSearch"
              />
            </div>
            <Button
              label="Salvar pedido"
              variant="orange"
              width="300px"
              type-button="submit"
              :loading="saving"
              @click="save"
            />
          </form>
        </div>
      </template>
    </Card>
  </div>
</template>

<style lang="less" scoped>
.order {
  margin: 0;
  min-height: 100vh;
  display: flex;
  justify-content: center;
  align-items: center;

  .title {
    padding: 15px 30px;
    display: flex;
    justify-content: space-between;
    align-items: stretch;
    gap: 8px;
    border-bottom: 1px solid #dadada;

    h2 {
      margin: 0;
    }
  }

  .card {
    min-height: 400px;
  }

  .form {
    padding: 0 30px;
    margin: 50px 0;
    display: flex;
    flex-direction: column;
    align-items: stretch;
    justify-content: center;

    @media (min-width: 768px) {
      min-width: 600px;
    }

    label {
      font-size: 1rem; //16px
      line-height: 1.25rem; //20px
      font-weight: 400;
      color: #33475b;

      span {
        color: #fc4b6c;
      }
    }

    .flex {
      @media (min-width: 768px) {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
      }
    }

    .group {
      margin-bottom: 18px;
      display: flex;
      flex-direction: column;
      gap: 6px;
      width: 100%;

      small {
        display: flex;
        align-items: baseline;
        gap: 6px;

        font-size: 0.75rem; //12px
        line-height: 1rem; //16px
        font-weight: 300;
        color: #fc4b6c;
      }
    }

    button {
      margin: 50px auto 0;
    }
  }
}
</style>
