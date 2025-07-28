export interface Props {
  id: string
  className?: string
  placeholder?: string
  borderRadius?: string
  padding?: string
  width?: string
  minWidth?: string
  maxWidth?: string
  type?: 'text' | 'email' | 'password'
  autocomplete?: string
  full?: boolean
  disabled?: boolean
  error?: boolean
}