<script setup>
import { Teleport } from 'vue';

defineProps({
  state: Boolean,
  entity: Object,
  style: Function
});
</script>

<template>
  <Teleport to="body">
    <div v-if="state === entity.id" :style="style"
      class="fixed z-50  px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 flex flex-col rounded-md mt-2">
      <slot />
    </div>
  </Teleport>
</template>
