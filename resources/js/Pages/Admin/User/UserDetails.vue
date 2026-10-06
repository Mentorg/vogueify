<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import UserDetailsTabs from '@/Pages/Admin/User/UserDetailsTabs.vue';
import UserPersonalInformation from '@/Pages/Admin/User/UserPersonalInformation.vue';
import UserOrders from '@/Pages/Admin/User/UserOrders.vue';
import UserInvoices from '@/Pages/Admin/User/UserInvoices.vue';
import UserCoupons from '@/Pages/Admin/User/UserCoupons.vue';
import { useNavigateUser } from '@/composables/user/useNavigateUser.js';


defineProps({
  user: Object,
  orders: Object,
  invoices: Object,
  coupons: Object,
  orderStatuses: Array,
});

const { t } = useI18n();

const {
  activeTab,
  navigate
} = useNavigateUser();

</script>

<template>

  <Head :title="t('page.admin.userDetails')" />
  <AdminDashboard>
    <div>
      <UserDetailsTabs :navigate="navigate" :active-tab="activeTab" />
    </div>
    <div>
      <UserPersonalInformation v-if="activeTab === 'personal-information'" :user="user" />
      <UserOrders v-else-if="activeTab === 'orders'" :user="user" :orders="orders" :order-statuses="orderStatuses" />
      <UserInvoices v-else-if="activeTab === 'invoices'" :user="user" :invoices="invoices" />
      <UserCoupons v-else-if="activeTab === 'coupons'" :user="user" :coupons="coupons" />
    </div>
  </AdminDashboard>
</template>
