<script setup>
import { computed, ref, onMounted, onBeforeUnmount } from "vue";
import AdminHeader from "@/Components/AdminHeader.vue";
import MobileSidebar from "@/Components/MobileSidebar.vue";
import Sidebar from "@/Components/Sidebar.vue";

const isMenuOpen = ref(false);
const activeSubmenu = ref(null);
const activeThirdLevelSubmenu = ref(null);
const hoveredItem = ref(false);
const activeItem = ref(null);
const isUserMenuOpen = ref(false);

const closeMenu = () => {
  isMenuOpen.value = false;
  activeSubmenu.value = null;
  activeThirdLevelSubmenu.value = null;
};

const openUserMenu = () => {
  isUserMenuOpen.value = !isUserMenuOpen.value;
}

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value;
}

const screenWidth = ref(window.innerWidth);

const isMobile = computed(() => screenWidth.value < 1024);

const handleResize = () => {
  screenWidth.value = window.innerWidth;
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});
</script>

<template>
  <AdminHeader />
  <div class="flex">
    <MobileSidebar v-if="isMobile" :isMenuOpen="isMenuOpen" :toggleMenu="toggleMenu" :closeMenu="closeMenu" />
    <Sidebar v-else />
    <section class="flex flex-col py-8 px-6 w-full">
      <slot />
    </section>
  </div>
</template>
