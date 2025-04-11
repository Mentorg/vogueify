<script setup>
import { defineProps, ref } from 'vue';
import {
  PhX,
  PhGlobe,
  PhCaretRight,
} from "@phosphor-icons/vue";
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  toggleMenu: Function,
  isMenuOpen: Boolean,
  closeMenu: Function
})

const subMenus = ref({
  level1: false,
  level2: false,
});

const toggleSubMenu = (level) => {
  subMenus.value[level] = !subMenus.value[level];
}

const isSubMenuOpen = (level) => {
  return subMenus.value[level];
}
</script>

<template>
  <div v-if="isMenuOpen"
    class="absolute z-20 left-0 top-0 w-64 h-full bg-white shadow-lg mt-2 rounded-lg px-4 py-[8px] md:px-[20px] md:py-[16px]">
    <div>
      <button @click="closeMenu">
        <PhX size="24" />
      </button>
    </div>
    <ul class="ml-2">
      <li>
        <Link href="/admin/products" class="flex gap-y-4 w-full text-left px-4 py-2">
        Products
        </Link>
      </li>
      <li>
        <Link href="/admin/product/create" class="flex gap-y-4 w-full text-left px-4 py-2">
        Create Product
        </Link>
      </li>
      <li>
        <Link href="/admin/users" class="flex gap-y-4 w-full text-left px-4 py-2">
        Users
        </Link>
      </li>
    </ul>
  </div>
</template>
