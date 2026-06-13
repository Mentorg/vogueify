import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateCouponStatus() {
  const { t } = useI18n();
  const toast = useToast();

  const updateCouponStatusForm = useForm({ status: null });

  const updateCouponStatus = (coupon, status) => {
    updateCouponStatusForm.status = status

    updateCouponStatusForm.patch(route('coupon.updateStatus', coupon.id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.coupon.couponStatusUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.coupon.couponStatusUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      }
    })
  }

  return { updateCouponStatus }
}
