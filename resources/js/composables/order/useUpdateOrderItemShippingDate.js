import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";
import { useContextMenu } from "@/composables/useContextMenu.js";

export function useUpdateOrderItemShippingDate() {
  const { t } = useI18n();
  const toast = useToast();

  const { toggleContextMenu } = useContextMenu();

  const shippingDateUpdateTarget = ref(null);

  const isUpdateOrderItemShippingDateModalOpen = computed(() => shippingDateUpdateTarget.value !== null);

  const updateOrderItemShippingDateForm = useForm({
    shipping_date: null,
  });

  const openUpdateOrderItemShippingDateModal = (selected) => {
    shippingDateUpdateTarget.value = selected;

    updateOrderItemShippingDateForm.shipping_date = selected.shipping_date;

    toggleContextMenu(selected.id);
  }

  const closeUpdateOrderItemShippingDateModal = () => {
    shippingDateUpdateTarget.value = null;

    updateOrderItemShippingDateForm.reset();
    updateOrderItemShippingDateForm.clearErrors();
  }

  const updateOrderItemShippingDate = () => {
    if (!shippingDateUpdateTarget.value) return;

    updateOrderItemShippingDateForm.patch(route('orderItems.updateShippingDate', shippingDateUpdateTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        shippingDateUpdateTarget.value.shipping_date = updateOrderItemShippingDateForm.shipping_date;
        closeUpdateOrderItemShippingDateModal();
        toast.open({
          message: t('common.toast.order.admin.orderItemShippingDateUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderItemShippingDateUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      }
    });
  };

  return {
    shippingDateUpdateTarget,
    isUpdateOrderItemShippingDateModalOpen,
    updateOrderItemShippingDateForm,
    openUpdateOrderItemShippingDateModal,
    closeUpdateOrderItemShippingDateModal,
    updateOrderItemShippingDate
  }
}
