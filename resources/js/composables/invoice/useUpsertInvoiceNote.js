import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpsertInvoiceNote() {
  const { t } = useI18n();
  const toast = useToast();

  const invoiceNoteTarget = ref(null);

  const isUpsertInvoiceNoteModalOpen = computed(
    () => invoiceNoteTarget.value !== null
  );

  const upsertInvoiceNoteForm = useForm({
    internal_note: null,
  });

  const openUpsertInvoiceNoteModal = (targetInvoice) => {
    invoiceNoteTarget.value = targetInvoice;

    upsertInvoiceNoteForm.internal_note = targetInvoice.internal_note;

    upsertInvoiceNoteForm.clearErrors();
  };

  const closeUpsertInvoiceNoteModal = () => {
    invoiceNoteTarget.value = null;

    upsertInvoiceNoteForm.clearErrors();
  };

  const upsertInvoiceNote = () => {
    if (!invoiceNoteTarget.value) return;

    upsertInvoiceNoteForm.patch(route('admin.invoice.updateNote', invoiceNoteTarget.value), {
        preserveScroll: true,
        onSuccess: () => {
          invoiceNoteTarget.value.internal_note = upsertInvoiceNoteForm.internal_note;
          closeUpsertInvoiceNoteModal();
          toast.open({
            message: t('common.toast.invoice.invoiceInternalNoteUpdate.successMessage'),
            type: 'success',
            position: 'top',
            duration: 4000,
          });
        },
        onError: (errors) => {
          toast.open({
            message: Object.values(errors)?.[0] || t('common.toast.invoice.invoiceInternalNoteUpdate.errorMessage'),
            type: 'error',
            position: 'top',
            duration: 4000,
          });
        },
      }
    )
  };

  return {
    invoiceNoteTarget,
    isUpsertInvoiceNoteModalOpen,
    upsertInvoiceNoteForm,
    openUpsertInvoiceNoteModal,
    closeUpsertInvoiceNoteModal,
    upsertInvoiceNote,
  }
}
