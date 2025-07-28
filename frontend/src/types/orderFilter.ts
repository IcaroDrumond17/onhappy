import { StatisticalValues } from './statisticalValues'

export interface PropsFilter {
  showFilter: boolean
  title?: string
  width?: string
}

export interface StoreOrderFilter {
  name: string
  status: StatisticalValues[]
  destinations: StatisticalValues[]
  startDate: string | Date | null
  endDate: string | Date | null
  departureDate: string | Date | null
  returnDate: string | Date | null
}
