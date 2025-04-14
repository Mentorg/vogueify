<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const currentPath = window.location.pathname;
const user = usePage().props.auth.user;

</script>

<template>
  <aside class="text-black w-[300px] h-dvh py-8 px-2 border-r-2 border-slate-200">
    <ul>
      <li v-if="user.role === 'admin'">
        <Link :href="route('admin.overview')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-black hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/overview' }">Overview</Link>
      </li>
      <li>
        <Link :href="route('admin.products')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-black hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/products' }">Products</Link>
      </li>
      <li>
        <Link :href="route('product.create')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-black hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/product/create' }">
        Create Product
        </Link>
      </li>
      <li v-if="user.role === 'admin'">
        <Link :href="route('admin.users')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-black hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/users' }">Users</Link>
      </li>
    </ul>
  </aside>
</template>
