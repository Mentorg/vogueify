<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import UsersTable from '@/Components/UsersTable.vue';

const props = defineProps({
  users: Array
});
</script>

<template>

  <Head title="Users" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Users</h1>
    <div class="flex w-full h-screen gap-x-8 py-8">
      <UsersTable :users="props.users" />
    </div>
  </AdminDashboard>
</template>
