import { computed, onBeforeUnmount, onMounted, ref, watch } from "vue";
import { router, useForm, usePage } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useAddToCart({ product = {}, activeVariation = {} }) {
  const { t } = useI18n();
  const toast = useToast();

  const user = usePage().props.auth.user;

  const isCartSidebarOpen = ref(false);

  const currentVariation = computed(() => {
    return activeVariation || (product.productVariations?.[0] ?? null);
  });

  const closeCartSidebar = () => isCartSidebarOpen.value = !isCartSidebarOpen.value;

  const addToCartForm = useForm({
    quantity: '1',
    price_at_time: currentVariation.value.price,
    product_variation_id: currentVariation.value.id,
    size_id: currentVariation.value.sizes?.length
      ? (currentVariation.value.sizes.find(item => item.pivot.stock > 0)?.id ?? null)
      : null
  });

  const addToCart = () => {
    if (user === null) {
      addToCartForm.visit(route('login'));
      return;
    }

    if (user.email_verified_at === null) {
      addToCartForm.visit(route('verification.notice'));
      return;
    }

    addToCartForm
      .transform(data => ({
        ...data,
      }))
      .post(route('cart.store'), {
        onSuccess: () => {
          isCartSidebarOpen.value = true;
        },
        onError: (errors) => {
          toast.open({
            message: Object.values(errors)?.[0] || t('common.toast.product.cart.errorMessage'),
            type: 'error',
            position: 'top',
            duration: 4000,
          });
        },
      });
  };

  const handleClickOutside = (event) => {
    if (!event.target.closest('.cartSidebar')) {
      isCartSidebarOpen.value = false;
    }
  }

  watch(
    () => activeVariation.sizes,
    (sizes) => {
      if (!sizes?.length) return;

      const selected = sizes.find(
        s => s.id === addToCartForm.size_id
      );

      if (selected && Number(selected.pivot?.stock ?? 0) < 1) {
        addToCartForm.size_id = null;
      }
    },
    { immediate: true }
  );

  watch(
    () => activeVariation.sizes,
    (sizes) => {
      if (!sizes?.length) return;

      const firstAvailable = sizes.find(
        s => Number(s.pivot?.stock ?? 0) > 0
      );

      if (firstAvailable) {
        addToCartForm.size_id = firstAvailable.id;
      }
    },
    { immediate: true }
  );

  onMounted(() => {
    window.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
  });

  return {
    isCartSidebarOpen,
    addToCartForm,
    addToCart,
    closeCartSidebar
  }
}
