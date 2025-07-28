<script setup lang="ts" generic="Option, Reduce, Label, Key, Create, Dropdown, Args">
import { CSSProperties } from 'vue'
import { debounce } from 'lodash'
import vSelect from 'vue-select'
import 'vue-select/dist/vue-select.css'

import { SelectProps } from './types'

const emits = defineEmits(['search'])

const selected = defineModel<Option | Option[]>({ required: true })

const props = withDefaults(
  defineProps<SelectProps<Option, Reduce, Label, Key, Create, Dropdown, Args>>(),
  {
    id: Math.floor(Math.random() * 1000).toString(),
    options: () => [],
    noOptionsTitle: 'Nenhuma opção correspondente cadastrada',
    allOptionsSelected: 'Todas as opções foram selecionadas',
    disabled: false,
    multiple: false,
    clearable: true,
    taggable: false,
    loading: false,
    filterable: true,
    full: false,
    closeOnSelect: true,
    deselectFromDropdown: false,
    pushTags: false,
    searchable: true,
    nowrap: false,
    noAllSelect: false,
    hideLabelOpenSelect: false,
  },
)

const searchSelectedTemp = ref<string>('')

const isMultiple = computed((): boolean => props.multiple)

const clearable = computed(() => !isMultiple.value && props.clearable)

const options = computed((): Option[] => {
  if (isMultiple.value && !props.noAllSelect && Array.isArray(selected.value)) {
    const selectedArray = selected.value as Option[]
    return props.options.filter((opt) => !selectedArray.includes(opt))
  }
  return props.options
})

const noOptions = computed((): string => {
  if (
    isMultiple.value &&
    !props.noAllSelect &&
    Array.isArray(selected.value) &&
    selected.value.length > 0 &&
    options.value.length === 0
  ) {
    return props.allOptionsSelected
  }
  return props.noOptionsTitle
})

const selectStyles = computed(
  (): CSSProperties => ({
    width: props.full ? '100%' : props.width,
    minWidth: props.full ? '100%' : props.minWidth,
    maxWidth: props.maxWidth,
  }),
)

const className = computed(() =>
  [
    props.className,
    props.disabled && 'select-disabled',
    props.error && 'select-error',
    props.nowrap && 'select-nowrap',
    props.hideLabelOpenSelect && 'hide-label',
  ]
    .filter(Boolean)
    .join(' '),
)

const onSearch = debounce((searchText: string) => {
  searchSelectedTemp.value = searchText
  if (searchText) {
    emits('search', searchText || '')
  }
}, 300)
</script>
<template>
  <v-select
    :id="id"
    v-model="selected"
    :value="selected"
    :options="options"
    :reduce="reduce"
    :disabled="disabled"
    :multiple="multiple"
    :clearable="clearable"
    :taggable="taggable"
    :loading="loading"
    :searchable="searchable"
    :filterable="filterable"
    :open-on-focus="focus"
    :label="label"
    :placeholder="placeholder"
    :get-option-key="getOptionKey"
    :get-option-label="getOptionLabel"
    :create-option="createOption"
    :dropdown-should-open="dropdownShouldOpen"
    :close-on-select="closeOnSelect"
    :deselect-from-dropdown="deselectFromDropdown"
    :push-tags="pushTags"
    :class="className"
    class="select-items"
    :style="selectStyles"
    @search="onSearch"
  >
    <template #no-options>
      {{ noOptions }}
    </template>
  </v-select>
</template>

<style lang="less" scoped>
@inputBackgroundColor: #f5f8fa;
@inputBackgroundColorOrange: #ff6b00;
@inputBackgroundDisabled: #ffffff96;
@inputColor: #33475b;
@inputColorHighlightTextColor: #ffffff;
@inputColor: #33475b;
@inputError: #fc4b6c;
@placeholderColor: #8c8c8c;
@borderColor: #dadada;

.select-items {
  background-color: @inputBackgroundColor;
}

.hide-label.vs--open:deep(.vs__selected) {
  display: none !important;
}

.select-items.vs--open:deep(.vs__dropdown-menu) {
  margin-top: 8px;
  padding: 8px;
  border-radius: 15px;
  background-color: @inputBackgroundColor;
  box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.08);

  .vs__dropdown-option {
    padding: 8px 16px;
    border-radius: 15px;
    color: #333333;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: 18px;
    letter-spacing: 0.3px;

    &--highlight {
      background-color: @inputBackgroundColorOrange;
      color: @inputColorHighlightTextColor;
    }
  }

  &::-webkit-scrollbar {
    width: 4px;
    height: 32px;
  }

  &::-webkit-scrollbar-track {
    background: @inputBackgroundColorOrange;
  }

  &::-webkit-scrollbar-thumb {
    border-radius: 100px;
    width: 4px;
    height: 32px;
    background: @inputBackgroundColorOrange;
  }
}

.select-items.vs--multiple:deep(.vs__dropdown-toggle) {
  .vs__selected-options {
    display: flex;
    align-items: center;
    gap: 4px;

    .vs__selected {
      margin: 0 !important;
      padding: 4px 8px !important;

      display: flex;
      flex-direction: row;
      align-items: center;
      justify-content: space-between;

      background: @inputBackgroundColor;
      font-size: 14px;
      font-style: normal;
      font-weight: 400;
      line-height: 18px;
      letter-spacing: 0.3px;
      color: @inputBackgroundColorOrange;
      border: 1px solid @inputBackgroundColorOrange;
      border-radius: 15px;
      gap: 4px;

      button {
        svg {
          fill: @inputBackgroundColorOrange;
        }
      }
    }
  }

  @supports selector(:has(*)) {
    .vs__selected-options:has(> .vs__selected) {
      padding: 8px 0;

      input {
        min-height: auto;
      }
    }
  }
}

.select-items:deep(.vs__search) {
  margin: 0 !important;
  padding: 0 2px !important;
  line-height: unset !important;
}

.select-items:deep(.vs__dropdown-toggle) {
  display: flex;
  align-items: center;
  justify-content: space-between;
  min-height: 36px;
  border: 1px solid @borderColor;
  border-radius: 15px;
  padding: 0 16px;

  .vs__selected-options {
    min-height: 36px;
    margin: 0;
  }

  .vs__selected {
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0;
    margin: 0;
  }

  .vs__actions {
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 4px;
    padding: 0;
    margin: 0;
  }

  .vs__selected,
  input {
    color: @inputColor;
    font-size: 14px;
    font-style: normal;
    font-weight: 400;
    line-height: 18px;
    letter-spacing: 0.3px;
  }

  input {
    min-height: 36px;
  }

  input::placeholder {
    color: @placeholderColor;
  }
}

.select-items.select-error:deep(.vs__dropdown-toggle) {
  border-color: @inputError;
}

.select-items.select-disabled:deep(.vs__dropdown-toggle) {
  border-color: @borderColor;
}

.select-items.select-disabled {
  background-color: @inputBackgroundDisabled;
  pointer-events: none;
}

.select-items.select-nowrap :deep(.vs__selected-options) {
  flex-wrap: nowrap !important;
  white-space: nowrap !important;
  overflow: hidden !important;
  text-overflow: ellipsis !important;
}

.select-items :deep(.vs__selected-options) {
  display: flex;
  align-items: center;
}
</style>
