<script setup>
import { Link } from '@inertiajs/vue3';

const currentPath = window.location.pathname;
</script>

<template>
  <aside class="text-black w-[300px] h-dvh py-8 px-2 border-r-2 border-slate-200">
    <Link :href="route('product.create')" class="flex w-full py-2 px-4 rounded-md"
      :class="{ 'bg-black text-white': currentPath === '/product/create' }">
    Create Product
    </Link>
  </aside>
</template>
