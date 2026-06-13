import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useProductDeletion() {
  const { t } = useI18n();
  const toast = useToast();

  const deletionTarget = ref(null);

  const isProductDeletionModalOpen = computed(
    () => deletionTarget.value !== null
  );

  const productDeletionForm = useForm({});

  const openProductDeletionModal = (targetVariation) => {
    deletionTarget.value = targetVariation;
  };

  const closeProductDeletionModal = () => {
    deletionTarget.value = null;
  };

  const performDeletion = (routeName, routeParams) => {
    productDeletionForm.delete(route(routeName, routeParams), {
      preserveScroll: true,
      onSuccess: () => {
        closeProductDeletionModal();
        toast.open({
          message: t('common.toast.product.productDelete.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.product.productDelete.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    })
  }

  const deleteProduct = () => {
    if (!deletionTarget.value?.product) return;

    performDeletion('product.delete', {
      product: deletionTarget.value.product,
    });
  };

  const deleteVariation = () => {
    if (!deletionTarget.value) return;

    performDeletion('productVariation.delete', {
      variation: deletionTarget.value,
    });
  };

  return {
    deletionTarget,
    isProductDeletionModalOpen,
    openProductDeletionModal,
    closeProductDeletionModal,
    deleteProduct,
    deleteVariation,
  }
}
