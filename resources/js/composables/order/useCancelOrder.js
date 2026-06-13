import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useCancelOrder() {
  const { t } = useI18n();
  const toast = useToast();

  const cancelOrderTarget = ref(null);

  const isCancelOrderModalOpen = computed(() => cancelOrderTarget.value !== null);

  const cancelOrderForm = useForm({});

  const openCancelOrderModal = (targetOrder) => cancelOrderTarget.value = targetOrder;

  const closeCancelOrderModal = () => {
    cancelOrderTarget.value = null;
  };

  const cancelOrder = () => {
    if (!cancelOrderTarget.value) return;

    cancelOrderForm.patch(route('order.cancel', {
        order: cancelOrderTarget.value.id,
      }), {
      preserveScroll: true,
      onSuccess: () => {
        closeCancelOrderModal();
        toast.open({
          message: t('common.toast.order.user.orderCancelation.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.user.orderCancelation.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      }
    });
  };

  return {
    cancelOrderTarget,
    isCancelOrderModalOpen,
    openCancelOrderModal,
    closeCancelOrderModal,
    cancelOrder,
  }
}
