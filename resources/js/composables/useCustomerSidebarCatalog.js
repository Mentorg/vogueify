import { computed, onBeforeUnmount, onMounted, ref } from "vue";

export function useCustomerSidebarCatalog() {
  const isMenuOpen = ref(false);
  const activeSubmenu = ref(null);
  const activeThirdLevelSubmenu = ref(null);
  const hoveredItem = ref(false);
  const activeItem = ref(null);
  const menuContainer = ref(null);

  const openSubmenu = (item) => {
    if (activeSubmenu.value === item) {
      activeSubmenu.value = null;
      activeThirdLevelSubmenu.value = null;
    } else {
      activeSubmenu.value = item;
      activeThirdLevelSubmenu.value = null;
    }
  };

  const openThirdLevelSubmenu = (item) => {
    if (activeThirdLevelSubmenu.value === item) {
      activeThirdLevelSubmenu.value = null;
    } else {
      activeThirdLevelSubmenu.value = item;
    }
  };

  const closeMenu = () => {
    isMenuOpen.value = false;
    activeSubmenu.value = null;
    activeThirdLevelSubmenu.value = null;
  };

  const handleClickOutsideMenu = (event) => {
    if (
      isMenuOpen.value &&
      menuContainer.value &&
      !menuContainer.value.contains(event.target)
    ) {
      closeMenu();
    }
  };

  const toggleMenu = () => {
    isMenuOpen.value = !isMenuOpen.value;
  }

  const screenWidth = ref(window.innerWidth);

  const isMobile = computed(() => screenWidth.value < 768);

  const handleResize = () => {
    screenWidth.value = window.innerWidth;
  }

  onMounted(() => {
    window.addEventListener('resize', handleResize);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
  });

  onMounted(() => {
    window.addEventListener('click', handleClickOutsideMenu);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('click', handleClickOutsideMenu);
  });

  return {
    isMenuOpen,
    activeSubmenu,
    activeThirdLevelSubmenu,
    hoveredItem,
    activeItem,
    menuContainer,
    openSubmenu,
    openThirdLevelSubmenu,
    closeMenu,
    toggleMenu,
    isMobile,
  }
}
