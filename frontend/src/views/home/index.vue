<script lang="ts" setup>
import { ROUTE_NAMES } from '@/router/names'
import { detectDeviceType, DeviceType } from '@/composables/responsive'
import { useStoreFilter } from '@/stores/storeFilter'
const storeFilter = useStoreFilter()
const router = useRouter()

import OrderList from './orders/index.vue'
import OrderFilter from './orders/filter.vue'

//@ts-ignore
const timeout = ref<NodeJS.Timeout>(null)

const initFilter = computed(() => storeFilter.getInitFilter)

const showFilter = computed((): boolean => storeFilter.getShowFilter)

const showModeMobile = computed((): boolean =>
  [DeviceType.Mobile, DeviceType.Tablet, DeviceType.Notebook].includes(
    detectDeviceType() || DeviceType.Desktop,
  ),
)

const search = computed({
  get(): string {
    return initFilter.value.name
  },
  set(val: string) {
    clearTimeout(timeout.value)

    timeout.value = setTimeout(() => {
      storeFilter.updateFilterField('name', val)
    }, 500)
  },
})

const openFilter = (): void => storeFilter.openFilter()

const gotToForm = (): void => {
  router.push({ name: ROUTE_NAMES.ORDER })
}

onBeforeMount(() => {
  storeFilter.clearFilter()
})
</script>

<template>
  <div class="home">
    <Card padding="15px 30px">
      <template #default>
        <div>
          <div class="title">
            <h2>Pedidos:</h2>
          </div>
          <div class="actions">
            <div class="filters">
              <InputSearch
                id="input-search"
                v-model="search"
                :padding="showModeMobile ? '0' : '0px 16px'"
                :min-width="showModeMobile ? '100%' : '400px'"
                placeholder="Pesquisar pedido..."
              />
              <div class="filter">
                <Button
                  label="filtro"
                  variant="clear"
                  :width="showModeMobile ? '100%' : 'auto'"
                  @click="openFilter"
                >
                  <template #prefix-icon>
                    <i class="fa fa-filter" aria-hidden="true"></i>
                  </template>
                </Button>
              </div>
            </div>
            <div class="add">
              <Button
                label="Adicionar pedido"
                variant="orange"
                :width="showModeMobile ? '100%' : 'auto'"
                @click="gotToForm"
              />
            </div>
          </div>
          <OrderList />
        </div>
      </template>
    </Card>

    <OrderFilter
      v-if="showFilter"
      :show-filter="showFilter"
      :width="showModeMobile ? 'auto' : '500px'"
    />
  </div>
</template>

<style lang="less" scoped>
.home {
  margin: 30px 0;
  min-height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;

  h2 {
    margin: 0;
  }

  .card {
    width: 80%;
    min-height: 70vh;

    .actions {
      padding: 16px 0;
      display: flex;
      flex-direction: column;
      align-items: stretch;
      justify-content: center;
      gap: 16px;

      border-bottom: 1px solid #dadada;

      @media (min-width: 768px) {
        flex-direction: row;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
      }

      .filters {
        display: flex;
        flex-direction: column;
        align-items: stretch;
        gap: 8px;

        @media (min-width: 768px) {
          flex-direction: row;
          align-items: center;
        }
      }
    }
  }
}
</style>
