<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from './SelectInput.vue';

const props = defineProps({
  order: Object,
  orderShippingAddressToEdit: Object,
  countries: Array,
  close: Function,
});

const { t } = useI18n();
const toast = useToast();

const form = useForm({
  shipping_address_line_1: props.order.order.shipping_address_line_1,
  shipping_address_line_2: props.order.order.shipping_address_line_2,
  shipping_city: props.order.order.shipping_city,
  shipping_state: props.order.order.shipping_state,
  shipping_postcode: props.order.order.shipping_postcode,
  shipping_country_id: props.order.order.shipping_country_id,
  shipping_phone_number: props.order.order.shipping_phone_number,
  shipping_date: props.order.order.shipping_date,
});

const submitForm = () => {
  form.patch(route('orders.updateShippingAddress', props.order.order), {
    preserveScroll: true,
    onSuccess: () => {
      props.close();
      toast.open({
        message: `${t('common.toast.order.admin.orderShippingDateUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.orderShippingDateUpdate.errorMessage')}!`,
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
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderShippingDateUpdate.title', {
        orderNumber: order.order.order_number
      }) }}</h2>
    </div>
    <form @submit.prevent="submitForm" class="flex flex-col gap-6">
      <div class="grid grid-cols-2 gap-6 mt-4">
        <div>
          <div>
            <InputLabel for="shipping_date" :value="t('common.form.order.updateShippingAddress.shippingDate')"
              class="mt-4" />
            <TextInput id="shipping_date" type="datetime-local" name="shipping_date" v-model="form.shipping_date"
              class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_address_line_1"
              :value="t('common.form.order.updateShippingAddress.shippingAddress1')" class="mt-4" />
            <TextInput id="shipping_address_line_1" type="text" name="shipping_address_line_1"
              v-model="form.shipping_address_line_1" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_city" :value="t('common.form.order.updateShippingAddress.shippingCity')"
              class="mt-4" />
            <TextInput id="shipping_city" type="text" name="shipping_city" v-model="form.shipping_city"
              class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_state" :value="t('common.form.order.updateShippingAddress.shippingState')"
              class="mt-4" />
            <TextInput id="shipping_state" type="text" name="shipping_state" v-model="form.shipping_state"
              class="mt-1 block w-full" />
          </div>
        </div>
        <div>
          <div>
            <InputLabel for="shipping_phone_number" :value="t('common.form.order.updateShippingAddress.shippingPhone')"
              class="mt-4" />
            <TextInput id="shipping_phone_number" type="text" name="shipping_phone_number"
              v-model="form.shipping_phone_number" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_address_line_2"
              :value="t('common.form.order.updateShippingAddress.shippingAddress2')" class="mt-4" />
            <TextInput id="shipping_address_line_2" type="text" name="shipping_address_line_2"
              v-model="form.shipping_address_line_2" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="shipping_country_id" :value="t('common.form.order.updateShippingAddress.shippingCountry')"
              class="mt-4" />
            <SelectInput name="shipping_country_id" id="shipping_country_id" v-model="form.shipping_country_id">
              <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
            </SelectInput>
          </div>
          <div>
            <InputLabel for="shipping_postcode" :value="t('common.form.order.updateShippingAddress.shippingPostcode')"
              class="mt-4" />
            <TextInput id="shipping_postcode" type="text" name="shipping_postcode" v-model="form.shipping_postcode"
              class="mt-1 block w-full" />
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <button @click="orderShippingAddressToEdit = null"
          class="bg-black text-white px-4 py-2 rounded hover:bg-slate-600 transition">{{ t('common.button.update')
          }}</button>
      </div>
    </form>
  </div>
</template>
