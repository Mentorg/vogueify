import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateOrderBillingAddress() {
  const { t } = useI18n();
  const toast = useToast();

  const hasSameBillingAndShippingAddress = (order) => {
    return (
      order.billing_address_line_1 === order.shipping_address_line_1 &&
      order.billing_address_line_2 === order.shipping_address_line_2 &&
      order.billing_city === order.shipping_city &&
      order.billing_state === order.shipping_state &&
      order.billing_postcode === order.shipping_postcode &&
      order.billing_country_id === order.shipping_country_id
    );
  };

  const orderBillingAddressTarget = ref(null);

  const isUpdateOrderBillingAddressModalOpen = computed(
    () => orderBillingAddressTarget.value !== null
  );

  const updateOrderBillingAddressForm = useForm({
    billing_address_line_1: null,
    billing_address_line_2: null,
    billing_city: null,
    billing_state: null,
    billing_postcode: null,
    billing_country_id: null,
    billing_phone_number: null,
  });

  const getBillingAddressData = (order) => ({
    billing_address_line_1: order.billing_address_line_1,
    billing_address_line_2: order.billing_address_line_2,
    billing_city: order.billing_city,
    billing_state: order.billing_state,
    billing_postcode: order.billing_postcode,
    billing_country_id: order.billing_country_id,
    billing_phone_number: order.billing_phone_number,
  });

  const fillForm = (order) => {
    updateOrderBillingAddressForm.defaults(getBillingAddressData(order));
    updateOrderBillingAddressForm.reset();
    updateOrderBillingAddressForm.clearErrors();
  };

  const openUpdateOrderBillingAddressModal = (targetOrder) => {
    orderBillingAddressTarget.value = targetOrder;

    fillForm(targetOrder.order);
  }

  const closeUpdateOrderBillingAddressModal = () => {
    orderBillingAddressTarget.value = null;

    updateOrderBillingAddressForm.reset();
    updateOrderBillingAddressForm.clearErrors();
  }

  const updateOrderBillingAddress = () => {
    if (!orderBillingAddressTarget.value) return;

    updateOrderBillingAddressForm.patch(route('orders.updateBillingAddress', orderBillingAddressTarget.value.order), {
      preserveScroll: true,
      onSuccess: () => {
        Object.assign(
          orderBillingAddressTarget.value.order,
          getBillingAddressData(updateOrderBillingAddressForm)
        );
        closeUpdateOrderBillingAddressModal();
        toast.open({
          message: t('common.toast.order.admin.orderBillingDateUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderBillingDateUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    })
  }

  return {
    hasSameBillingAndShippingAddress,
    orderBillingAddressTarget,
    isUpdateOrderBillingAddressModalOpen,
    updateOrderBillingAddressForm,
    openUpdateOrderBillingAddressModal,
    closeUpdateOrderBillingAddressModal,
    updateOrderBillingAddress,
  };
}
