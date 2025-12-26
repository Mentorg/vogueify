<script setup>
import { defineProps } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import UsersTable from '@/Components/Tables/UsersTable.vue';

const props = defineProps({
  users: Array
});

const { t } = useI18n();
</script>

<template>

  <Head :title="t('page.admin.users')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.users') }}</h1>
    <div class="flex w-full h-screen gap-x-8 py-8">
      <UsersTable :users="props.users" />
    </div>
  </AdminDashboard>
</template>
