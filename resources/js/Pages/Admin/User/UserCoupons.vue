<script setup>
import { provide } from 'vue';
import UserCouponsTable from '@/Components/Tables/UserCouponsTable.vue';
import { useUpsertCoupon } from '@/composables/coupon/useUpsertCoupon';

defineProps({
  user: Object,
  coupons: Array,
});

const couponManager = useUpsertCoupon();

provide('couponManager', couponManager);

</script>

<template>
  <div class="flex flex-col gap-2 items-center justify-between py-6 md:gap-6 md:flex-row">
    <UserCouponsTable :coupons="coupons" />
  </div>
</template>
