<script setup lang="ts">
import { Props } from './types'
const emit = defineEmits(['close'])

const props = withDefaults(defineProps<Props>(), {
  showSideBarFilter: false,
  noCloseOnBackdrop: false,
  hideClose: false,
  width: '400px',
  title: 'Filtros de Pesquisa',
})

const closeModal = () => emit('close')

const clickingAway = () => {
  if (!props.noCloseOnBackdrop) emit('close')
}
</script>

<template>
  <div
    class="side-bar-filter"
    :class="{
      show: showSideBarFilter,
      hide: !showSideBarFilter,
    }"
  >
    <div class="side-bar-body-filter" @click.self="clickingAway">
      <div class="side-filter" :style="{ width: width }">
        <div class="header">
          <div v-if="title" class="header-title">
            {{ title }}
          </div>
          <div v-if="!hideClose" class="header-close">
            <button type="button" aria-label="Close" class="button-close" @click="closeModal">
              ×
            </button>
          </div>
        </div>
        <div class="body">
          <slot name="content"></slot>
        </div>
      </div>
    </div>
  </div>
</template>

<style lang="less" scoped>
.side-bar-filter {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99999;
  background: rgba(60, 60, 60, 0.5);
  overflow: visible;
  -webkit-transition: all 4s;
  -moz-transition: all 4s;
  -ms-transition: all 4s;
  -o-transition: all 4s;
  transition: all 4s;

  .side-bar-body-filter {
    width: 100%;
    max-width: 100%;
    height: 100vh;
    max-height: 100%;
    .side-filter {
      max-width: 100%;
      height: 100vh;
      max-height: 100%;
      margin: 0;
      outline: 0;
      padding: 1rem 1.5rem;
      -webkit-transform: translateX(0);
      transform: translateX(0);
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      right: 0;
      background-color: #ffffff;
      border-radius: 24px 0px 0px 0;
      overflow-x: hidden;
      overflow-y: auto;
      animation: open-sidebar 0.5s;

      .header {
        display: flex;
        flex-direction: row;
        flex-wrap: nowrap;
        justify-content: space-between;
        align-items: center;

        .header-title {
          font-weight: 600;
          font-size: 1.25rem;
          line-height: 2rem;
          color: #616161;
        }

        .button-close {
          float: right;
          border: none;
          background: transparent;
          font-size: 1.5rem;
          font-weight: 700;
          line-height: 1;
          color: #000;
          text-shadow: 0 1px 0 #fff;
          opacity: 0.5;
          cursor: pointer;

          &:focus {
            outline-style: none;
          }
        }
      }

      @keyframes open-sidebar {
        from {
          transform: translateX(100%);
        }
        to {
          transform: translateX(0);
        }
      }
    }
  }
}

.show {
  display: block;
  transition-timing-function: ease-in;
}

.hide {
  display: none;
  transition-timing-function: ease-out;
}
</style>
