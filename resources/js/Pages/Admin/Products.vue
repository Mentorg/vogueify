<script setup>
import { defineProps } from 'vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ProductsTable from '@/Components/ProductsTable.vue';

const props = defineProps({
  products: Array
});
</script>

<template>
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Products</h1>
    <div class="flex w-full h-screen gap-x-8 py-8">
      <ProductsTable :products="props.products" />
    </div>
  </AdminDashboard>
</template>
