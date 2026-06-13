import { usePage } from "@inertiajs/vue3";
import { onBeforeUnmount, onMounted, ref } from "vue";

export function useHeaderActions() {
  const user = usePage().props.auth.user;
  const wishlist = usePage().props.auth.wishlist;
  const cart = usePage().props.auth.cart;


  const isUserMenuOpen = ref(false);
  const userMenu = ref(null);
  const userButton = ref(null);

  const toggleUserMenu = () => {
    isUserMenuOpen.value = !isUserMenuOpen.value;
  }

  const closeUserMenu = () => {
    isUserMenuOpen.value = false;
  }

  const handleClickOutside = (event) => {
    if (
      isUserMenuOpen.value &&
      userMenu.value &&
      !userMenu.value.contains(event.target) &&
      userButton.value &&
      !userButton.value.contains(event.target)
    ) {
      closeUserMenu();
    }
  }

  onMounted(() => {
    document.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
  });

  return {
    user,
    wishlist,
    cart,
    isUserMenuOpen,
    userMenu,
    userButton,
    toggleUserMenu,
  }
}
