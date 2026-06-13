import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";
import { getPreferredSizeSystem, isOutOfStock } from "@/utils/product";
import { useI18n } from "vue-i18n";

export function useShowProduct({ product = {}, activeVariation = {}}) {
  const user = usePage().props.auth.user;
  const { locale } = useI18n();

  const isStaff = computed(() => {
    return ['admin', 'staff'].includes(user?.role);
  });

  const outOfStock = computed(() => {
    return isOutOfStock(activeVariation);
  });

  const otherVariations = computed(() => {
    return product.product_variations.filter(
      v => v.id !== activeVariation.id
    )
  });

  const showSizes = computed(() => {
    return (
      product.category_id === 2 ||
      product.category_id === 6 ||
      (
        activeVariation &&
        ['belt', 'bracelet', 'ring'].includes(activeVariation.type.type)
      )
    );
  });

  const getSizeLabel = (size) => {
    const system = getPreferredSizeSystem(locale.value);
    const gender = product.gender;

    const exactMatch = size.size_labels.find(label =>
      label.system === system &&
      label.gender === gender
    );

    if (exactMatch) {
      return exactMatch.label;
    }

    const sameSystem = size.size_labels.find(label =>
      label.system === system
    );

    if (sameSystem) {
      return sameSystem.label;
    }

    return size.size_labels[0]?.label ?? '';
  };

  const isSizeAvailable = (size) => {
    return Number(size?.pivot?.stock ?? 0) > 0;
  };

  return {
    isStaff,
    outOfStock,
    otherVariations,
    showSizes,
    getSizeLabel,
    isSizeAvailable
  }
}
