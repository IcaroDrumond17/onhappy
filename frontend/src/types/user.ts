export enum UserType {
  Admin = 'admin',
  Default = 'default',
}

export interface User {
  id: number
  name: string
  email: string
  token: string
  type_user: UserType
}
