import type { AxiosError } from 'axios'

export function textError(e: unknown): string {
  let text = 'Falha!' // mensagem padrão caso não encontre mensagem específica

  // Verifica se é um erro do axios (requisição HTTP)
  if (isAxiosError(e)) {

    if (e.response?.data && typeof e.response.data === 'object') {

      const data = e.response.data as Record<string, unknown>

 
      if (typeof data.message === 'string') {
        text = data.message
      } 

      else if (typeof data.error === 'string') {
        text = data.error
      } 
      // Se existem erros de validação no formato 'errors'
      else if (data.errors) {

        if (typeof data.errors === 'string') {
          text = data.errors
        } 

        else if (typeof data.errors === 'object') {
          // Pega os valores do objeto errors (arrays ou strings)
          const errors = Object.values(data.errors)
          // Concatena todas mensagens, achata arrays se existirem
          text = errors
            .flatMap(val => (Array.isArray(val) ? val : [val]))
            .join(', ')
        }
      }
    } 
    // Se não tiver resposta ou corpo, tenta pegar mensagem do erro axios
    else if (typeof e.message === 'string') {
      text = e.message
    }
  } 
  // Se for erro padrão JS (Error), retorna mensagem
  else if (e instanceof Error) {
    text = e.message
  } 
  // Se for string pura, retorna direto
  else if (typeof e === 'string') {
    text = e
  }

  return text
}

/**
 * Helper para identificar se o erro é um AxiosError
 * @param e - erro qualquer
 * @returns boolean indicando se é um erro axios
 */
function isAxiosError(e: unknown): e is AxiosError {
  return (e as AxiosError).isAxiosError !== undefined
}
