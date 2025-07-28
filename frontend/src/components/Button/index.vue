<script setup lang="ts">
import { CSSProperties } from "vue";
import { Props } from "./types.js";

const props = withDefaults(defineProps<Props>(), {
  typeButton: "button",
  variant: "orange",
  size: "lg",
  format: "pill",
  loading: false,
  disabled: false,
  full: false,
  method: () => {},
});

const classButton = computed((): string => {
  return [
    "onhappy-button",
    props.variant && `onhappy-button-${props.variant}`,
    props.size && `onhappy-button-${props.size}`,
    props.format && `onhappy-button-${props.format}`,
    props.className && props.className,
  ]
    .filter(Boolean)
    .join(" ");
});

const classLabelButton = computed((): string => {
  if (props.variant) return `onhappy-button-${props.variant}__label`;
  else return "onhappy-button-orange__label";
});

const classLabelButtonIcon = computed((): string => {
  if (props.variant) return `onhappy-button-${props.variant}__label-icon`;
  else return "onhappy-button-orange__label-icon";
});

const buttonStyles = computed((): CSSProperties => {
  const styles: CSSProperties = {};

  if (props.width || props.full)
    styles.width = props.full ? "100%" : props.width;
  if (props.minWidth) styles.minWidth = props.minWidth;

  return styles;
});
</script>
<template>
  <button
    :id="id"
    :class="classButton"
    :disabled="disabled || loading"
    :type="typeButton"
    :style="buttonStyles"
  >
    <Loading v-if="loading" class="loading" />
    <div v-else :class="classLabelButton">
      <span v-if="$slots['prefix-icon']" :class="classLabelButtonIcon">
        <slot name="prefix-icon"></slot>
      </span>
      <span :style="{ margin: marginLabel }"> {{ label }} </span>
      <span v-if="$slots['suffix-icon']" :class="classLabelButtonIcon">
        <slot name="suffix-icon"></slot>
      </span>
    </div>
  </button>
</template>
<style lang="less" scoped>
@import "@/assets/buttons/index.less";
</style>