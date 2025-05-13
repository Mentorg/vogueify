<script setup>
import { PhInfo } from '@phosphor-icons/vue';

defineProps({
  message: String
})
</script>

<template>
  <div class="relative group">
    <PhInfo :size="16" class="cursor-pointer text-gray-600" />
    <div
      class="absolute left-full ml-2 top-1/2 -translate-y-1/2 w-max max-w-64 p-2 text-sm text-white bg-black rounded-md shadow-md opacity-0 group-hover:opacity-100 transition-opacity z-10">
      {{ message }}
    </div>
  </div>
</template>
