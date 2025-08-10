<script setup>
import { defineEmits, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  PhList,
  PhMagnifyingGlass,
  PhHeart,
  PhUser,
  PhBag,
} from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const user = usePage().props.auth.user;
const wishlist = usePage().props.auth.wishlist;
const cart = usePage().props.auth.cart;
const emit = defineEmits(['toggleMenu']);


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

</script>

<template>
  <header
    class="relative top-0 left-0 right-0 z-50 flex items-center justify-between px-4 py-[8px] border-b border-b-[#D9D9D9] bg-white md:px-[20px] md:py-[16px] lg:px-[60px] lg:py-[26px]">
    <div class="flex flex-row items-center gap-6">
      <button v-if="user?.role !== 'admin' && user?.role !== 'staff'" @click="emit('toggleMenu')"
        class="flex items-center gap-2">
        <PhList :size="24" />
        <span class="hidden text-sm font-light md:flex">{{ t('common.header.button.menu') }}</span>
      </button>
      <Link v-if="user?.role !== 'admin' && user?.role !== 'staff'" :href="route('search')"
        class="flex items-center gap-2">
      <PhMagnifyingGlass :size="24" />
      <span class="hidden text-sm font-light md:flex">{{ t('common.header.button.search') }}</span>
      </Link>
    </div>
    <div>
      <Link href="/" class="text-lg font-medium md:text-3xl">Vogueify</Link>
    </div>
    <div class="flex gap-4">
      <div v-if="user?.role === 'admin' || user?.role === 'staff'" class="locale-changer">
        <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
          class="py-0 cursor-pointer border-none text-sm">
          <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
            locale.charAt(0).toUpperCase() + locale.slice(1) }}
          </option>
        </select>
      </div>
      <div v-if="user?.role === 'customer'" class="relative">
        <Link :href="route('wishlist.index')">
        <PhHeart :size="24" />
        <span v-if="wishlist.length > 0"
          class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">{{
            wishlist.length < 9 ? wishlist.length : '+9' }}</span>
            </Link>
      </div>
      <div class="relative">
        <Link v-if="!user" :href="route('login')">
        <PhUser :size="24" />
        </Link>
        <button v-else ref="userButton" @click.stop="toggleUserMenu">
          <PhUser :size="24" />
        </button>
        <div ref="userMenu" class="bg-white w-40 border border-slate-300 flex-col py-2 absolute right-0 z-10"
          :class="{ 'flex': isUserMenuOpen, 'hidden': !isUserMenuOpen }">
          <Link
            :href="user?.role === 'admin' ? route('admin.overview') : user?.role === 'staff' ? route('admin.products') : route('dashboard')"
            class="py-2 px-4 text-sm w-full transition-all hover:bg-slate-200">
          {{ t('common.header.contextMenu.dashboard') }}</Link>
          <div class="border-t border-gray-200" />
          <Link v-if="user" :href="route('logout')" method="post"
            class="py-2 px-4 text-start text-sm w-full transition-all hover:bg-slate-200">
          {{ t('common.header.contextMenu.logOut') }}</Link>
        </div>
      </div>
      <Link :href="route('cart.index')" v-if="user?.role === 'customer'" class="relative">
      <PhBag :size="24" />
      <span v-if="cart.length > 0"
        class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">{{
          cart.length }}</span>
      </Link>
    </div>
  </header>
</template>
