<script setup>
import UserInvoicesTable from '@/Components/Tables/UserInvoicesTable.vue';

defineProps({
  user: Object,
  invoices: Array,
});

</script>

<template>
  <div class="flex flex-col gap-2 items-center justify-between py-6 md:gap-6 md:flex-row">
    <UserInvoicesTable :invoices="invoices" />
  </div>
</template>
