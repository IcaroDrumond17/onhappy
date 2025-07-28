import { jwtDecode } from 'jwt-decode'

interface JwtPayload {
  exp: number // tempo de expiração (timestamp unix, em segundos)
  iat?: number
  sub?: string | number
}

export function decodeToken(token: string): JwtPayload {
  return jwtDecode<JwtPayload>(token)
}
