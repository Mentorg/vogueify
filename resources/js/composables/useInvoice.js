import { onBeforeUnmount, onMounted, ref, watch } from "vue";
import { router, useForm } from '@inertiajs/vue3';
import { useToast } from "vue-toast-notification";
import { useI18n } from "vue-i18n";

export function useInvoice() {
  const { t } = useI18n();
  const toast = useToast();

  const isMenuOpen = ref(null);
  const invoiceToResend = ref(null);
  const invoiceToCancel = ref(null);
  const invoiceNoteToUpdate = ref(null);

  const updateInternalNoteForm = useForm({
    internal_note: '',
  });

  const isInvoiceMenuOpen = (invoice) => isMenuOpen.value === invoice;

  const toggleMenu = (invoice) => isMenuOpen.value = isMenuOpen.value === invoice ? null : invoice;

  const handleClickOutside = (event) => {
    if (!event.target.closest('.context-menu-wrapper')) {
      isMenuOpen.value = null;
    }
  }

  const openInvoiceResendModal = (invoice) => {
    invoiceToResend.value = invoice;
  }

  const openInvoiceCancelModal = (invoice) => {
    invoiceToCancel.value = invoice;
  }

  const openInvoiceNoteModal = (invoice) => {
    invoiceNoteToUpdate.value = invoice;
  }

  const closeInvoiceResendModal = () => {
    invoiceToResend.value = null;
  }

  const closeInvoiceCancelModal = () => {
    invoiceToCancel.value = null;
  }

  const closeInvoiceNoteModal = () => {
    invoiceNoteToUpdate.value = null;
    updateInternalNoteForm.reset();
  }

  const resendInvoice = (invoice) => {
    router.post(route('admin.invoice.resend', invoice), {}, {
      preserveScroll: true,
      onSuccess: () => {
        closeInvoiceResendModal();
        toast.open({
          message: `${t('common.toast.invoice.invoiceResend.successMessage')}.`,
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: () => {
        toast.open({
          message: `${t('common.toast.invoice.invoiceResend.errorMessage')}!`,
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    });
  };

  const cancelInvoice = (invoice) => {
    router.patch(route('admin.invoice.cancel', invoice), {}, {
      preserveScroll: true,
      onSuccess: () => {
        closeInvoiceCancelModal();
        toast.open({
          message: `${t('common.toast.invoice.invoiceCancel.successMessage')}.`,
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: () => {
        toast.open({
          message: `${t('common.toast.invoice.invoiceCancel.errorMessage')}.`,
          type: 'error',
          position: 'top',
          duration: 4000
        })
      },
    });
  };

  const updateInvoiceInternalNote = () => {
    if (!invoiceNoteToUpdate.value) return;

    updateInternalNoteForm.patch(
      route('admin.invoice.updateNote', invoiceNoteToUpdate.value),
      {
        preserveScroll: true,
        onSuccess: () => {
          closeInvoiceNoteModal();

          toast.open({
            message: `${t('common.toast.invoice.invoiceInternalNoteUpdate.successMessage')}.`,
            type: 'success',
            position: 'top',
            duration: 4000,
          });
        },
        onError: () => {
          toast.open({
            message: `${t('common.toast.invoice.invoiceInternalNoteUpdate.errorMessage')}.`,
            type: 'error',
            position: 'top',
            duration: 4000,
          });
        },
      }
    )
  }

  watch(invoiceNoteToUpdate, (invoice) => {
    if (invoice) {
      updateInternalNoteForm.internal_note = invoice.internal_note;
    }
  });

  onMounted(() => {
    window.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
  });

  return {
    isMenuOpen,
    invoiceToResend,
    invoiceToCancel,
    invoiceNoteToUpdate,
    updateInternalNoteForm,
    isInvoiceMenuOpen,
    toggleMenu,
    handleClickOutside,
    openInvoiceResendModal,
    openInvoiceCancelModal,
    openInvoiceNoteModal,
    closeInvoiceResendModal,
    closeInvoiceCancelModal,
    closeInvoiceNoteModal,
    resendInvoice,
    cancelInvoice,
    updateInvoiceInternalNote
  }
}
