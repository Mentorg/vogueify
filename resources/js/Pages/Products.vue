<script setup>
import { defineProps, ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import { PhHeart } from "@phosphor-icons/vue";
import Menu from '@Components/Menu.vue'
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  products: Array,
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const addToWishlist = async (productId) => {
  try {
    const response = await fetch(route('wishlist.store'), {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': csrfToken
      },
      body: JSON.stringify({ product_id: productId })
    });

    if (!response.ok) {
      throw new Error('Network response was not ok');
    }

    const data = await response.json();

    console.log(data.message);
  } catch (error) {
    console.error('Error adding to wishlist:', error);
  }
}

</script>

<template>

  <Head title="Products" />
  <Layout>
    <Menu />
    <main>
      <section class="grid grid-cols-2 md:grid-cols-4">
        <div v-for="product in products" :key="product.id" class="relative">
          <Link :href="route('product.show', product.id)" class="relative">
          <img v-if="product.product_variations && product.product_variations.length > 0"
            :src="product.product_variations[0].image" :alt="product.name" />
          <div class="absolute top-0 right-0 mt-2 mr-2">
            <button @click.prevent="addToWishlist(product.id)">
              <PhHeart size="18" />
            </button>
          </div>
          <div class="absolute bottom-0 left-0 bg-white py-1 px-3 ml-2 mb-2">
            <h2 class="line-clamp-1">{{ product.name }}</h2>
            <p v-if="product.product_variations && product.product_variations.length > 0">{{
              product.product_variations[0].price }}</p>
          </div>
          </Link>
        </div>
      </section>
    </main>
  </Layout>
  <Footer />
</template>
