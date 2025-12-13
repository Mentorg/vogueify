<script setup>
import { useI18n } from 'vue-i18n';

const props = defineProps({
  users: Array,
  coupon: Object,
})

const { t } = useI18n();

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-scroll">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[1fr,2fr,4fr,4fr,4fr]">
            <th scope="col" class="px-6 py-4 w-24">#</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponUsers.avatar') }}</th>
            <th scope="col" class="px-6 py-4 w-52">{{ t('common.table.couponUsers.name') }}</th>
            <th scope="col" class="px-6 py-4 w-42">{{ t('common.table.couponUsers.email') }}</th>
            <th scope="col" class="px-6 py-4 w-52">{{ t('common.table.couponUsers.redemptions') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in users" :key="user.id"
            class="grid grid-cols-[1fr,2fr,4fr,4fr,4fr] border-b border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4 w-24">{{ user.id }}</th>
            <th class="px-6 py-4 w-28">
              <img :src="user.profile_photo_url" alt="" class="w-8 h-8 rounded-full object-cover" />
            </th>
            <th scope="row" class="px-6 py-4 w-52">
              {{ user.name }}
            </th>
            <td class="px-6 py-4 w-42">{{ user.email }}</td>
            <td class="px-6 py-4 w-52">{{ user.pivot.uses }}/{{ coupon.max_uses_per_user }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
