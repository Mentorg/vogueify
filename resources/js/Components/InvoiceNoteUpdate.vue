<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextareaInput from '@Components/TextareaInput.vue';
import InputError from '@Components/InputError.vue';
import SubmitButton from '@Components/SubmitButton.vue';

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
        {{
          invoice?.internal_note
            ? t('common.modal.invoice.updateInvoiceNote.title', { internalNote: invoice.invoice_number })
            : t('common.modal.invoice.addInvoiceNote.title')
        }}
      </h2>
    </div>
    <p class="text-slate-500 mb-4">{{ t('common.modal.invoice.updateInvoiceNote.content') }}?</p>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="internal_note" :value="t('common.modal.invoice.updateInvoiceNote.internalNote')"
          class="mt-4" />
        <TextareaInput name="internal_note" id="internal_note" v-model="form.internal_note" class="mt-2 w-full" />
        <InputError :message="form.errors.internal_note" class="mt-2" />
      </div>
      <div class="flex justify-center mt-4">
        <SubmitButton :processing="form.processing"
          :idle-text="invoice?.internal_note ? t('common.button.update') : t('common.button.add')"
          :loading-text="invoice?.internal_note ? t('common.button.updating') : t('common.button.adding')"
          :title="invoice?.internal_note ? t('common.button.updateInvoiceNoteFormSubmitTitle') : t('common.button.createInvoiceNoteFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
