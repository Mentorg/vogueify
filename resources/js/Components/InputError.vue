<script setup>
defineProps({
  message: String,
});
</script>

<template>
  <div v-show="message">
    <p class="text-sm text-red-600 font-medium">
      {{ message }}
    </p>
  </div>
</template>
