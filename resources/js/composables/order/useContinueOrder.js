import { ref } from "vue";
import { router } from "@inertiajs/vue3";

export function useContinueOrder() {
  const isOrderModalOpen = ref(false);

  const openOrderModal = () => {
    isOrderModalOpen.value = true;
  }

  const closeOrderModal = () => {
    isOrderModalOpen.value = false;
  }

  const continueOrder = (order) => {
    router.post(route('order.continueOrder', order));
  }

  return {
    isOrderModalOpen,
    openOrderModal,
    closeOrderModal,
    continueOrder
  };
}
