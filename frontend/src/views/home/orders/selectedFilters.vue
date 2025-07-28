<script setup lang="ts">
import { formatDateToBR } from '@/composables/formatDate'
import { useStoreFilter } from '@/stores/storeFilter'
import { OrderStatus } from '@/types/orderStatus'
import { StoreOrderFilter } from '@/types/orderFilter'
const storeFilter = useStoreFilter()

interface RemoveArrFilter {
  field: string
  value: string | OrderStatus | null
}

interface Props {
  total: number
  showTotal: boolean
}

withDefaults(defineProps<Props>(), {
  showTotal: false,
})

const filter = computed(() => storeFilter.getFilter)

const selectedFilters = computed(() => storeFilter.getSelectedFilters)

const showSelectedFilters = computed((): boolean => {
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

const updateFilterField = <K extends keyof StoreOrderFilter>(
  field: K,
  val: StoreOrderFilter[K],
): void => storeFilter.updateFilterField(field, val)

const removeArrFilterItem = (val: RemoveArrFilter): void => storeFilter.removeArrFilterItem(val)
</script>
<template>
  <transition name="fade" mode="out-in">
    <div v-if="showSelectedFilters" key="selected-filters" class="selected-filters">
      <div class="filtered">
        <div class="title">Filtros:</div>
        <div class="items">
          <div v-if="selectedFilters.name">
            Nomo do solicitante: {{ selectedFilters.name }}
            <i class="fa fa-times" @click="updateFilterField('name', '')"></i>
          </div>

          <div v-if="selectedFilters.startDate">
            De: {{ formatDateToBR({ date: selectedFilters.startDate }) }}
            <i class="fa fa-times" @click="updateFilterField('startDate', '')"></i>
          </div>

          <div v-if="selectedFilters.endDate">
            Até: {{ formatDateToBR({ date: selectedFilters.endDate }) }}
            <i class="fa fa-times" @click="updateFilterField('endDate', '')"></i>
          </div>

          <div v-if="selectedFilters.departureDate">
            Data Ida: {{ formatDateToBR({ date: selectedFilters.departureDate }) }}
            <i class="fa fa-times" @click="updateFilterField('departureDate', '')"></i>
          </div>

          <div v-if="selectedFilters.returnDate">
            Data Volta: {{ formatDateToBR({ date: selectedFilters.returnDate }) }}
            <i class="fa fa-times" @click="updateFilterField('returnDate', '')"></i>
          </div>

          <div v-for="(status, index) of selectedFilters.status" :key="`status-${index}`">
            Status: {{ status.name }}
            <i
              class="fa fa-times"
              @click="
                removeArrFilterItem({
                  field: 'status',
                  value: status.value?.toString() || null,
                })
              "
            ></i>
          </div>

          <div
            v-for="(destination, index) of selectedFilters.destinations"
            :key="`destinations-${index}`"
          >
            Destino: {{ destination.name }}
            <i
              class="fa fa-times"
              @click="
                removeArrFilterItem({
                  field: 'destinations',
                  value: destination.value?.toString() || null,
                })
              "
            ></i>
          </div>
        </div>
      </div>
      <div v-show="showTotal" class="quantity">
        <p>
          Resultado(s) encontrado(s):
          <span>{{ total }} </span>
        </p>
      </div>
    </div>
  </transition>
</template>
<style lang="less" scoped>
.selected-filters {
  margin: 30px 0;
  display: flex;
  flex-direction: column;

  .filtered {
    display: flex;
    align-items: center;
    justify-content: flex-start;

    .title {
      margin-right: 5px;
      font-size: 0.875rem; //14px
      line-height: 1.125rem; //18px
      font-weight: 500;
      color: #333;
    }

    .items {
      display: flex;
      flex-wrap: wrap;

      div {
        margin: 2px 0.1rem;
        padding: 0.3rem 0.5rem;
        font-weight: 400;
        font-size: 0.625rem; //10px;
        line-height: 0.3rem; //4.8px;
        background: #ffffff;
        border: 1px solid #ff6b00;
        border-radius: 15px;
        color: #ff6b00;
        white-space: nowrap;

        i {
          margin-left: 4px;
          cursor: pointer;
        }
      }
    }
  }

  .quantity {
    margin-top: 5px;
    p {
      font-size: 0.875rem; //14px
      line-height: 1.125rem; //18px
      font-weight: 500;
      color: #333;

      span {
        color: #ff6b00;
      }
    }
  }
}
</style>
