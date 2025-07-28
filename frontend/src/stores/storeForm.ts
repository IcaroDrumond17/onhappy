import { defineStore } from 'pinia'
import { OrderForm } from '@/types/orderForm'
import { OrderStatus } from '@/types/orderStatus'

export const useStoreForm = defineStore('storeForm', () => {
  const orderForm = ref<OrderForm>({
    name: '',
    status: OrderStatus.Requested,
    destination: '',
    departureDate: '',
    returnDate: '',
  })

  const getOrderForm = computed((): OrderForm => orderForm.value)

  function clearForm() {
    orderForm.value = {
      name: '',
      status: OrderStatus.Requested,
      destination: '',
      departureDate: '',
      returnDate: '',
    }
  }

  function updateOrderForm<K extends keyof OrderForm>(field: K, val: OrderForm[K]) {
    orderForm.value[field] = val
  }

  return {
    getOrderForm,
    clearForm,
    updateOrderForm,
  }
})
