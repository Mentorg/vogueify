<script setup>
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
import StatusChip from '@/Components/StatusChip.vue';
import MenuItem from '@/Components/MenuItem.vue';
import ContextMenu from '@/Components/ContextMenu.vue';
import TableFooter from '@/Components/Tables/TableFooter.vue';
import DialogModal from '@/Components/DialogModal.vue';
import Modal from '@/Components/Modal.vue';
import InvoiceNoteUpdate from '@/Components/InvoiceNoteUpdate.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { useContextMenu } from '@/composables/useContextMenu';
import { useResendInvoice } from '@/composables/invoice/useResendInvoice';
import { useCancelInvoice } from '@/composables/invoice/useCancelInvoice';
import { useUpsertInvoiceNote } from '@/composables/invoice/useUpsertInvoiceNote';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  invoices: Array,
});

const { t } = useI18n();

const {
  isContextMenuOpen,
  dropdownStyle,
  toggleContextMenu,
} = useContextMenu();
const {
  resendInvoiceTarget,
  isResendInvoiceModalOpen,
  openResendInvoiceModal,
  closeResendInvoiceModal,
  resendInvoice,
} = useResendInvoice();
const {
  cancelInvoiceTarget,
  isCancelInvoiceModalOpen,
  openCancelInvoiceModal,
  closeCancelInvoiceModal,
  cancelInvoice,
} = useCancelInvoice();
const {
  invoiceNoteTarget,
  isUpsertInvoiceNoteModalOpen,
  upsertInvoiceNoteForm,
  openUpsertInvoiceNoteModal,
  closeUpsertInvoiceNoteModal,
  upsertInvoiceNote,
} = useUpsertInvoiceNote();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto isolate">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.invoice.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 z-20 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,2fr,2fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4 text-xs">#</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.number') }}</th>
            <th scope="col" class="flex items-center gap-2 px-6 py-4 text-xs">{{ t('common.table.invoice.issuedAt') }}
            </th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.amount') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.invoice.status') }}</th>
            <th scope="col" class="px-6 py-4 text-xs">{{ t('common.table.actions') }}</th>
          </tr>
        </thead>
        <tbody>
          <div v-if="invoices.data.length > 0">
            <tr v-for="(invoice, index) in invoices.data" :key="invoice.id"
              class="grid grid-cols-[0.5fr,2fr,2fr,2fr,2fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th class="place-content-center px-6 py-4">{{ (invoices.current_page - 1) * invoices.per_page +
                index + 1 }}</th>
              <th class="place-content-center px-6 py-4">
                <Link :href="route('admin.invoices.view', { invoice: invoice.id })"
                  :title="t('common.button.viewInvoiceTitle')" class="hover:underline">{{
                    invoice.invoice_number }}</Link>
              </th>
              <td class="place-content-center px-6 py-4">{{ formatDate(invoice.issued_at, '.', true) }}</td>
              <td class="place-content-center px-6 py-4">{{ invoice.currency === 'EUR' ? "€" : "$" }}{{
                invoice.total }}</td>
              <td class="place-content-center px-6 py-4 flex justify-start h-fit">
                <StatusChip :status="invoice.status">{{ capitalize(invoice.status) }}</StatusChip>
              </td>
              <td class="place-content-center px-6 py-4 flex items-center context-menu-wrapper">
                <div class="relative">
                  <button @click.stop="(e) => toggleContextMenu(invoice.id, e)"
                    :title="t('common.button.moreActionsTitle')"
                    class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                    <PhDotsThreeVertical :size="20" />
                  </button>
                  <ContextMenu :state="isContextMenuOpen" :entity="invoice" :style="dropdownStyle">
                    <MenuItem :href="route('admin.invoices.download', { invoice: invoice.id })"
                      :title="t('common.button.downloadInvoicePDFTitle')">
                      <PhDownloadSimple :size="16" color="green" />
                      {{ t('common.button.downloadPDF') }}
                    </MenuItem>
                    <MenuItem :action="() => openResendInvoiceModal(invoice)"
                      :title="t('common.button.resendInvoiceTitle')">
                      <PhEnvelope :size="16" color="green" />
                      {{ t('common.button.resendInvoice') }}
                    </MenuItem>
                    <MenuItem :action="() => openCancelInvoiceModal(invoice)"
                      :title="t('common.button.markAsCancelledTitle')" :disabled="invoice.status === 'cancelled'">
                      <PhProhibit :size="16" color="red" />
                      {{ t('common.button.markAsCancelled') }}
                    </MenuItem>
                    <MenuItem :href="route('admin.order', { order: invoice.order_id })"
                      :title="t('common.button.viewInvoiceOrderTitle')">
                      <PhEye :size="16" color="blue" />
                      {{ t('common.button.viewOrder') }}
                    </MenuItem>
                    <MenuItem :action="() => openUpsertInvoiceNoteModal(invoice)"
                      :title="invoice.internal_note ? t('common.button.updateInternalNote') : t('common.button.addInternalNote')">
                      <PhPencilSimple :size="16" color="green" />
                      {{ invoice.internal_note ? t('common.button.updateInternalNote') :
                        t('common.button.addInternalNote') }}
                    </MenuItem>
                  </ContextMenu>
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
      <DialogModal :show="isResendInvoiceModalOpen" @close="closeResendInvoiceModal">
        <template #title>
          {{ t('common.modal.invoice.resendInvoice.title', {
            invoice: resendInvoiceTarget?.invoice_number, user:
              resendInvoiceTarget?.user.name
          }) }}?
        </template>
        <template #content>
          {{ t('common.modal.invoice.resendInvoice.content', {
            invoice: resendInvoiceTarget?.invoice_number, user:
              resendInvoiceTarget?.user.name
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
      <DialogModal :show="isCancelInvoiceModalOpen" @close="closeCancelInvoiceModal">
        <template #title>
          {{ t('common.modal.invoice.cancelInvoice.title', {
            invoice: cancelInvoiceTarget?.invoice_number
          }) }}?
        </template>
        <template #content>
          {{ t('common.modal.invoice.cancelInvoice.content', {
            invoice: cancelInvoiceTarget?.invoice_number
          }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeCancelInvoiceModal" :title="t('common.button.abortInvoiceCancellationTitle')">
            {{ t('common.button.abortInvoiceCancellation') }}
          </SecondaryButton>
          <PrimaryButton @click="cancelInvoice(cancelInvoiceTarget)"
            :title="t('common.button.confirmInvoiceCancellationTitle')" class="ms-3">
            {{ t('common.button.confirmInvoiceCancellation') }}
          </PrimaryButton>
        </template>
      </DialogModal>
      <Modal :show="isUpsertInvoiceNoteModalOpen" @close="closeUpsertInvoiceNoteModal">
        <InvoiceNoteUpdate :invoice="invoiceNoteTarget" :form="upsertInvoiceNoteForm" :submit="upsertInvoiceNote" />
      </Modal>
    </div>
  </div>
</template>
