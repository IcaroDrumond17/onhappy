type City = {
  id: number
  nome: string
  microrregiao: any
}

class BrCitiesService {
  private cities: City[] | null = null

  // Busca as cidades da API e armazena localmente
  async fetchCities(): Promise<City[]> {
    if (this.cities) {
      return this.cities
    }

    try {
      const response = await fetch('https://servicodados.ibge.gov.br/api/v1/localidades/municipios')
      if (!response.ok) throw new Error('Erro ao buscar cidades do IBGE')

      this.cities = await response.json()
      return this.cities || []
    } catch (error) {
      console.error(error)
      throw error
    }
  }

  // Retorna as cidades armazenadas (ou null se não buscou ainda)
  getCities(): City[] | null {
    return this.cities || []
  }
}

export const brCitiesService = new BrCitiesService()
