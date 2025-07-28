import { OrderStatus } from './orderStatus'

export interface Order {
  id: number
  requestorName: string
  destination: string
  departureDate: string
  returnDate: string
  status: OrderStatus
  userId: number
}

export interface OrderApi {
  id: number
  requestor_name: string
  status: string
  destination: string
  departure_date: string
  return_date: string
  user_id: number
}
