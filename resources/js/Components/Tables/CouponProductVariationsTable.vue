<script setup>
import { useI18n } from 'vue-i18n';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  coupon: Object,
})

const { t } = useI18n();

const variations = props.coupon.product_variations;

</script>

<template>
  <div class="relative overflow-x-auto bg-white h-[350px] overflow-y-auto">
    <div class="bg-white w-fit">
      <table class="text-left text-sm w-full">
        <thead
          class="bg-white uppercase tracking-wider sticky top-0 border-b-2 outline outline-2 outline-neutral-300 border-neutral-300">
          <tr class="grid grid-cols-[2fr,2fr,4fr,2fr,2fr,3fr,3fr,3fr]">
            <th scope="col" class="px-6 py-4 w-24">#</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponProductVariations.image') }}</th>
            <th scope="col" class="px-6 py-4 w-52">{{ t('common.table.couponProductVariations.name') }}</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponProductVariations.price') }}</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponProductVariations.stock') }}</th>
            <th scope="col" class="px-6 py-4 w-28">{{ t('common.table.couponProductVariations.sku') }}</th>
            <th scope="col" class="px-6 py-4 w-36">{{ t('common.table.couponProductVariations.category') }}</th>
            <th scope="col" class="px-6 py-4 w-32">{{ t('common.table.couponProductVariations.createdAt') }}</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="variation in variations" :key="variation.id"
            class="grid grid-cols-[2fr,2fr,4fr,2fr,2fr,3fr,3fr,3fr] border-b dark:border-neutral-200 even:bg-slate-100">
            <th class="px-6 py-4 w-24">{{ variation.id }}</th>
            <th class="px-6 py-4 w-28">
              <img :src="variation.image"
                :alt="t('page.couponDetails.productImage', { product: variation.product.name })"
                class="w-8 h-8 rounded-md object-cover" />
            </th>
            <th scope="row" class="px-6 py-4 w-52 overflow-hidden text-ellipsis whitespace-nowrap">
              {{ variation.product.name }}
            </th>
            <td class="px-6 py-4 w-28 flex flex-col">
              <p class="line-through text-slate-400">${{ variation.original_price.toFixed(2) }}</p>
              <p class="font-semibold text-green-700">${{ variation.discounted_price.toFixed(2) }}</p>
              <p class="text-sm text-green-600">(-${{ variation.discount_amount.toFixed(2) }})</p>
            </td>
            <td class="px-6 py-4 w-28">
              {{variation.stock !== null ? variation.stock : variation.sizes.map(record =>
                record.pivot.stock).reduce((result, item) =>
                  result + item, 0)}}
            </td>
            <td class="px-6 py-4 w-28">{{ variation.sku }}</td>
            <td class="px-6 py-4 w-36">
              {{ capitalize(variation.product.category.name) }}
            </td>
            <td class="px-6 py-4 w-32">{{ formatDate(coupon.created_at, '.') }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>
