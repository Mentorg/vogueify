<script setup>
import { defineProps } from 'vue';
import {
  PhX,
} from "@phosphor-icons/vue";
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  toggleMenu: Function,
  isMenuOpen: Boolean,
  closeMenu: Function
})

const { t } = useI18n();
const user = usePage().props.auth.user;
</script>

<template>
  <div v-if="isMenuOpen"
    class="absolute z-20 left-0 top-0 w-64 h-full bg-white shadow-lg mt-2 rounded-lg px-4 py-[8px] md:px-[20px] md:py-[16px]">
    <div class="flex justify-between">
      <button @click="closeMenu">
        <PhX size="24" />
      </button>
      <div v-if="user?.role === 'admin' || user?.role === 'staff'" class="locale-changer">
        <select name="locale-changer" id="locale-changer" v-model="$i18n.locale"
          class="py-0 cursor-pointer border-none text-sm">
          <option v-for="locale in $i18n.availableLocales" :key="`locale-${locale}`" :value="locale">{{
            capitalize(locale) }}
          </option>
        </select>
      </div>
    </div>
    <ul class="ml-2">
      <li v-if="user.role === 'admin'">
        <Link href="/admin/overview" class="flex gap-y-4 w-full text-left px-4 py-2">
        {{ t('page.admin.overview') }}
        </Link>
      </li>
      <li>
        <Link href="/admin/products" class="flex gap-y-4 w-full text-left px-4 py-2">
        {{ t('page.admin.products') }}
        </Link>
      </li>
      <li>
        <Link href="/admin/product/create" class="flex gap-y-4 w-full text-left px-4 py-2">
        {{ t('page.admin.createProduct') }}
        </Link>
      </li>
      <li v-if="user.role === 'admin'">
        <Link href="/admin/users" class="flex gap-y-4 w-full text-left px-4 py-2">
        {{ t('page.admin.users') }}
        </Link>
      </li>
    </ul>
  </div>
</template>
