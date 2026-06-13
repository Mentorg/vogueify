<script setup>
import { inject, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import {
  PhArchive,
  PhBellRinging,
  PhCaretRight,
  PhCheck,
  PhDotsThreeVertical,
  PhPencilSimple,
  PhProhibit,
  PhTrash
} from '@phosphor-icons/vue';
import StatusChip from '@Components/StatusChip.vue';
import ContextMenu from '@Components/ContextMenu.vue';
import MenuItem from '@Components/MenuItem.vue';
import CouponForm from '@Components/CouponForm.vue';
import Modal from '@Components/Modal.vue';
import DialogModal from '@Components/DialogModal.vue';
import PrimaryButton from '@Components/PrimaryButton.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import DangerButton from '@Components/DangerButton.vue';
import TableFooter from '@Components/Tables/TableFooter.vue';
import { useContextMenu } from '@/composables/useContextMenu';
import { useSendCouponNotification } from '@/composables/coupon/useSendCouponNotification';
import { useUpdateCouponStatus } from '@/composables/coupon/useUpdateCouponStatus';
import { useDeleteCoupon } from '@/composables/coupon/useDeleteCoupon';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

defineProps({
  coupons: Object,
  entities: Object,
})

const { t } = useI18n();

const {
  isUpdateCouponModalOpen,
  openUpdateCouponModal,
  closeUpdateCouponModal,
} = inject('couponManager');

const {
  isContextMenuOpen,
  isSubMenuOpen,
  dropdownStyle,
  toggleContextMenu,
  toggleSubMenu,
  closeSubMenu
} = useContextMenu();
const {
  sendCouponNotificationTarget,
  isSendCouponNotificationModalOpen,
  openSendCouponNotificationModal,
  closeSendCouponNotificationModal,
  sendCouponNotification,
} = useSendCouponNotification();
const { updateCouponStatus } = useUpdateCouponStatus();
const {
  deleteCouponTarget,
  isDeleteCouponModalOpen,
  openDeleteCouponModal,
  closeDeleteCouponModal,
  deleteCoupon,
} = useDeleteCoupon();

watch(isContextMenuOpen, (val) => !val && closeSubMenu())

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto isolate">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.coupon.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 z-20 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr
            class="grid grid-cols-[0.5fr,3fr,2fr,1fr,1fr,2fr,2fr,4fr,1fr,1fr] md:grid-cols-[0.5fr,2fr,1fr,1fr,1fr,2fr,2fr,2fr,1fr,1fr]">
            <th scope="col" class="px-6 py-4 text-xs">#</th>
            <th scope="col" class="px-6 py-4 w-48 text-xs">{{ t('common.table.coupon.code') }}</th>
            <th scope="col" class="px-6 py-4 w-32 text-xs">{{ t('common.table.coupon.type') }}</th>
            <th scope="col" class="px-6 py-4 w-32 text-xs">{{ t('common.table.coupon.entity') }}</th>
            <th scope="col" class="px-6 py-4 w-24 text-xs">{{ t('common.table.coupon.value') }}</th>
            <th scope="col" class="px-6 py-4 w-32 text-xs">{{ t('common.table.coupon.createdAt') }}</th>
            <th scope="col" class="px-6 py-4 w-32 text-xs">{{ t('common.table.coupon.expiresAt') }}</th>
            <th scope="col" class="px-6 py-4 w-52 text-xs">{{ t('common.table.coupon.redemptions') }}</th>
            <th scope="col" class="px-6 py-4 w-28 text-xs">{{ t('common.table.coupon.status') }}</th>
            <th scope="col" class="px-6 py-4 w-28 text-xs"></th>
          </tr>
        </thead>
        <tbody>
          <div v-if="coupons.data.length > 0">
            <tr v-for="(coupon, index) in coupons.data" :key="coupon.id"
              class="grid grid-cols-[0.5fr,3fr,2fr,1fr,1fr,2fr,2fr,4fr,1fr,1fr] md:grid-cols-[0.5fr,2fr,1fr,1fr,1fr,2fr,2fr,2fr,1fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th scope="row" class="place-content-center px-6 py-4">{{ (coupons.current_page - 1) * coupons.per_page +
                index + 1 }}</th>
              <th scope="row" class="place-content-center px-6 py-4 w-48">
                <Link :href="route('coupon.show', { coupon: coupon })" :title="t('common.button.viewCouponTitle')"
                  class="hover:underline">
                  {{ coupon.code }}
                </Link>
              </th>
              <td class="place-content-center px-6 py-4 w-32">{{ capitalize(coupon.type) }}</td>
              <td class="place-content-center px-6 py-4 w-32">{{ capitalize(coupon.couponType) }}</td>
              <td class="place-content-center px-6 py-4 w-24">{{ coupon.type === 'percentage' ?
                `%${parseInt(coupon.value)}`
                : `$${parseInt(coupon.value)}` }}</td>
              <td class="place-content-center px-6 py-4 w-32">
                {{ formatDate(coupon.starts_at, '.', true) }}
              </td>
              <td class="place-content-center px-6 py-4 w-32">{{ formatDate(coupon.expires_at, '.', true) }}</td>
              <td class="place-content-center px-6 py-4 w-52">
                {{coupon.users.map(user => user.pivot.uses).reduce((acc, curr) => acc + curr, 0)}} / {{
                  coupon.max_uses }} ({{
                  coupon.max_uses_per_user }} {{ t('common.table.coupon.perUser') }})</td>
              <td class="place-content-center px-6 py-4 w-28">
                <StatusChip :status="coupon.status">{{ capitalize(coupon.status) }}</StatusChip>
              </td>
              <td class="place-content-center px-6 py-4 w-28 flex items-center context-menu-wrapper">
                <div class="relative context-menu-wrapper">
                  <button @click.stop="(e) => toggleContextMenu(coupon.id, e)"
                    :title="t('common.button.moreActionsTitle')"
                    class="relative rounded-full p-0.5 transition-all hover:bg-slate-200">
                    <PhDotsThreeVertical :size="20" />
                  </button>
                  <ContextMenu :state="isContextMenuOpen" :entity="coupon" :style="dropdownStyle">
                    <MenuItem :action="() => openUpdateCouponModal(coupon)"
                      :title="t('common.button.updateCouponTitle')">
                      <PhPencilSimple :size="16" color="green" />
                      {{ t('common.button.update') }}
                    </MenuItem>
                    <MenuItem :action="(e) => { e.stopPropagation(); toggleSubMenu(coupon.id) }"
                      :title="t('common.button.markCouponAsTitle')">
                      <PhPencilSimple :size="16" />
                      {{ t('common.button.markAs') }}
                      <PhCaretRight :size="14" />
                    </MenuItem>
                    <div v-if="isSubMenuOpen"
                      class="absolute z-10 -left-32 top-2.5 px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                      <MenuItem :action="() => updateCouponStatus(coupon, 'active')" :title="t('common.button.active')">
                        <PhCheck :size="14" color="green" />
                        {{ t('common.button.active') }}
                      </MenuItem>
                      <MenuItem :action="() => updateCouponStatus(coupon, 'inactive')"
                        :title="t('common.button.inactive')">
                        <PhProhibit :size="14" color="grey" />
                        {{ t('common.button.inactive') }}
                      </MenuItem>
                      <MenuItem :action="() => updateCouponStatus(coupon, 'archived')"
                        :title="t('common.button.archived')">
                        <PhArchive :size="14" color="red" />
                        {{ t('common.button.archived') }}
                      </MenuItem>
                    </div>
                    <MenuItem :action="() => openSendCouponNotificationModal(coupon)"
                      :title="t('common.button.notifyUsers')">
                      <PhBellRinging :size="14" color="blue" />
                      {{ t('common.button.notifyUsers') }}
                    </MenuItem>
                    <MenuItem :action="() => openDeleteCouponModal(coupon)"
                      :title="t('common.button.deleteCouponTitle')">
                      <PhTrash :size="16" color="red" />
                      {{ t('common.button.delete') }}
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
        <TableFooter :pagination="coupons" />
      </table>
      <Modal :show="isUpdateCouponModalOpen" @close="closeUpdateCouponModal">
        <CouponForm :entities="entities" />
      </Modal>
      <DialogModal :show="isSendCouponNotificationModalOpen" @close="closeSendCouponNotificationModal">
        <template #title>
          {{ t('common.modal.coupon.couponNotifyUsers.title', { coupon: sendCouponNotificationTarget?.code }) }}?
        </template>
        <template #content>
          {{ t('common.modal.coupon.couponNotifyUsers.content', { coupon: sendCouponNotificationTarget?.code }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeSendCouponNotificationModal"
            :title="t('common.button.cancelUserCouponNotificationTitle')">{{
              t('common.button.cancel') }}</SecondaryButton>
          <PrimaryButton @click="sendCouponNotification(sendCouponNotificationTarget)"
            :title="t('common.button.sendUserCouponNotificationTitle')" class="ms-3">{{ t('common.button.notifyUsers')
            }}</PrimaryButton>
        </template>
      </DialogModal>
      <DialogModal :show="isDeleteCouponModalOpen" @close="closeDeleteCouponModal">
        <template #title>
          {{ t('common.modal.coupon.couponDelete.title', { coupon: deleteCouponTarget?.code }) }}?
        </template>
        <template #content>
          {{ t('common.modal.coupon.couponDelete.content', { coupon: deleteCouponTarget?.code }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeDeleteCouponModal" :title="t('common.button.cancelCouponDeletionTitle')">{{
            t('common.button.cancel') }}
          </SecondaryButton>
          <DangerButton @click="deleteCoupon(deleteCouponTarget)" :title="t('common.button.confirmCouponDeletionTitle')"
            class="ms-3">{{
              t('common.button.delete') }}</DangerButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
