import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateOrderStatus() {
  const { t } = useI18n();
  const toast = useToast();

  const orderStatusTarget = ref(null);

  const isUpdateOrderStatusModalOpen = computed(
    () => orderStatusTarget.value !== null
  );

  const updateOrderStatusForm = useForm({
    order_status: null,
  });

  const openOrderStatusModal = (targetOrder) => {
    orderStatusTarget.value = targetOrder;

    updateOrderStatusForm.order_status = targetOrder.order_status;

    updateOrderStatusForm.clearErrors();
  };

  const closeOrderStatusModal = () => {
    orderStatusTarget.value = null;

    updateOrderStatusForm.clearErrors();
  };

  const updateOrderStatus = () => {
    if (!orderStatusTarget.value) return;

    updateOrderStatusForm.patch(route('orders.updateStatus', orderStatusTarget.value.id), {
      preserveScroll: true,
      onSuccess: () => {
        orderStatusTarget.value.order_status = updateOrderStatusForm.order_status;
        closeOrderStatusModal();
        toast.open({
          message: t('common.toast.order.admin.orderStatusUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderStatusUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    })
  };

  return {
    orderStatusTarget,
    isUpdateOrderStatusModalOpen,
    updateOrderStatusForm,
    openOrderStatusModal,
    closeOrderStatusModal,
    updateOrderStatus
  }
}
