<script setup>
import { useI18n } from 'vue-i18n';
import { PhClockClockwise, PhTrash } from '@phosphor-icons/vue';
import StatusChip from '@/Components/StatusChip.vue';
import DialogModal from '@/Components/DialogModal.vue';
import DangerButton from '@/Components/DangerButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputError from '@/Components/InputError.vue';
import { useDeactivateUser } from '@/composables/user/useDeactivateUser';
import { usePermanentlyDeleteUser } from '@/composables/user/usePermanentlyDeleteUser';
import { useRestoreUser } from '@/composables/user/useRestoreUser';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

defineProps({
  user: Object,
});

const { t } = useI18n();

const {
  deactivateUserTarget,
  isDeactivateUserModalOpen,
  openDeactivateUserModal,
  closeDeactivateUserModal,
  deactivateUser
} = useDeactivateUser();
const {
  deleteUserTarget,
  deleteUserForm,
  confirmationInput,
  isDeleteUserModalOpen,
  canDeleteUser,
  openDeleteUserModal,
  closeDeleteUserModal,
  deleteUser
} = usePermanentlyDeleteUser();

const {
  restoreUserTarget,
  isRestoreUserModalOpen,
  restoreUserForm,
  openRestoreUserModal,
  closeRestoreUserModal,
  restoreUser,
} = useRestoreUser();

</script>

<template>
  <div class="flex flex-col gap-2 items-center justify-between py-6 md:gap-6 md:flex-row">
    <div class="flex items-center gap-8">
      <div class="flex flex-col">
        <h1 class="text-xl font-medium"><span v-if="!user.deleted_at">{{ capitalize(user.title) }}. </span>{{
          user.name }}</h1>
        <p class="text-xs text-slate-500">{{ capitalize(user.role) }}</p>
      </div>
      <div class="flex items-center gap-2">
        <StatusChip :status="user.email_verified_at ? 'verified' : 'unverified'">
          {{ capitalize(user.email_verified_at ? t('page.userDetails.emailVerified') :
            t('page.userDetails.emailUnverified')) }}
        </StatusChip>
        <StatusChip :status="user.deleted_at === null ? 'active' : 'deactivated'">{{ capitalize(user.deleted_at
          === null ? t('page.userDetails.statusActive') : t('page.userDetails.statusDeactivated'))
          }}</StatusChip>
      </div>
    </div>
    <div class="flex gap-4">
      <button v-if="user.deleted_at === null" @click="() => openDeactivateUserModal(user)"
        :title="t('common.button.deactivateUserTitle')"
        class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
        {{ t('common.button.deactivate') }}
      </button>
      <button v-if="user.deleted_at !== null" @click="() => openRestoreUserModal(user)"
        :title="t('common.button.confirmUserRestoreTitle')"
        class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
        {{ t('common.button.restore') }}
      </button>
      <button v-if="user.deleted_at !== null" @click="() => openDeleteUserModal(user)"
        :title="t('common.button.deleteUserTitle')"
        class="py-1 px-4 rounded-md transition-all text-white bg-black border border-black hover:cursor-pointer hover:bg-slate-700">
        {{ t('common.button.delete') }}
      </button>
    </div>
  </div>
  <div class="flex justify-between">
    <div class="flex flex-col gap-y-6 border-t divide-y py-6 md:divide-y-0 md:gap-x-12 md:flex-row md:divide-x">
      <div class="flex flex-col pt-6 gap-4">
        <div>
          <h2 class="font-medium text-lg">{{ t('common.table.user.email') }}</h2>
          <p>{{ user.email }}</p>
        </div>
        <div>
          <h2 class="font-medium text-lg">{{ t('page.userDetails.accountCreatedAt') }}</h2>
          <p>
            {{ formatDate(user.created_at, '.', true) }}
          </p>
        </div>
      </div>
    </div>
    <div v-if="user.deleted_at"
      class="flex flex-col gap-y-6 border-t divide-y py-6 md:divide-y-0 md:gap-x-12 md:flex-row md:divide-x">
      <div class="pt-6">
        <h2 class="text-xl font-medium">{{ t('page.userDetails.administrativeInformation') }}</h2>
        <p class="font-medium text-sm">{{ t('page.userDetails.originalName') }}: <span class="font-normal">{{
          user.deactivation_snapshot.name
            }}</span>
        </p>
        <p class="font-medium text-sm">{{ t('page.userDetails.originalEmail') }}: <span class="font-normal">{{
          user.deactivation_snapshot.email
            }}</span>
        </p>
        <p class="font-medium text-sm">{{ t('page.userDetails.deactivatedAt') }}: <span class="font-normal">{{
          formatDate(user.deleted_at,
            '.', true)
            }}</span>
        </p>
      </div>
    </div>
  </div>
  <DialogModal :show="isDeactivateUserModalOpen" @close="closeDeactivateUserModal">
    <template #title>
      {{ t('common.modal.user.deactivate.title', { userToDeactivate: deactivateUserTarget?.name }) }}?
    </template>
    <template #content>
      {{ t('common.modal.user.deactivate.content') }}.
    </template>
    <template #footer>
      <SecondaryButton @click="closeDeactivateUserModal" :title="t('common.button.cancelUserDeactivationTitle')">{{
        t('common.button.cancel') }}</SecondaryButton>
      <DangerButton @click="deactivateUser(deactivateUserTarget)"
        :title="t('common.button.confirmUserDeactivationTitle')" class="ms-3">
        <PhTrash :size="16" color="white" class="mr-2" />
        {{ t('common.button.deactivate') }}
      </DangerButton>
    </template>
  </DialogModal>
  <DialogModal :show="isDeleteUserModalOpen" @close="closeDeleteUserModal">
    <template #title>
      {{ t('common.modal.user.delete.title', { userToDelete: deleteUserTarget?.name, userID: deleteUserTarget?.id })
      }}
    </template>
    <template #content>
      <p class="my-4">{{ t('common.modal.user.delete.warning') }}</p>
      <p>{{ t('common.modal.user.delete.onlyFor') }}</p>
      <ul class="list-disc list-inside">
        <li>{{ t('common.modal.user.delete.testAccounts') }}</li>
        <li>{{ t('common.modal.user.delete.duplicateAccounts') }}</li>
        <li>{{ t('common.modal.user.delete.spamAccounts') }}</li>
      </ul>
      <p class="my-4">{{ t('common.modal.user.delete.transactionWarning') }}</p>
      <i18n-t keypath="common.modal.user.delete.confirmationPrompt" tag="p">
        <template #keyword>
          <strong>{{ t('common.modal.user.delete.deleteKeyword') }}</strong>
        </template>
      </i18n-t>
      <TextInput ref="confirmationInput" v-model="deleteUserForm.confirmation" type="text" class="mt-1 block w-3/4" />
      <InputError :message="deleteUserForm.errors.confirmation" class="mt-2" />
    </template>
    <template #footer>
      <SecondaryButton @click="closeDeleteUserModal" :title="t('common.button.cancelUserDeletionTitle')">{{
        t('common.button.cancel') }}</SecondaryButton>
      <DangerButton @click="deleteUser" :disabled="!canDeleteUser" :title="t('common.button.confirmUserDeletionTitle')"
        class="ms-3 disabled:bg-red-300">
        <PhTrash :size="16" color="white" class="mr-2" />
        {{ t('common.button.permanentlyDelete') }}
      </DangerButton>
    </template>
  </DialogModal>
  <DialogModal :show="isRestoreUserModalOpen" @close="closeRestoreUserModal">
    <template #title>
      {{ t('common.modal.user.restore.title') }}
    </template>
    <template #content>
      {{ t('common.modal.user.restore.content') }}
    </template>
    <template #footer>
      <SecondaryButton @click="closeRestoreUserModal" :title="t('common.button.cancelUserRestoreTitle')">{{
        t('common.button.cancel') }}</SecondaryButton>
      <DangerButton @click="restoreUser(restoreUserTarget)" :disabled="restoreUserForm.processing"
        :title="t('common.button.confirmUserRestoreTitle')" class="ms-3 disabled:bg-red-300">
        <PhClockClockwise :size="16" color="white" class="mr-2" />
        {{ restoreUserForm.processing ? t('common.button.restoring') : t('common.button.restore') }}
      </DangerButton>
    </template>
  </DialogModal>
</template>
