<script setup>
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toast-notification';
import InputLabel from '@/Components/InputLabel.vue';
import TextareaInput from './TextareaInput.vue';

const props = defineProps({
  order: Object,
  orderNoteToEdit: Object,
  close: Function,
});

const { t } = useI18n();
const toast = useToast();

const form = useForm({
  order_note: props.order.order.order_note,
});

const submitForm = () => {
  form.patch(route('orders.updateNote', props.order.order), {
    preserveScroll: true,
    onSuccess: () => {
      props.close();
      toast.open({
        message: `${t('common.toast.order.admin.orderNoteUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.orderNoteUpdate.errorMessage')}!`,
        type: 'error',
        position: 'top',
        duration: 4000,
      })
    }
  })
}

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderNoteUpdate.title', {
        orderNumber:
          order.order.order_number
      }) }}</h2>
    </div>
    <p class="text-slate-500 mb-4">{{ t('common.modal.order.admin.orderNoteUpdate.content') }}?</p>
    <form @submit.prevent="submitForm">
      <div>
        <InputLabel for="order_note" :value="t('common.modal.order.admin.orderNoteUpdate.orderNote')" class="mt-4" />
        <TextareaInput id="order_note" v-model="form.order_note" class="mt-2 w-full" />
      </div>
      <div class="flex justify-center">
        <button @click="orderNoteToEdit = null"
          class="bg-black text-white px-4 py-2 rounded hover:bg-slate-600 transition">{{
            t('common.button.update') }}</button>
      </div>
    </form>
  </div>
</template>
