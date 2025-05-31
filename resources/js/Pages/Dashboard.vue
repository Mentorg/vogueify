<script setup>
import { defineProps } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';

const user = usePage().props.auth.user;

const props = defineProps({
  'wishlist': Array
});
</script>

<template>
  <DashboardLayout title="Overview">
    <section class="flex flex-col">
      <div class="flex justify-center my-8">
        <h1 class="text-2xl">{{ user.name }}</h1>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="grid gap-2 bg-white p-6 w-full h-fit lg:gap-4 lg:p-10">
          <h3 class="text-xl">My Profile</h3>
          <div class="flex flex-col gap-2 my-4 lg:my-8">
            <p class="font-medium">Email: <span class="font-normal">{{ user.email }}</span></p>
            <p v-if="user.address === null" class="font-medium">No address provided</p>
            <div v-else class="flex flex-col gap-2">
              <p class="font-medium">Address 1: <span class="font-normal">{{ user.address.address_line_1 }}</span></p>
              <p class="font-medium">Address 2: <span class="font-normal">{{ user.address.address_line_2 }}</span></p>
              <p class="font-medium">Location: <span class="font-normal">{{ user.address.city }}, {{ user.address.state
                !== null ?
                user.address.state + ', ' : '' }}
                  {{
                    user.address.country.iso_code }}</span></p>
              <p class="font-medium">Postcode: <span class="font-normal">{{ user.address.postcode }}</span></p>
              <p class="font-medium">Phone Number: <span class="font-normal">{{ user.address.phone_number }}</span></p>
            </div>
          </div>
          <Link href="profile"
            class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
          Edit My Profile</Link>
        </div>
        <div class="grid gap-6">
          <div class="bg-white p-6 w-full h-fit lg:p-10">
            <h3 class="text-xl">My Orders</h3>
            <div class="my-8">
              <p>There are no current orders</p>
            </div>
            <Link href="/"
              class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
            Start Shopping</Link>
          </div>
          <div class="bg-white p-6 w-full h-fit lg:p-10">
            <h3 class="text-xl">My Wishlist</h3>
            <div v-if="wishlist.length < 1">
              <div class="my-8">
                <p>Your wishlist is empty.</p>
              </div>
              <Link href="/"
                class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
              Start Shopping</Link>
            </div>
            <div v-else class="mt-8">
              <div v-for="item in wishlist.slice(0, 3)" :key="item.product_variation.id" class="flex my-4">
                <img :src="item.product_variation.image" :alt="item.product_variation.product.name"
                  class="w-[7.5%] rounded-full" />
                <div class="flex flex-col gap-1 ml-4">
                  <h4 class="font-medium">{{ item.product_variation.product.name }}</h4>
                  <p>${{ item.product_variation.price }}</p>
                </div>
              </div>
              <div v-if="wishlist.length > 3" class="mt-8">
                <Link href="/wishlist"
                  class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-white transition-all hover:bg-white hover:text-black">
                View Wishlist</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </DashboardLayout>
</template>
