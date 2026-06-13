<script setup>
const emit = defineEmits(['update:checked']);

const props = defineProps({
  modelValue: {
    type: [Array, Boolean],
    default: false,
  },
  name: {
    type: String,
    required: true
  },
  id: {
    type: String,
    required: true
  },
  value: {
    type: String,
    default: null,
  },
});

</script>


<template>
  <input :name="name" :id="id" type="checkbox" :checked="modelValue" :value="value"
    @change="$emit('update:modelValue', $event.target.checked)"
    class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500">
</template>
