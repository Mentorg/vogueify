<script setup>
import Button from '@Components/Button.vue';

defineProps({
  processing: {
    type: Boolean,
    default: false
  },
  disabled: {
    type: Boolean,
    default: false,
  },
  idleText: {
    type: String,
    required: true
  },
  loadingText: {
    type: String,
    required: true
  },
  fullWidth: {
    type: Boolean,
    default: false
  }
})

</script>

<template>
  <Button type="submit" :loading="processing" :disabled="disabled" :full-width="fullWidth">
    {{ processing ? loadingText : idleText }}
  </Button>
</template>
