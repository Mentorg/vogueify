<script setup>
import { defineProps } from "vue";
import { Head } from "@inertiajs/vue3";
import Menu from '@Components/Menu.vue'
import Footer from "@/Components/Footer.vue";

const props = defineProps({
  product: Object
})

</script>

<template>

  <Head title="Products" />
  <Layout>
    <Menu />
    <main>
      <section class="grid grid-cols-1 lg:grid-cols-2">
        <img v-if="product.product_variations && product.product_variations.length > 0"
          :src="'http://vogueify.test' + product.product_variations[0].image" :alt="product.name" />
        <div class="w-[60%] m-auto">
          <div class="my-4 lg:my-8">
            <p class="text-sm">{{ product.product_variations[0].sku }}</p>
            <h2 class="text-xl mt-4 md:text-3xl">{{ product.name }}</h2>
            <h3 class="mt-2 md:text-lg">{{ product.product_variations[0].price }}</h3>
          </div>
          <button
            class="bg-black border border-black py-2 w-full rounded-full text-white transition duration-500 ease-in-out hover:bg-white hover:text-black">Place
            in Cart</button>
          <div class="my-8 lg:my-14">
            <p>{{ product.description }}</p>
            <p class="my-4 font-medium">For {{ product.gender }}</p>
            <ul class="mt-6 grid grid-cols-2">
              <li v-for="feature in product.features" :key="feature"><span class="font-medium">{{ feature.title
                  }}</span>: {{
                    feature.description }}
              </li>
            </ul>
          </div>
        </div>
      </section>
    </main>
  </Layout>
  <Footer />
</template>
