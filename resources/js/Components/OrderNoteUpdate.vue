<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextareaInput from '@Components/TextareaInput.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import InputError from '@Components/InputError.vue';

const props = defineProps({
  order: Object,
  form: Object,
  submit: Function,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">
        {{
          order?.order.order_note
            ? t('common.modal.order.admin.orderNoteUpdate.title', { orderNumber: order?.order.order_number })
            : t('common.modal.order.admin.addOrderNote.title')
        }}
      </h2>
    </div>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderNoteUpdate.content') }}?</p>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="order_note" :value="t('common.modal.order.admin.orderNoteUpdate.orderNote')" class="mt-4" />
        <TextareaInput name="order_note" id="order_note" v-model="form.order_note" class="mt-2 w-full" />
        <InputError :message="form.errors.order_note" class="mt-2" />
      </div>
      <div class="flex justify-center mt-4">
        <SubmitButton :processing="form.processing"
          :idle-text="order?.order.order_note ? t('common.button.update') : t('common.button.add')"
          :loading-text="order?.order.order_note ? t('common.button.updating') : t('common.button.adding')"
          :title="t('common.button.updateOrderNoteFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
