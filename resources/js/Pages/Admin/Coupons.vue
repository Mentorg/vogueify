<script setup>
import { ref } from 'vue';
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import CouponsTable from '@/Components/Tables/CouponsTable.vue';
import Modal from '@/Components/Modal.vue';
import CouponForm from '@/Components/CouponForm.vue';

const props = defineProps({
  coupons: Array,
  categories: Array,
  products: Array,
  productVariations: Array,
  users: Array,
});

const isCouponModalOpen = ref(null);
const { t } = useI18n();

const entities = {
  coupons: props.coupons,
  categories: props.categories,
  products: props.products,
  productVariations: props.productVariations,
  users: props.users,
};

const openCouponModal = () => {
  isCouponModalOpen.value = !isCouponModalOpen.value;
}

const closeCouponModal = () => {
  isCouponModalOpen.value = null;
}

</script>

<template>

  <Head :title="t('page.admin.coupons')" />
  <AdminDashboard>
    <div class="flex items-center justify-between">
      <h1 class="text-2xl font-medium">{{ t('page.admin.coupons') }}</h1>
      <button @click="openCouponModal" :title="t('common.button.createCoupon')"
        class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
        {{ t('common.button.createCoupon') }}
      </button>
    </div>
    <div class="flex w-full gap-x-8 py-8">
      <CouponsTable :entities="entities" :coupons="coupons" />
    </div>
    <Modal :show="isCouponModalOpen !== null" @close="closeCouponModal">
      <CouponForm :entities="entities" :coupon="coupons" :isCouponModalOpen="isCouponModalOpen" formType="create"
        :close="closeCouponModal" />
    </Modal>
  </AdminDashboard>
</template>
