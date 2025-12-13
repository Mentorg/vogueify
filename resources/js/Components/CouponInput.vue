<script setup>
import { onUnmounted } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import ErrorMessage from '@Components/ErrorMessage.vue';

const props = defineProps({
  coupon: {
    type: String,
    default: null,
  },
  close: Function,
});

const toast = useToast();
const { t } = useI18n();

const form = useForm({
  code: props.cart?.coupon || '',
});

const resetCouponForm = () => {
  form.reset();
  form.clearErrors();
};

const submitForm = () => {
  form.post(route('coupon.apply'), {
    preserveScroll: true,
    onSuccess: () => {
      resetCouponForm();
      props.close();
      toast.open({
        message: `${t('common.toast.coupon.couponApply.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.coupon.couponApply.errorMessage')}!`,
        type: 'error',
        position: 'top',
        duration: 4000
      })
    }
  })
}

onUnmounted(() => {
  resetCouponForm();
});

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.coupon.couponApply.title') }}</h2>
    </div>
    <form @submit.prevent="submitForm">
      <div>
        <InputLabel for="code" :value="t('common.modal.coupon.couponApply.couponCode')" class="mt-4" />
        <TextInput id="code" type="text" name="code" v-model="form.code" />
        <ErrorMessage :message="form.errors.code || form.errors.coupon" />
      </div>
      <div>
        <button :disabled="!form.code" type="submit" :title="t('common.button.apply')"
          class="bg-black text-white px-4 py-2 mt-4 rounded hover:bg-slate-600 transition">
          {{ t('common.button.apply') }}
        </button>
      </div>
    </form>
  </div>
</template>
