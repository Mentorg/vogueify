<script setup>
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { formatDate } from '@/utils/dateFormat';
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
  orderDetails: Object
});

const { t } = useI18n();

const statusSteps = [
  'pending',
  'paid',
  'confirmed',
  'processing',
  'shipped',
  'in-transit',
  'out-for-delivery',
  'delivered'
];

const exceptionStatusMap = {
  'attempted-delivery': 'Attempted Delivery',
  'awaiting-pickup': 'Awaiting Pickup',
  'delayed': 'Delayed',
  'held-at-customs': 'Held at Customs',
  'canceled': 'Canceled'
};

const isExceptional = computed(() => {
  if (!props.orderDetails?.order) return false;
  return Object.keys(exceptionStatusMap).includes(props.orderDetails.order.order_status);
});
const isLost = computed(() => {
  if (!props.orderDetails?.order) return false;
  return props.orderDetails.order.order_status === 'lost';
});

const dynamicBranchFromIndex = computed(() => {
  if (!props.orderDetails?.order) return -1;
  if (props.orderDetails.order.order_status === 'canceled') {
    return statusSteps.indexOf('pending');
  }
  return statusSteps.indexOf('out-for-delivery');
});

const currentIndex = computed(() => {
  if (!props.orderDetails?.order) return -1;
  const currentStatus = props.orderDetails.order.order_status;
  if (isExceptional.value || isLost.value) {
    return dynamicBranchFromIndex.value;
  }
  return statusSteps.indexOf(currentStatus);
});

</script>

<template>
  <DashboardLayout :title="`${t('page.user.orders.singleOrder.label', { order: orderDetails?.order?.order_number })}`">
    <div v-if="orderDetails && orderDetails.order"
      class="bg-white flex flex-col py-8 px-4 rounded-2xl w-auto justify-self-auto lg:justify-self-center lg:w-fit">
      <div class="flex flex-col items-center my-4 gap-2">
        <h2 class="text-xl">{{ t('page.user.orders.singleOrder.orderStatus') }}</h2>
        <p class="text-sm">{{ t('page.user.orders.singleOrder.orderDate') }}: {{
          formatDate(orderDetails.order.order_date,
            '.') }}</p>
        <p v-if="orderDetails.order.shipping_date" class="text-sm">{{ t('page.user.orders.singleOrder.shippingDate') }}:
          {{
            formatDate(orderDetails.order.shipping_date,
              '.') }}</p>
      </div>
      <ul
        class="flex flex-nowrap justify-start overflow-x-auto overflow-y-hidden mt-4 text-xs text-gray-900 font-medium sm:text-base space-x-4 relative lg:justify-center lg:w-auto">
        <li v-for="(status, index) in statusSteps" :key="status" :class="[
          'relative flex-shrink-0 flex flex-col items-center',
          index < currentIndex ? 'text-indigo-600' : '',
          index === currentIndex ? 'text-indigo-600' : ''
        ]">
          <div class="relative flex flex-col items-center z-10">
            <div :class="[
              'whitespace-nowrap text-center min-w-[80px] flex flex-col items-center',
              index !== statusSteps.length - 1 && !(index === dynamicBranchFromIndex && (isExceptional || isLost))
                ? 'after:content-[\'\'] after:absolute after:left-[70%] after:top-[20px] after:w-full sm:after:w-full after:h-0.5 after:translate-y-[-50%]'
                : '',
              index < currentIndex ? 'after:bg-indigo-600' : 'after:bg-gray-200',
            ]">
              <span :class="[
                'w-10 h-10 border-2 rounded-full flex justify-center items-center mx-auto mb-2 text-sm relative z-10',
                index < currentIndex
                  ? 'bg-indigo-600 border-transparent text-white'
                  : index === currentIndex
                    ? 'bg-indigo-600 border-indigo-600 text-indigo-600'
                    : 'bg-gray-50 border-gray-200 text-gray-400'
              ]" />
              <p class="text-[11px] sm:text-sm">
                {{status.replace(/-/g, ' ').replace(/\b\w/g, l => l.toUpperCase())}}
              </p>
            </div>
            <div v-if="index === dynamicBranchFromIndex && (isExceptional || isLost)"
              class="flex flex-col items-center mt-2">
              <div class="w-0.5 h-8 bg-gray-300" />
              <span class="w-10 h-10 rounded-full flex items-center justify-center text-xs text-white mt-1" :class="{
                'bg-red-600': isLost,
                'bg-orange-500': !isLost && orderDetails.order.order_status === 'canceled',
                'bg-yellow-500': !isLost && orderDetails.order.order_status !== 'canceled'
              }" />
              <p class="text-[10px] sm:text-xs mt-1 text-center max-w-[80px]">
                {{ isLost ? 'Lost' : exceptionStatusMap[orderDetails.order.order_status] }}
              </p>
            </div>
          </div>
        </li>
      </ul>
    </div>
    <div v-if="orderDetails && orderDetails.order" class="flex flex-col gap-8 my-8 lg:flex-row">
      <div class="bg-white border rounded-md p-4 h-fit">
        <h2 class="text-2xl border-b pb-2">{{ t('page.user.orders.singleOrder.orderProducts') }}</h2>
        <div v-for="item in orderDetails.order.items" class="flex items-center gap-4 py-2 my-2">
          <img v-if="item.product_variation.image" :src="item.product_variation.image" alt=""
            class="w-[20%] md:w-[10%] lg:w-[5%]">
          <div class="flex flex-col justify-center">
            <Link
              :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })"
              class="text-lg">{{ item.product_variation.product.name }}</Link>
            <p class="text-sm">{{ t('page.user.orders.singleOrder.quantity') }}: {{ item.quantity }}</p>
          </div>
          <p class="ml-auto">${{ item.price_at_time.toFixed(2) }}</p>
        </div>
      </div>
      <div class="flex flex-col gap-8 md:flex-row lg:flex-col">
        <div class="bg-white border rounded-md p-4 h-fit w-full lg:w-auto">
          <h2 class="text-2xl border-b pb-2">{{ t('page.user.orders.singleOrder.orderSummary') }}</h2>
          <ul class="py-8">
            <li class="flex justify-between">
              <p>{{ t('common.product.subtotal') }}</p>
              <span>${{ orderDetails.subtotal.toFixed(2) }}</span>
            </li>
            <li class="flex justify-between mt-4">
              <p>{{ t('common.product.shipping') }}</p><span>${{ orderDetails.shipping.toFixed(2) }}</span>
            </li>
            <li class="flex flex-col mt-4">
              <div class="flex justify-between">
                <p class=":text-lg">{{ t('common.product.tax') }}</p>
                <span class=":text-lg">${{ orderDetails.tax.toFixed(2) }}</span>
              </div>
              <p class="text-xs text-slate-500">{{ t('page.user.orders.singleOrder.orderInfo') }}</p>
            </li>
            <li class="flex justify-between mt-4">
              <p>{{ t('common.product.total') }}</p>
              <span>${{ orderDetails.total }}</span>
            </li>
          </ul>
        </div>
        <div class="bg-white border rounded-md p-4 h-fit w-full lg:w-auto">
          <h2 class="text-2xl border-b pb-2">{{ t('page.user.orders.singleOrder.orderAddress') }}</h2>
          <div>
            <h3 class="mt-6 font-medium">{{ t('page.user.orders.singleOrder.billingAddress') }}</h3>
            <ul class="mt-2">
              <li class="text-sm">{{ orderDetails.order.billing_address_line_1 }}</li>
              <li class="text-sm">{{ orderDetails.order.billing_city }}</li>
              <li class="text-sm">{{ orderDetails.order.billing_country }}</li>
              <li class="text-sm">{{ orderDetails.order.billing_phone_number }}</li>
              <li class="text-sm">{{ orderDetails.order.billing_postcode }}</li>
              <li v-if="orderDetails.order.billing_state" class="text-sm">{{ orderDetails.order.billing_state }}</li>
            </ul>
            <h3 class="mt-6 font-medium">{{ t('page.user.orders.singleOrder.shippingAddress') }}</h3>
            <ul class="mt-2">
              <li class="text-sm">{{ orderDetails.order.shipping_address_line_1 }}</li>
              <li class="text-sm">{{ orderDetails.order.shipping_city }}</li>
              <li class="text-sm">{{ orderDetails.order.shipping_country }}</li>
              <li class="text-sm">{{ orderDetails.order.shipping_phone_number }}</li>
              <li class="text-sm">{{ orderDetails.order.shipping_postcode }}</li>
              <li v-if="orderDetails.order.shipping_state" class="text-sm">{{ orderDetails.order.shipping_state }}</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="text-center py-8">{{ t('page.user.orders.singleOrder.loadingOrders') }}...</div>
  </DashboardLayout>
</template>
