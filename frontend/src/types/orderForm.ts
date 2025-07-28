import { OrderStatus } from "./orderStatus"

export interface OrderForm {
  name: string | null
  status: OrderStatus
  destination: string | null
  departureDate: string | Date | null
  returnDate: string | Date | null
}
