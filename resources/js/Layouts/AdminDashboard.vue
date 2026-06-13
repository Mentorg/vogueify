<script setup>
import AdminHeader from "@/Layouts/AdminHeader.vue";
import MobileSidebar from "@/Layouts/MobileSidebar.vue";
import Sidebar from "@/Layouts/Sidebar.vue";
import { useAdminDashboardSidebar } from "@/composables/useAdminDashboardSidebar";
import { useScreen } from "@/composables/useScreen";

const isSmallScreen = useScreen();
const { isMenuOpen, toggleMenu, closeMenu } = useAdminDashboardSidebar();

</script>

<template>
  <div class="h-screen overflow-hidden flex flex-col">
    <AdminHeader :isSmallScreen="isSmallScreen" :toggleMenu="toggleMenu" />
    <div class="flex flex-1 overflow-hidden">
      <MobileSidebar v-if="isSmallScreen" :isMenuOpen="isMenuOpen" :toggleMenu="toggleMenu" :closeMenu="closeMenu" />
      <Sidebar v-else />
      <section class="flex flex-col py-8 px-6 w-full overflow-y-auto">
        <slot />
      </section>
    </div>
  </div>
</template>
