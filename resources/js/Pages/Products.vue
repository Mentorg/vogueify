<script setup>
import { defineProps } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { PhHeart } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue'
import Footer from "@/Layouts/Footer.vue";
import useWishlist from "@/composables/useWishlist";
import { capitalize } from "@/utils/capitalize";

const props = defineProps({
  products: Array,
});

const { t } = useI18n();
const productType = capitalize(props.products[0].product.category.name);
const productGender = capitalize(props.products[0].product.gender);

const { toggleWishlist } = useWishlist();

</script>

<template>

  <Head :title="t('page.products.label', { productGender: productGender, productType: productType })" />
  <Layout>
    <Menu />
    <main>
      <section class="grid grid-cols-2 md:grid-cols-4">
        <div v-for="variation in products" :key="variation.id" class="relative">
          <Link :href="route('product.show', { product: variation.product.slug, variation: variation.sku })"
            class="relative">
          <img :src="variation.image" :alt="variation.product.name" />
          <div class="absolute top-0 right-0 mt-2 mr-2">
            <button
              @click.prevent="toggleWishlist(variation.id, () => variation.isInWishlist = !variation.isInWishlist)"
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
  </Layout>
  <Footer />
</template>
