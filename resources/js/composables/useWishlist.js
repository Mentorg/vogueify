import { router, usePage } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';

export function useWishlist() {
  const toast = useToast();
  const user = usePage().props.auth.user;

  const toggleWishlist = (productVariationId, onSuccess) => {
    router.post(route('wishlist.toggle', productVariationId), {}, {
      preserveScroll: true,
      preserveState: true,
      onSuccess: () => {
        if (onSuccess) onSuccess();
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.wishlist.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000
        })
      }
    }
    );
  };

  return {
    toggleWishlist,
    user,
  };
}
