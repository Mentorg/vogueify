<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from 'vue-i18n';
import { PhArrowLeft, PhTrash } from "@phosphor-icons/vue";
import Menu from '@/Layouts/Menu.vue'
import Footer from "@/Layouts/Footer.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import Tooltip from "@/Components/Tooltip.vue";
import InputError from "@/Components/InputError.vue";
import ProgressBar from "@/Components/ProgressBar.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import { useRemoveCoupon } from "@/composables/coupon/useRemoveCoupon";
import { useCheckout } from "@/composables/useCheckout";

const props = defineProps({
  checkoutData: Object,
  errors: Object
})

const { t } = useI18n();

const checkoutData = props.checkoutData;
const pendingOrder = props.checkoutData.pendingOrder ?? null;
const address = props.checkoutData.address ?? {};
const cartItems = props.checkoutData.cart?.cart_items ?? [];

const { removeCouponCode } = useRemoveCoupon();
const {
  hasSubmittedOrder,
  createOrderForm,
  createOrder
} = useCheckout({
  checkoutData,
  pendingOrder,
  address,
  cartItems
});

</script>

<template>

  <Head :title="t('page.checkout.checkoutTitle')" />
  <Menu />
  <main class="flex flex-col gap-y-4 bg-slate-100 h-full p-4 lg:p-16">
    <div>
      <Link :href="route('cart.index')" :title="t('common.button.goToCartTitle')">
        <PhArrowLeft :size="24" />
      </Link>
    </div>
    <div class="flex flex-col gap-y-4 lg:grid lg:grid-cols-[4fr,1.5fr] lg:gap-x-4">
      <div class="bg-white">
        <div class="flex items-center gap-2 m-4">
          <h2 class="font-medium">{{ t('page.checkout.checkoutLabel') }}</h2>
        </div>
        <span class="h-0.5 w-auto flex bg-slate-200" />
        <form @submit.prevent="createOrder" class="flex flex-col gap-4 p-4">
          <div>
            <h3 class="font-medium my-4">{{ t('page.checkout.shippingHeading') }}</h3>
            <div class="my-2">
              <InputLabel for="shipping_address_line_1" :value="t('page.checkout.shippingAddress1')" />
              <TextInput name="shipping_address_line_1" id="shipping_address_line_1" type="text"
                v-model="createOrderForm.shipping_address_line_1" class="mt-1 block w-full" />
              <InputError :message="errors.shipping_address_line_1" class="mt-2" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_address_line_2" :value="t('page.checkout.shippingAddress2')" />
              <TextInput name="shipping_address_line_2" id="shipping_address_line_2" type="text"
                v-model="createOrderForm.shipping_address_line_2" class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_city" :value="t('page.checkout.shippingCity')" />
              <TextInput name="shipping_city" id="shipping_city" type="text" v-model="createOrderForm.shipping_city"
                class="mt-1 block w-full" />
              <InputError :message="errors.shipping_city" class="mt-2" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_state" :value="t('page.checkout.shippingState')" />
              <TextInput name="shipping_state" id="shipping_state" type="text" v-model="createOrderForm.shipping_state"
                class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_postcode" :value="t('page.checkout.shippingPostcode')" />
              <TextInput name="shipping_postcode" id="shipping_postcode" type="text"
                v-model="createOrderForm.shipping_postcode" class="mt-1 block w-full" />
              <InputError :message="errors.shipping_postcode" class="mt-2" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_country_id" :value="t('page.checkout.shippingCountry')" />
              <SelectInput name="shipping_country_id" id="shipping_country_id"
                v-model="createOrderForm.shipping_country_id">
                <option v-for="country in checkoutData.countries" :value="country.id">{{ country.name }}</option>
              </SelectInput>
              <InputError :message="errors.shipping_country_id" class="mt-2" />
            </div>
            <div class="my-2">
              <InputLabel for="shipping_phone_number" :value="t('page.checkout.shippingPhone')" />
              <TextInput name="shipping_phone_number" id="shipping_phone_number" type="text"
                v-model="createOrderForm.shipping_phone_number" class="mt-1 block w-full" />
            </div>
          </div>
          <div>
            <h3 class="font-medium">{{ t('page.checkout.billingHeading') }}</h3>
            <div class="my-2">
              <InputLabel for="billing_address_line_1" :value="t('page.checkout.billingAddress1')" />
              <TextInput name="billing_address_line_1" id="billing_address_line_1" type="text"
                v-model="createOrderForm.billing_address_line_1" class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="billing_address_line_2" :value="t('page.checkout.billingAddress2')" />
              <TextInput name="billing_address_line_2" id="billing_address_line_2" type="text"
                v-model="createOrderForm.billing_address_line_2" class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="billing_city" :value="t('page.checkout.billingCity')" />
              <TextInput name="billing_city" id="billing_city" type="text" v-model="createOrderForm.billing_city"
                class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="billing_state" :value="t('page.checkout.billingState')" />
              <TextInput name="billing_state" id="billing_state" type="text" v-model="createOrderForm.billing_state"
                class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="billing_postcode" :value="t('page.checkout.billingPostcode')" />
              <TextInput name="billing_postcode" id="billing_postcode" type="text"
                v-model="createOrderForm.billing_postcode" class="mt-1 block w-full" />
            </div>
            <div class="my-2">
              <InputLabel for="billing_country_id" :value="t('page.checkout.billingCountry')" />
              <SelectInput name="billing_country_id" id="billing_country_id"
                v-model="createOrderForm.billing_country_id">
                <option v-for="country in checkoutData.countries" :value="country.id">{{ country.name }}</option>
              </SelectInput>
            </div>
            <div class="my-2">
              <InputLabel for="billing_phone_number" :value="t('page.checkout.billingPhone')" />
              <TextInput name="billing_phone_number" id="billing_phone_number" type="text"
                v-model="createOrderForm.billing_phone_number" class="mt-1 block w-full" />
            </div>
            <div v-if="createOrderForm.progress" class="mt-6">
              <div class="flex justify-between text-sm mb-1">
                <span>{{ t('common.form.base.uploading') }}</span>
                <span>{{ createOrderForm.progress.percentage }}%</span>
              </div>
              <div class="w-full bg-gray-200 rounded-full h-3 overflow-hidden">
                <div :style="{ width: `${createOrderForm.progress.percentage}%` }"
                  class="bg-black h-3 transition-all duration-300" />
              </div>
            </div>
            <div class="flex justify-center my-4">
              <ProgressBar v-if="createOrderForm.progress" :percentage="createOrderForm.progress.percentage" />
              <SubmitButton :processing="createOrderForm.processing || hasSubmittedOrder"
                :idle-text="t('common.button.placeOrder')" :loading-text="t('common.button.processing')"
                :disabled="createOrderForm.processing" :title="t('common.button.placeOrderTitle')" />
            </div>
          </div>
        </form>
      </div>
      <div class="bg-white lg:h-fit">
        <div class="flex justify-between gap-2 m-4">
          <h2 class="font-medium">{{ t('page.cart.label') }} ({{ checkoutData.cart.cart_items.length }})</h2>
          <Link :href="route('cart.index')" :title="t('common.button.goToCartTitle')" class="text-sm underline">{{
            t('page.checkout.modifySelection') }}</Link>
        </div>
        <div class="m-4">
          <div v-for="item in checkoutData.cart.cart_items" :key="item.id"
            class="grid grid-cols-[1fr,3fr] py-6 border-b">
            <div>
              <img v-if="item.product_variation.image" :src="item.product_variation.image"
                :alt="`${item.product_variation.product.name}'s image`">
            </div>
            <div class="flex flex-col justify-center gap-2 my-2 mx-6">
              <h3 class="text-sm">{{ item.product_variation.product.name }}</h3>
              <h4 class="text-xl">${{ item.price_at_time }}</h4>
            </div>
          </div>
        </div>
        <div class="flex p-4 w-full">
          <ul class="flex flex-col w-full gap-2">
            <li class="flex justify-between lg:text-lg">
              <p>{{ t('common.product.subtotal') }}</p>
              <span>${{ checkoutData.subtotal.toFixed(2) }}</span>
            </li>
            <li v-if="checkoutData.discount > 0" class="flex justify-between text-green-700 lg:text-lg">
              <p>{{ t('common.product.discount') }}</p>
              <span>- ${{ checkoutData.discount.toFixed(2) }}</span>
            </li>
            <li class="flex flex-col mt-4">
              <div class="flex justify-between">
                <p class="flex gap-2 lg:text-lg">{{ t('common.product.tax') }}
                  <Tooltip v-if="checkoutData.isShippingTaxable" :message="t('common.product.taxShippingInfo')" />
                </p>
                <span class="lg:text-lg">${{ checkoutData.tax.toFixed(2) }}</span>
              </div>
              <p class="text-xs text-slate-500">{{ t('common.product.taxInfo') }}</p>
            </li>
            <li class="flex justify-between mt-4">
              <p>{{ t('common.product.shipping') }}</p>
              <span>${{ checkoutData.shipping.toFixed(2) }}</span>
            </li>
            <li class="flex justify-between mt-4">
              <p>{{ t('common.product.total') }}</p>
              <span>${{ checkoutData.total.toFixed(2) }}</span>
            </li>
            <div v-if="checkoutData.coupon_error" class="bg-red-100 text-red-700 p-3 rounded">
              {{ checkoutData.coupon_error }}
            </div>
            <div v-if="checkoutData.coupon" class="flex justify-between items-center mt-4 mb-4">
              <p>{{ t('page.cart.couponApplied') }}: <span class="text-green-700 font-semibold">{{ checkoutData.coupon
              }}</span></p>
              <form @submit.prevent="removeCouponCode">
                <button type="submit" :title="t('common.button.removeCoupon')" class="bg-slate-100 p-2 rounded-full">
                  <PhTrash size="18" color="black" />
                </button>
              </form>
            </div>
          </ul>
        </div>
      </div>
    </div>
    <Footer />
  </main>
</template>
