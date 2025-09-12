<script setup>
import { Head, Link, useForm } from "@inertiajs/vue3";
import { PhArrowLeft } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue'
import Footer from "@/Layouts/Footer.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import ErrorMessage from "@/Components/ErrorMessage.vue";
import { useToast } from "vue-toast-notification";

const props = defineProps({
  checkoutData: Object,
  countries: Array,
  errors: Object
})

const { t } = useI18n();
const toast = useToast();

const pendingOrder = props.checkoutData.pendingOrder ?? null;
const address = props.checkoutData.address ?? {};
const cartItems = props.checkoutData.cart?.cart_items ?? [];

const getField = (field, fallback = '') => {
  return pendingOrder?.[field] || address?.[field] || fallback;
};

const orderForm = useForm({
  shipping_address_line_1: getField('shipping_address_line_1'),
  shipping_address_line_2: getField('shipping_address_line_2'),
  shipping_city: getField('shipping_city'),
  shipping_state: getField('shipping_state'),
  shipping_postcode: getField('shipping_postcode'),
  shipping_country_id: pendingOrder?.shipping_country_id ?? address?.country_id ?? '',
  shipping_phone_number: getField('shipping_phone_number'),
  billing_address_line_1: getField('billing_address_line_1'),
  billing_address_line_2: getField('billing_address_line_2'),
  billing_city: getField('billing_city'),
  billing_state: getField('billing_state'),
  billing_postcode: getField('billing_postcode'),
  billing_country_id: pendingOrder?.billing_country_id ?? address?.country_id ?? '',
  billing_phone_number: getField('billing_phone_number'),
  items: (pendingOrder?.items ?? cartItems).map(item => ({
    product_variation_id: item.product_variation_id,
    size_id: item.size_id,
    quantity: item.quantity,
    price_at_time: item.price_at_time,
  }))
});

const submitOrder = () => {
  orderForm.post(route('order.store'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('common.toast.checkout.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      })
    },
    onError: (errors) => {
      if (errors.order) {
        toast.open({
          message: `${t('common.toast.checkout.errorMessage')}!` + errors.order,
          type: 'error',
          position: 'top',
          duration: 4000,
        })
      }
    }

  });
};
</script>

<template>

  <Head :title="t('page.checkout.checkoutTitle')" />
  <Layout>
    <Menu />
    <main class="flex flex-col gap-y-4 bg-slate-100 h-full p-4 lg:p-16">
      <div>
        <Link :href="route('cart.index')">
        <PhArrowLeft :size="24" />
        </Link>
      </div>
      <div class="flex flex-col gap-y-4 lg:grid lg:grid-cols-[4fr,1.5fr] lg:gap-x-4">
        <div class="bg-white">
          <div class="flex items-center gap-2 m-4">
            <h2 class="font-medium">{{ t('page.checkout.checkoutLabel') }}</h2>
          </div>
          <span class="h-0.5 w-auto flex bg-slate-200" />
          <form @submit.prevent="submitOrder" class="flex flex-col gap-4 p-4">
            <div>
              <h3 class="font-medium my-4">{{ t('page.checkout.shippingHeading') }}</h3>
              <div class="my-2">
                <InputLabel for="shipping_address_line_1" :value="`${t('page.checkout.shippingAddress1')}`" />
                <TextInput id="shipping_address_line_1" type="text" name="shipping_address_line_1"
                  v-model="orderForm.shipping_address_line_1" class="mt-1 block w-full" />
                <ErrorMessage :message="errors.shipping_address_line_1" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_address_line_2" :value="`${t('page.checkout.shippingAddress2')}`" />
                <TextInput id="shipping_address_line_2" type="text" name="shipping_address_line_2"
                  v-model="orderForm.shipping_address_line_2" class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_city" :value="`${t('page.checkout.shippingCity')}`" />
                <TextInput id="shipping_city" type="text" name="shipping_city" v-model="orderForm.shipping_city"
                  class="mt-1 block w-full" />
                <ErrorMessage :message="errors.shipping_city" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_state" :value="`${t('page.checkout.shippingState')}`" />
                <TextInput id="shipping_state" type="text" name="shipping_state" v-model="orderForm.shipping_state"
                  class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_postcode" :value="`${t('page.checkout.shippingPostcode')}`" />
                <TextInput id="shipping_postcode" type="text" name="shipping_postcode"
                  v-model="orderForm.shipping_postcode" class="mt-1 block w-full" />
                <ErrorMessage :message="errors.shipping_postcode" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_country_id" :value="`${t('page.checkout.shippingCountry')}`" />
                <SelectInput name="shipping_country_id" id="shipping_country_id"
                  v-model="orderForm.shipping_country_id">
                  <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                </SelectInput>
                <ErrorMessage :message="errors.shipping_country_id" />
              </div>
              <div class="my-2">
                <InputLabel for="shipping_phone_number" :value="`${t('page.checkout.shippingPhone')}`" />
                <TextInput id="shipping_phone_number" type="text" name="shipping_phone_number"
                  v-model="orderForm.shipping_phone_number" class="mt-1 block w-full" />
              </div>
            </div>
            <div>
              <h3 class="font-medium">{{ t('page.checkout.billingHeading') }}</h3>
              <div class="my-2">
                <InputLabel for="billing_address_line_1" :value="`${t('page.checkout.billingAddress1')}`" />
                <TextInput id="billing_address_line_1" type="text" name="billing_address_line_1"
                  v-model="orderForm.billing_address_line_1" class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="billing_address_line_2" :value="`${t('page.checkout.billingAddress2')}`" />
                <TextInput id="billing_address_line_2" type="text" name="billing_address_line_2"
                  v-model="orderForm.billing_address_line_2" class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="billing_city" :value="`${t('page.checkout.billingCity')}`" />
                <TextInput id="billing_city" type="text" name="billing_city" v-model="orderForm.billing_city"
                  class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="billing_state" :value="`${t('page.checkout.billingState')}`" />
                <TextInput id="billing_state" type="text" name="billing_state" v-model="orderForm.billing_state"
                  class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="billing_postcode" :value="`${t('page.checkout.billingPostcode')}`" />
                <TextInput id="billing_postcode" type="text" name="billing_postcode"
                  v-model="orderForm.billing_postcode" class="mt-1 block w-full" />
              </div>
              <div class="my-2">
                <InputLabel for="billing_country_id" :value="`${t('page.checkout.billingCountry')}`" />
                <SelectInput name="billing_country_id" id="billing_country_id" v-model="orderForm.billing_country_id">
                  <option v-for="country in countries" :value="country.id">{{ country.name }}</option>
                </SelectInput>
              </div>
              <div class="my-2">
                <InputLabel for="billing_phone_number" :value="`${t('page.checkout.billingPhone')}`" />
                <TextInput id="billing_phone_number" type="text" name="billing_phone_number"
                  v-model="orderForm.billing_phone_number" class="mt-1 block w-full" />
              </div>
              <div class="flex justify-center my-4">
                <button type="submit" :disabled="orderForm.processing"
                  class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base lg:w-1/2">
                  {{ t('common.button.continue') }}</button>
              </div>
            </div>
          </form>
        </div>
        <div class="bg-white lg:h-fit">
          <div class="flex justify-between gap-2 m-4">
            <h2 class="font-medium">{{ t('page.cart.label') }} ({{ checkoutData.cart.cart_items.length }})</h2>
            <Link :href="route('cart.index')" class="text-sm underline">{{ t('page.checkout.modifySelection') }}</Link>
          </div>
          <div class="m-4">
            <div v-for="item in checkoutData.cart.cart_items" :key="item.id"
              class="grid grid-cols-[1fr,3fr] py-6 border-b">
              <div>
                <img v-if="item.product_variation.image" :src="item.product_variation.image"
                  :alt="`${item.product_variation.product.name}'s image'`">
              </div>
              <div class="flex flex-col justify-center gap-2 my-2 mx-6">
                <h3 class="text-sm">{{ item.product_variation.product.name }}</h3>
                <h4 class="text-xl">${{ item.price_at_time }}</h4>
              </div>
            </div>
          </div>
          <div class="flex p-4 w-full">
            <ul class="w-full">
              <li class="flex justify-between">
                <p>{{ t('common.product.subtotal') }}</p>
                <span>${{ checkoutData.subtotal.toFixed(2) }}</span>
              </li>
              <li class="flex justify-between mt-4">
                <p>{{ t('common.product.shipping') }}</p>
                <span>${{ checkoutData.shipping.toFixed(2) }}</span>
              </li>
              <li class="flex flex-col mt-4">
                <div class="flex justify-between">
                  <p class=":text-lg">{{ t('common.product.tax') }}</p>
                  <span class=":text-lg">${{ checkoutData.tax.toFixed(2) }}</span>
                </div>
                <p class="text-xs text-slate-500">{{ t('common.product.taxInfo') }}</p>
              </li>
              <li class="flex justify-between mt-4">
                <p>{{ t('common.product.total') }}</p>
                <span>${{ checkoutData.total.toFixed(2) }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>
      <Footer />
    </main>
  </Layout>
</template>
