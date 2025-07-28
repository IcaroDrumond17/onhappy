<script lang="ts" setup>
import { Props } from './types.js'

defineProps<Props>()
</script>

<template>
  <small v-show="message">
    <i class="fa fa-info-circle" aria-hidden="true"></i>
    {{ message }}
  </small>
</template>

<style lang="less" scoped>
small {
  display: flex;
  align-items: baseline;
  gap: 6px;

  font-size: 0.75rem; //12px
  line-height: 1rem; //16px
  font-weight: 300;
  color: #fc4b6c;
}
</style>
