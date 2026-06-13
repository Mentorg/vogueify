<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import InputError from '@Components/InputError.vue';

const props = defineProps({
  order: Object,
  countries: Array,
  form: Object,
  submit: Function,
});

const { t } = useI18n();

</script>

<template>
  <div class="p-6">
    <div>
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderBillingDateUpdate.title', {
        orderNumber: order?.order.order_number
      }) }}</h2>
    </div>
    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid grid-cols-2 gap-6 mt-4">
        <div>
          <div>
            <InputLabel for="billing_address_line_1"
              :value="t('common.form.order.updateBillingAddress.billingAddress1')" class="mt-4" />
            <TextInput name="billing_address_line_1" id="billing_address_line_1" type="text"
              v-model="form.billing_address_line_1" class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_address_line_1" class="mt-2" />
          </div>
          <div>
            <InputLabel for="billing_city" :value="t('common.form.order.updateBillingAddress.billingCity')"
              class="mt-4" />
            <TextInput name="billing_city" id="billing_city" type="text" v-model="form.billing_city"
              class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_city" class="mt-2" />
          </div>
          <div>
            <InputLabel for="billing_state" :value="t('common.form.order.updateBillingAddress.billingState')"
              class="mt-4" />
            <TextInput name="billing_state" id="billing_state" type="text" v-model="form.billing_state"
              class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_state" class="mt-2" />
          </div>
          <div>
            <InputLabel for="billing_phone_number" :value="t('common.form.order.updateBillingAddress.billingPhone')"
              class="mt-4" />
            <TextInput name="billing_phone_number" id="billing_phone_number" type="text"
              v-model="form.billing_phone_number" class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_phone_number" class="mt-2" />
          </div>
        </div>
        <div>
          <div>
            <InputLabel for="billing_address_line_2"
              :value="t('common.form.order.updateBillingAddress.billingAddress2')" class="mt-4" />
            <TextInput name="billing_address_line_2" id="billing_address_line_2" type="text"
              v-model="form.billing_address_line_2" class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_address_line_2" class="mt-2" />
          </div>
          <div>
            <InputLabel for="billing_country_id" :value="t('common.form.order.updateBillingAddress.billingCountry')"
              class="mt-4" />
            <SelectInput name="billing_country_id" id="billing_country_id" v-model="form.billing_country_id">
              <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
            </SelectInput>
            <InputError :message="form.errors.billing_country_id" class="mt-2" />
          </div>
          <div>
            <InputLabel for="billing_postcode" :value="t('common.form.order.updateBillingAddress.billingPostcode')"
              class="mt-4" />
            <TextInput name="billing_postcode" id="billing_postcode" type="text" v-model="form.billing_postcode"
              class="mt-1 block w-full" />
            <InputError :message="form.errors.billing_postcode" class="mt-2" />
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.update')"
          :loading-text="t('common.button.updating')"
          :title="t('common.button.updateOrderBillingAddressFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
