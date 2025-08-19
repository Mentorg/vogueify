<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { PhCheck } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue';
import Footer from '@/Layouts/Footer.vue';

const props = defineProps({
  checkoutSession: Object,
});

const { t } = useI18n();
</script>

<template>

  <Head :title="t('page.checkout.checkoutSuccess.label')" />
  <Menu />
  <main>
    <div class="bg-slate-100 flex flex-col itemce place-content-center px-4 lg:px-20 min-h-[48rem] items-center">
      <PhCheck :size="48" />
      <div class="text-center my-8">
        <h1 class="text-2xl md:text-3xl">
          <i18n-t keypath="page.checkout.checkoutSuccess.thankYou">
            <template #userEmail>
              <span class="font-medium">{{ checkoutSession.session.customer_details.name }}</span>
            </template>
          </i18n-t>
        </h1>
        <p class="mt-4 text-sm md:text-base">
          <i18n-t keypath="page.checkout.checkoutSuccess.thankYouMessage">
            <template #userEmail>
              <span class="font-medium">{{ checkoutSession.session.customer_details.email }}</span>
            </template>
          </i18n-t>
        </p>
      </div>
      <div class="bg-white flex flex-col p-4 lg:p-8 my-8 items-center lg:w-1/2 md:w-full">
        <h2 class="text-lg font-medium md:text-xl">{{ t('page.checkout.checkoutSuccess.orderSummary') }}</h2>
        <div v-for="item in checkoutSession.order.items" :key="checkoutSession.order.id"
          class="flex mt-4 border-b border-slate-300 pb-4">
          <img v-if="item.product_variation.image" :src="item.product_variation.image"
            :alt="item.product_variation.product.name" class="w-[20%] h-[20%] md:w-[10%] md:h-[10%]">
          <div class="flex justify-between w-full p-4">
            <div class="flex flex-col">
              <h3 class="text-lg">{{ item.product_variation.product.name }}</h3>
              <p class="text-sm font-medium">x{{ item.quantity }}</p>
            </div>
            <p class="font-medium">${{ item.price_at_time * item.quantity }}</p>
          </div>
        </div>
        <div class="flex justify-between my-4 w-full">
          <h4 class="text-lg font-medium">{{ t('page.checkout.checkoutSuccess.total') }}</h4>
          <p class="text-lg font-medium">${{ (checkoutSession.session.amount_total / 100).toFixed(2) }}</p>
        </div>
      </div>
      <div class="flex flex-col gap-4 w-full md:flex-row lg:gap-10 lg:w-1/2">
        <Link href="/"
          class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base">
        {{ t('common.button.continueShopping') }}
        </Link>
        <Link :href="route('order.userOrders')"
          class="flex justify-center border border-black rounded-full py-2 w-full text-sm text-black transition-all hover:bg-slate-200 lg:text-base">
        {{ t('common.button.viewMyOrders') }}
        </Link>
      </div>
    </div>
  </main>
  <Footer />
</template>
