import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useResendOrderConfirmationEmail() {
  const { t } = useI18n();
  const toast = useToast();

  const orderConfirmationEmailTarget = ref(null);

  const isResendOrderConfirmationEmailModalOpen = computed(
    () => orderConfirmationEmailTarget.value !== null
  );

  const resendOrderConfirmationEmailForm = useForm({});

  const openResendOrderConfirmationEmailModal = (targetOrder) => {
    orderConfirmationEmailTarget.value = targetOrder;
  }

  const closeResendOrderConfirmationEmailModal = () => {
    orderConfirmationEmailTarget.value = null;
  }

  const resendOrderConfirmationEmail = () => {
    if (!orderConfirmationEmailTarget.value) return;

    resendOrderConfirmationEmailForm.post(route('orders.resendConfirmation', orderConfirmationEmailTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        closeResendOrderConfirmationEmailModal()
        toast.open({
          message: t('common.toast.order.admin.resendConfirmation.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.resendConfirmation.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    })
  }

  return {
    orderConfirmationEmailTarget,
    isResendOrderConfirmationEmailModalOpen,
    openResendOrderConfirmationEmailModal,
    closeResendOrderConfirmationEmailModal,
    resendOrderConfirmationEmail
  }
}
