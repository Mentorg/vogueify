<script setup>
import { inject } from 'vue';
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import MultiSelectInput from '@Components/MultiSelectInput.vue';
import Checkbox from '@Components/Checkbox.vue';
import InputError from '@Components/InputError.vue';
import RadioInput from '@Components/RadioInput.vue';
import ProgressBar from '@Components/ProgressBar.vue';
import SubmitButton from '@Components/SubmitButton.vue';

const props = defineProps({
  entities: {
    type: Object,
    required: true,
  },
});

const { t } = useI18n();
const {
  form,
  isUpdateMode,
  modalTitle,
  submitText,
  loadingText,
  upsertCoupon,
} = inject('couponManager');

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ modalTitle }}</h2>
    </div>
    <form @submit.prevent="upsertCoupon">
      <div>
        <p>{{ t('common.form.coupon.couponType') }}:</p>
        <div class="flex gap-4 my-4">
          <div class="flex items-center gap-2">
            <RadioInput name="couponType" id="categories" value="categories" v-model="form.couponType" />
            <InputLabel for="categories" :value="t('common.form.coupon.category', 1)" />
            <InputError :message="form.errors.couponType" class="mt-2" />
          </div>
          <div class="flex items-center gap-2">
            <RadioInput name="couponType" id="products" value="products" v-model="form.couponType" />
            <InputLabel for="products" :value="t('common.form.coupon.product', 1)" />
            <InputError :message="form.errors.couponType" class="mt-2" />
          </div>
          <div class="flex items-center gap-2">
            <RadioInput name="couponType" id="variations" value="variations" v-model="form.couponType" />
            <InputLabel for="variations" :value="t('common.form.coupon.productVariation', 1)" />
            <InputError :message="form.errors.couponType" class="mt-2" />
          </div>
        </div>
      </div>
      <div class="flex gap-4">
        <div class="flex flex-col">
          <InputLabel for="user" :value="t('common.form.coupon.user', 2)" />
          <MultiSelectInput :entity="entities.users" entityType="users" v-model:selectedEntity="form.users" />
          <InputError :message="form.errors.users" class="mt-2" />
        </div>
        <div v-if="form.couponType === 'categories'" class="flex flex-col">
          <InputLabel for="category" :value="t('common.form.coupon.category', 2)" />
          <MultiSelectInput :entity="entities.categories" entityType="categories"
            v-model:selectedEntity="form.categories" />
          <InputError :message="form.errors.categories" class="mt-2" />
        </div>
        <div v-if="form.couponType === 'products'" class="flex flex-col">
          <InputLabel for="product" :value="t('common.form.coupon.product', 2)" />
          <MultiSelectInput :entity="entities.products" entityType="products" v-model:selectedEntity="form.products" />
          <InputError :message="form.errors.products" class="mt-2" />
        </div>
        <div v-if="form.couponType === 'variations'" class="flex flex-col">
          <InputLabel for="productVariation" :value="t('common.form.coupon.productVariation', 2)" />
          <MultiSelectInput :entity="entities.productVariations" entityType="product variations"
            v-model:selectedEntity="form.productVariations" />
          <InputError :message="form.errors.productVariations" class="mt-2" />
        </div>
      </div>
      <div>
        <InputLabel for="code" :value="t('common.form.coupon.code')" class="mt-4" />
        <TextInput name="code" id="code" type="text" v-model="form.code" class="mt-1 block w-full" />
        <InputError :message="form.errors.code" class="mt-2" />
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="type" :value="t('common.form.coupon.type')" class="mt-4" />
          <SelectInput name="type" id="type" v-model="form.type">
            <option value="percentage">{{ t('common.form.coupon.percentage') }}</option>
            <option value="fixed">{{ t('common.form.coupon.fixedAmount') }}</option>
          </SelectInput>
          <InputError :message="form.errors.type" class="mt-2" />
        </div>
        <div class="w-full">
          <InputLabel for="value" :value="t('common.form.coupon.value')" class="mt-4" />
          <TextInput name="value" id="value" type="number" v-model="form.value" min="0" class="mt-1 block w-full" />
          <InputError :message="form.errors.value" class="mt-2" />
        </div>
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="starts_at" :value="t('common.form.coupon.startsAt')" class="mt-4" />
          <TextInput name="starts_at" id="starts_at" type="datetime-local" v-model="form.starts_at"
            class="mt-1 block w-full" />
          <InputError :message="form.errors.starts_at" class="mt-2" />
        </div>
        <div class="w-full">
          <InputLabel for="expires_at" :value="t('common.form.coupon.expiresAt')" class="mt-4" />
          <TextInput name="expires_at" id="expires_at" type="datetime-local" v-model="form.expires_at"
            class="mt-1 block w-full" />
          <InputError :message="form.errors.expires_at" class="mt-2" />
        </div>
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="max_uses" :value="t('common.form.coupon.maxUses')" class="mt-4" />
          <TextInput name="max_uses" id="max_uses" type="number" v-model="form.max_uses" min="0"
            class="mt-1 block w-full" />
          <InputError :message="form.errors.max_uses" class="mt-2" />
        </div>
        <div class="w-full">
          <InputLabel for="max_uses_per_user" :value="t('common.form.coupon.maxUsesPerUser')" class="mt-4" />
          <TextInput name="max_uses_per_user" id="max_uses_per_user" type="number" v-model="form.max_uses_per_user"
            min="0" class="mt-1 block w-full" />
          <InputError :message="form.errors.max_uses_per_user" class="mt-2" />
        </div>
      </div>
      <div>
        <InputLabel for="status" :value="t('common.form.coupon.status')" class="mt-4" />
        <SelectInput name="status" id="status" v-model="form.status">
          <option value="active">{{ t('common.form.coupon.active') }}</option>
          <option value="inactive">{{ t('common.form.coupon.inactive') }}</option>
        </SelectInput>
        <InputError :message="form.errors.status" class="mt-2" />
      </div>
      <div v-if="!isUpdateMode && form.status === 'active'" class="flex items-center gap-4 mt-4">
        <Checkbox name="sendNotification" id="sendNotification" v-model="form.sendNotification" />
        <InputLabel for="sendNotification" :value="t('common.form.coupon.sendNotification')" />
        <InputError :message="form.errors.sendNotification" class="mt-2" />
      </div>
      <div class="flex justify-center mt-4">
        <ProgressBar v-if="form.progress" :percentage="form.progress.percentage" />
        <SubmitButton :processing="form.processing" :idle-text="submitText" :loading-text="loadingText"
          :title="isUpdateMode ? t('common.button.updateCouponFormSubmitTitle') : t('common.button.createCouponFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
