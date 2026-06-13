import { computed, ref } from "vue";
import { router } from '@inertiajs/vue3';
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useResendInvoice() {
  const { t } = useI18n();
  const toast = useToast();

  const resendInvoiceTarget = ref(null);

  const isResendInvoiceModalOpen = computed(
    () => resendInvoiceTarget.value !== null
  );

  const openResendInvoiceModal = (targetInvoice) => resendInvoiceTarget.value = targetInvoice;

  const closeResendInvoiceModal = () => resendInvoiceTarget.value = null;

  const resendInvoice = () => {
    if (!resendInvoiceTarget.value) return;

    router.post(route('admin.invoice.resend', resendInvoiceTarget.value), {}, {
      preserveScroll: true,
      onSuccess: () => {
        closeResendInvoiceModal();
        toast.open({
          message: t('common.toast.invoice.invoiceResend.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.invoice.invoiceResend.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    });
  };

  return {
    resendInvoiceTarget,
    isResendInvoiceModalOpen,
    openResendInvoiceModal,
    closeResendInvoiceModal,
    resendInvoice,
  }
}
