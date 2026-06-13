<script setup>
defineProps({
  modelValue: {
    type: [String, Number, Boolean],
    default: null
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
    required: true
  }
})
defineEmits([
  'update:modelValue'
])
</script>

<template>
  <input :name="name" :id="id" type="radio" :value="value" :checked="modelValue === value"
    @change="$emit('update:modelValue', value)" />
</template>
