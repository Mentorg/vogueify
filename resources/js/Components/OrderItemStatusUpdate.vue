<script setup>
import { useI18n } from 'vue-i18n';
import StatusChip from '@Components/StatusChip.vue';
import InputLabel from '@Components/InputLabel.vue';
import SelectInput from '@Components/SelectInput.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  item: Object,
  orderStatuses: Array,
  submit: Function,
  form: Object,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderItemStatusUpdate.title', {
      orderStatus:
        item?.product_variation.product.name
    }) }}</h2>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderItemStatusUpdate.content') }}:</p>
    <div class="flex">
      <StatusChip :status="item?.order_status" class="rounded-md text-sm">
        {{ capitalize(item?.order_status) }}
      </StatusChip>
    </div>
    <form @submit.prevent="submit">
      <InputLabel for="order_status" :value="t('common.modal.order.admin.orderItemStatusUpdate.itemStatus')"
        class="mt-4" />
      <SelectInput name="order_status" id="order_status" v-model="form.order_status">
        <option v-for="status in orderStatuses" :key="status" :value="status">
          {{ capitalize(status) }}
        </option>
      </SelectInput>
      <div class="mt-4">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.update')"
          :loading-text="t('common.button.updating')"
          :title="t('common.button.updateOrderItemStatusFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
