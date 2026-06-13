import { computed } from "vue";
import { ORDER_EXCEPTION_STATUS_MAP, ORDER_STATUS_STEPS } from "@/constants/orderStatuses";

export function useOrderStatus({ orderDetails = {} }) {
  const isExceptional = computed(() => {
    if (!orderDetails.value?.order) return false;
    return Object.keys(ORDER_EXCEPTION_STATUS_MAP).includes(orderDetails.value.order.order_status);
  });

  const isLost = computed(() => {
    if (!orderDetails.value?.order) return false;
    return orderDetails.value.order.order_status === 'lost';
  });

  const dynamicBranchFromIndex = computed(() => {
    if (!orderDetails.value?.order) return -1;
    if (orderDetails.value.order.order_status === 'canceled') {
      return ORDER_STATUS_STEPS.indexOf('pending');
    }
    return ORDER_STATUS_STEPS.indexOf('out-for-delivery');
  });

  const currentIndex = computed(() => {
    if (!orderDetails.value?.order) return -1;
    const currentStatus = orderDetails.value.order.order_status;
    if (isExceptional.value || isLost.value) {
      return dynamicBranchFromIndex.value;
    }
    return ORDER_STATUS_STEPS.indexOf(currentStatus);
  });

  return {
    isExceptional,
    isLost,
    dynamicBranchFromIndex,
    currentIndex,
  }
}
