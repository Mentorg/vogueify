<script setup>
import { defineProps } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toast-notification';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';

const props = defineProps({
  orderItemShippingDateToEdit: Object,
  close: Function,
});

const { t } = useI18n();
const toast = useToast();

const form = useForm({
  shipping_date: props.orderItemShippingDateToEdit.shipping_date,
});

const submitForm = () => {
  form.patch(route('orderItems.updateShippingDate', props.orderItemShippingDateToEdit.id), {
    preserveScroll: true,
    onSuccess: () => {
      props.close();
      toast.open({
        message: `${t('common.toast.order.admin.orderItemShippingDateUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.orderItemShippingDateUpdate.errorMessage')}.`,
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
    <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderItemShippingDateUpdate.title', {
      item:
        orderItemShippingDateToEdit?.product_variation.product.name
    }) }}</h2>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderItemShippingDateUpdate.content') }}?</p>
    <form @submit.prevent="submitForm">
      <InputLabel for="shipping_date"
        :value="t('common.modal.order.admin.orderItemShippingDateUpdate.itemShippingDate')" class="mt-4" />
      <TextInput id="shipping_date" type="datetime-local" name="shipping_date" v-model="form.shipping_date" />
      <button
        class="flex my-4 py-1 px-4 rounded-md bg-black border border-black text-white transition-all hover:bg-white hover:text-black">{{
          t('common.button.update') }}</button>
    </form>
  </div>
</template>
