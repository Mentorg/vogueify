<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import DialogModal from '@Components/DialogModal.vue';
import StatusChip from '@Components/StatusChip.vue';
import PrimaryButton from '@Components/PrimaryButton.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import { useResendInvoice } from '@/composables/invoice/useResendInvoice';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

defineProps({
  invoice: Object
})

const { t } = useI18n();
const {
  resendInvoiceTarget,
  isResendInvoiceModalOpen,
  openResendInvoiceModal,
  closeResendInvoiceModal,
  resendInvoice,
} = useResendInvoice();

</script>

<template>

  <Head :title="t('page.admin.invoiceDetails')" />
  <AdminDashboard>
    <div>
      <div class="flex flex-col gap-2 items-center justify-between py-6 md:gap-6 md:flex-row">
        <div class="flex flex-col">
          <div class="flex gap-6">
            <h1 class="text-2xl font-medium">{{ invoice.invoice_number }}</h1>
            <StatusChip :status="invoice.status" class="rounded-md text-sm">
              {{ capitalize(invoice.status) }}
            </StatusChip>
          </div>
          <p class="my-4 text-slate-500">{{ t('page.invoiceDetails.issuedAt') }}: {{ formatDate(invoice.issued_at, '.',
            true) }}</p>
        </div>
        <div class="flex gap-4">
          <button @click="openResendInvoiceModal(invoice)" :title="t('common.button.resendInvoiceTitle')"
            class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
            {{ t('common.button.resendInvoice') }}
          </button>
          <a :href="route('admin.invoices.download', { invoice: invoice.id })"
            :title="t('common.button.downloadInvoicePDFTitle')"
            class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
            {{ t('common.button.downloadPDF') }}
          </a>
        </div>
      </div>
      <div class="flex flex-col gap-y-6 border-t divide-y py-6 md:divide-y-0 md:gap-x-12 md:flex-row md:divide-x">
        <div>
          <img :src="invoice.user.profile_photo_url"
            :alt="t('page.invoiceDetails.profilePictureAlt', { name: invoice.user.name })" class="rounded-full">
          <h2>{{ invoice.user.name }}</h2>
          <p>{{ invoice.user.email }}</p>
        </div>
        <div class="pt-6 md:pl-12">
          <h2 class="font-medium">{{ t('page.invoiceDetails.orderID') }}</h2>
          <p>{{ invoice.order.order_number }}</p>
          <h2 class="font-medium">{{ t('page.invoiceDetails.billingAddress') }}</h2>
          <p>
            {{
              invoice.order.billing_address_line_1 +
              ', ' +
              invoice.order.billing_city +
              (invoice.order.shipping_state ? ', ' + invoice.order.shipping_state : '')
            }}
          </p>
        </div>
        <div class="pt-6 md:pl-12">
          <h2 class="font-medium">{{ t('page.invoiceDetails.invoiceID') }}</h2>
          <p>{{ invoice.invoice_number }}</p>
          <h2 class="font-medium">{{ t('page.invoiceDetails.shippingAddress') }}</h2>
          <p>
            {{
              invoice.order.shipping_address_line_1 +
              ', ' +
              invoice.order.shipping_city +
              (invoice.order.billing_state ? ', ' + invoice.order.billing_state : '')
            }}
          </p>
        </div>
      </div>
      <div v-if="invoice.internal_note" class="flex flex-col gap-y-2 my-6 lg:w-1/2">
        <h2 class="text-xl font-medium">{{ t('page.invoiceDetails.internalNote') }}</h2>
        <p>{{ invoice.internal_note }}</p>
      </div>
      <div class="w-full overflow-x-auto scroll-smooth">
        <div class="min-w-[700px] flex flex-col divide-y">
          <div
            class="grid grid-cols-[2fr,1fr,1fr,1fr] md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr] md:py-4">
            <p class="justify-self-start font-medium uppercase">{{ t('page.invoiceDetails.items') }}</p>
            <p class="justify-self-start md:justify-self-end font-medium uppercase">{{ t('page.invoiceDetails.sku') }}
            </p>
            <p class="justify-self-start md:justify-self-end font-medium uppercase">{{ t('page.invoiceDetails.qty') }}
            </p>
            <p class="justify-self-start md:justify-self-end font-medium uppercase">{{ t('page.invoiceDetails.total') }}
            </p>
          </div>
          <div v-for="item in invoice.items"
            class="grid grid-cols-[2fr,1fr,1fr,1fr] py-2 md:grid-cols-[2fr,2fr,2fr,2fr] md:py-4 lg:grid-cols-[3fr,2fr,2fr,2fr] items-center">
            <div class="flex items-center gap-4">
              <img :src="'http://vogueify.test' + item.product_image"
                :alt="t('page.invoiceDetails.itemImageAlt', { name: item.product_name })"
                class="w-10 h-10 object-cover md:w-[10%]" />
              <h2 class="font-medium">{{ item.product_name }}</h2>
            </div>
            <p class="justify-self-start md:justify-self-end">{{ item.product_sku }}</p>
            <p class="justify-self-start md:justify-self-end">{{ item.quantity }}</p>
            <p class="justify-self-start font-medium md:justify-self-end md:text-end"><span v-if="item.quantity > 1"
                class="font-normal">({{ item.quantity }} x {{ invoice.currency === 'EUR' ? '€' : '$' }}{{
                  item.unit_price }})</span> {{ invoice.currency === 'EUR' ? '€' : '$' }}{{ item.total }}</p>
          </div>
          <div class="flex flex-col w-full divide-y">
            <div class="grid grid-cols-5 py-2 md:py-4 md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr]">
              <div class="md:col-start-3 font-medium uppercase md:justify-self-end">
                {{ t('page.invoiceDetails.subtotal') }}</div>
              <div class="col-start-4 text-right">
                {{ invoice.currency === 'EUR' ? "€" : "$" }}{{ invoice.subtotal }}
              </div>
            </div>
            <div class="grid grid-cols-5 py-2 md:py-4 md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr]">
              <div class="md:col-start-3 font-medium uppercase md:justify-self-end">
                {{ t('page.invoiceDetails.discount') }}</div>
              <div class="col-start-4 text-right">
                {{ invoice.currency === 'EUR' ? "€" : "$" }}{{ invoice.discount_amount }}
              </div>
            </div>
            <div class="grid grid-cols-5 py-2 md:py-4 md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr]">
              <div class="md:col-start-3 font-medium uppercase md:justify-self-end">
                {{ t('page.invoiceDetails.shipping') }}</div>
              <div class="col-start-4 text-right">
                {{ invoice.currency === 'EUR' ? "€" : "$" }}{{ invoice.shipping_cost }}
              </div>
            </div>
            <div class="grid grid-cols-5 py-2 md:py-4 md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr]">
              <div class="md:col-start-3 font-medium uppercase md:justify-self-end">{{ t('page.invoiceDetails.tax') }}
              </div>
              <div class="col-start-4 text-right">
                {{ invoice.currency === 'EUR' ? "€" : "$" }}{{ invoice.tax_amount }}
              </div>
            </div>
            <div
              class="grid grid-cols-5 py-2 md:py-4 md:grid-cols-[2fr,2fr,2fr,2fr] lg:grid-cols-[3fr,2fr,2fr,2fr] font-semibold">
              <div class="md:col-start-3 uppercase md:justify-self-end">{{ t('page.invoiceDetails.total') }}</div>
              <div class="col-start-4 text-right">
                {{ invoice.currency === 'EUR' ? "€" : "$" }}{{ invoice.total }}
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <DialogModal :show="isResendInvoiceModalOpen" @close="closeResendInvoiceModal">
      <template #title>
        {{ t('common.modal.invoice.resendInvoice.title', {
          invoice: resendInvoiceTarget?.invoice_number, user:
            resendInvoiceTarget?.user?.name
        }) }}?
      </template>
      <template #content>
        {{ t('common.modal.invoice.resendInvoice.content', {
          invoice: resendInvoiceTarget?.invoice_number, user:
            resendInvoiceTarget?.user?.name
        }) }}?
      </template>
      <template #footer>
        <SecondaryButton @click="closeResendInvoiceModal" :title="t('common.button.cancelResendInvoiceTitle')">
          {{ t('common.button.cancel') }}
        </SecondaryButton>
        <PrimaryButton class="ms-3" @click="resendInvoice(resendInvoiceTarget)"
          :title="t('common.button.resendInvoiceTitle')">
          {{ t('common.button.resendInvoice') }}
        </PrimaryButton>
      </template>
    </DialogModal>
  </AdminDashboard>
</template>
