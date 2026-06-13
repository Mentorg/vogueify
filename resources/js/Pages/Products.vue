<script setup>
import { computed } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { PhHeart } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue'
import Footer from "@/Layouts/Footer.vue";
import { useWishlist } from "@/composables/useWishlist";
import { capitalize } from "@/utils/capitalize";
import { getProductsOutOfStockMap } from "@/utils/product";

const props = defineProps({
  products: Array,
});

const { t } = useI18n();

const { toggleWishlist, user } = useWishlist();

const productsOutOfStock = computed(() => {
  return getProductsOutOfStockMap(props.products)
})

</script>

<template>

  <Head
    :title="t('page.products.label', { productGender: capitalize(products[0].product.gender), productType: capitalize(products[0].product.category.name) })" />
  <Menu />
  <main>
    <section class="grid grid-cols-2 md:grid-cols-4">
      <div v-for="variation in products" :key="variation.id" class="relative">
        <Link :href="route('product.show', { product: variation.product.slug, variation: variation.sku })"
          :title="t('common.button.goToProductTitle')" class="relative">
          <img :src="variation.image"
            :alt="t('page.products.productImage', { product: variation.product.name })"" class=" aspect-square
            object-cover" />
          <div v-if="productsOutOfStock[variation.id]"
            class="absolute top-0 left-0 w-full h-full bg-slate-100/35 flex justify-center items-center">
            <div class="bg-white py-4 px-8">
              <p class="text-xl">{{ t('page.product.outOfStock') }}</p>
            </div>
          </div>
          <div v-show="user.role === 'customer'" class="absolute top-0 right-0 mt-2 mr-2">
            <button
              @click.prevent="toggleWishlist(variation.id, () => variation.isInWishlist = !variation.isInWishlist)"
              :title="variation.isInWishlist ? t('common.button.removeFromWishlistTitle') : t('common.button.addToWishlistTitle')"
              class="bg-black p-2 rounded-full">
              <PhHeart size="18" color="white" :weight="variation.isInWishlist ? 'fill' : 'regular'" />
            </button>
          </div>
          <div class="absolute bottom-0 left-0 bg-white py-1 px-3 ml-2 mb-2">
            <h2 class="line-clamp-1">{{ variation.product.name }}</h2>
            <p>${{ variation.price }}</p>
          </div>
        </Link>
      </div>
    </section>
  </main>
  <Footer />
</template>
