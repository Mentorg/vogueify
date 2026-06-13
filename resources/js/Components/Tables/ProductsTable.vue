<script setup>
import { useI18n } from 'vue-i18n';
import { PhDotsThreeVertical, PhPencilSimple, PhTrash } from '@phosphor-icons/vue';
import ContextMenu from '@Components/ContextMenu.vue';
import MenuItem from '@Components/MenuItem.vue';
import TableFooter from '@Components/Tables/TableFooter.vue';
import DialogModal from '@Components/DialogModal.vue';
import DangerButton from '@Components/DangerButton.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import { useContextMenu } from '@/composables/useContextMenu';
import { useProductDeletion } from '@/composables/product/useProductDeletion';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from "@/utils/dateFormat.js";

defineProps({
  variations: Array,
  categories: Array
});

const { t } = useI18n();
const { isContextMenuOpen, dropdownStyle, toggleContextMenu } = useContextMenu();
const {
  deletionTarget,
  isProductDeletionModalOpen,
  openProductDeletionModal,
  closeProductDeletionModal,
  deleteProduct,
  deleteVariation,
} = useProductDeletion();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto isolate">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.product.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 z-20 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,4fr,2fr,2fr,3fr,3fr,3fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.name') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.price') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.stock') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.sku') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.category') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.product.createdAt') }}</th>
            <th scope="col" class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody>
          <div v-if="variations.data.length > 0">
            <tr v-for="(variation, index) in variations.data" :key="variation.id"
              class="grid grid-cols-[0.5fr,4fr,2fr,2fr,3fr,3fr,3fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th class="px-6 py-4">{{ (variations.current_page - 1) * variations.per_page + index + 1 }}</th>
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
                {{ capitalize(variation.product.category.name) }}
              </td>
              <td class="px-6 py-4">{{ formatDate(variation.created_at, '.') }}</td>
              <td class="px-6 py-4 justify-self-end context-menu-wrapper">
                <button @click.stop="(e) => toggleContextMenu(variation.id, e)"
                  :title="t('common.button.moreActionsTitle')"
                  class="relative rounded-full p-0.5 transition-all hover:bg-slate-200">
                  <PhDotsThreeVertical :size="20" />
                </button>
                <ContextMenu :state="isContextMenuOpen" :entity="variation" :style="dropdownStyle">
                  <MenuItem :href="route('product.edit', { product: variation.product.slug })"
                    :title="t('common.button.updateProductTitle')">
                    <PhPencilSimple :size="16" color="green" />
                    {{ t('common.button.update') }}
                  </MenuItem>
                  <MenuItem :action="() => openProductDeletionModal(variation)"
                    :title="t('common.button.deleteProductTitle')">
                    <PhTrash :size="16" color="red" />
                    {{ t('common.button.delete') }}
                  </MenuItem>
                </ContextMenu>
              </td>
            </tr>
          </div>
          <div v-else class="flex place-content-center h-60 py-4">
            <p class="text-center py-4 text-gray-500">{{ t('common.table.noData') }}</p>
          </div>
        </tbody>
        <TableFooter :pagination="variations" />
      </table>
      <DialogModal :show="isProductDeletionModalOpen" @close="closeProductDeletionModal">
        <template #title>
          {{ t('common.modal.product.title', { product: deletionTarget?.product.name }) }}?
        </template>
        <template #content>
          {{ t('common.modal.product.content', { sku: deletionTarget?.sku }) }}?
          <br />
          {{ t('common.modal.product.subContent') }}.
        </template>
        <template #footer>
          <SecondaryButton @click="closeProductDeletionModal" :title="t('common.button.cancelProductDeletionTitle')">{{
            t('common.button.cancel') }}</SecondaryButton>
          <DangerButton class="ms-3" @click="deleteVariation(deletionTarget)"
            :title="t('common.button.confirmVariationDeletionTitle')">
            <PhTrash :size="16" color="white" class="mr-2" />
            {{ t('common.button.deleteVariation') }}
          </DangerButton>
          <DangerButton class="ms-3" @click="deleteProduct(deletionTarget?.product)"
            :title="t('common.button.confirmProductDeletionTitle')">
            <PhTrash :size="16" color="white" class="mr-2" />
            {{ t('common.button.deleteProduct') }}
          </DangerButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
