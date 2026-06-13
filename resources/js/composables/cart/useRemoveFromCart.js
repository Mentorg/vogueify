import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useRemoveFromCart() {
  const { t } = useI18n();
  const toast = useToast();

  const isRemoveFromCartModalOpen = ref(null);

  const removeFromCartForm = useForm({});

  const openRemoveFromCartModal = (item) => isRemoveFromCartModalOpen.value = item;

  const closeRemoveFromCartModal = () => {
    isRemoveFromCartModalOpen.value = null;
    removeFromCartForm.reset()
  }

  const removeFromCart = (id) => {
    removeFromCartForm.delete(route('cart.delete', id), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.cart.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeRemoveFromCartModal()
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.cart.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      }
    });
  }

  return {
    isRemoveFromCartModalOpen,
    openRemoveFromCartModal,
    closeRemoveFromCartModal,
    removeFromCart,
  }
}
