<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { PhTrash } from '@phosphor-icons/vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  'wishlist': Array
});

const { t } = useI18n();
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

</script>

<template>
  <DashboardLayout :title="t('page.user.wishlist.label')">
    <div v-if="wishlist.length < 1" class="flex flex-col place-self-center w-fit">
      <h2 class="text-lg md:text-xl">{{ t('page.user.wishlist.noWishlistItems') }}</h2>
      <Link href="/"
        class="bg-black flex justify-center border border-black rounded-full py-2 mt-4 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
      {{ t('common.button.startShopping') }}
      </Link>
    </div>
    <div v-else class="grid grid-cols-4 gap-6">
      <div v-for="item in wishlist" :key="item.id" class="relative">
        <Link
          :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })"
          class="block">
        <img :src="item.product_variation.image" :alt="item.product_variation.product.name" />
        <div class="absolute bottom-0 left-0 bg-white py-1 px-3 ml-2 mb-2">
          <h2>{{ item.product_variation.product.name }}</h2>
          <p>${{ item.product_variation.price }}</p>
        </div>
        </Link>
        <form :action="route('wishlist.destroy', item.id)" method="post" class="absolute top-0 right-0 mt-2 mr-2 z-10"
          @click.stop>
          <input type="hidden" name="_token" :value="csrfToken" />
          <input type="hidden" name="_method" value="DELETE" />
          <button type="submit" title="Remove from wishlist" class="bg-black p-2 rounded-full">
            <PhTrash :size="18" color="white" />
          </button>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>
