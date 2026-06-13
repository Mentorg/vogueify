import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";
import { useContextMenu } from "@/composables/useContextMenu.js";

export function useUpdateOrderItemStatus() {
  const { t } = useI18n();
  const toast = useToast();

  const { toggleContextMenu } = useContextMenu();

  const statusUpdateTarget = ref(null);

  const isUpdateOrderItemStatusModalOpen = computed(() => statusUpdateTarget.value !== null);

  const updateOrderItemStatusForm = useForm({
    order_status: null,
  });

  const openUpdateOrderItemStatusModal = (targetItem) => {
    statusUpdateTarget.value = targetItem;

    updateOrderItemStatusForm.order_status = targetItem.order_status;

    toggleContextMenu(targetItem.id);
  };

  const closeUpdateOrderItemStatusModal = () => {
    statusUpdateTarget.value = null;

    updateOrderItemStatusForm.reset();
    updateOrderItemStatusForm.clearErrors();
  };

  const updateOrderItemStatus = () => {
    if (!statusUpdateTarget.value) return;

    updateOrderItemStatusForm.patch(route('orderItems.updateStatus', statusUpdateTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        statusUpdateTarget.value.order_status = updateOrderItemStatusForm.order_status;
        closeUpdateOrderItemStatusModal();
        toast.open({
          message: t('common.toast.order.admin.orderItemStatusUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderItemStatusUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    });
  };

  return {
    statusUpdateTarget,
    isUpdateOrderItemStatusModalOpen,
    updateOrderItemStatusForm,
    openUpdateOrderItemStatusModal,
    closeUpdateOrderItemStatusModal,
    updateOrderItemStatus,
  };
}
