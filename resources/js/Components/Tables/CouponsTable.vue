<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import { PhCaretRight, PhDotsThreeVertical } from '@phosphor-icons/vue';
import CouponForm from '@/Components/CouponForm.vue';
import Modal from '@/Components/Modal.vue';
import DialogModal from '@/Components/DialogModal.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';
import StatusChip from '../StatusChip.vue';
import TableFooter from '@Components/Tables/TableFooter.vue';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';


const props = defineProps({
  coupons: Object,
  entities: Object,
})

const form = useForm({});
const statusForm = useForm({ status: null })
const toast = useToast();
const { t } = useI18n();

const openCouponMenu = ref(null);
const openCouponStatusMenu = ref(null);
const couponToEdit = ref(null);
const notificationToSend = ref(null);
const couponToDelete = ref(null);

const toggleMenu = (couponId) => {
  openCouponMenu.value = openCouponMenu.value === couponId ? null : couponId;
  openCouponStatusMenu.value = openCouponStatusMenu.value === couponId && null;
}

const toggleCouponStatusMenu = (couponId) => {
  openCouponStatusMenu.value = openCouponStatusMenu.value === couponId ? null : couponId;
}

const isCouponMenuOpen = (couponId) => openCouponMenu.value === couponId;

const isCouponStatusMenuOpen = (couponId) => openCouponStatusMenu.value === couponId;

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openCouponMenu.value = null;
  }
};

const editCoupon = (coupon) => {
  couponToEdit.value = coupon;
}

const closeCouponModal = () => {
  couponToEdit.value = null;
}

const editCouponStatus = (coupon, status) => {
  statusForm.status = status

  statusForm.patch(route('coupon.updateStatus', coupon.id), {
    preserveScroll: true,
    onSuccess: () => {
      toggleMenu(coupon.id)
      toast.open({
        message: `${t('common.toast.coupon.couponStatusUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.coupon.couponStatusUpdate.errorMessage')}!`,
        type: 'error',
        position: 'top',
        duration: 4000
      })
    }
  })
}

const confirmUserNotifications = (coupon) => {
  notificationToSend.value = coupon;
}

const closeNotificationModal = () => {
  notificationToSend.value = null;
}

const sendUserNotifications = (coupon) => {
  router.post(route('coupon.sendUserNotifications', coupon.id), {}, {
    preserveScroll: true,
    onSuccess: () => {
      closeNotificationModal();
      toast.open({
        message: `${t('common.toast.coupon.couponNotifyUsers.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000
      })
    },
    onError: (errors) => {
      toast.open({
        message: `${t('common.toast.coupon.couponNotifyUsers.errorMessage')}! ${errors.message || ''}`,
        type: 'error',
        position: 'top',
        duration: 4000
      })
    },
  });
};


const confirmCouponDeletion = (coupon) => {
  couponToDelete.value = coupon;
}

const closeDeleteModal = () => {
  couponToDelete.value = null;
}

const destroy = (coupon) => {
  form.delete(route('coupon.delete', coupon), {
    preserveScroll: true,
    onSuccess: () => {
      closeDeleteModal();
      toast.open({
        message: `${t('common.toast.coupon.couponDelete.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      })
    },
    onError: () => {
      toast.open({
        message: `${t('common.toast.coupon.couponDelete.errorMessage')}!`,
        type: 'error',
        position: 'top',
        duration: 4000
      })
    },
    onFinish: () => form.reset()
  })
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.coupon.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr
            class="grid grid-cols-[3fr,2fr,1fr,1fr,2fr,2fr,4fr,1fr,1fr] md:grid-cols-[2fr,1fr,1fr,1fr,2fr,2fr,2fr,1fr,1fr]">
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
            <tr v-for="coupon in coupons.data" :key="coupon.id"
              class="grid grid-cols-[3fr,2fr,1fr,1fr,2fr,2fr,4fr,1fr,1fr] md:grid-cols-[2fr,1fr,1fr,1fr,2fr,2fr,2fr,1fr,1fr] border-b dark:border-neutral-200 even:bg-slate-100">
              <th scope="row" class="place-content-center px-6 py-4 w-48">{{ coupon.code }}</th>
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
                  <button @click.stop="toggleMenu(coupon.id)" :title="t('common.button.couponActions')"
                    class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                    <PhDotsThreeVertical :size="20" />
                  </button>
                  <div v-if="isCouponMenuOpen(coupon.id)"
                    class="absolute z-10 right-0 top-0 px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                    <Link :href="route('coupon.show', { coupon: coupon })" :title="t('common.button.viewDetails')"
                      class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                      {{ t('common.button.viewDetails') }}
                    </Link>
                    <button @click="editCoupon(coupon)" :title="t('common.button.updateCoupon')"
                      class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                      {{ t('common.button.updateCoupon') }}
                    </button>
                    <button @click.stop="toggleCouponStatusMenu(coupon.id)" :title="t('common.button.markAs')"
                      class="relative flex items-center justify-between w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                      {{ t('common.button.markAs') }}
                      <PhCaretRight :size="14" />
                    </button>
                    <div v-if="isCouponStatusMenuOpen(coupon.id)"
                      class="absolute z-10 -left-32 top-12 px-1 py-1 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 w-max flex flex-col rounded-md mt-6">
                      <button @click="editCouponStatus(coupon, 'active')" :title="t('common.button.active')"
                        class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                        {{ t('common.button.active') }}
                      </button>
                      <button @click="editCouponStatus(coupon, 'inactive')" :title="t('common.button.inactive')"
                        class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                        {{ t('common.button.inactive') }}
                      </button>
                      <button @click="editCouponStatus(coupon, 'archived')" :title="t('common.button.archived')"
                        class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                        {{ t('common.button.archived') }}
                      </button>
                    </div>
                    <button :title="t('common.button.notifyUsers')" @click=confirmUserNotifications(coupon)
                      class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                      {{ t('common.button.notifyUsers') }}
                    </button>
                    <button :title="t('common.button.deleteCoupon')" @click="confirmCouponDeletion(coupon)"
                      class="flex w-full px-2 py-2 rounded-md text-xs md:text-sm hover:bg-slate-100">
                      {{ t('common.button.deleteCoupon') }}
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
        <TableFooter :pagination="coupons" />
      </table>
      <Modal :show="couponToEdit !== null" @close="closeCouponModal">
        <CouponForm :entities="entities" :coupon="couponToEdit" formType="update" :close="closeCouponModal" />
      </Modal>
      <DialogModal :show="notificationToSend !== null" @close="closeNotificationModal">
        <template #title>
          {{ t('common.modal.coupon.couponNotifyUsers.title', { coupon: notificationToSend?.code }) }}?
        </template>
        <template #content>
          {{ t('common.modal.coupon.couponNotifyUsers.content', { coupon: notificationToSend?.code }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeNotificationModal" :title="t('common.button.cancel')">{{
            t('common.button.cancel') }}</SecondaryButton>
          <PrimaryButton class="ms-3" @click="sendUserNotifications(notificationToSend)"
            :title="t('common.button.notifyUsers')">{{ t('common.button.notifyUsers') }}</PrimaryButton>
        </template>
      </DialogModal>
      <DialogModal :show="couponToDelete !== null" @close="closeDeleteModal">
        <template #title>
          {{ t('common.modal.coupon.couponDelete.title', { coupon: couponToDelete?.code }) }}?
        </template>
        <template #content>
          {{ t('common.modal.coupon.couponDelete.content', { coupon: couponToDelete?.code }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeDeleteModal" :title="t('common.button.cancel')">{{ t('common.button.cancel') }}
          </SecondaryButton>
          <DangerButton class="ms-3" @click="destroy(couponToDelete)" :title="t('common.button.delete')">{{
            t('common.button.delete') }}</DangerButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
