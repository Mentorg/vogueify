<script setup>
import { defineProps, onBeforeUnmount, onMounted, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import { PhDotsThreeVertical, PhTrash } from '@phosphor-icons/vue';
import DialogModal from "@Components/DialogModal.vue";
import SecondaryButton from "@Components/SecondaryButton.vue";
import DangerButton from "@Components/DangerButton.vue";
import TableFooter from "@Components/Tables/TableFooter.vue";
import { formatDate } from "@/utils/dateFormat.js";

const props = defineProps({
  users: Object
});

const { t } = useI18n();
const toast = useToast();
const form = useForm({});
const openMenu = ref(null);
const userToDelete = ref(null);
const errorMessage = ref(null);

const toggleMenu = (id) => {
  openMenu.value = openMenu.value === id ? null : id;
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openMenu.value = null;
  }
}

const confirmUserDeletion = (user) => {
  userToDelete.value = user;
}

const destroy = (id) => {
  form.delete(route('user.destroy', id), {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('common.toast.user.userDelete.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      closeModal()
    },
    onError: (errors) => {
      toast.open({
        message: `${t('common.toast.user.userDelete.errorMessage')}! ` + errors.error,
        type: 'error',
        position: 'top',
        duration: 4000,
      });
      errorMessage.value = errors.error || `${t('common.toast.user.userDelete.errorMessage')}!`;
    },
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  userToDelete.value = null;
  form.reset()
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <caption class="sr-only">{{ t('common.table.users.caption') }}</caption>
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,2fr,3fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.name') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.email') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.role') }}</th>
            <th scope="col" class="px-6 py-4">{{ t('common.table.user.createdAt') }}</th>
            <th scope="col" class="px-6 py-4"></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users.data" :key="user.id"
            class="grid grid-cols-[0.5fr,2fr,3fr,2fr,2fr,1fr] border-b border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4">{{ user.id }}</th>
            <th scope="row" class="px-6 py-4">
              {{ user.name }}
            </th>
            <td class="px-6 py-4">{{ user.email }}</td>
            <td class="px-6 py-4">{{ user.role }}</td>
            <td class="px-6 py-4">{{ formatDate(user.created_at, '.') }}</td>
            <td class="px-6 py-4 justify-self-end">
              <button class="relative" @click.stop="toggleMenu(user.id)">
                <PhDotsThreeVertical :size="20" />
              </button>
              <div v-if="openMenu === user.id"
                class="absolute z-10 right-6 px-4 py-4 bg-white border border-gray-200 shadow-md hs-dropdown-menu min-w-32 flex flex-col gap-y-3 rounded-md mt-2">
                <div class="flex items-center gap-x-2 transition-all hover:text-slate-500">
                  <button @click="confirmUserDeletion(user)" class="flex items-center gap-2">
                    <PhTrash :size="16" color="red" />
                    {{ t('common.button.delete') }}
                  </button>
                </div>
              </div>
            </td>
          </tr>
          <DialogModal :show="userToDelete !== null" @close="closeModal">
            <template #title>
              {{ t('common.modal.user.title', { userToDelete: userToDelete?.name }) }}?
            </template>
            <template #content>
              {{ t('common.modal.user.content', { userToDelete: userToDelete?.name }) }}?
            </template>
            <template #footer>
              <SecondaryButton @click="closeModal">{{ t('common.button.cancel') }}</SecondaryButton>
              <DangerButton class="ms-3" @click="destroy(userToDelete.id)">
                <PhTrash :size="16" color="white" class="mr-2" />
                {{ t('common.button.delete') }}
              </DangerButton>
            </template>
          </DialogModal>
        </tbody>
        <TableFooter :pagination="users" />
      </table>
    </div>
  </div>
</template>
