<script lang="ts" setup>
import { cloneDeep } from 'lodash'
import { ROUTE_NAMES } from '@/router/names'
import { fetchOrdersWithCancel } from '@/services/orderService'
import { textError } from '@/composables/error'
import { callToasted } from '@/composables/toasted'
const router = useRouter()
import { useStoreFilter } from '@/stores/storeFilter'
const storeFilter = useStoreFilter()
import { Order, OrderApi } from '@/types/order'
import { OrderStatus } from '@/types/orderStatus'

import SelectedFilters from './selectedFilters.vue'
import Item from './item.vue'

const orders = ref<Order[]>([])
const loading = ref<boolean>(false)
//@ts-ignore
const timeout = ref<NodeJS.Timeout>(null)

const filter = computed(() => storeFilter.getFilter)

const isFilter = computed((): boolean => {
  const { requestor_name, status, destination, start_date, end_date, departure_date, return_date } =
    filter.value
  return !!(
    requestor_name ||
    (Array.isArray(status) && status?.length > 0) ||
    (Array.isArray(destination) && destination?.length > 0) ||
    start_date ||
    end_date ||
    departure_date ||
    return_date
  )
})

const getOrders = async (): Promise<void> => {
  try {
    loading.value = true
    const result = await fetchOrdersWithCancel(filter.value)

    if (!result) return

    orders.value = cloneDeep(
      result.data.map((r: OrderApi) => ({
        id: r.id,
        requestorName: r.requestor_name,
        status: r.status,
        destination: r.destination,
        departureDate: r.departure_date,
        returnDate: r.return_date,
        userId: r.user_id,
      })),
    )

    loading.value = false
  } catch (e) {
    callToasted({ text: textError(e) })
    loading.value = false
  }
}
const hasResult = computed((): boolean => orders.value.length > 0)

onBeforeMount(() => {
  orders.value = [];
  getOrders()
})

const afterUpdateStatus = ({ id, status }: { id: number; status: OrderStatus }): void => {
  if (orders.value.length > 0) {
    const index = orders.value.findIndex((f: Order) => f.id === id)

    if (index > -1) orders.value[index].status = status
  }
}

const afterDeleteOrder = (id: number): void => {
  if (orders.value.length > 0) {
    const index = orders.value.findIndex((f: Order) => f.id === id)

    if (index > -1) orders.value.splice(index, 1)
  }
}

const gotToForm = (): void => {
  router.push({ name: ROUTE_NAMES.ORDER })
}

watch(
  () => filter.value,
  () => {
    clearTimeout(timeout)
    timeout.value = setTimeout(() => {
      getOrders()
    }, 500)
  },
  { deep: true },
)
</script>

<template>
  <div>
    <SelectedFilters :total="orders.length" :show-total="!loading" />
    <transition name="fade" mode="out-in">
      <Loading v-if="loading" key="loading" center />
      <div v-else key="content">
        <div v-if="hasResult" class="table-responsive">
          <table>
            <thead>
              <tr>
                <th>Pedido</th>
                <th>Nome solicitante</th>
                <th>Destino</th>
                <th>Ida</th>
                <th>Volta</th>
                <th>Status</th>
                <th>Ações</th>
              </tr>
            </thead>
            <tbody>
              <Item
                v-for="order of orders"
                :key="order.id"
                :order="order"
                @after-update-status="afterUpdateStatus"
                @after-delete-order="afterDeleteOrder"
              />
            </tbody>
          </table>
        </div>
        <div v-else class="default">
          <div v-if="isFilter">
            <p>Nenhum pedido encontrado!</p>
          </div>
          <div v-else>
            <p>Sem pedidos cadastrados!</p>
            <Button
              label="Adicionar novo pedido"
              variant="orange"
              min-width="100"
              @click="gotToForm"
            />
          </div>
        </div>
      </div>
    </transition>
  </div>
</template>

<style lang="less" scoped>
.table-responsive {
  margin-top: 30px;
  width: 100%;
  min-height: 500px;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
  border-radius: 15px;

  table {
    width: 100%;
    min-width: 600px;
    border-collapse: collapse;
    font-size: 1rem; //16px
    color: #333;

    thead {
      background-color: #f5f5f5;
      width: 100%;

      tr {
        th {
          text-align: left;
          padding: 12px 16px;
          min-width: 100%;
          border-bottom: 2px solid #e0e0e0;
        }
      }
    }

    tbody {
      &:deep(tr) {
        &:nth-child(even) {
          background-color: #fafafa;
        }

        &:nth-child(odd) {
          background-color: #ffffff;
        }

        &:hover {
          background-color: #f0f8ff;
        }

        td {
          padding: 12px 16px;
          border-bottom: 1px solid #eaeaea;
        }
      }
    }

    caption {
      caption-side: top;
      padding: 8px;
      font-weight: bold;
      font-size: 16px;
    }
  }
}

.default {
  margin-top: 15%;
  display: flex;
  justify-content: center;
  align-items: center;
  gap: 8px;

  p {
    margin-bottom: 8px;
    font-size: 1rem; //16px
    line-height: 1.25rem; //20px
    font-weight: 400;
    color: #333;
  }
}
</style>
