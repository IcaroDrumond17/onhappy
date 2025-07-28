<script lang="ts" setup>
import { formatDateToBR } from '@/composables/formatDate'
import { OrderStatus } from '@/types/orderStatus'
import { Order } from '@/types/order'
import { callToasted } from '@/composables/toasted'
import { StatisticalValuesStatus } from '@/types/statisticalValues'
import { useStoreStatisticalValues } from '@/stores/storeStatisticalValues'
const storeStatisticalValues = useStoreStatisticalValues()
import { updateOrderStatus, deleteOrder } from '@/services/orderService'
import { textError } from '@/composables/error'
import { useAuthStore } from '@/stores/authStore'
const auth = useAuthStore()
import { useStoreNotification } from '@/stores/storeNotification'
const storeNotification = useStoreNotification()

const emits = defineEmits(['afterUpdateStatus', 'afterDeleteOrder'])

interface Props {
  order: Order
}

const props = defineProps<Props>()

const showUpdateStatus = ref<boolean>(false)
const temStatus = ref<OrderStatus>(props.order.status)
const updating = ref<boolean>(false)
const deleting = ref<boolean>(false)

const validAdmin = computed((): boolean => auth.validAdmin)

const enableEditing = computed((): boolean => {
  return validAdmin.value || props.order.userId === auth.user.id
})

const statusOptions = computed((): StatisticalValuesStatus[] =>
  storeStatisticalValues.getStatusOptions.filter(
    (f: StatisticalValuesStatus) => f.value !== OrderStatus.Requested,
  ),
)

const status = computed({
  get(): StatisticalValuesStatus[] {
    return statusOptions.value.filter((f: StatisticalValuesStatus) => f.value === temStatus.value)
  },
  set(val: StatisticalValuesStatus) {
    console.log(val)
    temStatus.value = val.value
  },
})

const statusBadge = computed(() => {
  const nameMap: Record<string, string> = {
    requested: 'badge-requested',
    approved: 'badge-approved',
    canceled: 'badge-canceled',
  }

  return nameMap[props.order.status] ?? ''
})

const statusLabel = computed(() => {
  const nameMap: Record<string, string> = {
    requested: 'Solicitado',
    approved: 'Aprovado',
    canceled: 'Cancelado',
  }

  return nameMap[props.order.status] ?? ''
})

const openUpdateStatus = (): void => {
  try {
    if (!enableEditing.value)
      throw 'Ação não permitida: você não possui acesso para editar este pedido.'

    showUpdateStatus.value = true
  } catch (e) {
    callToasted({ text: textError(e) })
    showUpdateStatus.value = false
  }
}

const updateStatus = async (): Promise<void> => {
  try {
    if (updating.value || deleting.value) return

    if (!enableEditing.value)
      throw 'Ação não permitida: você não possui acesso para editar este pedido.'

    updating.value = true
    await updateOrderStatus(props.order.id, temStatus.value)
    await storeNotification.loadNotifications()

    emits('afterUpdateStatus', { id: props.order.id, status: temStatus.value })

    callToasted({ text: 'Status atualizado com sucesso!' })
  } catch (e) {
    callToasted({ text: textError(e) })
  } finally {
    showUpdateStatus.value = false
    updating.value = false
  }
}

const removeOrder = async (): Promise<void> => {
  try {
    if (updating.value || deleting.value) return

    if (!enableEditing.value)
      throw 'Ação não permitida: você não possui acesso para remover este pedido.'

    if (!confirm('Tem certeza que deseja excluir este pedido?')) return

    deleting.value = true
    await deleteOrder(props.order.id)

    emits('afterDeleteOrder', props.order.id)

    callToasted({ text: 'Pedido removido com sucesso!' })
  } catch (e) {
    callToasted({ text: textError(e) })
  } finally {
    deleting.value = false
  }
}
</script>

<template>
  <tr class="item">
    <td>
      <p class="id">{{ order.id }}</p>
    </td>
    <td>
      <p class="name">{{ order.requestorName }}</p>
    </td>
    <td>
      <p class="destination">{{ order.destination }}</p>
    </td>
    <td>
      <p class="date">{{ formatDateToBR({ date: order.departureDate }) }}</p>
    </td>
    <td>
      <p class="date">{{ formatDateToBR({ date: order.returnDate }) }}</p>
    </td>
    <td style="width: 150px">
      <div v-if="showUpdateStatus" class="status">
        <div>
          <InputSelect
            id="status"
            v-model="status"
            :options="statusOptions"
            :clearable="false"
            :disabled="updating"
            placeholder="Selecione..."
            label="name"
            width="150px"
            @update:modelValue="updateStatus"
          />
        </div>
      </div>
      <div v-else class="status">
        <div
          v-tippy="{
            content: enableEditing
              ? 'Editar status'
              : 'Ação não permitida: você não possui acesso para editar este pedido.',
          }"
          class="badge"
          :class="statusBadge"
          @click="openUpdateStatus"
        >
          <p>{{ statusLabel }}</p>
        </div>
      </div>
    </td>
    <td>
      <div v-if="showUpdateStatus" class="status">
        <div class="actions">
          <i
            v-tippy="{ content: 'Fechar' }"
            class="fa fa-times"
            aria-hidden="true"
            @click="showUpdateStatus = false"
          ></i>
        </div>
      </div>
      <div v-else class="status">
        <div class="actions">
          <i
            v-tippy="{
              content: enableEditing
                ? 'Remover pedido'
                : 'Ação não permitida: você não possui acesso para remover este pedido.',
            }"
            class="fa"
            :class="{ 'fa-spin fa-spinner': deleting, 'fa-trash-o': !deleting }"
            aria-hidden="true"
            :style="{
              opacity: enableEditing ? '1' : '0.5',
            }"
            @click="removeOrder"
          ></i>
        </div>
      </div>
    </td>
  </tr>
</template>

<style lang="less" scoped>
.item {
  cursor: pointer;

  height: 64px;

  .name,
  .destination {
    max-width: 230px;
    white-space: break-spaces;
  }

  p {
    font-size: 1rem; //16px
    line-height: 1.25rem; //20px
    font-weight: 400;
    color: #333;
  }

  .status {
    .badge {
      width: 100px;
      padding: 4px 0;
      border-radius: 15px;
      text-align: center;
      p {
        margin: 0;
        font-size: 0.75rem; //12px
        line-height: 0.875rem; //14px
        font-weight: 500;
        color: #ffffff;
      }
    }

    .badge-requested {
      background-color: #f8af41;
    }

    .badge-approved {
      background-color: #4caf50;
    }

    .badge-canceled {
      background-color: #f44336;
    }

    .actions {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 12px;
    }

    i {
      cursor: pointer;
      color: #8c8c8c;
    }
  }
}
</style>
