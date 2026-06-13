import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpsertOrderNote() {
  const { t } = useI18n();
  const toast = useToast();

  const orderNoteTarget = ref(null);

  const isUpsertOrderNoteModalOpen = computed(
    () => orderNoteTarget.value !== null
  );

  const upsertOrderNoteForm = useForm({
    order_note: null,
  });

  const openUpsertOrderNoteModal = (targetOrder) => {
    orderNoteTarget.value = targetOrder;

    upsertOrderNoteForm.defaults({
      order_note: targetOrder.order.order_note,
    });

    upsertOrderNoteForm.reset();
    upsertOrderNoteForm.clearErrors();
  };

  const closeUpsertOrderNoteModal = () => {
    orderNoteTarget.value = null;

    upsertOrderNoteForm.reset();
    upsertOrderNoteForm.clearErrors();
  }

  const upsertOrderNote = () => {
    if (!orderNoteTarget.value) return;

    upsertOrderNoteForm.patch(route('orders.updateNote', orderNoteTarget.value.order), {
      preserveScroll: true,
      onSuccess: () => {
        orderNoteTarget.value.order.order_note = upsertOrderNoteForm.order_note;
        closeUpsertOrderNoteModal();
        toast.open({
          message: t('common.toast.order.admin.orderNoteUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderNoteUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    })
  }

  return {
    orderNoteTarget,
    isUpsertOrderNoteModalOpen,
    upsertOrderNoteForm,
    openUpsertOrderNoteModal,
    closeUpsertOrderNoteModal,
    upsertOrderNote
  }
}
