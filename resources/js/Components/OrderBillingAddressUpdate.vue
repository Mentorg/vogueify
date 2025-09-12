<script setup>
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from './SelectInput.vue';

const props = defineProps({
  order: Object,
  orderBillingAddressToEdit: Object,
  countries: Array,
  close: Function,
});

const { t } = useI18n();
const toast = useToast();

const form = useForm({
  billing_address_line_1: props.order.order.billing_address_line_1,
  billing_address_line_2: props.order.order.billing_address_line_2,
  billing_city: props.order.order.billing_city,
  billing_state: props.order.order.billing_state,
  billing_postcode: props.order.order.billing_postcode,
  billing_country_id: props.order.order.billing_country_id,
  billing_phone_number: props.order.order.billing_phone_number,
});

const submitForm = () => {
  form.patch(route('orders.updateBillingAddress', props.order.order), {
    preserveScroll: true,
    onSuccess: () => {
      props.close();
      toast.open({
        message: `${t('common.toast.order.admin.orderBillingDateUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.orderBillingDateUpdate.errorMessage')}!`,
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
      <h2 class="text-xl font-medium mb-4">{{ t('common.modal.order.admin.orderBillingDateUpdate.title', {
        orderNumber:
          order.order.order_number
      }) }}</h2>
    </div>
    <form @submit.prevent="submitForm" class="flex flex-col gap-6">
      <div class="grid grid-cols-2 gap-6 mt-4">
        <div>
          <div>
            <InputLabel for="billing_address_line_1"
              :value="t('common.form.order.updateBillingAddress.billingAddress1')" class="mt-4" />
            <TextInput id="billing_address_line_1" type="text" name="billing_address_line_1"
              v-model="form.billing_address_line_1" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="billing_city" :value="t('common.form.order.updateBillingAddress.billingCity')"
              class="mt-4" />
            <TextInput id="billing_city" type="text" name="billing_city" v-model="form.billing_city"
              class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="billing_state" :value="t('common.form.order.updateBillingAddress.billingState')"
              class="mt-4" />
            <TextInput id="billing_state" type="text" name="billing_state" v-model="form.billing_state"
              class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="billing_phone_number" :value="t('common.form.order.updateBillingAddress.billingPhone')"
              class="mt-4" />
            <TextInput id="billing_phone_number" type="text" name="billing_phone_number"
              v-model="form.billing_phone_number" class="mt-1 block w-full" />
          </div>
        </div>
        <div>
          <div>
            <InputLabel for="billing_address_line_2"
              :value="t('common.form.order.updateBillingAddress.billingAddress2')" class="mt-4" />
            <TextInput id="billing_address_line_2" type="text" name="billing_address_line_2"
              v-model="form.billing_address_line_2" class="mt-1 block w-full" />
          </div>
          <div>
            <InputLabel for="billing_country_id" :value="t('common.form.order.updateBillingAddress.billingCountry')"
              class="mt-4" />
            <SelectInput name="billing_country_id" id="billing_country_id" v-model="form.billing_country_id">
              <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
            </SelectInput>
          </div>
          <div>
            <InputLabel for="billing_postcode" :value="t('common.form.order.updateBillingAddress.billingPostcode')"
              class="mt-4" />
            <TextInput id="billing_postcode" type="text" name="billing_postcode" v-model="form.billing_postcode"
              class="mt-1 block w-full" />
          </div>
        </div>
      </div>
      <div class="flex justify-center">
        <button @click="orderBillingAddressToEdit = null"
          class="bg-black text-white px-4 py-2 rounded hover:bg-slate-600 transition">{{ t('common.button.update')
          }}</button>
      </div>
    </form>
  </div>
</template>
