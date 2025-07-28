import { defineStore } from 'pinia'
import * as momentTemp from 'moment'
const moment = momentTemp['default']
import { StoreOrderFilter } from '@/types/orderFilter'
import { OrderStatus } from '@/types/orderStatus'
import { StatisticalValues } from '@/types/statisticalValues'

export const useStoreFilter = defineStore('storeFilter', () => {
  const showFilter = ref<boolean>(false)

  const initFilter = ref<StoreOrderFilter>({
    name: '',
    status: [],
    destinations: [],
    startDate: null,
    endDate: null,
    departureDate: null,
    returnDate: null,
  })

  const selectedFilters = ref<StoreOrderFilter>({
    name: '',
    status: [],
    destinations: [],
    startDate: null,
    endDate: null,
    departureDate: null,
    returnDate: null,
  })

  const getShowFilter = computed((): boolean => showFilter.value)

  const getInitFilter = computed(() => initFilter.value)

  const getSelectedFilters = computed(() => selectedFilters.value)

  const getFilter = computed(() => {
    const rawFilter = {
      requestor_name: selectedFilters.value.name?.trim() || undefined,

      status: selectedFilters.value.status.length
        ? selectedFilters.value.status.map((s) => s.value)
        : undefined,

      destination: selectedFilters.value.destinations.length
        ? selectedFilters.value.destinations.map((d) => d.name)
        : undefined,

      start_date: selectedFilters.value.startDate
        ? moment(selectedFilters.value.startDate).format('YYYY-MM-DD')
        : undefined,

      end_date: selectedFilters.value.endDate
        ? moment(selectedFilters.value.endDate).format('YYYY-MM-DD')
        : undefined,

      departure_date: selectedFilters.value.departureDate
        ? moment(selectedFilters.value.departureDate).format('YYYY-MM-DD')
        : undefined,

      return_date: selectedFilters.value.returnDate
        ? moment(selectedFilters.value.returnDate).format('YYYY-MM-DD')
        : undefined,
    }

    // remover chaves null ou undefines
    return Object.fromEntries(
      Object.entries(rawFilter).filter(
        ([_, v]) =>
          v !== undefined &&
          v !== null &&
          !(Array.isArray(v) && v.length === 0) &&
          !(typeof v === 'string' && v.trim() === ''),
      ),
    )
  })

  function openFilter() {
    showFilter.value = true
  }

  function closeFilter() {
    showFilter.value = false
  }

  function clearFilter() {
    initFilter.value.name = ''
    initFilter.value.status = []
    initFilter.value.destinations = []
    initFilter.value.startDate = null
    initFilter.value.endDate = null
    initFilter.value.departureDate = null
    initFilter.value.returnDate = null

    selectedFilters.value.name = ''
    selectedFilters.value.status = []
    selectedFilters.value.destinations = []
    selectedFilters.value.startDate = null
    selectedFilters.value.endDate = null
    selectedFilters.value.departureDate = null
    selectedFilters.value.returnDate = null
  }

  function appliedFilter(val: StoreOrderFilter) {
    selectedFilters.value = val
  }

  function updateInitFilterField<K extends keyof StoreOrderFilter>(
    field: K,
    val: StoreOrderFilter[K],
  ) {
    initFilter.value[field] = val
  }

  function updateFilterField<K extends keyof StoreOrderFilter>(field: K, val: StoreOrderFilter[K]) {
    initFilter.value[field] = val
    selectedFilters.value[field] = val
  }

  function removeArrFilterItem({
    field,
    value,
  }: {
    field: string
    value: string | OrderStatus | null
  }) {
    if (field === 'status') {
      const initIndex = initFilter.value.status.findIndex(
        (f: StatisticalValues) => f.value == value,
      )
      if (initIndex > -1) initFilter.value.status.splice(initIndex, 1)

      const indexSelected = selectedFilters.value.status.findIndex(
        (f: StatisticalValues) => f.value == value,
      )
      if (initIndex > -1) selectedFilters.value.status.splice(indexSelected, 1)
    }

    if (field === 'destinations') {
      const initIndex = initFilter.value.destinations.findIndex(
        (f: StatisticalValues) => f.value == value,
      )
      if (initIndex > -1) initFilter.value.destinations.splice(initIndex, 1)

      const indexSelected = selectedFilters.value.destinations.findIndex(
        (f: StatisticalValues) => f.value == value,
      )
      if (initIndex > -1) selectedFilters.value.destinations.splice(indexSelected, 1)
    }
  }

  return {
    getFilter,
    getShowFilter,
    getInitFilter,
    getSelectedFilters,
    openFilter,
    closeFilter,
    clearFilter,
    appliedFilter,
    updateInitFilterField,
    updateFilterField,
    removeArrFilterItem,
  }
})
