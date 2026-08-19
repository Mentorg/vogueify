<script setup>
import { useI18n } from 'vue-i18n';
import { PhClockClockwise, PhDotsThreeVertical, PhTrash } from '@phosphor-icons/vue';
import StatusChip from '../StatusChip.vue';
import ContextMenu from '../ContextMenu.vue';
import MenuItem from '../MenuItem.vue';
import DialogModal from "@/Components/DialogModal.vue";
import TextInput from '../TextInput.vue';
import InputError from '../InputError.vue';
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TableFooter from "@/Components/Tables/TableFooter.vue";
import { useContextMenu } from '@/composables/useContextMenu.js';
import { useDeactivateUser } from "@/composables/user/useDeactivateUser.js";
import { usePermanentlyDeleteUser } from '@/composables/user/usePermanentlyDeleteUser.js';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from "@/utils/dateFormat.js";
import { useRestoreUser } from '@/composables/user/useRestoreUser.js';

defineProps({
  users: Object
});

const { t } = useI18n();

const {
  isContextMenuOpen,
  dropdownStyle,
  toggleContextMenu,
} = useContextMenu();
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
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.user.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,3fr,3fr,1fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.name') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.email') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.role') }}</th>
            <th scope="col" class="px-6 py-4">Status</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.createdAt') }}</th>
            <th scope="col" class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in users.data" :key="user.id"
            class="grid grid-cols-[0.5fr,3fr,3fr,1fr,2fr,2fr,1fr] border-b border-neutral-200 items-baseline even:bg-slate-100">
            <th class="px-6 py-4">{{ (users.current_page - 1) * users.per_page +
              index + 1 }}</th>
            <th scope="row" class="px-6 py-4">
              <img v-if="user.profile_photo_url" :src="user.profile_photo_url"
                :alt="t('page.user.profile.basicInfo.picture', { user: user.name })"
                class="w-8 h-8 rounded-full inline-block mr-2">
              {{ user.name }}
            </th>
            <td class="px-6 py-4">{{ user.email }}</td>
            <td class="px-6 py-4">{{ capitalize(user.role) }}</td>
            <td class="px-6 py-4">
              <StatusChip :status="user.deleted_at === null ? 'active' : 'deactivated'">{{ capitalize(user.deleted_at
                === null ? 'Active' : 'Deactivated')
                }}</StatusChip>
            </td>
            <td class=" px-6 py-4">{{ formatDate(user.created_at, '.') }}
            </td>
            <td class="px-6 py-4 justify-self-end">
              <button v-if="user.role !== 'admin'" @click.stop="(e) => toggleContextMenu(user.id, e)"
                :title="t('common.button.moreActionsTitle')"
                class="rounded-full p-0.5 transition-all hover:bg-slate-200">
                <PhDotsThreeVertical :size="20" />
              </button>
              <ContextMenu v-if="user.role !== 'admin'" :state="isContextMenuOpen" :entity="user"
                :style="dropdownStyle">
                <MenuItem v-if="user.deleted_at === null" :action="() => openDeactivateUserModal(user)"
                  :title="t('common.button.deactivateUserTitle')">
                  <PhTrash :size="16" color="red" />
                  {{ t('common.button.deactivate') }}
                </MenuItem>
                <MenuItem v-if="user.deleted_at !== null" :action="() => openRestoreUserModal(user)"
                  :title="t('common.button.confirmUserRestoreTitle')">
                  <PhClockClockwise :size="16" color="blue" />
                  {{ t('common.button.restore') }}
                </MenuItem>
                <MenuItem v-if="user.deleted_at !== null" :action="() => openDeleteUserModal(user)"
                  :title="t('common.button.deleteUserTitle')">
                  <PhTrash :size="16" color="red" />
                  {{ t('common.button.delete') }}
                </MenuItem>
              </ContextMenu>
            </td>
          </tr>
        </tbody>
        <TableFooter :pagination="users" />
      </table>
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
              <strong>DELETE</strong>
            </template>
          </i18n-t>
          <TextInput ref="confirmationInput" v-model="deleteUserForm.confirmation" type="text"
            class="mt-1 block w-3/4" />
          <InputError :message="deleteUserForm.errors.confirmation" class="mt-2" />
        </template>
        <template #footer>
          <SecondaryButton @click="closeDeleteUserModal" :title="t('common.button.cancelUserDeletionTitle')">{{
            t('common.button.cancel') }}</SecondaryButton>
          <DangerButton @click="deleteUser" :disabled="!canDeleteUser"
            :title="t('common.button.confirmUserDeletionTitle')" class="ms-3 disabled:bg-red-300">
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
    </div>
  </div>
</template>
