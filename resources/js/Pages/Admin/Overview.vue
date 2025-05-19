<script setup>
import { defineProps } from 'vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ProductsTable from '@/Components/ProductsTable.vue';
import UsersTable from '@/Components/UsersTable.vue';
import { Head } from '@inertiajs/vue3';

defineProps({
  variations: Array,
  categories: Array,
  users: Object
})
</script>

<template>

  <Head title="Overview" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Overview</h1>
    <div class="grid grid-cols-1 gap-6 py-8 md:gap-12 lg:grid-cols-2">
      <div>
        <h2 class="text-lg font-medium my-4">Products</h2>
        <ProductsTable :variations="variations" :categories="categories" />
      </div>
      <div>
        <h2 class="text-lg font-medium my-4">Users</h2>
        <UsersTable :users="users" />
      </div>
    </div>
  </AdminDashboard>
</template>
