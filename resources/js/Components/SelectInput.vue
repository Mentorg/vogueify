<script setup>

defineProps({
  modelValue: [String, Number]
});

defineEmits(['update:modelValue']);

</script>

<template>
  <select :value="modelValue" @input="$emit('update:modelValue', $event.target.value)"
    class="mt-1 w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
    <slot />
  </select>
</template>
