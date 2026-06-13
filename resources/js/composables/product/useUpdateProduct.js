import { computed, onMounted, onBeforeUnmount, watch } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateProduct({ product, sizes, types }) {
  const { t } = useI18n();
  const toast = useToast();

  const MAX_FILE_SIZE = 5 * 1024 * 1024;

  const ALLOWED_IMAGE_TYPES = [
    "image/png",
    "image/jpeg",
    "image/webp",
  ];

  const ALLOWED_EXTENSIONS = [
    "png",
    "jpg",
    "jpeg",
    "webp",
  ];

  // Helpers
  const sizeMap = Object.fromEntries(
    sizes.map((size) => [
      size.id,
      size.size_labels?.[0]?.label ?? null,
    ])
  );

  const revokePreviewUrl = (url) => {
    if (url?.startsWith("blob:")) {
      URL.revokeObjectURL(url);
    }
  };

  const getImageUrl = (url) => {
    if (!url) {
      return "";
    }

    if (
      url.startsWith("blob:") ||
      url.startsWith("http")
    ) {
      return url;
    }

    return `${window.location.origin}${url.startsWith("/") ? "" : "/"
      }${url}`;
  };

  const scrollToFirstError = (errors) => {
    const firstErrorKey = Object.keys(errors)?.[0];

    if (!firstErrorKey) {
      return;
    }

    const field = document.querySelector(
      `[name="${firstErrorKey}"]`
    );

    field?.scrollIntoView({
      behavior: "smooth",
      block: "center",
    });

    field?.focus?.();
  };

  const getSizesForVariation = (variation) => {
    if (!variation.product_type_id) {
      return [];
    }

    return sizes.filter(
      (size) =>
        size.product_type_id ===
        variation.product_type_id
    );
  };

  const syncVariationSizes = (variation) => {
    const availableSizes =
      getSizesForVariation(variation);

    variation.sizes = availableSizes.map(
      (size) => ({
        id: size.id,
        label: sizeMap[size.id] ?? null,
        stock: 0,
      })
    );
  };

  const createEmptyVariation = () => {
    const variation = {
      id: null,
      sku: "",
      stock: 0,
      price: 0,
      product_type_id:
        types?.[0]?.id ?? null,
      color_id: null,
      primary_color_id: null,
      secondary_color_id: null,
      existing_image: null,
      preview_url: null,
      image: null,
      collapsed: false,
      sizes: [],
    };

    syncVariationSizes(variation);

    return variation;
  };

  // Form
  const updateProductForm = useForm({
    id: product.id,
    slug: product.slug,
    name: product.name ?? "",
    description: product.description ?? "",
    gender: product.gender ?? "",
    category_id: product.category_id ?? null,
    features: product.features?.map((feature) => ({
      title: feature.title ?? "",
      description: feature.description ?? "",
    })) ?? [],
    variations: product.product_variations?.map((variation) => ({
      id: variation.id,
      sku: variation.sku ?? "",
      stock: variation.stock ?? 0,
      price: variation.price ?? 0,
      product_type_id: variation.product_type_id ?? null,
      color_id: variation.color_id ?? null,
      primary_color_id: variation.primary_color_id ?? null,
      secondary_color_id: variation.secondary_color_id ?? null,
      existing_image: variation.image ?? null,
      preview_url: variation.image ?? null,
      image: null,
      collapsed: false,
      sizes: variation.sizes?.map((size) => ({
        id: size.id,
        label: sizeMap[size.id] ?? null,
        stock: size.pivot?.stock ?? 0,
      })) ?? [],
    })) ?? [],
  });

  const hasVariations = computed(() => {
    return updateProductForm.variations.length > 0;
  });

  const isDirty = computed(() => {
    return updateProductForm.isDirty;
  });

  // Variation handlers
  const addVariation = () => {
    updateProductForm.variations.push(
      createEmptyVariation()
    );
  };

  const removeVariation = (index) => {
    const variation = updateProductForm.variations[index];

    revokePreviewUrl(variation.preview_url);

    updateProductForm.variations.splice(index, 1);
  };

  const beforeUnloadHandler = (event) => {
    if (
      !isDirty.value ||
      updateProductForm.processing
    ) {
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

  // Image handling
  const handleImageChange = (event, index) => {
    const file = event.target.files?.[0];
    const variation = updateProductForm.variations[index];

    if (!variation) {
      return;
    }

    if (!file) {
      revokePreviewUrl(variation.preview_url);

      variation.image = null;
      variation.preview_url = variation.existing_image;

      return;
    }

    const extension = file.name.split(".").pop()?.toLowerCase();

    const validMime = ALLOWED_IMAGE_TYPES.includes(file.type);

    const validExtension = ALLOWED_EXTENSIONS.includes(extension);

    if (!validMime && !validExtension) {
      toast.open({
        message: t("common.toast.product.productCreate.invalidImageType"),
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

  onBeforeUnmount(() => {
    window.removeEventListener(
      "beforeunload",
      beforeUnloadHandler
    );

    updateProductForm.variations.forEach(
      (variation) => {
        revokePreviewUrl(
          variation.preview_url
        );
      }
    );
  });

  // Submit
  const updateProduct = () => {
    if (updateProductForm.processing) {
      return;
    }

    updateProductForm.transform((data) => ({
      _method: "PUT",
      id: data.id,
      name: data.name,
      description: data.description,
      gender: data.gender,
      category_id: data.category_id,
      features: data.features.map((feature) => ({
        title: feature.title,
        description: feature.description,
      })
      ),
      variations: data.variations.map((variation) => {
        const payload = {
          id: variation.id,
          sku: variation.sku,
          stock: variation.stock,
          price: variation.price,
          product_type_id: variation.product_type_id,
          color_id: variation.color_id,
          primary_color_id: variation.primary_color_id,
          secondary_color_id: variation.secondary_color_id,
          sizes: variation.sizes.map((size) => ({
            id: size.id,
            stock: size.stock,
          })
          ),
        };
        if (variation.image instanceof File) {
          payload.image = variation.image;
        }
        else if (variation.existing_image) {
          payload.image = variation.existing_image;
        }
        return payload;
      }
      ),
    }))
      .post(route("product.update", updateProductForm.slug),
        {
          preserveScroll: true,
          forceFormData: true,
          onSuccess: () => {
            toast.open({
              message: t("common.toast.product.productUpdate.successMessage"),
              type: "success",
              position: "top",
              duration: 4000,
            });

            updateProductForm.variations.forEach((variation) => {
              revokePreviewUrl(variation.preview_url);
            });

            updateProductForm.variations.forEach((variation) => {
              if (variation.preview_url?.startsWith("blob:")) {
                revokePreviewUrl(variation.preview_url);
              }

              variation.existing_image = variation.preview_url;
            }
            );

            updateProductForm.defaults();
          },
          onError: (errors) => {
            scrollToFirstError(errors);

            toast.open({
              message: Object.values(errors)?.[0] || t("common.toast.product.productUpdate.errorValidationMessage"),
              type: "error",
              position: "top",
              duration: 4000,
            });
          },
        }
      );
  };

  watch(
    () =>
      updateProductForm.variations.map(
        (variation) =>
          variation.product_type_id
      ),

    () => {
      updateProductForm.variations.forEach(
        (variation) => {
          syncVariationSizes(variation);
        }
      );
    },

    {
      deep: true,
    }
  );

  return {
    updateProductForm,
    hasVariations,
    addVariation,
    removeVariation,
    handleImageChange,
    getImageUrl,
    updateProduct,
  };
}
