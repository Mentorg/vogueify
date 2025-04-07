<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Link } from '@inertiajs/vue3';

</script>

<template>
  <DashboardLayout title="My Orders">
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
      <div>
        <h2 class="text-xl">Current Orders</h2>
        <p class="mt-6">There are no current orders</p>
        <Link href="/"
          class="bg-black flex justify-center border border-black rounded-full py-2 mt-4 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
        Start Shopping</Link>
      </div>
      <div>
        <h2 class="text-xl">My Purchase History</h2>
        <p class="mt-6">No past orders available</p>
      </div>
    </div>
  </DashboardLayout>
</template>
