<script lang="ts" setup>
import { Props } from './types'
import { CSSProperties } from 'vue'

const props = withDefaults(defineProps<Props>(), {
  borderRadius: '15px',
  margin: '0',
  padding: '30px',
  className: 'card',
})

const cardStyles = computed((): CSSProperties => {
  const styles: CSSProperties = {}

  if (props.margin) styles.margin = props.margin
  if (props.padding) styles.padding = props.padding
  if (props.borderRadius) styles.borderRadius = props.borderRadius
  if (props.width) styles.width = props.width
  if (props.minWidth) styles.minWidth = props.minWidth
  if (props.maxWidth) styles.maxWidth = props.maxWidth

  return styles
})
</script>

<template>
  <div :class="className" :style="cardStyles">
    <slot name="default"></slot>
  </div>
</template>

<style lang="less" scoped>
.card{
  background-color: #fff;
}
</style>
