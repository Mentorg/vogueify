<script setup>
import { defineProps } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  PhDotsThreeVertical,
  PhDownloadSimple,
  PhEnvelope,
  PhEye,
  PhPencilSimple,
  PhProhibit
} from '@phosphor-icons/vue';
import StatusChip from '@Components/StatusChip.vue';
import TableFooter from '@Components/Tables/TableFooter.vue';
import DialogModal from '@Components/DialogModal.vue';
import Modal from '@Components/Modal.vue';
import InvoiceNoteUpdate from '@Components/InvoiceNoteUpdate.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import PrimaryButton from '@Components/PrimaryButton.vue';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';
import { useInvoice } from '@/composables/useInvoice';

const props = defineProps({
  invoices: Array,
});

const { t } = useI18n();
const {
  invoiceToResend,
  invoiceToCancel,
  invoiceNoteToUpdate,
  updateInternalNoteForm,
  isInvoiceMenuOpen,
  toggleMenu,
  openInvoiceResendModal,
  openInvoiceCancelModal,
  openInvoiceNoteModal,
  closeInvoiceResendModal,
  closeInvoiceCancelModal,
  closeInvoiceNoteModal,
  resendInvoice,
  cancelInvoice,
  updateInvoiceInternalNote,
} = useInvoice();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.invoice.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[2fr,2fr,2fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4 text-xs">#</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.customerName') }}</th>
            <th scope="col" class="flex items-center gap-2 px-6 py-4 text-xs">{{ t('common.table.invoice.issuedAt') }}
            </th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.amount') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.status') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <div v-if="invoices.data.length > 0">
            <tr v-for="invoice in invoices.data" :key="invoice.id"
              class="grid grid-cols-[2fr,2fr,2fr,2fr,2fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th class="place-content-center px-6 py-4">
                <Link :href="route('admin.invoices.view', { invoice: invoice.id })" class="hover:underline">{{
                  invoice.invoice_number }}</Link>
              </th>
              <th class="place-content-center px-6 py-4">{{ invoice.user.name }}</th>
              <td class="place-content-center px-6 py-4">{{ formatDate(invoice.issued_at, '.', true) }}</td>
              <td class="place-content-center px-6 py-4">{{ invoice.currency === 'EUR' ? "€" : "$" }}{{
                invoice.order.total }}</td>
              <td class="place-content-center px-6 py-4 flex justify-start h-fit">
                <StatusChip :status="invoice.status">{{ capitalize(invoice.status) }}</StatusChip>
              </td>
              <td class="place-content-center px-6 py-4 flex items-center context-menu-wrapper">
                <div class="relative">
                  <button @click.stop="toggleMenu(invoice.id)" :title="t('common.button.moreActionsTitle')"
                    class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                    <PhDotsThreeVertical :size="20" />
                  </button>
                  <div v-if="isInvoiceMenuOpen(invoice.id)"
                    class="absolute z-10 right-0 top-0 px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                    <a :href="route('admin.invoices.download', { invoice: invoice.id })"
                      :title="t('common.button.downloadInvoicePDFTitle')"
                      class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
                      <PhDownloadSimple :size="16" color="green" />
                      {{ t('common.button.downloadPDF') }}
                    </a>
                    <button @click="openInvoiceResendModal(invoice)" :title="t('common.button.resendInvoiceTitle')"
                      class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
                      <PhEnvelope :size="16" color="green" />
                      {{ t('common.button.resendInvoice') }}
                    </button>
                    <button :disabled="invoice.status === 'cancelled'" @click="openInvoiceCancelModal(invoice)"
                      :title="t('common.button.markAsCancelledTitle')"
                      class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
                      <PhProhibit :size="16" color="red" />
                      {{ t('common.button.markAsCancelled') }}
                    </button>
                    <Link :href="route('admin.order', { order: invoice.order.id })"
                      :title="t('common.button.viewOrderTitle')"
                      class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
                      <PhEye :size="16" color="blue" />
                      {{ t('common.button.viewOrder') }}
                    </Link>
                    <button @click="openInvoiceNoteModal(invoice)"
                      :title="invoice.internal_note ? t('common.button.updateInternalNote') : t('common.button.addInternalNote')"
                      class="flex items-center gap-2 w-full px-2 py-2 rounded-md text-sm hover:bg-slate-100 disabled:opacity-50 disabled:hover:bg-transparent">
                      <PhPencilSimple :size="16" color="green" />
                      {{ invoice.internal_note ? t('common.button.updateInternalNote') :
                        t('common.button.addInternalNote') }}
                    </button>
                  </div>
                </div>
              </td>
            </tr>
          </div>
          <div v-else class="flex place-content-center h-60 text-center py-4 text-gray-500">
            <p class="text-center py-4 text-gray-500">{{ t('common.table.noData') }}</p>
          </div>
        </tbody>
        <TableFooter :pagination="invoices" />
      </table>
      <DialogModal :show="invoiceToResend !== null" @close="closeInvoiceResendModal">
        <template #title>
          {{ t('common.modal.invoice.resendInvoice.title', {
            invoice: invoiceToResend?.invoice_number, user:
              invoiceToResend?.user.name
          }) }}?
        </template>
        <template #content>
          {{ t('common.modal.invoice.resendInvoice.content', {
            invoice: invoiceToResend?.invoice_number, user:
              invoiceToResend?.user.name
          }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeInvoiceResendModal" :title="t('common.button.cancelResendInvoiceTitle')">
            {{ t('common.button.cancel') }}
          </SecondaryButton>
          <PrimaryButton class="ms-3" @click="resendInvoice(invoiceToResend)"
            :title="t('common.button.resendInvoiceTitle')">
            {{ t('common.button.resendInvoice') }}
          </PrimaryButton>
        </template>
      </DialogModal>
      <DialogModal :show="invoiceToCancel !== null" @close="closeInvoiceCancelModal">
        <template #title>
          {{ t('common.modal.invoice.cancelInvoice.title', {
            invoice: invoiceToCancel?.invoice_number
          }) }}?
        </template>
        <template #content>
          {{ t('common.modal.invoice.cancelInvoice.content', {
            invoice: invoiceToCancel?.invoice_number
          }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeInvoiceCancelModal" :title="t('common.button.abortInvoiceCancellationTitle')">
            {{ t('common.button.abortInvoiceCancellation') }}
          </SecondaryButton>
          <PrimaryButton class="ms-3" @click="cancelInvoice(invoiceToCancel)"
            :title="t('common.button.confirmInvoiceCancellationTitle')">
            {{ t('common.button.confirmInvoiceCancellation') }}
          </PrimaryButton>
        </template>
      </DialogModal>
      <Modal :show="invoiceNoteToUpdate !== null" @close="closeInvoiceNoteModal">
        <InvoiceNoteUpdate :invoice="invoiceNoteToUpdate" :form="updateInternalNoteForm"
          :submit="updateInvoiceInternalNote" />
      </Modal>
    </div>
  </div>
</template>
