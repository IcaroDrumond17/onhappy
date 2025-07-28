<script lang="ts" setup>
import VueDatePicker from '@vuepic/vue-datepicker'

import '@vuepic/vue-datepicker/dist/main.css'
import { Props } from './types'
import { CSSProperties } from 'vue'

const date = defineModel<string | Date | undefined>()

const props = withDefaults(defineProps<Props>(), {
  padding: '0',
  disabled: false,
  square: true,
  full: false,
  error: false,
  range: false,
  monthPicker: false,
  timePicker: false,
  textInput: false,
  inline: false,
  enableTimePicker: false,
  timePickerInline: true,
  is24: true,
  enableMinutes: true,
  enableSeconds: true,
  autoApply: true,
  locale: 'pt-br',
  selectText: 'Salvar',
  cancelText: 'Cancelar',
  format: 'dd/MM/yyyy',
})

const className = computed((): string => {
  return [props.error && `input-error`, props.className && props.className]
    .filter(Boolean)
    .join(' ')
})

const inputStyles = computed((): CSSProperties => {
  const styles: CSSProperties = {}

  if (props.padding) styles.padding = props.padding
  if (props.width || props.full) styles.width = props.full ? '100%' : props.width
  if (props.minWidth) styles.minWidth = props.minWidth
  if (props.maxWidth) styles.maxWidth = props.maxWidth

  return styles
})
</script>

<template>
  <VueDatePicker
    :id="id"
    v-model="date"
    :class="className"
    :placeholder="placeholder"
    :range="range"
    :multiCalendars="multiCalendars"
    :monthPicker="monthPicker"
    :timePicker="timePicker"
    :textInput="textInput"
    :inline="inline"
    :timezone="timezone"
    :locale="locale"
    :enableTimePicker="enableTimePicker"
    :timePickerInline="timePickerInline"
    :is24="is24"
    :enableMinutes="enableMinutes"
    :enableSeconds="enableSeconds"
    :format="format"
    :selectText="selectText"
    :cancelText="cancelText"
    :position="position"
    :disabled="disabled"
    :auto-apply="autoApply"
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

.dp__main:deep(.dp__input_wrap) {
  input {
    height: 40px;
    display: initial;
    background-image: none;
    background-color: @inputBackgroundColor;
    border: 1px solid @borderColor;
    border-radius: 15px;
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
}

.input-error {
  border: solid 1px @inputError !important;

  &:focus {
    outline: 1px solid @inputError;
  }
}
</style>
