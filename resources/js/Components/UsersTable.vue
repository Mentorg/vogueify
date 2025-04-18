<script setup>
import { defineProps, onBeforeUnmount, onMounted, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { PhDotsThreeVertical, PhCaretLeft, PhCaretRight, PhTrash } from '@phosphor-icons/vue';
import { formatDate } from "@/utils/dateFormat.js";
import DialogModal from "./DialogModal.vue";
import SecondaryButton from "./SecondaryButton.vue";
import DangerButton from "./DangerButton.vue";

const props = defineProps({
  users: Object
});

const form = useForm({});

const openMenu = ref(null);
const userToDelete = ref(null);

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
    onSuccess: () => closeModal(),
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
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[0.5fr,2fr,3fr,2fr,2fr,1fr]">
            <th scope="col" class="px-6 py-4">#</th>
            <th scope="col" class="px-6 py-4">
              Name
            </th>
            <th scope="col" class="px-6 py-4">
              Email
            </th>
            <th scope="col" class="px-6 py-4">
              Role
            </th>
            <th scope="col" class="px-6 py-4">Created</th>
            <th scope="col" class="px-6 py-4">&nbsp;</th>
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
                    Delete
                  </button>
                </div>
              </div>
            </td>
          </tr>
          <DialogModal :show="userToDelete !== null" @close="closeModal">
            <template #title>
              Delete '{{ userToDelete?.name }}'?
            </template>
            <template #content>
              Are you sure you want to delete '{{ userToDelete?.name }}'?
            </template>
            <template #footer>
              <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
              <DangerButton class="ms-3" @click="destroy(userToDelete.id)">
                <PhTrash :size="16" color="white" class="mr-2" />
                Delete
              </DangerButton>
            </template>
          </DialogModal>
        </tbody>
      </table>
      <nav class="bg-white sticky bottom-0 flex py-2 px-4 border-t-2 items-center justify-between text-sm"
        aria-label="Page navigation example">
        <p>
          Showing <strong>{{ users.from }}-{{ users.to }}</strong> of <strong>{{ users.total }}</strong>
        </p>
        <ul class="list-style-none flex gap-x-4 mx-2">
          <li v-if="users.prev_page_url">
            <button class="bg-slate-500 text-white flex items-center rounded px-3 py-1.5 text-sm"
              @click="router.visit(users.prev_page_url)">
              <PhCaretLeft :size="12" />
              Previous
            </button>
          </li>
          <li v-if="users.next_page_url">
            <button class="bg-slate-500 text-white flex items-center rounded px-3 py-1.5 text-sm"
              @click="router.visit(users.next_page_url)">
              Next
              <PhCaretRight :size="12" />
            </button>
          </li>
        </ul>
      </nav>
    </div>
  </div>
</template>
