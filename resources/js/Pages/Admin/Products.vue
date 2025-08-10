<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ProductsTable from '@/Components/ProductsTable.vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  variations: Array,
  categories: Array
});

const { t } = useI18n();
</script>

<template>

  <Head :title="t('page.admin.products')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.products') }}</h1>
    <div class="flex w-full h-screen gap-x-8 py-8">
      <ProductsTable :variations="props.variations" :categories="props.categories" />
    </div>
  </AdminDashboard>
</template>
