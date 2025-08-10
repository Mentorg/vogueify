<script setup>
import { defineProps, onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { PhDotsThreeVertical, PhCaretLeft, PhCaretRight, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';
import { formatDate } from "@/utils/dateFormat.js";
import DialogModal from './DialogModal.vue';
import DangerButton from './DangerButton.vue';
import SecondaryButton from './SecondaryButton.vue';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  variations: Array,
  categories: Array
});

const { t } = useI18n();
const toast = useToast();
const form = useForm({});
const openMenu = ref(null);
const variationToDelete = ref(null);
const errorMessage = ref(null);

const toggleMenu = (id) => {
  openMenu.value = openMenu.value === id ? null : id;
};

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openMenu.value = null;
  }
};

const confirmVariationDeletion = (variation) => {
  variationToDelete.value = variation;
};

const destroy = (id, type) => {
  if (id === null || id === undefined) {
    toast.open({
      message: 'Invalid variation ID! Please try again.',
      type: 'error',
      position: 'top',
      duration: 4000,
    });
    errorMessage.value = 'Invalid variation ID! Please try again.';
    return;
  }
  form.delete(route('product.delete', { id }) + `?type=${type}`, {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: 'Product deleted successfully.',
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      closeModal();
    },
    onError: (errors) => {
      toast.open({
        message: 'Failed to delete product! ' + errors.error,
        type: 'error',
        position: 'top',
        duration: 4000,
      });
      errorMessage.value = errors.error || 'Failed to delete product!';
    },
    onFinish: () => form.reset()
  });

};

const closeModal = () => {
  variationToDelete.value = null;
  errorMessage.value = null;
  form.reset();
};

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
            <th scope="col" class="px-6 py-4">{{ t('common.product.name') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.product.price') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.product.stock') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.product.sku') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.product.category') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.product.createdAt') }}</th>
            <th scope="col" class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="variation in variations.data" :key="variation.id"
            class="grid grid-cols-[0.5fr,4fr,2fr,2fr,3fr,3fr,3fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4">{{ variation.id }}</th>
            <th scope="row" class="px-6 py-4 overflow-hidden text-ellipsis whitespace-nowrap">
              {{ variation.product.name }}
            </th>
            <td class="px-6 py-4">${{ variation.price }}</td>
            <td class="px-6 py-4">
              {{variation.stock !== null ? variation.stock : variation.sizes.map(record =>
                record.pivot.stock).reduce((result, item) =>
                  result + item, 0)}}
            </td>
            <td class="px-6 py-4">{{ variation.sku }}</td>
            <td class="px-6 py-4">
              {{ variation.product.category.name.charAt(0).toUpperCase() + variation.product.category.name.slice(1) }}
            </td>
            <td class="px-6 py-4">{{ formatDate(variation.created_at, '.') }}</td>
            <td class="px-6 py-4 justify-self-end context-menu-wrapper">
              <button class="relative" @click.stop="toggleMenu(variation.id)">
                <PhDotsThreeVertical :size="20" />
              </button>
              <div v-if="openMenu === variation.id"
                class="absolute z-10 right-6 px-4 py-4 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 flex flex-col gap-y-3 rounded-md mt-2">
                <Link :href="route('product.edit', { product: variation.product.slug })"
                  class="flex items-center gap-x-2 transition-all hover:text-slate-500">
                <PhPencilSimple :size="16" color="green" />
                {{ t('common.button.update') }}
                </Link>
                <button @click="confirmVariationDeletion(variation)"
                  class="flex items-center gap-x-2 transition-all hover:text-slate-500">
                  <PhTrash :size="16" color="red" />
                  {{ t('common.button.delete') }}
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
      <DialogModal :show="variationToDelete !== null" @close="closeModal">
        <template #title>
          {{ t('common.modal.product.title', { product: variationToDelete?.product.name }) }}?
        </template>
        <template #content>
          {{ t('common.modal.product.content', { sku: variationToDelete?.sku }) }}?
          <br />
          {{ t('common.modal.product.subContent') }}.
          <div v-if="errorMessage" class="text-red-500 mt-2">
            {{ errorMessage }}
          </div>
        </template>
        <template #footer>
          <SecondaryButton @click="closeModal">{{ t('common.button.cancel') }}</SecondaryButton>
          <DangerButton class="ms-3" @click="destroy(variationToDelete?.id, 'variation')">
            <PhTrash :size="16" color="white" class="mr-2" />
            {{ t('common.button.deleteVariation') }}
          </DangerButton>
          <DangerButton class="ms-3" @click="destroy(variationToDelete?.product_id, 'product')">
            <PhTrash :size="16" color="white" class="mr-2" />
            {{ t('common.button.deleteProduct') }}
          </DangerButton>
        </template>
      </DialogModal>
      <nav class="bg-white sticky bottom-0 flex py-2 px-4 border-t-2 items-center justify-between text-sm"
        aria-label="Page navigation example">
        <p>
          {{ t('common.table.pagination', { from: variations.from, to: variations.to, total: variations.total }) }}
        </p>
        <ul class="list-style-none flex gap-x-4 mx-2">
          <li v-if="variations.first_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(variations.first_page_url)">
              {{ t('common.button.first') }}
            </button>
          </li>
          <li v-if="variations.prev_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(variations.prev_page_url)">
              <PhCaretLeft :size="12" />
              {{ t('common.button.previous') }}
            </button>
          </li>
          <li v-if="variations.next_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(variations.next_page_url)">
              {{ t('common.button.next') }}
              <PhCaretRight :size="12" />
            </button>
          </li>
          <li v-if="variations.last_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(variations.last_page_url)">
              {{ t('common.button.last') }}
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
