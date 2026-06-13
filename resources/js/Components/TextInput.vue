<script setup>

const props = defineProps({
  modelValue: [String, Number],
  type: {
    type: String,
    default: 'text'
  },
  name: {
    type: String,
    required: true
  },
  id: {
    type: String,
    required: true
  },
});
const emit = defineEmits(['update:modelValue']);

const updateValue = (e) => {
  const value = e.target.value;
  const parsed = props.type === 'number' ? Number(value) : value;
  emit('update:modelValue', parsed);
};
</script>

<template>
  <input :name="name" :id="id" ref="input" :type="type"
    class="mt-1 border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm w-full"
    :value="modelValue" @input="updateValue" />
</template>
