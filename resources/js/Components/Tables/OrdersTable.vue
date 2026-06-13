<script setup>
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { PhPencilSimple } from '@phosphor-icons/vue';
import Modal from '@Components/Modal.vue';
import Tooltip from '@Components/Tooltip.vue';
import StatusChip from '@Components/StatusChip.vue';
import OrderStatusUpdate from '@Components/OrderStatusUpdate.vue';
import TableFooter from '@Components/Tables/TableFooter.vue';
import { useUpdateOrderStatus } from '@/composables/order/useUpdateOrderStatus';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  orders: Array,
  orderStatuses: Array,
});

const { t } = useI18n();

const {
  orderStatusTarget,
  isUpdateOrderStatusModalOpen,
  updateOrderStatusForm,
  openOrderStatusModal,
  closeOrderStatusModal,
  updateOrderStatus
} = useUpdateOrderStatus();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto isolate">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.order.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 z-20 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,2fr,2fr,2fr,2fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4 text-xs">#</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.order.number') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.order.datePlaced') }}</th>
            <th scope="col" class="flex items-center gap-2 px-6 py-4 text-xs">{{ t('common.table.order.deliveryDate') }}
              <Tooltip :message="t('common.table.order.deliveryDateTooltip')" />
            </th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.order.status') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.order.customer') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.order.total') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <div v-if="orders.data.length > 0">
            <tr v-for="(order, index) in orders.data" :key="order.id"
              class="grid grid-cols-[0.5fr,2fr,2fr,2fr,2fr,2fr,2fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th class="place-content-center px-6 py-4">{{ (orders.current_page - 1) * orders.per_page +
                index + 1 }}</th>
              <th class="place-content-center px-6 py-4">
                <Link :href="route('admin.order', { order: order.id })" :title="t('common.button.viewOrderTitle')"
                  class="hover:underline">
                  {{ order.order_number }}
                </Link>
              </th>
              <td class="place-content-center px-6 py-4">{{ formatDate(order.created_at, '.', true) }}</td>
              <td class="place-content-center px-6 py-4">{{ order.shipping_date ? formatDate(order.shipping_date, '.',
                true) : t('common.table.undetermined')
                }}</td>
              <td class="place-content-center px-6 py-4 flex justify-start h-fit">
                <StatusChip :status="order.order_status">{{ capitalize(order.order_status) }}</StatusChip>
              </td>
              <td class="place-content-center px-6 py-4">
                <img v-if="order.user.profile_photo_url" :src="order.user.profile_photo_url"
                  :alt="t('page.user.profile.basicInfo.picture', { user: order.user.name })"
                  class="w-8 h-8 rounded-full inline-block mr-2">
                {{ order.user.name }}
              </td>
              <td class="place-content-center px-6 py-4">${{ order.total }}</td>
              <td class="place-content-center px-6 py-4 flex items-center context-menu-wrapper">
                <button @click="openOrderStatusModal(order)" :title="`${t('common.button.markOrderAsTitle')}..`"
                  class="rounded-full p-1 transition-all hover:bg-slate-200">
                  <PhPencilSimple :size="20" />
                </button>
              </td>
            </tr>
          </div>
          <div v-else class="flex place-content-center h-60 py-4">
            <p class="text-center py-4 text-gray-500">{{ t('common.table.noData') }}</p>
          </div>
        </tbody>
        <TableFooter :pagination="orders" />
      </table>
      <Modal :show="isUpdateOrderStatusModalOpen" @close="closeOrderStatusModal">
        <OrderStatusUpdate :order="orderStatusTarget" :orderStatuses="orderStatuses" :form="updateOrderStatusForm"
          :submit="updateOrderStatus" />
      </Modal>
    </div>
  </div>
</template>
