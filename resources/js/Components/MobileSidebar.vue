<script setup>
import { defineProps } from 'vue';
import {
  PhX,
} from "@phosphor-icons/vue";
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
  toggleMenu: Function,
  isMenuOpen: Boolean,
  closeMenu: Function
})

const user = usePage().props.auth.user;
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
      <li v-if="user.role === 'admin'">
        <Link href="/admin/overview" class="flex gap-y-4 w-full text-left px-4 py-2">
        Overview
        </Link>
      </li>
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
      <li v-if="user.role === 'admin'">
        <Link href="/admin/users" class="flex gap-y-4 w-full text-left px-4 py-2">
        Users
        </Link>
      </li>
    </ul>
  </div>
</template>
