<script setup>
import { ref, defineProps } from 'vue';
import { Head, Link } from "@inertiajs/vue3";
import {
  PhX,
  PhHeart,
  PhMagnifyingGlass,
} from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import useWishlist from '@/composables/useWishlist';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  categories: Array,
  womenSeasonalBags: Array,
  menSeasonalBags: Array
})

const { t } = useI18n();
const query = ref('');

const { toggleWishlist } = useWishlist();

const onInput = (e) => {
  query.value = e.target.value;
}

const goBack = () => {
  window.history.back();
}

</script>

<template>

  <Head :title="t('page.search.label')" />
  <div class="bg-white fixed top-0 left-0 w-full h-full z-50">
    <div class="flex justify-between px-4 py-[8px] md:px-[60px] md:py-[17px]">
      <div></div>
      <div>
        <Link href="/" class="text-lg font-medium md:text-3xl">VOGUEIFY</Link>
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
        <li v-for="category in categories" :key="category.id" class="text-xs">{{ capitalize(category.name) }}</li>
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
              <button @click.prevent="toggleWishlist(product.id, () => product.isInWishlist = !product.isInWishlist)"
                class="bg-black p-2 rounded-full">
                <PhHeart size="18" color="white" :weight="product.isInWishlist ? 'fill' : 'regular'" />
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
              <button @click.prevent="toggleWishlist(product.id, () => product.isInWishlist = !product.isInWishlist)"
                class="bg-black p-2 rounded-full">
                <PhHeart size="18" color="white" :weight="product.isInWishlist ? 'fill' : 'regular'" />
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
