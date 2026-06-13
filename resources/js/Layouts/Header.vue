<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  PhList,
  PhMagnifyingGlass,
  PhHeart,
  PhUser,
  PhBag,
} from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';
import { useHeaderActions } from '@/composables/useHeaderActions';

const { t } = useI18n();
const emit = defineEmits(['toggleMenu']);

const {
  user,
  wishlist,
  cart,
  isUserMenuOpen,
  userMenu,
  userButton,
  toggleUserMenu,
} = useHeaderActions();

</script>

<template>
  <header
    class="relative top-0 left-0 right-0 z-50 flex items-center justify-between px-4 py-[8px] border-b border-b-[#D9D9D9] bg-white md:px-[20px] md:py-[16px] lg:px-[60px] lg:py-[26px]">
    <div class="flex flex-row items-center gap-6">
      <button v-if="user?.role !== 'admin' && user?.role !== 'staff'" @click.stop="emit('toggleMenu')"
        :title="t('common.button.openCategoryMenuTitle')" class="flex items-center gap-2">
        <PhList :size="24" />
        <span class="hidden text-sm font-light md:flex">{{ t('common.header.button.menu') }}</span>
      </button>
      <Link v-if="user?.role !== 'admin' && user?.role !== 'staff'" :href="route('search')"
        :title="t('common.button.goToSearchTitle')" class="flex items-center gap-2">
        <PhMagnifyingGlass :size="24" />
        <span class="hidden text-sm font-light md:flex">{{ t('common.header.button.search') }}</span>
      </Link>
    </div>
    <div>
      <Link href="/" :title="t('common.button.goToHomeTitle')" class="text-lg font-medium md:text-3xl">VOGUEIFY</Link>
    </div>
    <div class="flex gap-4">
      <div v-if="user?.role === 'admin' || user?.role === 'staff'" class="locale-changer">
        <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
          class="py-0 cursor-pointer border-none text-sm">
          <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
            capitalize(locale) }}
          </option>
        </select>
      </div>
      <div v-if="user?.role === 'customer'" class="relative">
        <Link :href="route('wishlist.index')" :title="t('common.button.goToUserWishlistTitle')">
          <PhHeart :size="24" />
          <span v-if="wishlist.length > 0"
            class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">{{
              wishlist.length < 9 ? wishlist.length : '+9' }}</span>
        </Link>
      </div>
      <div class="relative">
        <Link v-if="!user" :href="route('login')" :title="t('common.button.goToLoginTitle')">
          <PhUser :size="24" />
        </Link>
        <button v-else ref="userButton" @click.stop="toggleUserMenu" :title="t('common.button.openUserMenu')">
          <PhUser :size="24" />
        </button>
        <div ref="userMenu" class="bg-white w-40 border border-slate-300 flex-col py-2 absolute right-0 z-10"
          :class="{ 'flex': isUserMenuOpen, 'hidden': !isUserMenuOpen }">
          <Link
            :href="user?.role === 'admin' ? route('admin.overview') : user?.role === 'staff' ? route('admin.products') : route('dashboard')"
            :title="user?.role === 'admin' ? t('common.button.goToOverviewTitle') : user?.role === 'staff' ? t('common.button.goToProductsTitle') : t('common.button.goToProfileTitle')"
            class="py-2 px-4 text-sm w-full transition-all hover:bg-slate-200">
            {{ t('common.header.contextMenu.dashboard') }}</Link>
          <div class="border-t border-gray-200" />
          <Link v-if="user" :href="route('logout')" :title="t('common.button.logoutTitle')" method="post"
            class="py-2 px-4 text-start text-sm w-full transition-all hover:bg-slate-200">
            {{ t('common.header.contextMenu.logOut') }}</Link>
        </div>
      </div>
      <Link :href="route('cart.index')" v-if="user?.role === 'customer'" :title="t('common.button.goToCartTitle')"
        class="relative">
        <PhBag :size="24" />
        <span v-if="cart.length > 0"
          class="absolute -right-2 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-black text-xs text-white">{{
            cart.length }}</span>
      </Link>
    </div>
  </header>
</template>
