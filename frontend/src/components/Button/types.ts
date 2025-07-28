export interface Props {
  id?: string
  typeButton?: 'button' | 'submit' | 'reset' | undefined
  label?: string
  className?: string
  variant?: 'blue' | 'orange' | 'clear'
  size?: 'sm' | 'md' | 'lg'
  format?: 'pill' | 'rounded'
  width?: string
  minWidth?: string
  marginLabel?: string
  loading?: boolean
  disabled?: boolean
  full?: boolean
}
