<script setup>
import { computed, defineProps, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { PhCaretRight, PhHeart, PhTrash, PhXCircle } from '@phosphor-icons/vue';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue';
import Footer from '@/Layouts/Footer.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import useWishlist from '@/composables/useWishlist';

const props = defineProps({
  cart: Object,
})

const { t } = useI18n();
const toast = useToast();
const form = useForm({});
const user = usePage().props.auth.user;
const openMenu = ref(null);
const itemToDelete = ref(null);

const { toggleWishlist } = useWishlist();

const confirmItemDeletion = (item) => {
  itemToDelete.value = item;
}

const destroy = (id) => {
  form.delete(route('cart.delete', id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('common.toast.cart.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      closeModal()
    },
    onError: (errors) => {
      toast.open({
        message: `${t('common.toast.cart.errorMessage')}! ` + errors.error,
        type: 'error',
        position: 'top',
        duration: 4000,
      });
    }
  })
}

const closeModal = () => {
  itemToDelete.value = null;
  form.reset()
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openMenu.value = null;
  }
}

const screenWidth = ref(window.innerWidth);

const isTablet = computed(() => screenWidth.value < 1024);

const handleResize = () => {
  screenWidth.value = window.innerWidth;
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>

<template>

  <Head :title="t('page.cart.label')" />
  <Layout>
    <Menu />
    <main>
      <section v-if="cart?.cartItems?.length > 0" class="bg-slate-50 grid lg:grid-cols-[2fr,1fr] h-fit w-full gap-8">
        <div class="px-4 md:px-8 lg:px-16">
          <div class="flex flex-col gap-4">
            <div class="flex flex-col justify-between order-2 lg:order-1 lg:py-10 lg:flex-row">
              <h1 class="order-2 lg:order-1 lg:text-2xl">{{ t('page.cart.label') }} <span
                  class="text-xs text-slate-500 lg:text-sm">({{
                    cart.cartItems.length
                  }})</span>
              </h1>
              <Link href="/" class="order-1 mb-8 text-center lg:order-2 text-sm underline lg:text-base lg:mb-0">
              {{ t('common.button.continueShopping') }}
              </Link>
            </div>
            <div v-if="isTablet" class="flex flex-col order-3 gap-4 lg:order-2">
              <div v-for="item in cart.cartItems" class="bg-white">
                <div class="flex justify-between items-center border-b">
                  <div class="grid grid-cols-[1fr,4fr]">
                    <div class="place-content-center place-items-center">
                      <img v-if="item.product_variation && item.product_variation.image"
                        :src="'http://vogueify.test' + item.product_variation.image"
                        :alt="item.product_variation.product.name"
                        class="w-full h-full p-1 md:w-[95%] md:h-[95%] md:p-0" />
                    </div>
                    <div class="flex flex-col justify-center ml-4">
                      <p class="text-xs">{{ item.product_variation.sku }}</p>
                      <h2 class="md:text-lg">{{ item.product_variation.product.name }}</h2>
                    </div>
                  </div>
                  <div class="mr-2">
                    <Link
                      :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })">
                    <PhCaretRight :size="18" />
                    </Link>
                  </div>
                </div>
                <div class="p-2 md:p-3">
                  <ul>
                    <li v-for="feature in item.product_variation.product.features" :key="feature.title"
                      class="flex justify-between my-1">
                      <p class="text-xs">{{ feature.title }}</p>
                      <p class="text-xs">{{ feature.description }}</p>
                    </li>
                  </ul>
                  <div class="flex justify-between items-end mt-6">
                    <div class="flex flex-col">
                      <div v-if="item.size !== null" class="flex gap-2">
                        <h4 class="text-sm">{{ t('common.product.size') }}:</h4>
                        <p class="text-sm">{{ item.size.label }}</p>
                      </div>
                      <div class="flex gap-2">
                        <h4 class="text-sm">{{ t('common.product.quantity') }}:</h4>
                        <p class="text-sm">{{ item.quantity }}</p>
                      </div>
                    </div>
                    <div class="flex flex-col items-end">
                      <h3 class="text-lg">${{ item.price_at_time * item.quantity }}</h3>
                      <p v-if="item.quantity > 1" class="text-xs text-slate-500">${{ item.price_at_time }}
                        {{ t('page.cart.perPiece') }}</p>
                    </div>
                  </div>
                </div>
                <div class="flex divide-x-2">
                  <button
                    @click.prevent="toggleWishlist(item.product_variation.id, () => item.product_variation.isInWishlist = !item.product_variation.isInWishlist)"
                    class="flex items-center gap-2 text-xs border-t border-t-slate-200 w-full justify-center py-2 transition-all md:text-sm">
                    <PhHeart size="14" color="red" :weight="item.product_variation.isInWishlist ? 'fill' : 'regular'" />
                    {{ t('page.cart.button.addToWishlist') }}
                  </button>
                  <button @click="confirmItemDeletion(item)"
                    class="flex items-center gap-2 text-xs border-t border-t-slate-200 w-full justify-center py-2 transition-all md:text-sm">
                    <PhXCircle size="14" />
                    {{ t('common.button.remove') }}
                  </button>
                </div>
              </div>
            </div>
            <div v-else class="flex flex-col order-3 gap-4 lg:order-2">
              <div v-for="item in cart.cartItems" class="bg-white grid grid-cols-2">
                <div class="place-content-center place-items-center">
                  <img v-if="item.product_variation && item.product_variation.image"
                    :src="'http://vogueify.test' + item.product_variation.image"
                    :alt="item.product_variation.product.name" class="w-[95%] h-[95%]" />
                </div>
                <div class="flex flex-col h-full border-l">
                  <div class="flex flex-col h-full">
                    <div class="flex items-center justify-between border-b py-6 px-8">
                      <div>
                        <p class="text-sm">{{ item.product_variation.sku }}</p>
                        <h2 class="text-2xl">{{ item.product_variation.product.name }}</h2>
                      </div>
                      <Link
                        :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })">
                      <PhCaretRight :size="24" />
                      </Link>
                    </div>
                    <div class="flex-1 py-6 px-8">
                      <ul>
                        <li v-for="feature in item.product_variation.product.features" :key="feature.title"
                          class="flex justify-between">
                          <p>{{ feature.title }}</p>
                          <p>{{ feature.description }}</p>
                        </li>
                      </ul>
                    </div>
                    <div class="flex items-end justify-between mt-auto py-6 px-8">
                      <div class="flex flex-col">
                        <div v-if="item.size !== null" class="flex gap-2">
                          <h4>{{ t('common.product.size') }}:</h4>
                          <p>{{ item.size.label }}</p>
                        </div>
                        <div class="flex gap-2">
                          <h4>{{ t('common.product.quantity') }}:</h4>
                          <p>{{ item.quantity }}</p>
                        </div>
                      </div>
                      <div class="flex flex-col items-end">
                        <h3 class="text-2xl">${{ item.price_at_time * item.quantity }}</h3>
                        <p v-if="item.quantity > 1" class="text-xs text-slate-500">${{ item.price_at_time }} {{
                          t('page.cart.perPiece') }}</p>
                      </div>
                    </div>
                  </div>
                  <div class="flex divide-x-2">
                    <button
                      @click.prevent="toggleWishlist(item.product_variation.id, () => item.product_variation.isInWishlist = !item.product_variation.isInWishlist)"
                      class="flex items-center gap-2 text-lg border-t border-t-slate-200 w-full justify-center py-3 px-2 transition-all hover:bg-black hover:text-white">
                      <PhHeart size="18" color="red"
                        :weight="item.product_variation.isInWishlist ? 'fill' : 'regular'" />
                      {{ t('page.cart.button.addToWishlist') }}
                    </button>
                    <button @click="confirmItemDeletion(item)"
                      class="flex items-center gap-2 text-lg border-t border-t-slate-200 w-full justify-center py-3 transition-all hover:bg-black hover:text-white">
                      <PhXCircle :size="18" />
                      {{ t('common.button.remove') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex order-1 mt-8 lg:order-3 lg:my-10">
              <Link :href="user === null ? route('login') : route('checkout')"
                class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base">
              {{ t('page.cart.button.proceedToCheckout') }}
              </Link>
            </div>
          </div>
          <DialogModal :show="itemToDelete !== null" @close="closeModal">
            <template #title>
              {{ t('common.modal.cart.title', { itemToDelete: itemToDelete.product_variation.product.name }) }}?
            </template>
            <template #content>
              {{ t('common.modal.cart.content', { itemToDelete: itemToDelete.product_variation.product.name }) }}?
            </template>
            <template #footer>
              <SecondaryButton @click="closeModal">{{ t('common.button.cancel') }}</SecondaryButton>
              <DangerButton class="ms-3" @click="destroy(itemToDelete.id)">
                <PhTrash :size="16" color="white" class="mr-2" />
                {{ t('common.button.delete') }}
              </DangerButton>
            </template>
          </DialogModal>
        </div>
        <div class="bg-white py-10 px-6 md:px-10">
          <ul class="flex flex-col gap-2">
            <li class="flex justify-between lg:text-lg">
              <p>{{ t('common.product.subtotal') }}</p>
              <span>
                ${{ cart.subtotal.toFixed(2) }}
              </span>
            </li>
            <li class="flex justify-between :text-lg">
              <p>{{ t('common.product.shipping') }}</p><span>${{ cart.shipping.toFixed(2) }}</span>
            </li>
            <li class="flex flex-col">
              <div class="flex justify-between">
                <p class=":text-lg">{{ t('common.product.tax') }}</p>
                <span class=":text-lg">${{ cart.tax.toFixed(2) }}</span>
              </div>
              <p class="text-xs text-slate-500">{{ t('common.product.taxInfo') }}</p>
            </li>
            <li class="flex justify-between mt-4 text-lg">
              <p>{{ t('common.product.total') }}</p>
              <span>
                ${{ cart.total.toFixed(2) }}
              </span>
            </li>
          </ul>
          <div>
            <Link :href="user === null ? route('login') : route('checkout')"
              class="bg-black flex justify-center border border-black rounded-full py-2 mt-8 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base">
            {{ t('page.cart.button.proceedToCheckout') }}</Link>
          </div>
        </div>
      </section>
      <section v-else class="w-full h-fit">
        <div class="flex flex-col items-center w-full h-full py-[5rem] lg:py-[8rem]">
          <img src="../../../public/images/empty-bag.png" alt="Empty bag" class="w-fit">
          <p class="text-lg">{{ t('page.cart.emptyCart') }}</p>
        </div>
      </section>
      <Footer />
    </main>
  </Layout>
</template>
