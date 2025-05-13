<script setup>
import { defineProps } from 'vue';
import { Head, Link } from "@inertiajs/vue3";
import categoryMen from "../../../public/images/category-men.png";
import categoryWomen from "../../../public/images/category-women.png";
import Menu from '@Components/Menu.vue'
import Footer from '@/Components/Footer.vue';

const props = defineProps({
  categories: Array,
  latestWomenBags: Array,
});

</script>

<template>

  <Head title="Home" />
  <Menu />
  <div class="hero h-dvh" />
  <main>
    <section class="container py-4 m-auto lg:py-10">
      <div class="flex flex-col py-6 gap-2 items-center">
        <h2 class="text-lg font-semibold lg:text-2xl">Explore Our Most Popular Categories</h2>
        <p class="text-sm text-slate-500 font-semibold">Discover popular seasonal prices</p>
      </div>
      <div class="grid grid-cols-1 gap-x-4 gap-y-6 md:grid-cols-2 lg:grid-cols-3 justify-items-center">
        <div v-for="category in props.categories" :key="category.id" class="flex flex-col items-center">
          <Link :href="'/products?category=' + category.name">
          <img v-if="category.image" :src="category.image" :alt="category.name">
          <h3 class="mt-2 text-center">{{ category.name.charAt(0).toUpperCase() + category.name.slice(1) }}</h3>
          </Link>
        </div>
      </div>
    </section>
    <section class="flex flex-col lg:flex-row">
      <Link href="/products?gender=men">
      <img :src="categoryMen" alt="">
      </Link>
      <Link href="/products?gender=women">
      <img :src="categoryWomen" alt="">
      </Link>
    </section>
    <section class="container py-4 m-auto lg:py-10">
      <div class="flex flex-col py-6 gap-2 items-center">
        <h2 class="text-lg font-semibold lg:text-2xl">New In: Women's Handbags</h2>
      </div>
      <div class="grid grid-cols-1 gap-x-4 gap-y-6 md:grid-cols-4 lg:grid-cols-4 justify-items-center">
        <div v-for="bag in props.latestWomenBags" :key="bag.id" class="flex flex-col items-center">
          <Link :href="route('product.show', bag.slug)">
          <img v-if="bag.product_variations && bag.product_variations.length > 0"
            :src="bag.product_variations[0]?.image" alt="Product Image" class="w-full h-auto" />
          </Link>
        </div>
      </div>
      <div class="flex justify-center mt-8">
        <Link href="/products?gender=women&category=bags&home=1"
          class="text-sm py-2 px-6 border border-black rounded-full transition-all hover:bg-black hover:text-white md:text-base">
        Shop Now
        </Link>
      </div>
    </section>
    <Footer />
  </main>
</template>
