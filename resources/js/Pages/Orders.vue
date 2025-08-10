<script setup>
import { ref } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { PhXCircle } from '@phosphor-icons/vue';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  orders: Array
});

const { t } = useI18n();
const toast = useToast();
const form = useForm({});
const itemToCancel = ref(null);
const errorMessage = ref(null);

const formatStatus = (status) => {
  return status
    .split('-')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
};

const confirmOrderCancelation = (order) => {
  itemToCancel.value = order;
};

const closeModal = () => {
  itemToCancel.value = null;
  errorMessage.value = null;
}

const cancel = (id) => {
  if (id === null || id === undefined) {
    errorMessage.value = `${t('page.user.orders.invalidOrderId')}.`;
    return;
  }
  form.put(route('order.cancel', { order: id }), {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('page.user.orders.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      closeModal();
    },
    onError: (errors) => {
      toast.open({
        message: `${t('page.user.orders.errorMessage')}! ` + errors.error,
        type: 'error',
        position: 'top',
        duration: 4000,
      });
      errorMessage.value = errors.error || `${t('page.user.orders.errorMessage')}!`;
    }
  });
};

const activeStatuses = [
  'pending',
  'paid',
  'confirmed',
  'processing',
  'shipped',
  'in-transit',
  'out-for-delivery',
  'attempted-delivery',
  'awaiting-pickup',
  'delayed',
  'held-at-customs',
  'lost'
];

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
          <div v-for="item in orders.filter(order => activeStatuses.includes(order.order_status))"
            class="flex justify-between items-center gap-2 my-4">
            <div class="flex items-center gap-4">
              <template v-if="item.items.length > 1">
                <div class="flex -space-x-2">
                  <div v-for="(orderItem, index) in item.items.slice(0, 2)" :key="index"
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
              'bg-yellow-100': item.order_status === 'pending',
              'bg-green-100': item.order_status === 'paid',
              'bg-blue-100': item.order_status === 'confirmed',
              'bg-gray-200': item.order_status === 'processing',
              'bg-sky-100': item.order_status === 'shipped',
              'bg-sky-200': item.order_status === 'in-transit',
              'bg-emerald-100': item.order_status === 'out-for-delivery',
              'bg-yellow-200': item.order_status === 'attempted-delivery',
              'bg-gray-300': item.order_status === 'awaiting-pickup',
              'bg-orange-200': item.order_status === 'delayed',
              'bg-amber-200': item.order_status === 'held-at-customs',
              'bg-rose-200': item.order_status === 'lost'
            }" class="rounded-full py-2 px-4">
              <p class="text-sm">{{ formatStatus(item.order_status) }}</p>
            </div>
            <p>${{ item.total }}</p>
            <div v-if="item.order_status === 'pending'">
              <button @click="confirmOrderCancelation(item)" class="bg-slate-100 rounded-full p-1.5">
                <PhXCircle :size="20" />
              </button>
            </div>
            <DialogModal :show="itemToCancel !== null" @close="closeModal">
              <template #title>
                {{ t('common.modal.order.title', { order: itemToCancel?.order_number }) }}?
              </template>
              <template #content>
                {{ t('common.modal.order.content', { order: item?.order_number }) }}?
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
            class="flex justify-between items-center gap-2 my-4">
            <div class="flex items-center gap-4">
              <template v-if="item.items.length > 1">
                <div class="flex -space-x-2">
                  <div v-for="(orderItem, index) in item.items.slice(0, 2)" :key="index"
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
