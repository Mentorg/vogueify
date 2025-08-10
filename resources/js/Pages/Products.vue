<script setup>
import { defineProps, ref } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";
import { PhHeart } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Menu from '@Components/Menu.vue'
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  products: Array,
});

const { t } = useI18n();
const wishlist = usePage().props.auth.wishlist;
const localWishlist = ref([...wishlist]);

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
const productType = props.products[0].product.category.name.charAt(0).toUpperCase() + props.products[0].product.category.name.slice(1);
const productGender = props.products[0].product.gender.charAt(0).toUpperCase() + props.products[0].product.gender.slice(1);

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
            <button @click.prevent="addToWishlist(variation.id)" class="bg-black p-2 rounded-full">
              <PhHeart size="18" color="white"
                :weight="localWishlist.some(record => record.product_variation_id === variation.id) ? 'fill' : 'regular'" />
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
