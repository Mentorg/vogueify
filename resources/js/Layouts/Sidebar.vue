<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const currentPath = window.location.pathname;
const user = usePage().props.auth.user;

</script>

<template>
  <aside class="text-black w-[300px] py-8 px-2 border-r-2 border-slate-200">
    <ul>
      <li v-if="user.role === 'admin'">
        <Link :href="route('admin.overview')" :title="t('page.admin.overviewTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/overview' }">{{ t('page.admin.overview') }}
        </Link>
      </li>
      <li>
        <Link :href="route('admin.products')" :title="t('page.admin.productsTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/products' }">{{ t('page.admin.products') }}
        </Link>
      </li>
      <li>
        <Link :href="route('product.create')" :title="t('page.admin.createProductTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/product/create' }">
          {{ t('page.admin.createProduct') }}
        </Link>
      </li>
      <li>
        <Link :href="route('admin.orders')" :title="t('page.admin.ordersTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/orders' }">{{ t('page.admin.orders') }}
        </Link>
      </li>
      <li>
        <Link :href="route('admin.invoices')" :title="t('page.admin.invoicesTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/invoices' }">
          {{ t('page.admin.invoices') }}
        </Link>
      </li>
      <li>
        <Link :href="route('admin.coupons')" :title="t('page.admin.couponsTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/coupons' }">
          {{ t('page.admin.coupons') }}
        </Link>
      </li>
      <li v-if="user.role === 'admin'">
        <Link :href="route('admin.users')" :title="t('page.admin.usersTitle')"
          class="flex w-full py-2 px-4 rounded-md transition-all hover:bg-zinc-800 hover:text-white"
          :class="{ 'bg-black text-white': currentPath === '/admin/users' }">{{ t('page.admin.users') }}
        </Link>
      </li>
    </ul>
  </aside>
</template>
