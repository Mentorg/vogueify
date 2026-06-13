<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import OrdersTable from '@Components/Tables/OrdersTable.vue';

defineProps({
  orders: Array,
  orderStatuses: Array,
});

const { t } = useI18n();
</script>

<template>

  <Head :title="t('page.admin.orders')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.orders') }}</h1>
    <div class="flex w-full gap-x-8 py-8">
      <OrdersTable :orders="orders" :orderStatuses="orderStatuses" />
    </div>
  </AdminDashboard>
</template>
