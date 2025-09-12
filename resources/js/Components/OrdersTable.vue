<script setup>
import { defineProps, ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import { PhCaretLeft, PhCaretRight, PhDotsThreeVertical, PhPencilSimple } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import Modal from './Modal.vue';
import Tooltip from './Tooltip.vue';
import OrderStatusChip from './OrderStatusChip.vue';
import OrderStatusUpdate from './OrderStatusUpdate.vue';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';
import { useDropdown } from '@/composables/useDropdown';

const props = defineProps({
  orders: Array,
  orderStatuses: Array,
});

const {
  toggleMenu,
  isMenuOpen,
} = useDropdown();

const { t } = useI18n();
const orderStatusToEdit = ref(null);

const editOrderStatus = (order) => {
  orderStatusToEdit.value = order;
};

const closeStatusModal = () => {
  orderStatusToEdit.value = null;
};

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[2fr,2fr,2fr,2fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4 text-xs">#</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.order.datePlaced') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.order.deliveryDate') }}</th>
            <th scope="col" class="flex items-center gap-2 px-6 py-4 text-xs">{{ t('common.order.status') }}
              <Tooltip :message="t('common.order.statusDescription')" />
            </th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.order.customer') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.order.total') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.order.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <div v-if="orders.data.length > 0">
            <tr v-for="order in orders.data" :key="order.id"
              class="grid grid-cols-[2fr,2fr,2fr,2fr,2fr,2fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th class="place-content-center px-6 py-4">{{ order.order_number }}</th>
              <td class="place-content-center px-6 py-4">{{ formatDate(order.created_at, '.', true) }}</td>
              <td class="place-content-center px-6 py-4">{{ order.shipping_date ? formatDate(order.shipping_date, '.',
                true) : 'Undetermined'
              }}</td>
              <td class="place-content-center px-6 py-4 flex justify-start h-fit">
                <OrderStatusChip :status="order.order_status">{{ capitalize(order.order_status) }}</OrderStatusChip>
              </td>
              <td class="place-content-center px-6 py-4">
                <img v-if="order.user.profile_photo_url" :src="order.user.profile_photo_url" alt="User profile photo"
                  class="w-8 h-8 rounded-full inline-block mr-2">
                {{ order.user.name }}
              </td>
              <td class="place-content-center px-6 py-4">${{ order.total }}</td>
              <td class="place-content-center px-6 py-4 flex items-center context-menu-wrapper">
                <button :title="`${t('common.button.markAsTitle')}..`" @click="editOrderStatus(order)" class="me-4">
                  <PhPencilSimple :size="20" />
                </button>
                <div class="relative">
                  <button @click.stop="toggleMenu(`order-${order.id}`)" :title="t('common.button.moreActionsTitle')"
                    class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                    <PhDotsThreeVertical :size="20" />
                  </button>
                  <div v-if="isMenuOpen(`order-${order.id}`)"
                    class="absolute z-10 right-0 top-0 px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                    <Link :href="route('admin.order', { order: order.id })"
                      class="flex w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100">
                    {{ t('common.button.viewDetails') }}
                    </Link>
                  </div>
                </div>
              </td>
            </tr>
          </div>
          <div v-else class="flex place-content-center h-60 text-center py-4 text-gray-500">
            <p class="text-center py-4 text-gray-500">{{ t('common.table.noData') }}</p>
          </div>
        </tbody>
      </table>
      <Modal :show="orderStatusToEdit !== null" @close="closeStatusModal">
        <OrderStatusUpdate :orderStatusToEdit="orderStatusToEdit" :orderStatuses="props.orderStatuses"
          :close="closeStatusModal" />
      </Modal>
      <nav :class="orders.length > 0 && 'border-t-2'"
        class="bg-white sticky bottom-0 flex py-2 px-4 items-center justify-between text-sm"
        aria-label="Page navigation example">
        <p>
          {{ t('common.table.pagination', { from: orders.from, to: orders.to, total: orders.total }) }}
        </p>
        <ul class="list-style-none flex gap-x-4 mx-2">
          <li v-if="orders.first_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(orders.first_page_url)">
              {{ t('common.button.first') }}
            </button>
          </li>
          <li v-if="orders.prev_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(orders.prev_page_url)">
              <PhCaretLeft :size="12" />
              {{ t('common.button.previous') }}
            </button>
          </li>
          <li v-if="orders.next_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(orders.next_page_url)">
              {{ t('common.button.next') }}
              <PhCaretRight :size="12" />
            </button>
          </li>
          <li v-if="orders.last_page_url">
            <button class="bg-slate-500 text-white flex items-center gap-2 rounded px-3 py-1.5 text-sm"
              @click="router.visit(orders.last_page_url)">
              {{ t('common.button.last') }}
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
