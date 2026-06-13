<script setup>
import { useI18n } from 'vue-i18n';
import { Head } from '@inertiajs/vue3';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import InvoicesTable from '@Components/Tables/InvoicesTable.vue';

defineProps({
  invoices: Array,
});

const { t } = useI18n();

</script>

<template>

  <Head :title="t('page.admin.invoices')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.invoices') }}</h1>
    <div class="flex w-full gap-x-8 py-8">
      <InvoicesTable :invoices="invoices" />
    </div>
  </AdminDashboard>
</template>
