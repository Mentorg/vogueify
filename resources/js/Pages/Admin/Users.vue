<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import UsersTable from '@Components/Tables/UsersTable.vue';

defineProps({
  users: Array
});

const { t } = useI18n();
</script>

<template>

  <Head :title="t('page.admin.users')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.users') }}</h1>
    <div class="flex w-full gap-x-8 py-8">
      <UsersTable :users="users" />
    </div>
  </AdminDashboard>
</template>
