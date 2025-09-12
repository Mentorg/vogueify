import { ref, onMounted, onBeforeUnmount } from 'vue';

export function useDropdown(menuClassSelectors = []) {
  const openMenu = ref(null);

  const toggleMenu = (menuId) => {
    openMenu.value = openMenu.value === menuId ? null : menuId;
  };

  const isMenuOpen = (menuId) => openMenu.value === menuId;

  const closeMenu = () => {
    openMenu.value = null;
  };

  const handleClickOutside = (event) => {
    const clickedInsideAny = menuClassSelectors.some(selector =>
      event.target.closest(selector)
    );

    if (!clickedInsideAny) {
      closeMenu();
    }
  };

  onMounted(() => {
    window.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
  });

  return {
    openMenu,
    toggleMenu,
    isMenuOpen,
    closeMenu,
  };
}
