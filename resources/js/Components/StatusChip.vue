<script setup>
const props = defineProps({
  status: String,
  class: {
    type: String,
    default: ''
  }
});

</script>

<template>
  <div :class="[
    {
      'bg-yellow-100': status === 'pending',
      'bg-green-100': status === 'paid' || status === 'active',
      'bg-blue-100': status === 'confirmed',
      'bg-red-200': status === 'canceled' || status === 'archived',
      'bg-gray-200': status === 'processing' || status === 'inactive',
      'bg-sky-100': status === 'shipped',
      'bg-sky-200': status === 'in-transit',
      'bg-emerald-100': status === 'out-for-delivery',
      'bg-green-200': status === 'delivered',
      'bg-yellow-200': status === 'attempted-delivery',
      'bg-gray-300': status === 'awaiting-pickup',
      'bg-orange-200': status === 'delayed',
      'bg-amber-200': status === 'held-at-customs',
      'bg-rose-200': status === 'lost',
    },
    'rounded-full py-2 px-4 w-fit',
    props.class
  ]">
    <slot />
  </div>
</template>
