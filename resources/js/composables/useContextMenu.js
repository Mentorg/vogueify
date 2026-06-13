import { ref, onMounted, onBeforeUnmount } from 'vue';

export function useContextMenu(menuClassSelectors = []) {
  const isContextMenuOpen = ref(null);
  const isSubMenuOpen = ref(null);
  const dropdownStyle = ref({})

  const toggleContextMenu = (entityID, event) => {
    if (isContextMenuOpen.value === entityID) {
      isContextMenuOpen.value = null;
      return;
    }

    isContextMenuOpen.value = entityID;

    const rect = event.currentTarget.getBoundingClientRect();

    dropdownStyle.value = {
      top: `${rect.bottom + window.scrollY}px`,
      left: `${rect.right - 130}px`
    };
  };

  const toggleSubMenu = (entityID) => {
    if (isSubMenuOpen.value === entityID) {
      isSubMenuOpen.value = null;
      return;
    }

    isSubMenuOpen.value = entityID;
  }

  const closeContextMenu = () => isContextMenuOpen.value = null;
  const closeSubMenu = () => isSubMenuOpen.value = null;

  const handleClickOutside = (event) => {
    const clickedInsideAny = menuClassSelectors.some(selector =>
      event.target.closest(selector)
    );

    if (!clickedInsideAny) {
      closeContextMenu();
    }
  };

  onMounted(() => {
    window.addEventListener('click', handleClickOutside);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutside);
  });

  return {
    isContextMenuOpen,
    isSubMenuOpen,
    dropdownStyle,
    toggleContextMenu,
    toggleSubMenu,
    closeSubMenu,
  };
}
