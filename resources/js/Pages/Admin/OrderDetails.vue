<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { PhChat, PhPencilSimple } from '@phosphor-icons/vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import Modal from '@Components/Modal.vue';
import StatusChip from '@Components/StatusChip.vue';
import OrderItem from '@Components/OrderItem.vue';
import OrderShippingAddressUpdate from '@Components/OrderShippingAddressUpdate.vue';
import OrderBillingAddressUpdate from '@Components/OrderBillingAddressUpdate.vue';
import OrderNoteUpdate from '@Components/OrderNoteUpdate.vue';
import DialogModal from '@Components/DialogModal.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useUpdateOrderBillingAddress } from '@/composables/order/useUpdateOrderBillingAddress';
import { useUpdateOrderShippingAddress } from '@/composables/order/useUpdateOrderShippingAddress';
import { useResendOrderConfirmationEmail } from '@/composables/order/useResendOrderConfirmationEmail';
import { useUpsertOrderNote } from '@/composables/order/useUpsertOrderNote';
import { capitalize } from '@/utils/capitalize';
import { formatDate, formatShippingDate } from '@/utils/dateFormat';

const props = defineProps({
  order: Object,
  orderStatuses: Array,
  countries: Array,
});

const { t } = useI18n();

const {
  hasSameBillingAndShippingAddress,
  orderBillingAddressTarget,
  isUpdateOrderBillingAddressModalOpen,
  updateOrderBillingAddressForm,
  openUpdateOrderBillingAddressModal,
  closeUpdateOrderBillingAddressModal,
  updateOrderBillingAddress,
} = useUpdateOrderBillingAddress();
const {
  orderShippingAddressTarget,
  isUpdateOrderShippingAddressModalOpen,
  updateOrderShippingAddressForm,
  openUpdateOrderShippingAddressModal,
  closeUpdateOrderShippingAddressModal,
  updateOrderShippingAddress,
} = useUpdateOrderShippingAddress();
const {
  orderConfirmationEmailTarget,
  isResendOrderConfirmationEmailModalOpen,
  openResendOrderConfirmationEmailModal,
  closeResendOrderConfirmationEmailModal,
  resendOrderConfirmationEmail,
} = useResendOrderConfirmationEmail();
const {
  orderNoteTarget,
  isUpsertOrderNoteModalOpen,
  upsertOrderNoteForm,
  openUpsertOrderNoteModal,
  closeUpsertOrderNoteModal,
  upsertOrderNote
} = useUpsertOrderNote();

</script>

<template>

  <Head :title="t('page.admin.orderDetails')" />
  <AdminDashboard>
    <div>
      <div class="flex items-center justify-between">
        <div class="flex items-center gap-8">
          <h1 class="text-2xl font-medium">{{ order.order.order_number }}</h1>
          <StatusChip :status="order.order.order_status" class="rounded-md text-sm">
            {{ capitalize(order.order.order_status) }}
          </StatusChip>
        </div>
        <button v-if="order.order.order_status === 'paid'" @click="openResendOrderConfirmationEmailModal(order)"
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
            <OrderItem :item="item" :orderStatuses="orderStatuses" />
          </div>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <h2 class="text-xl font-medium mb-4">{{ t('page.orderDetails.orderSummary') }}</h2>
          <div class="flex my-4">
            <StatusChip :status="order.order.order_status" class="rounded-md text-sm">
              {{ capitalize(order.order.order_status) }}
            </StatusChip>
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
            <button @click="openUpsertOrderNoteModal(order)" :title="t('common.button.updateOrderNoteTitle')"
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
              :alt="t('page.user.profile.basicInfo.picture', { user: order.order.user.name })"
              class="w-14 h-14 rounded-full inline-block mr-2 mb-2" />
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
            <button @click="openUpdateOrderShippingAddressModal(order)"
              :title="t('common.button.updateOrderShippingAddressTitle')"
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
                formatShippingDate(order.order.shipping_date, t) }}
              </p>
            </li>
          </ul>
        </div>
        <div class="p-6 border border-gray-200 rounded-lg">
          <div class="flex items-center justify-between">
            <h3 class="text-lg font-medium">{{ t('page.orderDetails.billingAddress') }}</h3>
            <button @click="openUpdateOrderBillingAddressModal(order)"
              :title="t('common.button.updateOrderBillingAddressTitle')"
              class="rounded-full p-0.5 transition-all hover:bg-slate-200">
              <PhPencilSimple :size="20" />
            </button>
          </div>
          <p v-if="hasSameBillingAndShippingAddress(order.order)" class="mt-2">
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
      <DialogModal :show="isResendOrderConfirmationEmailModalOpen" @close="closeResendOrderConfirmationEmailModal">
        <template #title>
          {{ t('common.modal.order.admin.resendConfirmation.title', {
            order:
              orderConfirmationEmailTarget?.order.order_number
          }) }}?
        </template>
        <template #content>
          <i18n-t keypath="common.modal.order.admin.resendConfirmation.content">
            <template #user>
              <span class="font-medium">{{ orderConfirmationEmailTarget?.order.user.name }}</span>
            </template>
            <template #orderNumber>
              <span class="font-medium">{{ orderConfirmationEmailTarget?.order.order_number }}</span>
            </template>
          </i18n-t>
        </template>
        <template #footer>
          <SecondaryButton @click="closeResendOrderConfirmationEmailModal"
            :title="t('common.button.cancelResendOrderConfirmationEmailTitle')">{{ t('common.button.cancel') }}
          </SecondaryButton>
          <PrimaryButton class="ms-3" @click="resendOrderConfirmationEmail(orderConfirmationEmailTarget)"
            :title="t('common.button.confirmResendOrderConfirmationEmailTitle')">
            {{
              t('common.button.resendConfirmation') }}
          </PrimaryButton>
        </template>
      </DialogModal>
      <Modal :show="isUpsertOrderNoteModalOpen" @close="closeUpsertOrderNoteModal">
        <OrderNoteUpdate :order="orderNoteTarget" :form="upsertOrderNoteForm" :submit="upsertOrderNote" />
      </Modal>
      <Modal :show="isUpdateOrderShippingAddressModalOpen" @close="closeUpdateOrderShippingAddressModal">
        <OrderShippingAddressUpdate :order="orderShippingAddressTarget" :form="updateOrderShippingAddressForm"
          :countries="countries" :submit="updateOrderShippingAddress" />
      </Modal>
      <Modal :show="isUpdateOrderBillingAddressModalOpen" @close="closeUpdateOrderBillingAddressModal">
        <OrderBillingAddressUpdate :order="orderBillingAddressTarget" :form="updateOrderBillingAddressForm"
          :countries="countries" :submit="updateOrderBillingAddress" />
      </Modal>
    </div>
  </AdminDashboard>
</template>
