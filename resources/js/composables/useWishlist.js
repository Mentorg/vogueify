import { router } from '@inertiajs/vue3';

export default function useWishlist() {
  const toggleWishlist = (productVariationId, onSuccess) => {
    router.post(
      route('wishlist.toggle', productVariationId),
      {},
      {
        preserveScroll: true,
        preserveState: true,
        onSuccess: () => {
          if (onSuccess) onSuccess();
        },
      }
    );
  };

  return {
    toggleWishlist,
  };
}
