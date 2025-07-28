<script lang="ts" setup>
import { CSSProperties } from 'vue'
import { Props } from './types'

const text = defineModel<string | number | undefined>()

const props = withDefaults(defineProps<Props>(), {
  className: 'input-form',
  padding: '0 8px',
  type: 'text',
  disabled: false,
  full: false,
  error: false,
})

const className = computed((): string => {
  return [props.error && `input-error`, props.className && props.className]
    .filter(Boolean)
    .join(' ')
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
</script>

<template>
  <input
    :id="id"
    v-model="text"
    :type="type"
    :class="className"
    :placeholder="placeholder"
    :autocomplete="autocomplete"
    :disabled="disabled"
    :style="inputStyles"
  />
</template>

<style lang="less" scoped>
@inputBackgroundColor: #f5f8fa;
@inputBackgroundDisabled: #ffffff96;
@inputColor: #33475b;
@inputError: #fc4b6c;
@placeholderColor: #8c8c8c;
@borderColor: #dadada;

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

.input-error {
  border: solid 1px @inputError !important;

  &:focus {
    outline: 1px solid @inputError;
  }
}
</style>
