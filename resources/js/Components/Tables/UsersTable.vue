<script setup>
import { useI18n } from 'vue-i18n';
import { PhTrash } from '@phosphor-icons/vue';
import DialogModal from "@/Components/DialogModal.vue";
import SecondaryButton from "@/Components/SecondaryButton.vue";
import DangerButton from "@/Components/DangerButton.vue";
import TableFooter from "@/Components/Tables/TableFooter.vue";
import { useDeleteUser } from "@/composables/user/useDeleteUser";
import { capitalize } from '@/utils/capitalize';
import { formatDate } from "@/utils/dateFormat.js";

defineProps({
  users: Object
});

const { t } = useI18n();
const {
  deleteUserTarget,
  isDeleteUserModalOpen,
  openDeleteUserModal,
  closeDeleteUserModal,
  deleteUser
} = useDeleteUser();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.user.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,3fr,3fr,1fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.name') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.email') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.role') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.createdAt') }}</th>
            <th scope="col" class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(user, index) in users.data" :key="user.id"
            class="grid grid-cols-[0.5fr,3fr,3fr,1fr,2fr,1fr] border-b border-neutral-200 even:bg-slate-100">
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
            <td class="px-6 py-4">{{ formatDate(user.created_at, '.') }}</td>
            <td class="px-6 py-4 justify-self-end">
              <button v-if="user.role !== 'admin'" @click="openDeleteUserModal(user)"
                :title="t('common.button.deleteUserTitle')" class="rounded-full p-1 transition-all hover:bg-slate-200">
                <PhTrash :size="20" color="red" />
              </button>
            </td>
          </tr>
        </tbody>
        <TableFooter :pagination="users" />
      </table>
      <DialogModal :show="isDeleteUserModalOpen" @close="closeDeleteUserModal">
        <template #title>
          {{ t('common.modal.user.title', { userToDelete: deleteUserTarget?.name }) }}?
        </template>
        <template #content>
          {{ t('common.modal.user.content', { userToDelete: deleteUserTarget?.name }) }}?
        </template>
        <template #footer>
          <SecondaryButton @click="closeDeleteUserModal" :title="t('common.button.cancelUserDeletionTitle')">{{
            t('common.button.cancel') }}</SecondaryButton>
          <DangerButton @click="deleteUser(deleteUserTarget)" :title="t('common.button.confirmUserDeletionTitle')"
            class="ms-3">
            <PhTrash :size="16" color="white" class="mr-2" />
            {{ t('common.button.delete') }}
          </DangerButton>
        </template>
      </DialogModal>
    </div>
  </div>
</template>
