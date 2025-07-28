<script setup lang="ts">
import { Props } from './types'
import { CSSProperties } from 'vue'

const props = withDefaults(defineProps<Props>(), {
  center: false,
  height: '50vh',
})

const loadingStyles = computed((): CSSProperties => {
  const styles: CSSProperties = {}

  styles.cursor = 'wait'
  if (props.center) styles.height = props.height

  return styles
})
</script>
<template>
  <div
    :class="{ 'onhappy-loading': !center, 'onhappy-loading-center': center }"
    :style="loadingStyles"
  >
    <i class="fa fa-spin fa-spinner"></i>
  </div>
</template>
<style lang="less" scoped>
.onhappy-loading {
  text-align: center;
}

.onhappy-loading-center {
  display: flex;
  justify-content: center;
  align-items: center;
}
</style>
