<script setup>
import { defineProps, defineEmits } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  PhList,
  PhMagnifyingGlass,
  PhHeart,
  PhUser,
  PhBag,
} from "@phosphor-icons/vue";

const props = defineProps({
  isUserMenuOpen: Boolean,
  openUserMenu: Function
});

const user = usePage().props.auth.user;
const wishlist = usePage().props.auth.wishlist;
const emit = defineEmits(['toggleMenu']);
</script>

<template>
  <header
    class="flex items-center justify-between px-4 py-[8px] border-b border-b-[#D9D9D9] md:px-[20px] md:py-[16px] lg:px-[60px] lg:py-[16px]">
    <div class="flex flex-row items-center gap-6">
      <button v-if="user?.role !== 'admin' || user?.role !== 'staff'" @click="emit('toggleMenu')"
        class="flex items-center gap-2">
        <PhList :size="24" />
        <span class="hidden md:flex">Menu</span>
      </button>
      <Link v-if="user?.role !== 'admin' || user?.role !== 'staff'" :href="route('search')"
        class="flex items-center gap-2">
      <PhMagnifyingGlass :size="24" />
      <span class="hidden md:flex">Search</span>
      </Link>
    </div>
    <div>
      <Link href="/" class="text-lg font-medium md:text-3xl">Vogueify</Link>
    </div>
    <div class="flex gap-4">
      <div v-if="user?.role === 'customer'" class="relative">
        <Link :href="route('wishlist.index')">
        <PhHeart :size="24" />
        <span
          class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">{{
            wishlist.length < 9 ? wishlist.length : '+9' }}</span>
            </Link>
      </div>
      <div class="relative">
        <Link v-if="!user" :href="route('login')">
        <PhUser :size="24" />
        </Link>
        <button v-else @click="openUserMenu">
          <PhUser :size="24" />
        </button>
        <div class="bg-white w-40 border border-slate-300 flex-col py-2 absolute right-0 z-10"
          :class="{ 'flex': isUserMenuOpen, 'hidden': !isUserMenuOpen }">
          <Link
            :href="user?.role === 'admin' ? route('admin.overview') : user?.role === 'staff' ? route('admin.products') : route('dashboard')"
            class="py-2 px-4 text-sm w-full transition-all hover:bg-slate-200">
          Dashboard</Link>
          <div class="border-t border-gray-200" />
          <Link v-if="!user" :href="route('login')" class="py-2 px-4 text-sm w-full transition-all hover:bg-slate-200">
          Login
          </Link>
          <Link v-if="user" :href="route('logout')" method="post"
            class="py-2 px-4 text-start text-sm w-full transition-all hover:bg-slate-200">
          Log Out</Link>
        </div>
      </div>
      <button v-if="user?.role === 'customer'" class="relative">
        <PhBag :size="24" />
        <span
          class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">0</span>
      </button>
    </div>
  </header>
</template>
