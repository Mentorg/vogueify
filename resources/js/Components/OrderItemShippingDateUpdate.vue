<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import SubmitButton from '@Components/SubmitButton.vue';

const props = defineProps({
  item: Object,
  submit: Function,
  form: Object
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderItemShippingDateUpdate.title', {
      item:
        item?.product_variation.product.name
    }) }}</h2>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderItemShippingDateUpdate.content') }}?</p>
    <form @submit.prevent="submit">
      <InputLabel for="shipping_date"
        :value="t('common.modal.order.admin.orderItemShippingDateUpdate.itemShippingDate')" class="mt-4" />
      <TextInput name="shipping_date" id="shipping_date" type="datetime-local" v-model="form.shipping_date" />
      <div class="mt-4">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.update')"
          :loading-text="t('common.button.updating')"
          :title="t('common.button.updateOrderItemShippingDateFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
