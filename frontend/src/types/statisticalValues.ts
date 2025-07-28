import { OrderStatus } from './orderStatus'

export interface StatisticalValues {
  name: string
  value: string | number | null
}

export interface StatisticalValuesStatus {
  name: string
  value: OrderStatus
}
