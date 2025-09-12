import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";
import { capitalize } from "@/utils/capitalize";

export function useOrder() {
  const form = useForm({});
  const itemToCancel = ref(null);
  const errorMessage = ref(null);
  const toast = useToast();
  const {t} = useI18n();

  const formatStatus = (status) => {
    return status
      .split('-')
      .map(word => capitalize(word))
      .join(' ');
  }

  const activeStatuses = [
    'pending',
    'paid',
    'confirmed',
    'processing',
    'shipped',
    'in-transit',
    'out-for-delivery',
    'attempted-delivery',
    'awaiting-pickup',
    'delayed',
    'held-at-customs',
    'lost'
  ];

  const confirmOrderCancelation = (order) => {
    itemToCancel.value = order;
  }

  const closeModal = () => {
    itemToCancel.value = null;
    errorMessage.value = null;
  }

  const cancel = (id) => {
    form.patch(route('order.cancel', { order: id }), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: `${t('common.toast.order.user.orderCancelation.successMessage')}.`,
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeModal();
      },
      onError: (errors) => {
        toast.open({
          message: `${t('common.toast.order.user.orderCancelation.errorMessage')}! ` + errors.error,
          type: 'error',
          position: 'top',
          duration: 4000,
        });
        errorMessage.value = errors.error || `${t('common.toast.order.user.orderCancelation.errorMessage')}!`;
      }
    });
  };

  return {
    cancel,
    confirmOrderCancelation,
    closeModal,
    itemToCancel,
    errorMessage,
    formatStatus,
    activeStatuses
  }
}
