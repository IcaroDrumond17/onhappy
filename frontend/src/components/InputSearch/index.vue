<script setup lang="ts">
import { CSSProperties } from 'vue'
import { Props } from './types'

const search = defineModel<string | number | undefined>()

const emits = defineEmits(['clear'])

const props = withDefaults(defineProps<Props>(), {
  className: 'input-search',
  padding: '0 16px',
  type: 'text',
  disabled: false,
  full: false,
})

const inputStyles = computed((): CSSProperties => {
  const styles: CSSProperties = {}

  styles.borderRadius = props.borderRadius || '15px'
  if (props.padding) styles.padding = props.padding
  if (props.width || props.full) styles.width = props.full ? '100%' : props.width
  if (props.minWidth) styles.minWidth = props.minWidth
  if (props.maxWidth) styles.maxWidth = props.maxWidth
  return styles
})

const clearSearch = (): void => {
  emits('clear')
  search.value = ''
}
</script>
<template>
  <div class="search">
    <input
      :id="id"
      v-model="search"
      :class="className"
      type="text"
      :placeholder="placeholder"
      :autocomplete="autocomplete"
      :disabled="disabled"
      :style="inputStyles"
    />
    <i v-show="!search" class="fa fa-search"></i>
    <i v-show="search" class="fa fa-close" @click="clearSearch"></i>
  </div>
</template>
<style lang="less" scoped>
@inputBackgroundColor: #f5f8fa;
@inputBackgroundDisabled: #ffffff96;
@inputColor: #33475b;
@inputIconcolor: #8c8c8c;
@placeholderColor: #8c8c8c;
@borderColor: #dadada;

.search {
  position: relative;

  input {
    height: 40px;
    display: initial;
    background-image: none;
    background-clip: padding-box;
    background-color: @inputBackgroundColor;
    border: 1px solid @borderColor;
    transition:
      border-color ease-in-out 0.15s,
      box-shadow ease-in-out 0.15s;

    font-size: 1rem; //16px
    line-height: 1.25rem; //20px
    font-weight: 400;
    color: @inputColor;
  }

  input::placeholder,
  :-ms-input-placeholder,
  ::-ms-input-placeholder {
    color: @placeholderColor;
  }

  input:disabled {
    cursor: default !important;
    opacity: 0.7 !important;
    background-color: @inputBackgroundDisabled !important;
  }

  input:focus {
    outline: 1px solid rgba(82, 168, 236, 0.8);
  }

  i {
    position: absolute;
    right: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: @inputIconcolor;
  }

  i.fa-close {
    cursor: pointer;
  }
}
</style>
