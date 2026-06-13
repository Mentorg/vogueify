<script setup>
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import InputError from '@Components/InputError.vue';
import SubmitButton from '@Components/SubmitButton.vue';

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
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderShippingDateUpdate.title', {
        orderNumber: order?.order.order_number
      }) }}</h2>
    </div>
    <form @submit.prevent="submit" class="flex flex-col gap-6">
      <div class="grid grid-cols-2 gap-6 mt-4">
        <div>
          <div>
            <InputLabel for="shipping_date" :value="t('common.form.order.updateShippingAddress.shippingDate')"
              class="mt-4" />
            <TextInput name="shipping_date" id="shipping_date" type="datetime-local" v-model="form.shipping_date"
              class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_address_line_1"
              :value="t('common.form.order.updateShippingAddress.shippingAddress1')" class="mt-4" />
            <TextInput name="shipping_address_line_1" id="shipping_address_line_1" type="text"
              v-model="form.shipping_address_line_1" class="mt-1 block w-full" />
            <InputError :message="form.errors.shipping_address_line_1" class="mt-2" />
          </div>
          <div>
            <InputLabel for="shipping_city" :value="t('common.form.order.updateShippingAddress.shippingCity')"
              class="mt-4" />
            <TextInput name="shipping_city" id="shipping_city" type="text" v-model="form.shipping_city"
              class="mt-1 block w-full" />
            <InputError :message="form.errors.shipping_city" class="mt-2" />
          </div>
          <div>
            <InputLabel for="shipping_state" :value="t('common.form.order.updateShippingAddress.shippingState')"
              class="mt-4" />
            <TextInput name="shipping_state" id="shipping_state" type="text" v-model="form.shipping_state"
              class="mt-1 block w-full" />
          </div>
        </div>
        <div>
          <div>
            <InputLabel for="shipping_phone_number" :value="t('common.form.order.updateShippingAddress.shippingPhone')"
              class="mt-4" />
            <TextInput name="shipping_phone_number" id="shipping_phone_number" type="text"
              v-model="form.shipping_phone_number" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_address_line_2"
              :value="t('common.form.order.updateShippingAddress.shippingAddress2')" class="mt-4" />
            <TextInput name="shipping_address_line_2" id="shipping_address_line_2" type="text"
              v-model="form.shipping_address_line_2" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_country_id" :value="t('common.form.order.updateShippingAddress.shippingCountry')"
              class="mt-4" />
            <SelectInput name="shipping_country_id" id="shipping_country_id" v-model.number="form.shipping_country_id">
              <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
            </SelectInput>
          </div>
          <div>
            <InputLabel for="shipping_postcode" :value="t('common.form.order.updateShippingAddress.shippingPostcode')"
              class="mt-4" />
            <TextInput name="shipping_postcode" id="shipping_postcode" type="text" v-model="form.shipping_postcode"
              class="mt-1 block w-full" />
            <InputError :message="form.errors.shipping_postcode" class="mt-2" />
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <SubmitButton :processing="form.processing" :idle-text="t('common.button.update')"
          :loading-text="t('common.button.updating')"
          :title="t('common.button.updateOrderShippingAddressFormSubmitTitle')" />
      </div>
    </form>
  </div>
</template>
