import { defineStore } from 'pinia'
import type { PersistenceOptions } from 'pinia-plugin-persistedstate'
import { OrderStatus } from '@/types/orderStatus'
import { StatisticalValues, StatisticalValuesStatus } from '@/types/statisticalValues'
import { City } from '@/types/city'

export const useStoreStatisticalValues = defineStore(
  'storeStatisticalValues',
  () => {
    const statusOptions = ref<StatisticalValuesStatus[]>([
      { name: 'Solicitado', value: OrderStatus.Requested },
      { name: 'Aprovado', value: OrderStatus.Approved },
      { name: 'Cancelado', value: OrderStatus.Canceled },
    ])

    const destinationOptions = ref<StatisticalValues[]>([
      { name: 'Belo Horizonte', value: 1 },
      { name: 'São Paulo', value: 2 },
      { name: 'Rio de Janeiro', value: 3 },
    ])

    const getStatusOptions = computed((): StatisticalValuesStatus[] => statusOptions.value)

    const getDestinationOptions = computed((): StatisticalValues[] => destinationOptions.value)

    function setCities(cities: City[]) {
      if (cities.length > 0) {
        destinationOptions.value = cities.map((c: City): StatisticalValues => {
          return {
            value: c.id,
            name: c.nome,
          }
        })
      }
    }
    return {
      destinationOptions,
      getStatusOptions,
      getDestinationOptions,
      setCities,
    }
  },
  {
    persist: {
      storage: localStorage,
      paths: ['destinationOptions'],
    } as unknown as PersistenceOptions,
  },
)
