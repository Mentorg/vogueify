import { computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useCreateProduct({
  categories,
  types,
  sizes,
}) {
  const { t } = useI18n();
  const toast = useToast();

  const CATEGORY_CLOTHING = 1;
  const CATEGORY_SHOES = 2;
  const CATEGORY_ACCESSORIES = 6;

  const MAX_FILE_SIZE = 5 * 1024 * 1024;

  const ALLOWED_IMAGE_TYPES = [
    "image/png",
    "image/jpeg",
    "image/webp",
  ];

  const productTypeGenderRules = {
    men: [1, 2, 3, 4],
    women: [5, 6, 7, 8],
  };

  // Helpers
  const sizeMap = Object.fromEntries(sizes.map((size) => [
    size.id,
    size.size_labels?.[0]?.label ?? null,
  ])
  );

  const revokePreviewUrl = (url) => {
    if (url?.startsWith("blob:")) {
      URL.revokeObjectURL(url);
    }
  };

  const scrollToFirstError = (errors) => {
    const firstErrorKey = Object.keys(errors)?.[0];

    if (!firstErrorKey) return;

    const field = document.querySelector(
      `[name="${firstErrorKey}"]`
    );

    field?.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });

    field?.focus?.();
  };

  // Form
  const createProductForm = useForm({
    name: "",
    description: "",
    gender: "men",
    category_id: categories?.[0]?.id ?? null,
    features: [
      {
        title: "",
        description: ""
      }
    ],
    variations: [],
  });

  const canAddVariation = computed(() => {
    return Boolean(
      createProductForm.name &&
      createProductForm.description &&
      createProductForm.gender &&
      createProductForm.category_id &&
      productTypesForCategory.value.length
    );
  });

  const hasVariations = computed(() => {
    return createProductForm.variations.length > 0;
  });

  const isDirty = computed(() => {
    return createProductForm.isDirty;
  });

  const canAddColor = computed(() => {
    return createProductForm.category_id !== CATEGORY_ACCESSORIES;
  });

  const productTypesForCategory = computed(() => {
    let filtered = types.filter((type) =>
      type.category_id === createProductForm.category_id
    );

    if (
      createProductForm.category_id === CATEGORY_CLOTHING &&
      createProductForm.gender !== "unisex"
    ) {
      const allowedIds = productTypeGenderRules[createProductForm.gender] ?? [];

      filtered = filtered.filter((type) =>
        allowedIds.includes(type.id)
      );
    }

    return filtered;
  });

  // Variation helpers
  const getTypeLabel = (variation) => {
    return (
      types.find((type) => type.id === variation.product_type_id)?.label ?? "N/A"
    );
  };

  const getSizesForVariation = (variation) => {
    if (!variation.product_type_id) {
      return [];
    }

    if (createProductForm.category_id === CATEGORY_SHOES) {
      let filtered = sizes.filter((size) =>
        size.product_type_id === 9
      );

      if (createProductForm.gender !== "unisex") {
        filtered = filtered.filter((size) =>
          size.size_labels?.some((label) =>
            label.gender === createProductForm.gender
          )
        );
      }

      return filtered;
    }

    return sizes.filter((size) =>
      size.product_type_id === variation.product_type_id
    );
  };

  const syncVariationSizes = (variation) => {
    const availableSizes = getSizesForVariation(variation);

    variation.sizes = availableSizes.map((size) => {
      const existing = (variation.sizes || []).find((s) => s.id === size.id);

      return {
        id: size.id,
        label: sizeMap[size.id] ?? null,
        stock: existing?.stock ?? 0,
      };
    });
  };

  // Variation handlers
  const addVariation = () => {
    const availableTypes = productTypesForCategory.value;

    const variation = {
      image: null,
      preview_url: null,
      product_type_id: availableTypes[0]?.id ?? null,
      color_id: "",
      primary_color_id: "",
      secondary_color_id: "",
      price: 0,
      sku: "",
      stock: 0,
      sizes: [],
      collapsed: false,
    };

    if (variation.product_type_id) {
      syncVariationSizes(variation);
    }

    createProductForm.variations.push(variation);
  };

  const removeVariation = (index) => {
    const variation = createProductForm.variations[index];

    revokePreviewUrl(variation.preview_url);

    createProductForm.variations.splice(index, 1);
  };

  const handleBeforeUnload = (event) => {
    if (createProductForm.isDirty) {
      event.preventDefault();
      event.returnValue = "";
    }
  };

  // Image handling
  const handleImageChange = (event, index) => {
    const file = event.target.files?.[0];
    const variation = createProductForm.variations[index];

    if (!variation) {
      return;
    }

    if (!file) {
      revokePreviewUrl(variation.preview_url);

      variation.image = null;
      variation.preview_url = null;

      return;
    }

    if (!ALLOWED_IMAGE_TYPES.includes(file.type)) {
      toast.open({
        message: t("common.toast.product.invalidImageType"),
        type: "error",
        position: "top",
        duration: 4000,
      });

      return;
    }

    if (file.size > MAX_FILE_SIZE) {
      toast.open({
        message: t("common.toast.product.imageTooLarge"),
        type: "error",
        position: "top",
        duration: 4000,
      });

      return;
    }

    revokePreviewUrl(variation.preview_url);

    variation.image = file;
    variation.preview_url = URL.createObjectURL(file);
  };

  const beforeUnloadHandler = (event) => {
    if (!isDirty.value || createProductForm.processing) {
      return;
    }

    event.preventDefault();

    event.returnValue = "";
  };

  onMounted(() => {
    window.addEventListener(
      "beforeunload",
      beforeUnloadHandler
    );
  });

  // Cleanup previews
  onBeforeUnmount(() => {
    window.removeEventListener(
      "beforeunload",
      beforeUnloadHandler
    );

    createProductForm.variations.forEach((variation) => {
      revokePreviewUrl(variation.preview_url);
    });
  });

  onBeforeUnmount(() => {
    window.removeEventListener(
      "beforeunload",
      handleBeforeUnload
    );
  });

  // Submit
  const createProduct = () => {
    if (createProductForm.processing) {
      return;
    }

    createProductForm.post(route("product.store"), {
      preserveScroll: true,
      forceFormData: true,

      onSuccess: () => {
        toast.open({
          message: t("common.toast.product.productCreate.successMessage"),
          type: "success",
          position: "top",
          duration: 4000,
        });

        createProductForm.variations.forEach((variation) => {
          revokePreviewUrl(variation.preview_url);
        });

        createProductForm.reset();

        createProductForm.defaults();

        createProductForm.gender = "men";

        createProductForm.category_id = categories.length ? categories[0].id : null;

        createProductForm.features = [
          {
            title: "",
            description: "",
          },
        ];

        createProductForm.variations = [];
      },

      onError: (errors) => {
        scrollToFirstError(errors);

        toast.open({
          message: Object.values(errors)?.[0] || t("common.toast.product.productCreate.errorMessage"),
          type: "error",
          position: "top",
          duration: 4000,
        });
      },
    });
  };

  watch(
    () => [
      createProductForm.category_id,
      createProductForm.gender,
    ],
    () => {
      const availableTypes = productTypesForCategory.value;

      createProductForm.variations.forEach((variation) => {
        const isValid = availableTypes.some((type) => type.id === variation.product_type_id);

        if (!isValid) {
          variation.product_type_id = availableTypes[0]?.id ?? null;
        }

        syncVariationSizes(variation);
      });
    }
  );

  return {
    createProductForm,
    canAddVariation,
    hasVariations,
    canAddColor,
    productTypesForCategory,
    getTypeLabel,
    addVariation,
    removeVariation,
    handleImageChange,
    createProduct,
  };
}
