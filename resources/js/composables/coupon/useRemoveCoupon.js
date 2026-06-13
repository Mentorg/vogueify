import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useRemoveCoupon() {
  const { t } = useI18n();
  const toast = useToast();

  const removeCouponCodeForm = useForm({});

  const removeCouponCode = () => {
    removeCouponCodeForm.post(route('coupon.remove'), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.coupon.couponRemove.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        });
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.coupon.couponRemove.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        });
      }
    });
  }

  return { removeCouponCode }
}
