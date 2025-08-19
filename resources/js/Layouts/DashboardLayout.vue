<script setup>
import { defineProps, computed } from 'vue';
import { Head, Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue';
import Footer from '@/Layouts/Footer.vue';

defineProps({
  title: String
})

const { t } = useI18n();
const isDashboard = computed(() => usePage().url === '/dashboard');
const isProfile = computed(() => usePage().url === '/profile');
const isOrders = computed(() => usePage().url === '/orders');
const isWishlist = computed(() => usePage().url === '/wishlist');

</script>

<template>

  <Head :title="title" />
  <Menu />
  <div class="bg-[#F5F5F5]">
    <nav class="bg-white flex justify-end border-b">
      <Link :href="route('dashboard')"
        class="relative border-l w-full py-4 text-sm text-center md:py-4 md:px-8 md:text-base">
      {{ t('page.user.overview') }} <span
        :class="{ 'absolute bottom-0 left-0 w-full h-[2px] bg-black': isDashboard }" />
      </Link>
      <Link :href="route('profile')"
        class="relative border-l w-full py-4 text-sm text-center md:py-4 md:px-8 md:text-base">{{
          t('page.user.profile.label') }} <span
        :class="{ 'absolute bottom-0 left-0 w-full h-[2px] bg-black': isProfile }" />
      </Link>
      <Link :href="route('order.userOrders')"
        class="relative border-l w-full py-4 text-sm text-center md:py-4 md:px-8 md:text-base">{{
          t('page.user.orders.label')
        }}
      <span :class="{ 'absolute bottom-0 left-0 w-full h-[2px] bg-black': isOrders }" />
      </Link>
      <Link :href="route('wishlist.index')"
        class="relative border-l w-full py-4 text-sm text-center md:py-4 md:px-8 md:text-base">{{
          t('page.user.wishlist.label') }} <span
        :class="{ 'absolute bottom-0 left-0 w-full h-[2px] bg-black': isWishlist }" />
      </Link>
    </nav>
    <div v-if="isDashboard" class="profile-header h-[20rem]" />
    <div v-if="!isDashboard" class="py-4 px-8 md:py-8 md:px-16">
      <h1 class="text-2xl">{{ title }}</h1>
    </div>
    <section class="py-4 px-8 min-h-96 lg:py-8 lg:px-16">
      <slot />
    </section>
  </div>
  <Footer />
</template>
