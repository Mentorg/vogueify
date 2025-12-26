<script setup>
import { defineProps, ref, onMounted, onBeforeUnmount } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import {
  PhList,
  PhUser
} from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  isMobile: Boolean,
  toggleMenu: Function
});

const { t } = useI18n();
const user = usePage().props.auth.user;
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
    !userButton.value.contains(event.target)) {
    closeUserMenu();
  }
}

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  document.removeEventListener('click', handleClickOutside);
})

</script>

<template>
  <header
    class="flex items-center justify-between px-4 py-[8px] border-b border-b-[#D9D9D9] md:px-[20px] md:py-[16px] lg:px-[60px] lg:py-[16px]">
    <div class="flex flex-row items-center gap-6">
      <button @click="toggleMenu()" class="flex items-center gap-2 lg:hidden">
        <PhList :size="24" />
      </button>
    </div>
    <div>
      <Link href="/" class="text-lg font-medium md:text-3xl">VOGUEIFY</Link>
    </div>
    <div class="flex gap-4">
      <div v-if="!isMobile" class="locale-changer">
        <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
          class="py-0 cursor-pointer border-none text-sm">
          <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
            capitalize(locale) }}
          </option>
        </select>
      </div>
      <div class="relative">
        <Link v-if="!user" :href="route('login')">
          <PhUser :size="24" />
        </Link>
        <button ref="userButton" @click.stop="toggleUserMenu">
          <PhUser :size="24" />
        </button>
        <div ref="userMenu" class="bg-white w-40 border border-slate-300 flex-col py-2 absolute right-0 z-10"
          :class="{ 'flex': isUserMenuOpen, 'hidden': !isUserMenuOpen }">
          <Link
            :href="user?.role === 'admin' ? route('admin.overview') : user?.role === 'staff' ? route('admin.products') : route('dashboard')"
            class="py-2 px-4 text-sm w-full transition-all hover:bg-slate-200">
            {{ t('common.header.contextMenu.dashboard') }}
          </Link>
          <div class="border-t border-gray-200" />
          <Link v-if="user" :href="route('logout')" method="post"
            class="py-2 px-4 text-start text-sm w-full transition-all hover:bg-slate-200">
            {{ t('common.header.contextMenu.logOut') }}</Link>
        </div>
      </div>
    </div>
  </header>
</template>
