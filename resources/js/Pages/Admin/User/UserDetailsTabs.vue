<script setup>
import { useI18n } from 'vue-i18n';

defineProps({
  navigate: Function,
  activeTab: String,
});

const { t } = useI18n();

</script>

<template>
  <ul class="flex bg-slate-100 w-fit my-4 gap-4 py-2 px-3 rounded-md">
    <li>
      <button @click="navigate('personal-information')" :title="t('page.userDetails.personalInformationTitle')"
        class="py-2 px-4 text-sm font-medium rounded-md transition-all" :class="{
          'bg-white': activeTab === 'personal-information',
          'hover:bg-white': activeTab !== 'personal-information',
        }">
        {{ t('page.userDetails.personalInformation') }}
      </button>
    </li>
    <li>
      <button @click="navigate('orders')" :title="t('page.userDetails.ordersTitle')"
        class="py-2 px-4 text-sm font-medium rounded-md transition-all" :class="{
          'bg-white': activeTab === 'orders',
          'hover:bg-white': activeTab !== 'orders',
        }">
        {{ t('page.admin.orders') }}
      </button>
    </li>
    <li>
      <button @click="navigate('invoices')" :title="t('page.userDetails.invoicesTitle')"
        class="py-2 px-4 text-sm font-medium rounded-md transition-all" :class="{
          'bg-white': activeTab === 'invoices',
          'hover:bg-white': activeTab !== 'invoices',
        }">
        {{ t('page.admin.invoices') }}
      </button>
    </li>
    <li>
      <button @click="navigate('coupons')" :title="t('page.userDetails.couponsTitle')"
        class="py-2 px-4 text-sm font-medium rounded-md transition-all" :class="{
          'bg-white': activeTab === 'coupons',
          'hover:bg-white': activeTab !== 'coupons',
        }">
        {{ t('page.admin.coupons') }}
      </button>
    </li>
  </ul>
</template>
