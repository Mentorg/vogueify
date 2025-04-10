<script setup>
import { defineProps } from 'vue';
import { router } from '@inertiajs/vue3';
import { PhDotsThreeVertical, PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';
import { formatDate } from "@/utils/dateFormat.js";

const props = defineProps({
  products: Object
});
</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-full">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,3fr,2fr,2fr,3fr,3fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">
              Product
            </th>
            <th scope="col" class="px-6 py-4">
              Price
            </th>
            <th scope="col" class="px-6 py-4">
              Stock
            </th>
            <th scope="col" class="px-6 py-4">
              SKU
            </th>
            <th scope="col" class="px-6 py-4">Created</th>
            <th scope="col" class="px-6 py-4">Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products.data" :key="product.id"
            class="grid grid-cols-[0.5fr,3fr,2fr,2fr,3fr,3fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4">{{ product.id + 1 }}</th>
            <th scope="row" class="px-6 py-4">
              {{ product.name }}
            </th>
            <td class="px-6 py-4">{{ product.product_variations[0].price }}</td>
            <td class="px-6 py-4">{{ product.product_variations[0].stock }}</td>
            <td class="px-6 py-4">{{ product.product_variations[0].sku }}</td>
            <td class="px-6 py-4">{{ formatDate(product.created_at, '.') }}</td>
            <td class="px-6 py-4 justify-self-end">
              <button>
                <PhDotsThreeVertical :size="20" />
              </button>
            </td>
          </tr>
        </tbody>
      </table>
      <nav class="bg-white sticky bottom-0 flex py-2 px-4 border-t-2 items-center justify-between text-sm"
        aria-label="Page navigation example">
        <p>
          Showing <strong>{{ products.from }}-{{ products.to }}</strong> of <strong>{{ products.total }}</strong>
        </p>
        <ul class="list-style-none flex gap-x-4 mx-2">
          <li v-if="products.prev_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(products.prev_page_url)">
              <PhCaretLeft :size="12" />
              Previous
            </button>
          </li>
          <li v-if="products.next_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(products.next_page_url)">
              Next
              <PhCaretRight :size="12" />
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
