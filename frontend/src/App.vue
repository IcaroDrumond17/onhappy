<script setup lang="ts">
import { useLoading } from '@/composables/useLoading'
import { brCitiesService } from './services/brCitiesService'
import { useStoreStatisticalValues } from '@/stores/storeStatisticalValues'

const storeStatisticalValues = useStoreStatisticalValues();

const { isLoading } = useLoading()

async function loadCities() {
  try {
    const cities = await brCitiesService.fetchCities()

    storeStatisticalValues.setCities(cities)
  } catch {
    console.log('Error loading cities...')
  }
}

onMounted(() => {
  loadCities()
})
</script>

<template>
  <div>
    <NavigationBar />
    <LoadingOverlay :active="isLoading" :is-full-page="true" />

    <router-view v-slot="{ Component }">
      <transition name="fade" mode="out-in">
        <component :is="Component" />
      </transition>
    </router-view>
  </div>
</template>

<style lang="less" scoped></style>
