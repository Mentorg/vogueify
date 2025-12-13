<script setup>
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  coupon: Object,
})

const { t } = useI18n();

const categories = props.coupon.categories;

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[1fr,2fr,4fr,3fr]">
            <th scope="col" class="px-6 py-4 w-24">#</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponCategories.image') }}</th>
            <th scope="col" class="px-6 py-4 w-52">{{ t('common.table.couponCategories.name') }}</th>
            <th scope="col" class="px-6 py-4 w-32">{{ t('common.table.couponCategories.createdAt') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="category in categories" :key="category.id"
            class="grid grid-cols-[1fr,2fr,4fr,3fr] border-b dark:border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4 w-24">{{ category.id }}</th>
            <th class="px-6 py-4 w-28">
              <img :src="category.image"
                :alt="capitalize(t('page.couponDetails.categoryImage', { category: category.name }))"
                class="w-8 h-8 rounded-md object-cover" />
            </th>
            <th scope="row" class="px-6 py-4 w-52 overflow-hidden text-ellipsis whitespace-nowrap">
              {{ capitalize(category.name) }}
            </th>
            <td class="px-6 py-4 w-32">{{ formatDate(coupon.created_at, '.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
