import { computed, ref } from "vue";
import { router } from '@inertiajs/vue3';
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useCancelInvoice() {
  const { t } = useI18n();
  const toast = useToast();

  const cancelInvoiceTarget = ref(null);

  const isCancelInvoiceModalOpen = computed(
    () => cancelInvoiceTarget.value !== null
  );

  const openCancelInvoiceModal = (targetInvoice) => cancelInvoiceTarget.value = targetInvoice;

  const closeCancelInvoiceModal = () => cancelInvoiceTarget.value = null;

  const cancelInvoice = () => {
    if (!cancelInvoiceTarget.value) return;

    router.patch(route('admin.invoice.cancel', cancelInvoiceTarget.value), {}, {
      preserveScroll: true,
      onSuccess: () => {
        closeCancelInvoiceModal();
        toast.open({
          message: t('common.toast.invoice.invoiceCancel.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.invoice.invoiceCancel.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    });
  };

  return {
    cancelInvoiceTarget,
    isCancelInvoiceModalOpen,
    openCancelInvoiceModal,
    closeCancelInvoiceModal,
    cancelInvoice,
  }
}
