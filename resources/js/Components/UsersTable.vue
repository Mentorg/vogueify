<script setup>
import { defineProps } from "vue";
import { PhDotsThreeVertical, PhCaretLeft, PhCaretRight } from '@phosphor-icons/vue';
import { formatDate } from "@/utils/dateFormat.js";

const props = defineProps({
  users: Object
});
</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-full">
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
            <th scope="col" class="px-6 py-4">
              Actions
            </th>
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
              <button>
                <PhDotsThreeVertical :size="20" />
              </button>
            </td>
          </tr>
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
