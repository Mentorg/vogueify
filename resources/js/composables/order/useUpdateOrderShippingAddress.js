import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateOrderShippingAddress() {
  const { t } = useI18n();
  const toast = useToast();

  const orderShippingAddressTarget = ref(null);

  const isUpdateOrderShippingAddressModalOpen = computed(
    () => orderShippingAddressTarget.value !== null
  );

  const updateOrderShippingAddressForm = useForm({
    shipping_address_line_1: null,
    shipping_address_line_2: null,
    shipping_city: null,
    shipping_state: null,
    shipping_postcode: null,
    shipping_country_id: null,
    shipping_phone_number: null,
    shipping_date: null,
  });

  const getShippingAddressData = (order) => ({
    shipping_address_line_1: order.shipping_address_line_1,
    shipping_address_line_2: order.shipping_address_line_2,
    shipping_city: order.shipping_city,
    shipping_state: order.shipping_state,
    shipping_postcode: order.shipping_postcode,
    shipping_country_id: order.shipping_country_id,
    shipping_phone_number: order.shipping_phone_number,
    shipping_date: order.shipping_date,
  });

  const fillForm = (order) => {
    updateOrderShippingAddressForm.defaults(getShippingAddressData(order));
    updateOrderShippingAddressForm.reset();
    updateOrderShippingAddressForm.clearErrors();
  }

  const openUpdateOrderShippingAddressModal = (targetOrder) => {
    orderShippingAddressTarget.value = targetOrder;

    fillForm(targetOrder.order);
  }

  const closeUpdateOrderShippingAddressModal = () => {
    orderShippingAddressTarget.value = null;

    updateOrderShippingAddressForm.reset();
    updateOrderShippingAddressForm.clearErrors();
  }

  const updateOrderShippingAddress = () => {
    if (!orderShippingAddressTarget.value) return;

    updateOrderShippingAddressForm.patch(route('orders.updateShippingAddress', orderShippingAddressTarget.value.order), {
      preserveScroll: true,
      onSuccess: () => {
        Object.assign(
          orderShippingAddressTarget.value.order,
          getShippingAddressData(updateOrderShippingAddressForm)
        );
        closeUpdateOrderShippingAddressModal();
        toast.open({
          message: t('common.toast.order.admin.orderShippingDateUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000
        })
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.order.admin.orderShippingDateUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    })
  }

  return {
    orderShippingAddressTarget,
    isUpdateOrderShippingAddressModalOpen,
    updateOrderShippingAddressForm,
    openUpdateOrderShippingAddressModal,
    closeUpdateOrderShippingAddressModal,
    updateOrderShippingAddress
  }
}
