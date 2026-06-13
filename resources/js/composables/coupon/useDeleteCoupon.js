import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useDeleteCoupon() {
  const { t } = useI18n();
  const toast = useToast();

  const deleteCouponTarget = ref(null);

  const isDeleteCouponModalOpen = computed(
    () => deleteCouponTarget.value !== null
  );

  const deleteCouponForm = useForm({});

  const openDeleteCouponModal = (targetCoupon) => deleteCouponTarget.value = targetCoupon;

  const closeDeleteCouponModal = () => deleteCouponTarget.value = null;

  const deleteCoupon = () => {
    if (!deleteCouponTarget.value) return;

    deleteCouponForm.delete(route('coupon.delete', deleteCouponTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeDeleteCouponModal();
        toast.open({
          message: t('common.toast.coupon.couponDelete.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.coupon.couponDelete.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    })
  }

  return {
    deleteCouponTarget,
    isDeleteCouponModalOpen,
    openDeleteCouponModal,
    closeDeleteCouponModal,
    deleteCoupon,
  }
}
