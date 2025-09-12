<script setup>
import { reactive } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import { PhChat, PhDotsThreeVertical, PhPencilSimple } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import { useToast } from 'vue-toast-notification';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import Modal from '@/Components/Modal.vue';
import OrderStatusChip from '@/Components/OrderStatusChip.vue';
import OrderItemStatusUpdate from '@/Components/OrderItemStatusUpdate.vue';
import OrderItemShippingDateUpdate from '@/Components/OrderItemShippingDateUpdate.vue';
import OrderShippingAddressUpdate from '@/Components/OrderShippingAddressUpdate.vue';
import OrderBillingAddressUpdate from '@/Components/OrderBillingAddressUpdate.vue';
import OrderNoteUpdate from '@/Components/OrderNoteUpdate.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import { useDropdown } from '@/composables/useDropdown';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  order: Object,
  orderStatuses: Array,
  countries: Array,
});

const { t } = useI18n();
const toast = useToast();
const form = useForm({});

const {
  toggleMenu,
  isMenuOpen,
} = useDropdown([
  '.context-order-item-menu-wrapper',
]);

const modals = reactive({
  resendConfirmation: null,
  itemStatus: null,
  itemShippingDate: null,
  note: null,
  shippingAddress: null,
  billingAddress: null,
});

const resendConfirmationSubmittion = () => {
  form.post(route('orders.resendConfirmation', props.order.order.id), {
    preserveScroll: true,
    onSuccess: () => {
      modals.resendConfirmation = null
      toast.open({
        message: `${t('common.toast.order.admin.resendConfirmation.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.order.admin.resendConfirmation.errorMessage')}!`,
        type: 'error',
        position: 'top',
        duration: 4000,
      })
    }
  })
}

const confirmResendConfirmation = (order) => {
  modals.resendConfirmation = order;
}

const editOrderItemStatus = (item) => {
  modals.itemStatus = item
  toggleMenu(item.id)
}

const editOrderItemShippingDate = (item) => {
  modals.itemShippingDate = item
  toggleMenu(item.id)
}

const editOrderNote = (order) => {
  modals.note = order
}

const editOrderShippingData = (order) => {
  modals.shippingAddress = order
}

const editOrderBillingData = (order) => {
  modals.billingAddress = order
}

const closeModals = {
  resendConfirmation: () => (modals.resendConfirmation = null),
  itemStatus: () => (modals.itemStatus = null),
  itemShippingDate: () => (modals.itemShippingDate = null),
  note: () => (modals.note = null),
  shippingAddress: () => (modals.shippingAddress = null),
  billingAddress: () => (modals.billingAddress = null),
}

const formatShippingDate = (date) => date ? formatDate(date, '.', true) : t('page.orderDetails.undetermined')

</script>

<template>

  <Head :title="t('page.admin.orderDetails')" />
  <AdminDashboard>
    <div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-8">
          <h1 class="text-2xl font-medium">{{ order.order.order_number }}</h1>
          <OrderStatusChip :status="order.order.order_status" class="rounded-md text-sm">
            {{ capitalize(order.order.order_status) }}
          </OrderStatusChip>
        </div>
        <button v-if="order.order.order_status === 'paid'" @click="confirmResendConfirmation(order.order.id)"
          :title="t('common.button.resendConfirmationTitle')"
          class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
          {{ t('common.button.resendConfirmation') }}
        </button>
      </div>
      <p class="my-4 text-slate-500">{{ formatDate(order.order.created_at, '.', true) }}</p>
    </div>
    <div class="flex flex-col xl:grid xl:grid-cols-[3fr,1fr] gap-8 my-8">
      <div class="flex flex-col gap-8">
        <div class="p-6 border border-gray-200 rounded-lg">
          <h2 class="text-xl font-medium mb-4">{{ t('page.orderDetails.orderItems') }}</h2>
          <div v-for="item in order.order.items" :key="item.id"
            class="grid grid-cols-[3fr,3fr,2fr] grid-rows-[2fr,0.5fr,1fr] gap-4 border-b py-4 last:border-0 xl:grid-cols-[3fr,2fr,2fr] xl:grid-rows-[0.5fr,0.5fr]">
            <div class="flex gap-4 col-start-1 col-end-3 row-start-1 row-end-1 xl:col-end-1 xl:row-end-3">
              <img :src="item.product_variation.image" alt="Product Image" class="w-24 h-24 object-cover rounded-md" />
              <div class="flex flex-col justify-between">
                <div>
                  <p class="text-sm text-slate-500">{{ item.product_variation.type.label }}</p>
                  <h3 class="text-lg font-medium w-`36` overflow-hidden text-ellipsis whitespace-nowrap">{{
                    item.product_variation.product.name }}</h3>
                </div>
                <div class="flex items-center gap-4">
                  <p v-if="item.size" class="text-slate-500 text-xs lg:text-base">{{ t('page.orderDetails.size') }} {{
                    item.size?.label }}</p>
                  <p v-if="item.size && item.product_variation.color">|</p>
                  <div class="flex items-center gap-2">
                    <div :style="item.product_variation.color?.name === 'Multicolor'
                      ? { background: 'linear-gradient(135deg, #ff0000, #ffff00, #3333ff)' }
                      : { backgroundColor: item.product_variation.color?.hex_code }"
                      :class="{ border: item.product_variation.color?.hex_code === '#FFFFFF' }"
                      class="rounded-sm w-4 h-4" />
                    <p class="text-xs lg:text-base">{{ item.product_variation.color?.name }}</p>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex justify-center col-start-1 col-end-4 row-start-2 row-end-2 xl:col-start-2 xl:col-end-2">
              <p class="text-center"><span class="font-medium">{{ t('page.orderDetails.shippingDate') }}:</span> {{
                formatShippingDate(item.shipping_date) }}</p>
            </div>
            <div
              class="flex justify-start gap-4 col-start-1 col-end-3 row-start-3 row-end-3 h-fit xl:col-start-2 xl:col-end-2 xl:row-start-1 xl:row-end-1 xl:justify-center">
              <OrderStatusChip :status="item.order_status" class="rounded-md text-sm">
                {{ capitalize(item.order_status) }}
              </OrderStatusChip>
            </div>
            <div
              class="flex flex-col items-end col-start-3 col-end-3 row-start-1 row-end-1 xl:col-start-3 xl:col-end-3 xl:row-start-1 xl:row-end-1">
              <div class="relative context-order-item-menu-wrapper">
                <button @click.stop="toggleMenu(`orderItem-${item.id}`)"
                  :title="t('common.button.moreItemActionsTitle')"
                  class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                  <PhDotsThreeVertical :size="20" />
                </button>
                <div v-if="isMenuOpen(`orderItem-${item.id}`)"
                  class="absolute z-10 right-0 top-0 px-3 py-3 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                  <button @click="editOrderItemStatus(item)" :title="t('common.button.updateStatusTitle')"
                    class="flex w-full text-sm hover:bg-slate-100 px-2 py-2 rounded-md">
                    {{ t('common.button.updateStatus') }}
                  </button>
                  <button @click="editOrderItemShippingDate(item)"
                    :title="t(item.shipping_date ? 'common.button.updateDateTitle' : 'common.button.setDateTitle')"
                    class="flex w-full text-sm hover:bg-slate-100 px-2 py-2 rounded-md">
                    {{ t(item.shipping_date ? 'common.button.updateDate' : 'common.button.setDate') }}
                  </button>
                </div>
              </div>
            </div>
            <div
              class="flex justify-start items-center h-fit gap-4 col-start-3 col-end-3 row-start-3 row-end-3 xl:col-start-3 xl:col-end-3 xl:row-start-2 xl:row-end-2 xl:justify-end">
              <div class="py-1 px-2 border rounded-md w-fit h-fit">
                <p class="text-nowrap">{{ item.quantity }} x ${{ item.price_at_time }}</p>
              </div>
              <p class="font-medium">${{ item.price_at_time * item.quantity }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <h2 class="text-xl font-medium mb-4">{{ t('page.orderDetails.orderSummary') }}</h2>
          <div class="flex my-4">
            <OrderStatusChip :status="order.order.order_status" class="rounded-md text-sm">
              {{ capitalize(order.order.order_status) }}
            </OrderStatusChip>
          </div>
          <div class="grid grid-cols-3">
            <div>
              <h3 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.subtotal') }}</h3>
              <h3 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.shippingCost') }}</h3>
              <h3 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.tax') }}</h3>
              <h3 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.total') }}</h3>
            </div>
            <div class="flex flex-col items-center">
              <h4 class="text-sm md:text-base xl:text-lg">
                {{t('page.orderDetails.item', {
                  count: order.order.items.reduce((sum, item) => sum + item.quantity, 0)
                })}}
              </h4>
              <h4 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.freeShipping') }}</h4>
              <h4 class="text-sm md:text-base xl:text-lg">{{ t('page.orderDetails.notIncluded') }}</h4>
            </div>
            <div class="flex flex-col items-end">
              <p class="text-sm md:text-base xl:text-lg font-medium">${{ order.subtotal }}</p>
              <p class="text-sm md:text-base xl:text-lg font-medium">${{ order.shipping }}</p>
              <p class="text-sm md:text-base xl:text-lg font-medium">${{ order.tax }}</p>
              <p class="text-sm md:text-base xl:text-lg font-medium">${{ order.total }}</p>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-8">
        <div class="p-6 border border-gray-200 rounded-lg">
          <div class="flex items-center justify-between">
            <h2 class="text-xl font-medium">{{ t('page.orderDetails.notes') }}</h2>
            <button @click="editOrderNote(order)" :title="t('common.button.updateOrderNoteTitle')"
              class="rounded-full p-0.5 transition-all hover:bg-slate-200">
              <PhPencilSimple :size="20" />
            </button>
          </div>
          <p class="text-slate-500 mt-2">{{ order.order.order_note || `${t('page.orderDetails.noNote')}.` }}</p>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <div class="flex items-center justify-between">
            <h2 class="text-lg font-medium mb-4">{{ t('page.orderDetails.customer') }}</h2>
            <a :href="'mailto:' + order.order.user.email" :title="t('common.button.contactCustomerTitle')"
              class="rounded-full p-0.5 transition-all hover:bg-slate-200">
              <PhChat :size="20" />
            </a>
          </div>
          <div class="flex">
            <img v-if="order.order.user.profile_photo_url" :src="order.order.user.profile_photo_url"
              alt="User profile photo" class="w-14 h-14 rounded-full inline-block mr-2 mb-2" />
            <div class="flex flex-col justify-center gap-1 ml-2">
              <h3 class="font-medium">{{ order.order.user.name }}</h3>
              <p class="text-slate-500 text-sm">{{ t('page.orderDetails.order',
                { count: order.order.user.orders.length }) }}</p>
            </div>
          </div>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">{{ t('page.orderDetails.shippingAddress') }}</h3>
            <button @click="editOrderShippingData(order)" :title="t('common.button.updateOrderShippingAddressTitle')"
              class="rounded-full p-0.5 transition-all hover:bg-slate-200">
              <PhPencilSimple :size="20" />
            </button>
          </div>
          <ul class="flex flex-col mt-2 gap-1">
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.phoneNumber') }}: </span>{{
                order.order.shipping_phone_number }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.firstAddressLine') }}: </span>{{
                order.order.shipping_address_line_1 }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.secondAddressLine') }}:
                </span>{{
                  order.order.shipping_address_line_2 }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.city') }}: </span>{{
                order.order.shipping_city }}</p>
            </li>
            <li v-if="order.order.shipping_state">
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.state') }}: </span>{{
                order.order.shipping_state }}
              </p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.postcode') }}: </span>{{
                order.order.shipping_postcode }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.country') }}: </span>{{
                countries.find(country => country.id === order.order.shipping_country_id)?.name
                }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.shippingDate') }}: </span>{{
                formatShippingDate(order.order.shipping_date) }}
              </p>
            </li>
          </ul>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <div class="flex items-center justify-between">
            <h3 class="text-lg text-sm font-medium md:text-base">{{ t('page.orderDetails.billingAddress') }}</h3>
            <button @click="editOrderBillingData(order)" :title="t('common.button.updateOrderBillingAddressTitle')"
              class="rounded-full p-0.5 transition-all hover:bg-slate-200">
              <PhPencilSimple :size="20" />
            </button>
          </div>
          <p v-if="order.order.billing_address_line_1 === order.order.shipping_address_line_1 &&
            order.order.billing_address_line_2 === order.order.shipping_address_line_2 &&
            order.order.billing_city === order.order.shipping_city &&
            order.order.billing_state === order.order.shipping_state &&
            order.order.billing_postcode === order.order.shipping_postcode &&
            order.order.billing_country_id === order.order.shipping_country_id" class="mt-2">
            {{ t('page.orderDetails.sameAsShipping') }}</p>
          <ul v-else class="flex flex-col mt-2 gap-1">
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.phoneNumber') }}: </span>{{
                order.order.billing_phone_number }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.firstAddressLine') }}: </span>{{
                order.order.billing_address_line_1 }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.secondAddressLine') }}:
                </span>{{
                  order.order.billing_address_line_2 }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.city') }}: </span>{{
                order.order.billing_city }}</p>
            </li>
            <li v-if="order.order.billing_state">
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.state') }}: </span>{{
                order.order.billing_state }}
              </p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.postcode') }}: </span>{{
                order.order.billing_postcode
              }}</p>
            </li>
            <li>
              <p><span class="text-sm font-medium md:text-base">{{ t('page.orderDetails.country') }}: </span>{{
                countries.find(country => country.id === order.order.billing_country_id)?.name
              }}</p>
            </li>
          </ul>
        </div>
      </div>
      <DialogModal :show="modals.resendConfirmation" @close="closeModals.resendConfirmation">
        <template #title>
          {{ t('common.modal.order.admin.resendConfirmation.title', { order: order.order.order_number }) }}?
        </template>
        <template #content>
          <i18n-t keypath="common.modal.order.admin.resendConfirmation.content">
            <template #user>
              <span class="font-medium">{{ order.order.user.name }}</span>
            </template>
            <template #orderNumber>
              <span class="font-medium">{{ order.order.order_number }}</span>
            </template>
          </i18n-t>
        </template>
        <template #footer>
          <SecondaryButton @click="closeModals.resendConfirmation">{{ t('common.button.cancel') }}</SecondaryButton>
          <DangerButton class="ms-3" @click="resendConfirmationSubmittion(order.order.id)">{{
            t('common.button.resendConfirmation') }}
          </DangerButton>
        </template>
      </DialogModal>
      <Modal :show="modals.itemStatus !== null" @close="closeModals.itemStatus">
        <OrderItemStatusUpdate :orderItemStatusToEdit="modals.itemStatus" :orderStatuses="props.orderStatuses"
          :close="closeModals.itemStatus" />
      </Modal>
      <Modal :show="modals.itemShippingDate !== null" @close="closeModals.itemShippingDate">
        <OrderItemShippingDateUpdate :orderItemShippingDateToEdit="modals.itemShippingDate"
          :close="closeModals.itemShippingDate" />
      </Modal>
      <Modal :show="modals.note !== null" @close="closeModals.note">
        <OrderNoteUpdate :order="order" :orderNoteToEdit="modals.note" :close="closeModals.note" />
      </Modal>
      <Modal :show="modals.shippingAddress !== null" @close="closeModals.shippingAddress">
        <OrderShippingAddressUpdate :order="order" :countries="countries"
          :orderShippingAddressToEdit="modals.shippingAddress" :close="closeModals.shippingAddress" />
      </Modal>
      <Modal :show="modals.billingAddress !== null" :countries="countries" @close="closeModals.billingAddress">
        <OrderBillingAddressUpdate :order="order" :countries="countries"
          :orderBillingAddressToEdit="modals.billingAddress" :close="closeModals.billingAddress" />
      </Modal>
    </div>
  </AdminDashboard>
</template>
