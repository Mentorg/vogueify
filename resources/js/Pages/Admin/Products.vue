<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ProductsTable from '@Components/Tables/ProductsTable.vue';

defineProps({
  variations: Array,
  categories: Array
});

const { t } = useI18n();

</script>

<template>

  <Head :title="t('page.admin.products')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.products') }}</h1>
    <div class="flex w-full gap-x-8 py-8">
      <ProductsTable :variations="variations" :categories="categories" />
    </div>
  </AdminDashboard>
</template>
