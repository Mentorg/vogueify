<script setup>
import { computed } from 'vue'

const props = defineProps({
  href: String,
  action: Function,
  title: String,
  disabled: Boolean,
  as: Object
})

const component = computed(() => {
  if (props.as) return props.as
  if (props.href) return 'a'
  return 'button'
})
</script>

<template>
  <component :is="component" :href="component === 'a' ? href : undefined" :onClick="!href ? action : undefined"
    :title="title" :disabled="component === 'button' ? disabled : undefined"
    class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
    <slot />
  </component>
</template>
