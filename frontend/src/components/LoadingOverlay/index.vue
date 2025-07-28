<script setup lang="ts">
import { useLoading } from '@/composables/useLoading'
import LoadingComponent from 'vue-loading-overlay'
import 'vue-loading-overlay/dist/css/index.css'

const { isLoading } = useLoading()
</script>

<template>
  <LoadingComponent
    :show="isLoading"
    :isFullPage="true"
    :canCancel="false"
    color="#000"
    loader="spinner"
    :width="64"
    :height="64"
  />
</template>
