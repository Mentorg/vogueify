<script setup>
import { Head, Link } from "@inertiajs/vue3";
import Menu from '@Components/Menu.vue';
import { PhArrowLeft } from "@phosphor-icons/vue";
import InputLabel from "@/Components/InputLabel.vue";
import { reactive } from "vue";
import TextInput from "@/Components/TextInput.vue";
import Footer from "@/Components/Footer.vue";
import SelectInput from "@/Components/SelectInput.vue";
import axios from 'axios';
import { useI18n } from 'vue-i18n';

const props = defineProps({
  checkoutData: Object,
  countries: Array,
})

const { t } = useI18n();

const orderForm = reactive({
  shipping_address_line_1: props.checkoutData.address.address_line_1,
  shipping_address_line_2: props.checkoutData.address.address_line_2,
  shipping_city: props.checkoutData.address.city,
  shipping_state: props.checkoutData.address.state ?? null,
  shipping_postcode: props.checkoutData.address.postcode,
  shipping_country: props.countries.find((record) => record.id === props.checkoutData.address.country_id).name,
  shipping_phone_number: props.checkoutData.address.phone_number,

  billing_address_line_1: props.checkoutData.address.address_line_1,
  billing_address_line_2: props.checkoutData.address.address_line_2,
  billing_city: props.checkoutData.address.city,
  billing_state: props.checkoutData.address.state ?? null,
  billing_postcode: props.checkoutData.address.postcode,
  billing_country: props.countries.find((record) => record.id === props.checkoutData.address.country_id).name,
  billing_phone_number: props.checkoutData.address.phone_number,

  items: props.checkoutData.cart.cart_items.map(item => ({
    product_variation_id: item.product_variation_id,
    size_id: item.size_id,
    quantity: item.quantity,
    price_at_time: item.price_at_time,
  }))
});

const submitOrderForm = async () => {
  try {
    const response = await axios.post(route('order.store'), orderForm, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      }
    });

    const orderId = response.data.order_id;

    if (orderId) {
      await startPayment(orderId);
    } else {
      console.error("No order ID returned from order.store");
    }
  } catch (error) {
    console.error("Order submission failed:", error);
  }
};

const startPayment = async (orderId) => {
  try {
    const response = await axios.post(route('checkout.create'), {
      order_id: orderId
    }, {
      headers: {
        'X-Requested-With': 'XMLHttpRequest',
        'Accept': 'application/json',
      }
    });

    if (response.data.redirect_url) {
      window.location.href = response.data.redirect_url;
    } else {
      console.error("Stripe session URL not provided!");
    }
  } catch (error) {
    console.error("Stripe session creation failed:", error);
  }
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
          <form @submit.prevent="submitOrderForm" class="flex flex-col gap-4 p-4">
            <div>
              <h3 class="font-medium my-4">{{ t('page.checkout.shippingHeading') }}</h3>
              <div class="my-2">
                <InputLabel for="shipping_address_line_1" :value="`${t('page.checkout.shippingAddress1')}`" />
                <TextInput id="shipping_address_line_1" type="text" name="shipping_address_line_1"
                  v-model="orderForm.shipping_address_line_1" class="mt-1 block w-full" />
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
              </div>
              <div class="my-2">
                <InputLabel for="shipping_country" :value="`${t('page.checkout.shippingCountry')}`" />
                <SelectInput name="shipping_country" id="shipping_country" v-model="orderForm.shipping_country">
                  <option v-for="country in countries" :value="country.name">{{ country.name }}</option>
                </SelectInput>
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
                <InputLabel for="billing_country" :value="`${t('page.checkout.billingCountry')}`" />
                <SelectInput name="billing_country" id="billing_country" v-model="orderForm.billing_country">
                  <option v-for="country in countries" :value="country.name">{{ country.name }}</option>
                </SelectInput>
              </div>
              <div class="my-2">
                <InputLabel for="billing_phone_number" :value="`${t('page.checkout.billingPhone')}`" />
                <TextInput id="billing_phone_number" type="text" name="billing_phone_number"
                  v-model="orderForm.billing_phone_number" class="mt-1 block w-full" />
              </div>
              <div class="flex justify-center my-4">
                <button
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
            <div v-for="item in checkoutData.cart.cart_items" class="grid grid-cols-[1fr,3fr] py-6 border-b">
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
