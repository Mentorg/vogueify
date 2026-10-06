<script setup>
import UserOrdersTable from '@/Components/Tables/UserOrdersTable.vue';

defineProps({
  user: Object,
  orders: Array,
  orderStatuses: Array,
});

</script>

<template>
  <div class="flex flex-col gap-2 items-center justify-between py-6 md:gap-6 md:flex-row">
    <UserOrdersTable :orders="orders" :order-statuses="orderStatuses" />
  </div>
</template>
