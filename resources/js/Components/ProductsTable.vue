<script setup>
import { defineProps, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { PhDotsThreeVertical, PhCaretLeft, PhCaretRight, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';
import { formatDate } from "@/utils/dateFormat.js";
import DialogModal from './DialogModal.vue';
import DangerButton from './DangerButton.vue';
import SecondaryButton from './SecondaryButton.vue';

const props = defineProps({
  products: Object,
  categories: Array
});

const form = useForm({});

const openMenu = ref(null);
const productToDelete = ref(null);

const toggleMenu = (id) => {
  openMenu.value = openMenu.value === id ? null : id;
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openMenu.value = null;
  }
}

const confirmProductDeletion = (product) => {
  productToDelete.value = product;
}

const destroy = (id) => {
  form.delete(route('product.delete', id), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onFinish: () => form.reset()
  });
  console.log(id)
}

const closeModal = () => {
  productToDelete.value = null;
  form.reset()
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,4fr,2fr,2fr,3fr,3fr,3fr,1fr]">
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
            <th scope="col" class="px-6 py-4">
              Category
            </th>
            <th scope="col" class="px-6 py-4">Created</th>
            <th scope="col" class="px-6 py-4">&nbsp;</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="product in products.data" :key="product.id"
            class="grid grid-cols-[0.5fr,4fr,2fr,2fr,3fr,3fr,3fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4">{{ product.id }}</th>
            <th scope="row" class="px-6 py-4 overflow-hidden text-ellipsis whitespace-nowrap">
              {{ product.name }}
            </th>
            <td class="px-6 py-4">{{ product.product_variations[0].price }}</td>
            <td class="px-6 py-4">{{ product.product_variations[0].stock }}</td>
            <td class="px-6 py-4">{{ product.product_variations[0].sku }}</td>
            <td class="px-6 py-4">
              {{
                categories && categories.find(category => category.id === product.category_id)?.name
                  ? categories.find(category => category.id === product.category_id).name.charAt(0).toUpperCase() +
                  categories.find(category => category.id === product.category_id).name.slice(1)
                  : 'Uncategorized'
              }}
            </td>
            <td class="px-6 py-4">{{ formatDate(product.created_at, '.') }}</td>
            <td class="px-6 py-4 justify-self-end">
              <button class="relative" @click.stop="toggleMenu(product.id)">
                <PhDotsThreeVertical :size="20" />
              </button>
              <div v-if="openMenu === product.id"
                class="absolute z-10 right-6 px-4 py-4 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 flex flex-col gap-y-3 rounded-md mt-2">
                <Link :href="route('product.edit', { product: product.slug })"
                  class="flex items-center gap-x-2 transition-all hover:text-slate-500">
                <PhPencilSimple :size="16" color="green" />
                Update
                </Link>
                <div class="flex items-center gap-x-2 transition-all hover:text-slate-500">
                  <button @click="confirmProductDeletion(product)" class="flex items-center gap-2">
                    <PhTrash :size="16" color="red" />
                    Delete
                  </button>
                </div>
              </div>
            </td>
          </tr>
          <DialogModal :show="productToDelete !== null" @close="closeModal">
            <template #title>
              Delete '{{ productToDelete?.name }}'?
            </template>
            <template #content>
              Are you sure you want to delete '{{ productToDelete?.name }}'?
            </template>
            <template #footer>
              <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
              <DangerButton class="ms-3" @click="destroy(productToDelete.id)">
                <PhTrash :size="16" color="white" class="mr-2" />
                Delete
              </DangerButton>
            </template>
          </DialogModal>
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
