<script setup>
import { ref, defineProps } from 'vue';
import { Head, Link, usePage } from "@inertiajs/vue3";
import {
  PhX,
  PhHeart,
  PhMagnifyingGlass,
} from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';

const props = defineProps({
  categories: Array,
  womenSeasonalBags: Array,
  menSeasonalBags: Array
})

const { t } = useI18n();
const wishlist = usePage().props.auth.wishlist;
const query = ref('');
const localWishlist = ref([...wishlist]);

const onInput = (e) => {
  query.value = e.target.value;
}

const goBack = () => {
  window.history.back();
}

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const addToWishlist = async (productVariationId) => {
  const existing = localWishlist.value.find(item => item.product_variation_id === productVariationId);

  if (existing) {
    try {
      const response = await fetch(route('wishlist.destroy', existing.id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ _method: 'DELETE' })
      });

      if (!response.ok) throw new Error('Failed to remove from wishlist');

      localWishlist.value = localWishlist.value.filter(item => item.id !== existing.id);
    } catch (error) {
      console.error('Error removing from wishlist:', error);
    }

  } else {
    try {
      const response = await fetch(route('wishlist.store'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ product_variation_id: productVariationId })
      });

      if (!response.ok) throw new Error('Failed to add to wishlist');

      const newItem = await response.json();

      localWishlist.value.push(newItem);
    } catch (error) {
      console.error('Error adding to wishlist:', error);
    }
  }
};
</script>

<template>

  <Head :title="t('page.search.label')" />
  <div class="bg-white fixed top-0 left-0 w-full h-full z-50">
    <div class="flex justify-between px-4 py-[8px] md:px-[60px] md:py-[17px]">
      <div></div>
      <div>
        <Link href="/" class="text-lg font-medium md:text-3xl">Vogueify</Link>
      </div>
      <button @click="goBack">
        <PhX :size="24" />
      </button>
    </div>
    <div class="px-4 py-[8px] md:px-[60px] md:py-[17px]">
      <form :action="route('products.search')" method="get" class="flex gap-4">
        <input type="text" name="search" :value="query" @input="onInput" id="search"
          :placeholder="t('page.search.searchPlaceholder')" class="w-full rounded-full py-2 px-8 text-center" autofocus>
        <button type="submit" class="p-4 border border-black rounded-full">
          <PhMagnifyingGlass />
        </button>
      </form>
      <ul class="flex justify-center mt-4 gap-x-4">
        <p class="text-xs uppercase">{{ t('page.search.trendingSearches') }}</p>
        <li v-for="category in categories" :key="category.id" class="text-xs">{{ category.name.charAt(0).toUpperCase() +
          category.name.slice(1) }}</li>
      </ul>
    </div>
    <div class="overflow-y-auto max-h-[calc(100vh-200px)]">
      <div class="py-6">
        <h2 class="font-medium ml-4 my-4">{{ t('page.search.womenSeasonal') }}</h2>
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 justify-items-center">
          <div v-for="product in womenSeasonalBags" :key="product.id"
            class="parent-class relative flex flex-col items-center w-full">
            <Link :href="route('product.show', { product: product.product.slug, variation: product.sku })">
            <img :src="product?.image" :alt="product.product.name" class="w-full h-auto" />
            <div class="absolute top-0 right-0 mt-2 mr-2">
              <button @click.prevent="addToWishlist(product.id)">
                <PhHeart size="18" color="red"
                  :weight="localWishlist.some(record => record.product_variation_id === product.id) ? 'fill' : 'regular'" />
              </button>
            </div>
            <div
              class="absolute hidden bottom-0 left-0 bg-white justify-between items-center py-1 px-3 mb-2 lg:flex w-[90%] max-w-full">
              <p class="truncate flex-1 overflow-hidden text-ellipsis whitespace-nowrap">{{ product.product.name }}</p>
              <span class="mx-2">-</span>
              <p class="font-medium flex-shrink-0">${{ product.price }}</p>
            </div>
            <div class="flex flex-col py-1 px-3 w-full lg:hidden">
              <p class="truncate flex-1 overflow-hidden text-ellipsis whitespace-nowrap">{{ product.product.name }}</p>
              <p class="font-medium flex-shrink-0">${{ product.price }}</p>
            </div>
            </Link>
          </div>
        </div>
      </div>
      <div class="py-6">
        <h2 class="font-medium ml-4 my-4">{{ t('page.search.menSeasonal') }}</h2>
        <div class="grid grid-cols-2 gap-y-6 md:grid-cols-4 lg:grid-cols-6 justify-items-center">
          <div v-for="product in menSeasonalBags" :key="product.id"
            class="parent-class relative flex flex-col items-center w-full">
            <Link :href="route('product.show', { product: product.product.slug, variation: product.sku })">
            <img :src="product?.image" :alt="product.product.name" class="w-full h-auto" />
            <div class="absolute top-0 right-0 mt-2 mr-2">
              <button @click.prevent="addToWishlist(product.id)">
                <PhHeart size="18" color="red"
                  :weight="localWishlist.some(record => record.product_variation_id === product.id) ? 'fill' : 'regular'" />
              </button>
            </div>
            <div
              class="absolute hidden bottom-0 left-0 bg-white justify-between items-center py-1 px-3 mb-2 lg:flex w-[90%] max-w-full">
              <p class="truncate flex-1 overflow-hidden text-ellipsis whitespace-nowrap">{{ product.product.name }}</p>
              <span class="mx-2">-</span>
              <p class="font-medium flex-shrink-0">${{ product.price }}</p>
            </div>
            <div class="flex flex-col py-1 px-3 w-full lg:hidden">
              <p class="truncate flex-1 overflow-hidden text-ellipsis whitespace-nowrap">{{ product.product.name }}</p>
              <p class="font-medium flex-shrink-0">${{ product.price }}</p>
            </div>
            </Link>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
