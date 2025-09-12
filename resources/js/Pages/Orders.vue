<script setup>
import { Link } from '@inertiajs/vue3';
import { PhDotsThree, PhTrash, PhXCircle } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import OrderStatusChip from '@/Components/OrderStatusChip.vue';
import { formatDate } from '@/utils/dateFormat';
import { useOrder } from '@/composables/useOrder';

const props = defineProps({
  orders: Array
});

const { t } = useI18n();
const {
  cancel,
  confirmOrderCancelation,
  closeModal,
  itemToCancel,
  errorMessage,
  formatStatus,
  activeStatuses
} = useOrder();

</script>

<template>
  <DashboardLayout :title="t('page.user.orders.label')">
    <div class="grid grid-cols-1 gap-12 md:grid-cols-2">
      <div>
        <h2 class="text-xl">{{ t('page.user.orders.currentOrders') }}</h2>
        <div v-if="orders.length === 0 || !orders.some(order => activeStatuses.includes(order.order_status))">
          <div class="my-8">
            <p>{{ t('page.user.orders.noCurrentOrders') }}</p>
          </div>
          <Link href="/"
            class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
          {{ t('common.button.startShopping') }}</Link>
        </div>
        <div v-else class="mt-8">
          <div v-for="item in orders.filter(order => activeStatuses.includes(order.order_status))" :key="item.id"
            class="grid grid-cols-3 grid-rows-2 items-center gap-2 my-4 border-b border-slate-300 py-4 lg:border-none lg:justify-between lg:grid-cols-[3fr,2fr,2fr,2fr,0.5fr] lg:grid-rows-1">
            <div
              class="flex items-center gap-4 col-start-1 col-end-3 row-start-1 row-end-1 lg:col-start-1 lg:col-end-1 lg:row-start-1 lg:row-end-1">
              <template v-if="item.items.length > 1">
                <div class="flex -space-x-2">
                  <div v-for="orderItem in item.items.slice(0, 2)" :key="orderItem.id"
                    class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                    <img v-if="orderItem.product_variation.image" :src="orderItem.product_variation.image" alt=""
                      class="w-full h-full object-cover" />
                  </div>
                  <div v-if="item.items.length > 2"
                    class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-gray-600 border-2 border-white">
                    <PhDotsThree :size="16" />
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                  <img v-if="item.items[0].product_variation.image" :src="item.items[0].product_variation.image" alt=""
                    class="w-full h-full object-cover" />
                </div>
              </template>
              <Link :href="route('order.show', { order: item.id })">{{ item.order_number }}</Link>
            </div>
            <div
              class="flex col-start-1 col-end-1 row-start-2 row-end-2 md:justify-start lg:justify-center lg:col-start-2 lg:col-end-2 lg:row-start-1 lg:row-end-1">
              <p>{{ formatDate(item.order_date, '.', false) }}</p>
            </div>
            <div
              class="flex justify-center col-start-2 col-end-2 row-start-2 row-end-2 lg:col-start-3 lg:col-end-3 lg:row-start-1 lg:row-end-1">
              <OrderStatusChip :status="item.order_status">
                <p class="text-sm">{{ formatStatus(item.order_status) }}</p>
              </OrderStatusChip>
            </div>
            <div
              class="flex justify-end col-start-3 col-end-3 row-start-2 row-end-2 lg:col-start-4 lg:col-end-4 lg:row-start-1 lg:row-end-1">
              <p>${{ item.total }}</p>
            </div>
            <div
              class="flex justify-end col-start-3 col-end-3 row-start-1 row-end-1 lg:col-start-5 lg:col-end-5 lg:row-start-1 lg:row-end-1">
              <button @click="confirmOrderCancelation(item)" :disabled="item.order_status !== 'pending'"
                title="Cancel order" class="bg-slate-100 rounded-full p-1.5">
                <PhXCircle :color="item.order_status !== 'pending' ? 'gray' : 'black'" :size="20" />
              </button>
            </div>
            <DialogModal :show="itemToCancel !== null" @close="closeModal">
              <template #title>
                {{ t('common.modal.order.user.orderCancelation.title', { order: itemToCancel?.order_number }) }}?
              </template>
              <template #content>
                {{ t('common.modal.order.user.orderCancelation.content', { order: item?.order_number }) }}?
                <div v-if="errorMessage" class="text-red-500 mt-2">
                  {{ errorMessage }}
                </div>
              </template>
              <template #footer>
                <SecondaryButton @click="closeModal">{{ t('common.button.cancel') }}</SecondaryButton>
                <DangerButton class="ms-3" @click="cancel(itemToCancel?.id)">
                  <PhTrash :size="16" color="white" class="mr-2" />
                  {{ t('common.button.cancelOrder') }}
                </DangerButton>
              </template>
            </DialogModal>
          </div>
        </div>
      </div>
      <div>
        <h2 class="text-xl">{{ t('page.user.orders.purchaseHistory') }}</h2>
        <div
          v-if="orders.length === 0 || !orders.some(order => order.order_status === 'delivered' || order.order_status === 'canceled')">
          <div class="my-8">
            <p class="mt-6">{{ t('page.user.orders.noPastOrders') }}</p>
          </div>
          <Link href="/"
            class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
          {{ t('common.button.startShopping') }}</Link>
        </div>
        <div v-else class="mt-8">
          <div
            v-for="item in orders.filter(order => order.order_status === 'delivered' || order.order_status === 'canceled').slice(0, 3)"
            :key="item.id" class="flex justify-between items-center gap-2 my-4">
            <div class="flex items-center gap-4">
              <template v-if="item.items.length > 1">
                <div class="flex -space-x-2">
                  <div v-for="orderItem in item.items.slice(0, 2)" :key="orderItem.id"
                    class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                    <img v-if="orderItem.product_variation.image" :src="orderItem.product_variation.image" alt=""
                      class="w-full h-full object-cover" />
                  </div>
                  <div v-if="item.items.length > 2"
                    class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-gray-600 border-2 border-white">
                    <PhDotsThree :size="16" />
                  </div>
                </div>
              </template>
              <template v-else>
                <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                  <img v-if="item.items[0].product_variation.image" :src="item.items[0].product_variation.image" alt=""
                    class="w-full h-full object-cover" />
                </div>
              </template>
              <Link :href="route('order.show', { order: item.id })">{{ item.order_number }}</Link>
            </div>
            <p>{{ formatDate(item.order_date, '.', false) }}</p>
            <div :class="{
              'bg-green-200': item.order_status === 'delivered',
              'bg-red-100': item.order_status === 'canceled',
            }" class="rounded-full py-2 px-4">
              <p class="text-sm">{{ formatStatus(item.order_status) }}</p>
            </div>
            <p>${{ item.total }}</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
