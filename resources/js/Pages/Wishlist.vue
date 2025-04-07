<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhTrash } from '@phosphor-icons/vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const props = defineProps({
  'wishlist': Array
});

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

</script>

<template>
  <DashboardLayout title="My Wishlist">
    <div v-if="wishlist.length < 1" class="flex flex-col place-self-center w-fit">
      <h2 class="text-lg md:text-xl">No current products on your wishlist</h2>
      <Link href="/"
        class="bg-black flex justify-center border border-black rounded-full py-2 mt-4 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
      Start Shopping</Link>
    </div>
    <div v-else class="grid grid-cols-4">
      <div v-for="item in wishlist" :key="item.id">
        <div class="relative">
          <img v-if="item.product.product_variations && item.product.product_variations.length > 0"
            :src="item.product.product_variations[0].image" :alt="item.product.name" />
          <form :action="route('wishlist.destroy', item.id)" method="post" class="absolute top-0 right-0 mt-2 mr-2">
            <input type="hidden" name="_token" :value="csrfToken" />
            <input type="hidden" name="_method" value="DELETE">
            <button type="submit">
              <PhTrash :size="24" />
            </button>
          </form>
          <div class="absolute bottom-0 left-0 bg-white py-1 px-3 ml-2 mb-2">
            <h2>{{ item.product.name }}</h2>
            <p v-if="item.product.product_variations && item.product.product_variations.length > 0">{{
              item.product.product_variations[0].price }}</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
