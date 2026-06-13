import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";
import { toDatetimeLocalFormat } from "@/utils/dateFormat";

const defaultFormData = (coupon = null) => ({
  couponType: coupon?.couponType ?? 'categories',
  code: coupon?.code ?? '',
  type: coupon?.type ?? '',
  value: coupon?.value ?? '',
  starts_at: coupon?.starts_at ? toDatetimeLocalFormat(coupon.starts_at) : '',
  expires_at: coupon?.expires_at ? toDatetimeLocalFormat(coupon.expires_at) : '',
  status: coupon?.status ?? 'active',
  max_uses: coupon?.max_uses ?? '',
  max_uses_per_user: coupon?.max_uses_per_user ?? '',
  sendNotification: false,
  categories: coupon?.categories ?? [],
  products: coupon?.products ?? [],
  productVariations: coupon?.product_variations ?? [],
  users: coupon?.users ?? [],
});

const isCreateCouponModalOpen = ref(false);
const selectedCoupon = ref(null);

const form = useForm(defaultFormData());

export function useUpsertCoupon() {
  const { t } = useI18n();
  const toast = useToast();

  const resetForm = (coupon = null) => {
    form.defaults(defaultFormData(coupon));
    form.reset();
    form.clearErrors();
  };

  const openCreateCouponModal = () => {
    selectedCoupon.value = null;
    resetForm();
    isCreateCouponModalOpen.value = true;
  };

  const closeCreateCouponModal = () => {
    isCreateCouponModalOpen.value = false;
    resetForm();
  };

  const openUpdateCouponModal = (coupon) => {
    selectedCoupon.value = coupon;
    resetForm(coupon);
  };

  const closeUpdateCouponModal = () => {
    selectedCoupon.value = null;
    resetForm();
  };

  const isUpdateCouponModalOpen = computed(() => selectedCoupon.value !== null);

  const isUpdateMode = computed(() => selectedCoupon.value !== null);

  const modalTitle = computed(() =>
    isUpdateMode.value
      ? t('page.admin.updateCoupon', {
        coupon: selectedCoupon.value?.code,
      })
      : t('page.admin.createCoupon')
  );

  const submitText = computed(() =>
    isUpdateMode.value
      ? t('common.button.update')
      : t('common.button.create')
  );

  const loadingText = computed(() =>
    isUpdateMode.value
      ? t('common.button.updating')
      : t('common.button.creating')
  );

  const buildPayload = () => ({
    ...form.data(),

    categories: form.categories.map(category =>
      typeof category === 'object'
        ? category.id
        : category
    ),

    products: form.products.map(product =>
      typeof product === 'object'
        ? product.id
        : product
    ),

    productVariations: form.productVariations.map(variation =>
      typeof variation === 'object'
        ? variation.id
        : variation
    ),

    users: form.users.map(user =>
      typeof user === 'object'
        ? user.id
        : user
    ),
  });

  const showToast = ({ type, message, }) => {
    toast.open({
      type,
      message,
      position: 'top',
      duration: 4000,
    });
  };

  const requestOptions = ({ successMessage, errorMessage, onSuccess }) => ({
    preserveScroll: true,
    onSuccess: () => {
      onSuccess?.();
      showToast({
        type: 'success',
        message: successMessage,
      });
    },

    onError: errors => {
      showToast({
        type: 'error',
        message: Object.values(errors)?.[0] ?? errorMessage,
      });
    },
  });

  const upsertCoupon = () => {
    const payload = buildPayload();

    if (selectedCoupon.value) {
      form.transform(() => payload).put(route('coupon.update', selectedCoupon.value.id),
        requestOptions({
          successMessage: t('common.toast.coupon.couponUpdate.successMessage'),
          errorMessage: t('common.toast.coupon.couponUpdate.errorMessage'),
          onSuccess: closeUpdateCouponModal,
        })
      );
      return;
    }

    form
      .transform(() => payload)
      .post(route('coupon.store'), requestOptions({
        successMessage: t('common.toast.coupon.couponCreate.successMessage'),
        errorMessage: t('common.toast.coupon.couponCreate.errorMessage'),
        onSuccess: closeCreateCouponModal,
      })
      );
  };

  return {
    form,
    selectedCoupon,
    isCreateCouponModalOpen,
    isUpdateCouponModalOpen,
    isUpdateMode,
    modalTitle,
    submitText,
    loadingText,
    openCreateCouponModal,
    closeCreateCouponModal,
    openUpdateCouponModal,
    closeUpdateCouponModal,
    upsertCoupon,
  };
}
