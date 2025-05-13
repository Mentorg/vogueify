<script setup>
defineProps({
  message: String
});
</script>

<template>
  <p v-if="message" class="font-medium text-red-600 text-sm mt-1">
    {{ message }}
  </p>
</template>
