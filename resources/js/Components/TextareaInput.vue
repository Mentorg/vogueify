<script setup>
import { ref } from 'vue';

defineProps({
  modelValue: String,
});

defineEmits(['update:modelValue']);

const input = ref(null);

</script>

<template>
  <textarea ref="input"
    class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm resize-none"
    :value="modelValue" @input="$emit('update:modelValue', $event.target.value)" rows="10" resize="none"></textarea>
</template>
