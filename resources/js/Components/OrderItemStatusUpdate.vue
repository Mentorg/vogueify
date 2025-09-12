<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toast-notification';
import OrderStatusChip from './OrderStatusChip.vue';
import InputLabel from './InputLabel.vue';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  orderItemStatusToEdit: Object,
  orderStatuses: Array,
  close: Function,
});

const { t } = useI18n();
const toast = useToast();

const form = useForm({
  order_status: props.orderItemStatusToEdit.order_status,
});

const submitForm = () => {
  form.patch(route('orderItems.updateStatus', props.orderItemStatusToEdit.id), {
    preserveScroll: true,
    onSuccess: () => {
      props.close();
      toast.open({
        message: `${t('common.toast.order.admin.orderItemStatusUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.orderItemStatusUpdate.errorMessage')}.`,
        type: 'error',
        position: 'top',
        duration: 4000
      })
    }
  })
};

</script>

<template>
  <div class="p-6">
    <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderItemStatusUpdate.title', {
      orderStatus:
        orderItemStatusToEdit?.product_variation.product.name
    }) }}</h2>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderItemStatusUpdate.content') }}:</p>
    <div class="flex">
      <OrderStatusChip :status="orderItemStatusToEdit?.order_status" class="rounded-md text-sm">
        {{ capitalize(orderItemStatusToEdit?.order_status) }}
      </OrderStatusChip>
    </div>
    <form @submit.prevent="submitForm">
      <InputLabel for="order_status" :value="t('common.modal.order.admin.orderItemStatusUpdate.itemStatus')"
        class="mt-4" />
      <select id="order_status" v-model="form.order_status"
        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring focus:ring-opacity-50">
        <option v-for="status in orderStatuses" :key="status" :value="status">
          {{ capitalize(status) }}
        </option>
      </select>
      <button
        class="flex my-4 py-1 px-4 rounded-md bg-black border border-black text-white transition-all hover:bg-white hover:text-black">{{
          t('common.button.update') }}</button>
    </form>
  </div>
</template>
