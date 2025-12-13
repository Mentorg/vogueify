<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import MultiSelectInput from '@Components/MultiSelectInput.vue';
import Checkbox from '@Components/Checkbox.vue';
import ErrorMessage from '@Components/ErrorMessage.vue';
import { toDatetimeLocalFormat } from '@/utils/dateFormat';

const props = defineProps({
  isCouponModalOpen: Boolean,
  entities: Object,
  coupon: Object,
  formType: String,
  close: Function,
});

const categories = props.entities.categories;
const products = props.entities.products;
const productVariations = props.entities.productVariations;
const users = props.entities.users;

const toast = useToast();
const { t } = useI18n();

const form = useForm({
  couponType: props.formType === 'update' ? props.coupon.couponType : 'categories',
  code: props.formType === 'update' ? props.coupon.code : '',
  type: props.formType === 'update' ? props.coupon.type : '',
  value: props.formType === 'update' ? props.coupon.value : '',
  starts_at: props.formType === 'update' ? toDatetimeLocalFormat(props.coupon.starts_at) : '',
  expires_at: props.formType === 'update' ? toDatetimeLocalFormat(props.coupon.expires_at) : '',
  status: props.formType === 'update' ? props.coupon.status : 'active',
  max_uses: props.formType === 'update' ? props.coupon.max_uses : '',
  max_uses_per_user: props.formType === 'update' ? props.coupon.max_uses_per_user : '',
  sendNotification: false,
  categories: props.formType === 'update' ? props.coupon.categories.map(category => category) : [],
  products: props.formType === 'update' ? props.coupon.products.map(product => product) : [],
  productVariations: props.formType === 'update' ? props.coupon.product_variations.map(variation => variation) : [],
  users: props.formType === 'update' ? props.coupon.users.map(user => user) : [],
});

const submitForm = () => {
  form.categories = form.categories.map(category => category.id);
  form.products = form.products.map(product => product.id);
  form.productVariations = form.productVariations.map(variation => variation.id);
  form.users = form.users.map(user => user.id);

  if (props.formType === 'update') {
    form.put(route('coupon.update', props.coupon), {
      data: { ...form },
      preserveScroll: true,
      onSuccess: () => {
        props.close();
        toast.open({
          message: `${t('common.toast.coupon.couponUpdate.successMessage')}.`,
          type: 'success',
          position: 'top',
          duration: 4000
        });
      },
      onError: () => {
        toast.open({
          message: `${t('common.toast.coupon.couponUpdate.errorMessage')}.`,
          type: 'error',
          position: 'top',
          duration: 4000
        });
      }
    });
  } else {
    form.post(route('coupon.store'), {
      data: { ...form },
      preserveScroll: true,
      onSuccess: () => {
        props.close();
        toast.open({
          message: `${t('common.toast.coupon.couponCreate.successMessage')}.`,
          type: 'success',
          position: 'top',
          duration: 4000
        });
      },
      onError: () => {
        toast.open({
          message: `${t('common.toast.coupon.couponCreate.errorMessage')}!`,
          type: 'error',
          position: 'top',
          duration: 4000
        });
      }
    });
  }
};

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ formType === 'create' ? t('page.admin.createCoupon') :
        t('page.admin.updateCoupon', { coupon: coupon.code }) }}</h2>
    </div>
    <form @submit.prevent="submitForm">
      <div>
        <p>{{ t('common.form.coupon.couponType') }}:</p>
        <div class="flex gap-4 my-4">
          <div class="flex items-center gap-2">
            <input type="radio" name="couponType" id="categories" value="categories" v-model="form.couponType" />
            <InputLabel for="categories" :value="t('common.form.coupon.category', 1)" />
          </div>
          <div class="flex items-center gap-2">
            <input type="radio" name="couponType" id="products" value="products" v-model="form.couponType" />
            <InputLabel for="products" :value="t('common.form.coupon.product', 1)" />
          </div>
          <div class="flex items-center gap-2">
            <input type="radio" name="couponType" id="variations" value="variations" v-model="form.couponType" />
            <InputLabel for="variations" :value="t('common.form.coupon.productVariation', 1)" />
          </div>
        </div>
      </div>
      <div class="flex gap-4">
        <div class="flex flex-col">
          <label>{{ t('common.form.coupon.user', 2) }}</label>
          <MultiSelectInput :entity="users" entityType="users" v-model:selectedEntity="form.users" />
        </div>
        <div v-if="form.couponType === 'categories'" class="flex flex-col">
          <label>{{ t('common.form.coupon.category', 2) }}</label>
          <MultiSelectInput :entity="categories" entityType="categories" v-model:selectedEntity="form.categories" />
        </div>
        <div v-if="form.couponType === 'products'" class="flex flex-col">
          <label>{{ t('common.form.coupon.product', 2) }}</label>
          <MultiSelectInput :entity="products" entityType="products" v-model:selectedEntity="form.products" />
        </div>
        <div v-if="form.couponType === 'variations'" class="flex flex-col">
          <label>{{ t('common.form.coupon.productVariation', 2) }}</label>
          <MultiSelectInput :entity="productVariations" entityType="product variations"
            v-model:selectedEntity="form.productVariations" />
        </div>
      </div>
      <div>
        <InputLabel for="code" :value="t('common.form.coupon.code')" class="mt-4" />
        <TextInput id="code" type="text" name="code" v-model="form.code" class="mt-1 block w-full" />
        <ErrorMessage :message="form.errors.code" class="mt-2" />
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="type" :value="t('common.form.coupon.type')" class="mt-4" />
          <SelectInput name="type" id="type" v-model="form.type">
            <option value="percentage">{{ t('common.form.coupon.percentage') }}</option>
            <option value="fixed">{{ t('common.form.coupon.fixedAmount') }}</option>
          </SelectInput>
          <ErrorMessage :message="form.errors.type" class="mt-2" />
        </div>
        <div class="w-full">
          <InputLabel for="value" :value="t('common.form.coupon.value')" class="mt-4" />
          <TextInput id="value" type="number" name="value" v-model="form.value" min="0" class="mt-1 block w-full" />
          <ErrorMessage :message="form.errors.value" class="mt-2" />
        </div>
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="starts_at" :value="t('common.form.coupon.startsAt')" class="mt-4" />
          <TextInput id="starts_at" type="datetime-local" name="starts_at" v-model="form.starts_at"
            class="mt-1 block w-full" />
          <ErrorMessage :message="form.errors.starts_at" class="mt-2" />
        </div>
        <div class="w-full">
          <InputLabel for="expires_at" :value="t('common.form.coupon.expiresAt')" class="mt-4" />
          <TextInput id="expires_at" type="datetime-local" name="expires_at" v-model="form.expires_at"
            class="mt-1 block w-full" />
          <ErrorMessage :message="form.errors.expires_at" class="mt-2" />
        </div>
      </div>
      <div class="flex flex-col gap-4 md:flex-row">
        <div class="w-full">
          <InputLabel for="max_uses" :value="t('common.form.coupon.maxUses')" class="mt-4" />
          <TextInput id="max_uses" type="number" name="max_uses" v-model="form.max_uses" min="0"
            class="mt-1 block w-full" />
        </div>
        <div class="w-full">
          <InputLabel for="max_uses_per_user" :value="t('common.form.coupon.maxUsesPerUser')" class="mt-4" />
          <TextInput id="max_uses_per_user" type="number" name="max_uses_per_user" v-model="form.max_uses_per_user"
            min="0" class="mt-1 block w-full" />
        </div>
      </div>
      <div>
        <InputLabel for="status" :value="t('common.form.coupon.status')" class="mt-4" />
        <SelectInput name="status" id="status" v-model="form.status">
          <option value="active">{{ t('common.form.coupon.active') }}</option>
          <option value="inactive">{{ t('common.form.coupon.inactive') }}</option>
        </SelectInput>
        <ErrorMessage :message="form.errors.status" class="mt-2" />
      </div>
      <div v-if="formType === 'create' && form.status === 'active'" class="flex items-center gap-4 mt-4">
        <Checkbox id="sendNotification" v-model="form.sendNotification" />
        <InputLabel for="sendNotification" :value="t('common.form.coupon.sendNotification')" />
      </div>
      <div class="flex justify-center mt-4">
        <button @click="isCouponModalOpen = null"
          :title="formType === 'create' ? t('common.button.create') : t('common.button.update')"
          class="bg-black text-white px-4 py-2 rounded hover:bg-slate-600 transition">{{
            formType === 'create' ? t('common.button.create') : t('common.button.update') }}</button>
      </div>
    </form>
  </div>
</template>
