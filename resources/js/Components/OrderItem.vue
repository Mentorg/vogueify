<script setup>
import { PhDotsThreeVertical } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import ContextMenu from '@Components/ContextMenu.vue';
import MenuItem from '@Components/MenuItem.vue';
import StatusChip from '@Components/StatusChip.vue';
import Modal from '@Components/Modal.vue';
import OrderItemStatusUpdate from '@Components/OrderItemStatusUpdate.vue';
import OrderItemShippingDateUpdate from '@Components/OrderItemShippingDateUpdate.vue';
import { useContextMenu } from '@/composables/useContextMenu';
import { useUpdateOrderItemStatus } from '@/composables/order/useUpdateOrderItemStatus';
import { useUpdateOrderItemShippingDate } from '@/composables/order/useUpdateOrderItemShippingDate';
import { capitalize } from '@/utils/capitalize';
import { formatShippingDate } from '@/utils/dateFormat';

const props = defineProps({
  item: Object,
  orderStatuses: Object,
});

const { t } = useI18n();

const { isContextMenuOpen, dropdownStyle, toggleContextMenu } = useContextMenu();
const {
  statusUpdateTarget,
  isUpdateOrderItemStatusModalOpen,
  updateOrderItemStatusForm,
  openUpdateOrderItemStatusModal,
  closeUpdateOrderItemStatusModal,
  updateOrderItemStatus,
} = useUpdateOrderItemStatus();
const {
  shippingDateUpdateTarget,
  isUpdateOrderItemShippingDateModalOpen,
  updateOrderItemShippingDateForm,
  openUpdateOrderItemShippingDateModal,
  closeUpdateOrderItemShippingDateModal,
  updateOrderItemShippingDate
} = useUpdateOrderItemShippingDate();

</script>

<template>
  <div class="flex gap-4 col-start-1 col-end-3 row-start-1 row-end-1 xl:col-end-1 xl:row-end-3">
    <img :src="item.product_variation.image"
      :alt="t('page.orderDetails.productImage', { product: item.product_variation.product.name })"
      class="w-24 h-24 object-cover rounded-md" />
    <div class="flex flex-col justify-between">
      <div>
        <p class="text-sm text-slate-500">{{ item.product_variation.type.label }}</p>
        <h3 class="text-lg font-medium w-`36` overflow-hidden text-ellipsis whitespace-nowrap">{{
          item.product_variation.product.name }}</h3>
      </div>
      <div class="flex items-center gap-4">
        <p v-if="item.size" class="text-slate-500 text-xs lg:text-base">{{ t('page.orderDetails.size') }} {{
          item.size?.label }}</p>
        <p v-if="item.size && item.product_variation.color">|</p>
        <div class="flex items-center gap-2">
          <div :style="item.product_variation.color?.name === 'Multicolor'
            ? { background: 'linear-gradient(135deg, #ff0000, #ffff00, #3333ff)' }
            : { backgroundColor: item.product_variation.color?.hex_code }"
            :class="{ border: item.product_variation.color?.hex_code === '#FFFFFF' }" class="rounded-sm w-4 h-4" />
          <p class="text-xs lg:text-base">{{ item.product_variation.color?.name }}</p>
        </div>
      </div>
    </div>
  </div>
  <div class="flex justify-center col-start-1 col-end-4 row-start-2 row-end-2 xl:col-start-2 xl:col-end-2">
    <p class="text-center"><span class="font-medium">{{ t('page.orderDetails.shippingDate') }}:</span> {{
      formatShippingDate(item.shipping_date, t) }}</p>
  </div>
  <div
    class="flex justify-start gap-4 col-start-1 col-end-3 row-start-3 row-end-3 h-fit xl:col-start-2 xl:col-end-2 xl:row-start-1 xl:row-end-1 xl:justify-center">
    <StatusChip :status="item.order_status" class="rounded-md text-sm">
      {{ capitalize(item.order_status) }}
    </StatusChip>
  </div>
  <div
    class="flex flex-col items-end col-start-3 col-end-3 row-start-1 row-end-1 xl:col-start-3 xl:col-end-3 xl:row-start-1 xl:row-end-1">
    <div class="relative context-order-item-menu-wrapper">
      <button @click.stop="(e) => toggleContextMenu(item.id, e)" :title="t('common.button.moreItemActionsTitle')"
        class="rounded-full p-0.5 transition-all hover:bg-slate-200">
        <PhDotsThreeVertical :size="20" />
      </button>
      <ContextMenu :state="isContextMenuOpen" :entity="item" :style="dropdownStyle">
        <MenuItem :action="() => openUpdateOrderItemStatusModal(item)" :title="t('common.button.updateStatusTitle')">
          {{ t('common.button.updateStatus') }}
        </MenuItem>
        <MenuItem :action="() => openUpdateOrderItemShippingDateModal(item)"
          :title="t(item.shipping_date ? 'common.button.updateDateTitle' : 'common.button.setDateTitle')">
          {{ t(item.shipping_date ? 'common.button.updateDate' : 'common.button.setDate') }}
        </MenuItem>
      </ContextMenu>
    </div>
  </div>
  <div
    class="flex justify-start items-center h-fit gap-4 col-start-3 col-end-3 row-start-3 row-end-3 xl:col-start-3 xl:col-end-3 xl:row-start-2 xl:row-end-2 xl:justify-end">
    <div class="py-1 px-2 border rounded-md w-fit h-fit">
      <p class="text-nowrap">{{ item.quantity }} x ${{ item.price_at_time }}</p>
    </div>
    <p class="font-medium">${{ item.price_at_time * item.quantity }}</p>
  </div>
  <Modal :show="isUpdateOrderItemStatusModalOpen" @close="closeUpdateOrderItemStatusModal">
    <OrderItemStatusUpdate :item="statusUpdateTarget" :form="updateOrderItemStatusForm" :orderStatuses="orderStatuses"
      :submit="updateOrderItemStatus" />
  </Modal>
  <Modal :show="isUpdateOrderItemShippingDateModalOpen" @close="closeUpdateOrderItemShippingDateModal">
    <OrderItemShippingDateUpdate :item="shippingDateUpdateTarget" :form="updateOrderItemShippingDateForm"
      :submit="updateOrderItemShippingDate" />
  </Modal>
</template>
