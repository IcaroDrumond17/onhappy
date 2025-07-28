<script lang="ts" setup>
import { cloneDeep } from 'lodash'
import { PropsFilter } from '@/types/orderFilter'
import { StatisticalValues } from '@/types/statisticalValues'
import { useStoreFilter } from '@/stores/storeFilter'
const storeFilter = useStoreFilter()
import { useStoreStatisticalValues } from '@/stores/storeStatisticalValues'
const storeStatisticalValues = useStoreStatisticalValues()

withDefaults(defineProps<PropsFilter>(), {
  title: 'Filtros de Pesquisa',
  width: '500px',
})

const saving = ref<boolean>(false)
const search = ref<string>('')

const initFilter = computed(() => storeFilter.getInitFilter)

const statusOptions = computed((): StatisticalValues[] => storeStatisticalValues.getStatusOptions)

const status = computed({
  get(): StatisticalValues[] {
    return initFilter.value.status
  },
  set(val: StatisticalValues[]) {
    storeFilter.updateInitFilterField('status', val)
  },
})

const startDate = computed({
  get(): string | Date | null {
    return initFilter.value.startDate
  },
  set(val: string | Date | null) {
    storeFilter.updateInitFilterField('startDate', val)
  },
})

const endDate = computed({
  get(): string | Date | null {
    return initFilter.value.endDate
  },
  set(val: string | Date | null) {
    storeFilter.updateInitFilterField('endDate', val)
  },
})

const departureDate = computed({
  get(): string | Date | null {
    return initFilter.value.departureDate
  },
  set(val: string | Date | null) {
    storeFilter.updateInitFilterField('departureDate', val)
  },
})

const returnDate = computed({
  get(): string | Date | null {
    return initFilter.value.returnDate
  },
  set(val: string | Date | null) {
    storeFilter.updateInitFilterField('returnDate', val)
  },
})

const destinationOptions = computed(
  (): StatisticalValues[] => storeStatisticalValues.getDestinationOptions,
)

const destination = computed({
  get(): StatisticalValues[] {
    return initFilter.value.destinations
  },
  set(val: StatisticalValues[]) {
    storeFilter.updateInitFilterField('destinations', val)
  },
})

const closeFilter = (): void => storeFilter.closeFilter()

const clearFilter = (): void => storeFilter.clearFilter()

const refetchFilter = (): void => {
  const result = cloneDeep(initFilter.value)

  storeFilter.appliedFilter(result)
  closeFilter()
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
  <SideBar :showSideBarFilter="showFilter" :title="title" :width="width" @close="closeFilter">
    <!-- CONTENT -->
    <template #content>
      <div class="content">
        <div class="form">
          <div class="group flex">
            <div>
              <label for="start-date">De</label>
              <InputDate id="start-date" v-model="startDate" placeholder="__/__/____" full />
            </div>
            <div>
              <label for="end-date">Até</label>
              <InputDate id="end-date" v-model="endDate" placeholder="__/__/____" full />
            </div>
          </div>
          <div class="group flex">
            <div>
              <label for="departure-date">Data Ida</label>
              <InputDate
                id="departure-date"
                v-model="departureDate"
                placeholder="__/__/____"
                full
              />
            </div>
            <div>
              <label for="return-date">Data Volta</label>
              <InputDate id="return-date" v-model="returnDate" placeholder="__/__/____" full />
            </div>
          </div>
          <div class="group">
            <label for="status">Status do pedido</label>
            <InputSelect
              id="status"
              v-model="status"
              label="name"
              :options="statusOptions"
              placeholder="Selecione..."
              multiple
              full
            />
          </div>
          <div class="group">
            <label for="destination">Destino</label>
            <InputSelect
              id="destination"
              v-model="destination"
              label="name"
              :options="filteredOptions"
              placeholder="Digite uma cidade..."
              noOptionsTitle="Nenhuma cidade encontrada..."
              multiple
              full
              :filterable="false"
              :focus="false"
              @search="onSearch"
            />
          </div>
        </div>
        <!--ACTIONS -->
        <div class="actions">
          <Button
            label="Aplicar filtros"
            variant="orange"
            full
            :loading="saving"
            @click="refetchFilter()"
          />
          <Button
            label="Limpar Filtros"
            variant="clear"
            full
            :disabled="saving"
            @click="clearFilter()"
          />
        </div>
      </div>
    </template>
  </SideBar>
</template>

<style lang="less" scoped>
.form {
  padding: 1rem 0;

  .flex {
    @media (min-width: 768px) {
      display: flex;
      align-items: center;
      gap: 8px;
    }
  }

  .group {
    margin-bottom: 1rem;

    label {
      font-size: 1rem; //16px
      line-height: 1.25rem; //20px
      font-weight: 400;
      color: #33475b;
    }
  }
}

.actions {
  bottom: 0;
  position: relative;
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: nowrap;
  padding: 0 1rem;
  margin: 2rem 0;
  button {
    margin: 0 0.1rem;
  }
}
</style>
