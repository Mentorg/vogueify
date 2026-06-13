import { computed, ref } from "vue";
import { router } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useSendCouponNotification() {
  const { t } = useI18n();
  const toast = useToast();

  const sendCouponNotificationTarget = ref(null);

  const isSendCouponNotificationModalOpen = computed(
    () => sendCouponNotificationTarget.value !== null
  );

  const openSendCouponNotificationModal = (targetCoupon) => {
    sendCouponNotificationTarget.value = targetCoupon;
  }

  const closeSendCouponNotificationModal = () => {
    sendCouponNotificationTarget.value = null;
  }

  const sendCouponNotification = () => {
    if (!sendCouponNotificationTarget.value) return;

    router.post(route('coupon.sendUserNotifications', sendCouponNotificationTarget.value), {}, {
      preserveScroll: true,
      onSuccess: () => {
        closeSendCouponNotificationModal();
        toast.open({
          message: t('common.toast.coupon.couponNotifyUsers.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.coupon.couponNotifyUsers.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    });
  };

  return {
    sendCouponNotificationTarget,
    isSendCouponNotificationModalOpen,
    openSendCouponNotificationModal,
    closeSendCouponNotificationModal,
    sendCouponNotification
  }
}
