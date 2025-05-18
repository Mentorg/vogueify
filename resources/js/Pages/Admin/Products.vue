<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ProductsTable from '@/Components/ProductsTable.vue';

const props = defineProps({
  variations: Array,
  categories: Array
});
</script>

<template>

  <Head title="Products" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Products</h1>
    <div class="flex w-full h-screen gap-x-8 py-8">
      <ProductsTable :variations="props.variations" :categories="props.categories" />
    </div>
  </AdminDashboard>
</template>
