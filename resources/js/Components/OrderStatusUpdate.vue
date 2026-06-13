<script setup>
import { useI18n } from 'vue-i18n';
import StatusChip from '@Components/StatusChip.vue';
import InputLabel from '@Components/InputLabel.vue';
import SelectInput from '@Components/SelectInput.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  order: Object,
  orderStatuses: Array,
  form: Object,
  submit: Function,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderStatusUpdate.title', {
      orderStatus:
        order.order_number
    }) }}</h2>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderStatusUpdate.content') }}:</p>
    <div class="flex">
      <StatusChip :status="order.order_status" class="rounded-md text-sm">
        {{ capitalize(order.order_status) }}
      </StatusChip>
    </div>
    <form @submit.prevent="submit">
      <InputLabel for="order_status" :value="t('common.modal.order.admin.orderStatusUpdate.itemStatus')" class="mt-4" />
      <SelectInput name="order_status" id="order_status" v-model="form.order_status">
        <option v-for="status in orderStatuses" :key="status" :value="status">
          {{ capitalize(status) }}
        </option>
      </SelectInput>
      <div class="mt-4">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.update')"
          :loading-text="t('common.button.updating')" :title="t('common.button.updateOrderStatusFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
