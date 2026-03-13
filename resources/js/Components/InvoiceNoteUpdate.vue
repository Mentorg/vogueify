<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from '@Components/TextareaInput.vue';

const props = defineProps({
  invoice: Object,
  form: Object,
  submit: Function,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">
        {{ invoice.internal_note
          ? t('common.modal.invoice.updateInvoiceNote.title', { internalNote: invoice.invoice_number })
          : t('common.modal.invoice.addInvoiceNote.title')
        }}
      </h2>
    </div>
    <p v-if="invoice.internal_note" class="text-slate-500 mb-4">{{ t('common.modal.invoice.updateInvoiceNote.content',
      {
        internalNote:
          invoice?.invoice_number
      }) }}?</p>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="internal_note" :value="t('common.modal.invoice.updateInvoiceNote.internalNote')"
          class="mt-4" />
        <p v-if="form.errors.internal_note" class="text-sm text-red-500">
          {{ form.errors.internal_note }}
        </p>
        <TextareaInput id="internal_note" v-model="form.internal_note" class="mt-2 w-full" />
      </div>
      <div class="flex justify-center">
        <button type="submit" class="bg-black text-white px-4 py-2 rounded hover:bg-slate-600 transition">{{
          invoice.internal_note ? t('common.button.update') : t('common.button.add') }}</button>
      </div>
    </form>
  </div>
</template>
