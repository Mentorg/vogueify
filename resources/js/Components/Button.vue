<script setup>
import { computed } from 'vue';

const props = defineProps({
  type: {
    type: String,
    default: 'button'
  },
  loading: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

const classes = computed(() => [
  'bg-black border border-black text-white py-2 px-6 rounded-full transition-all',
  'hover:bg-white hover:text-black',
  'disabled:bg-slate-400 disabled:text-white disabled:border-slate-400 disabled:cursor-not-allowed',
  'flex items-center justify-center gap-2',

  props.fullWidth
    ? 'w-full'
    : 'w-auto'
])
</script>

<template>
  <button :type="type" :disabled="disabled || loading" :class="classes">
    <svg v-if="loading" class="animate-spin h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
    </svg>
    <span>
      <slot />
    </span>
  </button>
</template>
