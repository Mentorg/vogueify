<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import InputError from '@Components/InputError.vue';
import SubmitButton from '@Components/SubmitButton.vue';

const props = defineProps({
  coupon: {
    type: String,
    default: null,
  },
  form: Object,
  submit: Function,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.coupon.couponApply.title') }}</h2>
    </div>
    <form @submit.prevent="submit">
      <div>
        <InputLabel for="code" :value="t('common.modal.coupon.couponApply.couponCode')" class="mt-4" />
        <TextInput name="code" id="code" type="text" v-model="form.code" />
        <InputError :message="form.errors.code" class="mt-2" />
      </div>
      <div class="mt-4">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.apply')"
          :loading-text="t('common.button.applying')" :title="t('common.button.applyCouponCodeTitle')" />
      </div>
    </form>
  </div>
</template>
