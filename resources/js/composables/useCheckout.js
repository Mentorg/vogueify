import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useCheckout({ checkoutData = {}, pendingOrder = {}, address = {}, cartItems = {} }) {
  const { t } = useI18n();
  const toast = useToast();

  const hasSubmittedOrder = ref(false);

  const getField = (field, fallback = '') => {
    if (pendingOrder?.[field] != null) {
      return pendingOrder[field];
    }

    const addressFieldMap = {
      shipping_address_line_1: 'address_line_1',
      shipping_address_line_2: 'address_line_2',
      shipping_city: 'city',
      shipping_state: 'state',
      shipping_postcode: 'postcode',
      shipping_phone_number: 'phone_number',
      billing_address_line_1: 'address_line_1',
      billing_address_line_2: 'address_line_2',
      billing_city: 'city',
      billing_state: 'state',
      billing_postcode: 'postcode',
      billing_phone_number: 'phone_number',
    };

    const mappedField = addressFieldMap[field];
    return mappedField && address?.[mappedField] ? address[mappedField] : fallback;
  };

  const createOrderForm = useForm({
    shipping_address_line_1: getField('shipping_address_line_1'),
    shipping_address_line_2: getField('shipping_address_line_2'),
    shipping_city: getField('shipping_city'),
    shipping_state: getField('shipping_state'),
    shipping_postcode: getField('shipping_postcode'),
    shipping_country_id: pendingOrder?.shipping_country_id ?? address?.country_id ?? '',
    shipping_phone_number: getField('shipping_phone_number'),
    billing_address_line_1: getField('billing_address_line_1'),
    billing_address_line_2: getField('billing_address_line_2'),
    billing_city: getField('billing_city'),
    billing_state: getField('billing_state'),
    billing_postcode: getField('billing_postcode'),
    billing_country_id: pendingOrder?.billing_country_id ?? address?.country_id ?? '',
    billing_phone_number: getField('billing_phone_number'),
    items: (pendingOrder?.items ?? cartItems).map(item => ({
      product_variation_id: item.product_variation_id,
      size_id: item.size_id,
      quantity: item.quantity,
      price_at_time: item.price_at_time,
    })),
    discount_amount: checkoutData.discount ?? 0,
    coupon: checkoutData.coupon ?? null,
  });

  const createOrder = () => {
    if (hasSubmittedOrder.value) return;

    hasSubmittedOrder.value = true;

    createOrderForm.post(route('order.store'), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.checkout.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        })
      },
      onError: (errors) => {
        hasSubmittedOrder.value = false;

        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.checkout.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }

    });
  };

  return {
    hasSubmittedOrder,
    createOrderForm,
    createOrder,
  }
}
