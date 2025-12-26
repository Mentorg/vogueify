<script setup>
import { defineProps } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import { PhDotsThree, PhTrash, PhXCircle } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import StatusChip from '@/Components/StatusChip.vue';
import { useOrder } from '@/composables/useOrder';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  orders: Array,
  wishlist: Array
});

const { t } = useI18n();
const user = usePage().props.auth.user;
const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const {
  cancel,
  confirmOrderCancelation,
  closeModal,
  itemToCancel,
  errorMessage,
  formatStatus,
  activeStatuses
} = useOrder();

</script>

<template>
  <DashboardLayout :title="t('page.user.overview')">
    <section class="flex flex-col">
      <div class="flex justify-center my-8">
        <h1 class="text-2xl">{{ user.name }}</h1>
      </div>
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div class="grid gap-2 bg-white p-6 w-full h-fit lg:gap-4 lg:p-10">
          <h3 class="text-xl">{{ t('page.user.profile.label') }}</h3>
          <div class="flex flex-col gap-2 my-4 lg:my-8">
            <p class="font-medium">{{ t('page.user.profile.basicInfo.email') }}: <span class="font-normal">{{ user.email
            }}</span></p>
            <p v-if="user.address === null" class="font-medium">{{ t('page.user.profile.basicInfo.noAddress') }}</p>
            <div v-else class="flex flex-col gap-2">
              <p class="font-medium">{{ t('page.user.profile.basicInfo.address1') }}: <span class="font-normal">{{
                user.address.address_line_1 }}</span></p>
              <p class="font-medium">{{ t('page.user.profile.basicInfo.address2') }}: <span class="font-normal">{{
                user.address.address_line_2 }}</span></p>
              <p class="font-medium">{{ t('page.user.profile.basicInfo.location') }}: <span class="font-normal">{{
                user.address.city }}, {{ user.address.state
                    !== null ?
                    user.address.state + ', ' : '' }}
                  {{
                    user.address.country.iso_code }}</span></p>
              <p class="font-medium">{{ t('page.user.profile.basicInfo.postcode') }}: <span class="font-normal">{{
                user.address.postcode }}</span></p>
              <p class="font-medium">{{ t('page.user.profile.basicInfo.phone') }}: <span class="font-normal">{{
                user.address.phone_number }}</span></p>
            </div>
          </div>
          <Link href="profile"
            class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
            {{ t('common.button.editProfile') }}</Link>
        </div>
        <div class="grid gap-6">
          <div class="bg-white p-6 w-full h-fit lg:p-10">
            <h3 class="text-xl">{{ t('page.user.orders.label') }}</h3>
            <div v-if="orders.length === 0 || !orders.some(order => activeStatuses.includes(order.order_status))">
              <div class="my-8">
                <p>{{ t('page.user.orders.noCurrentOrders') }}</p>
              </div>
              <Link href="/"
                class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
                {{ t('common.button.startShopping') }}</Link>
            </div>
            <div v-else class="mt-8">
              <div v-for="item in orders.filter(order => activeStatuses.includes(order.order_status)).slice(0, 3)"
                :key="item.id"
                class="grid grid-cols-3 grid-rows-2 items-center gap-2 py-2 border-b border-slate-300 lg:border-none lg:justify-between lg:grid-cols-[3fr,2fr,2fr,2fr,0.5fr] lg:grid-rows-1">
                <div
                  class="flex items-center gap-4 col-start-1 col-end-3 row-start-1 row-end-1 lg:col-start-1 lg:col-end-1 lg:row-start-1 lg:row-end-1">
                  <template v-if="item.items.length > 1">
                    <div class="flex -space-x-2">
                      <div v-for="(orderItem, index) in item.items.slice(0, 2)" :key="orderItem.id"
                        class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                        <img v-if="orderItem.product_variation.image" :src="orderItem.product_variation.image" alt=""
                          class="w-full h-full object-cover" />
                      </div>
                      <div v-if="item.items.length > 2"
                        class="h-10 w-10 rounded-full bg-slate-100 flex items-center justify-center text-gray-600 border-2 border-white">
                        <PhDotsThree :size="16" />
                      </div>
                    </div>
                  </template>
                  <template v-else>
                    <div class="h-10 w-10 rounded-full overflow-hidden border-2 border-white">
                      <img v-if="item.items[0].product_variation.image" :src="item.items[0].product_variation.image"
                        alt="" class="w-full h-full object-cover" />
                    </div>
                  </template>
                  <Link :href="route('order.show', { order: item.id })">{{ item.order_number }}</Link>
                </div>
                <div
                  class="flex col-start-1 col-end-1 row-start-2 row-end-2 md:justify-start lg:justify-center lg:col-start-2 lg:col-end-2 lg:row-start-1 lg:row-end-1">
                  <p>{{ formatDate(item.order_date, '.', false) }}</p>
                </div>
                <div
                  class="flex justify-center col-start-2 col-end-2 row-start-2 row-end-2 lg:col-start-3 lg:col-end-3 lg:row-start-1 lg:row-end-1">
                  <StatusChip :status="item.order_status">
                    <p class="text-sm">{{ formatStatus(item.order_status) }}</p>
                  </StatusChip>
                </div>
                <div
                  class="flex justify-end col-start-3 col-end-3 row-start-2 row-end-2 lg:col-start-4 lg:col-end-4 lg:row-start-1 lg:row-end-1">
                  <p>${{ item.total }}</p>
                </div>
                <div
                  class="flex justify-end col-start-3 col-end-3 row-start-1 row-end-1 lg:col-start-5 lg:col-end-5 lg:row-start-1 lg:row-end-1">
                  <button @click="confirmOrderCancelation(item)" :disabled="item.order_status !== 'pending'"
                    class="bg-slate-100 rounded-full p-1.5">
                    <PhXCircle :size="20" />
                  </button>
                </div>
                <DialogModal :show="itemToCancel !== null" @close="closeModal">
                  <template #title>
                    {{ t('common.modal.order.user.orderCancelation.title', { order: itemToCancel?.order_number }) }}?
                  </template>
                  <template #content>
                    {{ t('common.modal.order.user.orderCancelation.content', { order: item?.order_number }) }}?
                    <div v-if="errorMessage" class="text-red-500 mt-2">
                      {{ errorMessage }}
                    </div>
                  </template>
                  <template #footer>
                    <SecondaryButton @click="closeModal">{{ t('common.button.cancel') }}</SecondaryButton>
                    <DangerButton class="ms-3" @click="cancel(itemToCancel?.id)">
                      <PhTrash :size="16" color="white" class="mr-2" />
                      {{ t('common.button.cancelOrder') }}
                    </DangerButton>
                  </template>
                </DialogModal>
              </div>
              <div v-if="orders.length > 3" class="mt-8">
                <Link href="/orders/user/orders"
                  class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
                  {{ t('common.button.viewOrders') }}</Link>
              </div>
            </div>
          </div>
          <div class="bg-white p-6 w-full h-fit lg:p-10">
            <h3 class="text-xl">{{ t('page.user.wishlist.label') }}</h3>
            <div v-if="wishlist.length < 1">
              <div class="my-8">
                <p>{{ t('page.user.wishlist.noWishlistItems') }}.</p>
              </div>
              <Link href="/"
                class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
                {{ t('common.button.startShopping') }}</Link>
            </div>
            <div v-else class="mt-8">
              <div v-for="item in wishlist.slice(0, 3)" :key="item.id" class="flex items-center my-4">
                <div class="flex">
                  <img :src="item.product_variation.image" :alt="item.product_variation.product.name"
                    class="w-[7.5%] rounded-full" />
                  <div class="flex flex-col gap-1 ml-4">
                    <Link href="/wishlist" class="font-medium">{{ item.product_variation.product.name }}</Link>
                    <p>${{ item.product_variation.price }}</p>
                  </div>
                </div>
                <form :action="route('wishlist.destroy', item.id)" method="post" @click.stop>
                  <input type="hidden" name="_token" :value="csrfToken" />
                  <input type="hidden" name="_method" value="DELETE" />
                  <button type="submit" title="Remove from wishlist" class="bg-slate-100 p-2 rounded-full">
                    <PhTrash :size="18" color="black" />
                  </button>
                </form>
              </div>
              <div v-if="wishlist.length > 3" class="mt-8">
                <Link href="/wishlist"
                  class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-white transition-all hover:bg-white hover:text-black">
                  {{ t('common.button.viewWishlist') }}</Link>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </DashboardLayout>
</template>
