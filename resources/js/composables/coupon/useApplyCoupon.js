import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useApplyCoupon() {
  const { t } = useI18n();
  const toast = useToast();

  const isApplyCouponModalOpen = ref(false);

  const applyCouponForm = useForm({
    code: '',
  });

  const openApplyCouponModal = (coupon) => {
    applyCouponForm.code = coupon;
    applyCouponForm.clearErrors();

    isApplyCouponModalOpen.value = true;
  };

  const closeApplyCouponModal = () => {
    isApplyCouponModalOpen.value = false;

    applyCouponForm.clearErrors();
  }

  const applyCoupon = () => {
    applyCouponForm.post(route('coupon.apply'), {
      preserveScroll: true,
      onSuccess: () => {
        closeApplyCouponModal();
        toast.open({
          message: t('common.toast.coupon.couponApply.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.coupon.couponApply.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      }
    })
  }

  return {
    isApplyCouponModalOpen,
    applyCouponForm,
    openApplyCouponModal,
    closeApplyCouponModal,
    applyCoupon
  }
}
