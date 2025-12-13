<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import CouponCategoriesTable from '@/Components/Tables/CouponCategoriesTable.vue';
import CouponProductsTable from '@/Components/Tables/CouponProductsTable.vue';
import CouponProductVariationsTable from '@/Components/Tables/CouponProductVariationsTable.vue';
import CouponUsersTable from '@/Components/Tables/CouponUsersTable.vue';
import { capitalize } from '@/utils/capitalize';
import { formatDate } from '@/utils/dateFormat';

const props = defineProps({
  coupon: Object,
})

const { t } = useI18n();

</script>

<template>

  <Head :title="t('page.couponDetails.coupon', { coupon: coupon.code })" />
  <AdminDashboard>
    <div class="flex justify-between items-center">
      <div class="flex flex-col gap-y-2">
        <div>
          <h1 class="text-2xl font-medium">{{ coupon.code }}</h1>
          <div class="w-fit my-2 border border-green-700 text-green-700 px-3 py-1 rounded-full">
            <p>{{ capitalize(coupon.status) }}</p>
          </div>
        </div>
        <p>
          {{ t('page.couponDetails.createdOn', {
            date: formatDate(coupon.created_at, '.', true),
            user: coupon.author.name
          }) }}
          <span class="text-sm text-slate-400">
            {{
              coupon.updated_at
                ? `(${t('page.couponDetails.updatedOn', {
                  date: formatDate(coupon.updated_at, '.', true),
                  user: coupon.updated_by.name
                })})`
                : ''
            }}
          </span>
        </p>
      </div>
    </div>
    <div class="flex flex-col gap-y-8 md:gap-y-12">
      <div class="flex flex-col gap-2 md:flex-row md:gap-8">
        <div class="mt-4 border p-4 rounded-md">
          <h2 class="text-lg font-medium">{{ t('page.couponDetails.couponDetails') }}</h2>
          <div class="flex flex-col mt-2">
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.code') }}: <span class="font-normal">{{ coupon.code
                  }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.type') }}: <span class="font-normal">{{
                capitalize(coupon.type) }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.entity') }}: <span class="font-normal">{{
                capitalize(coupon.couponType) }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.discount') }}:
                <span v-if="coupon.type === 'fixed'" class="font-normal">${{ coupon.value }}</span>
                <span v-else-if="coupon.type === 'percentage'" class="font-normal">{{ coupon.value }}%</span>
              </p>
            </div>
          </div>
        </div>
        <div class="mt-4 border p-4 rounded-md">
          <h2 class="text-lg font-medium">{{ t('page.couponDetails.usageRestrictions') }}</h2>
          <div class="flex flex-col mt-2">
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.timeframe') }}: <span class="font-normal">{{
                formatDate(coupon.starts_at, '.', true) }}
                  &rarr; {{
                    formatDate(coupon.expires_at, '.', true) }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.maximumUses') }}: <span class="font-normal">{{
                coupon.max_uses }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.maximumUsesPerUser') }}: <span class="font-normal">{{
                coupon.max_uses_per_user
                  }}</span></p>
            </div>
          </div>
        </div>
        <div class="mt-4 border p-4 rounded-md">
          <h2 class="text-lg font-medium">{{ t('page.couponDetails.statusAndTracking') }}</h2>
          <div class="flex flex-col mt-2">
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.status') }}: <span class="font-normal">{{
                capitalize(coupon.status) }}</span></p>
            </div>
            <div class="mt-2">
              <p class="font-medium">{{ t('page.couponDetails.redemptions') }}: <span
                  class="font-normal">{{coupon.users.map(user =>
                    user.pivot.uses).reduce((acc, curr) => acc + curr, 0)}}</span></p>
            </div>
          </div>
        </div>
      </div>
      <div class="flex flex-col gap-12 lg:flex-row lg:gap-8"
        :class="{ 'lg:flex-col': coupon.users.length > 1 && coupon.product_variations.length > 1 }">
        <div v-if="coupon.users.length === 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.user', 1) }}</h2>
          <div class="flex gap-8 border p-4 rounded-md mt-4">
            <div v-for="user in coupon.users" :key="user.id" class="flex gap-4 items-center">
              <img :src="user.profile_photo_url" :alt="t('page.couponDetails.userPicture', { user: user.name })"
                class="w-12 h-12 rounded-full object-cover" />
              <div>
                <p class="font-medium">{{ user.name }}</p>
                <p class="text-slate-400 text-sm">{{ user.email }}</p>
              </div>
              <div>
                <p class="text-sm"><span class="font-medium">{{ t('page.couponDetails.used') }}:</span> {{
                  user.pivot.uses }}/{{
                    coupon.max_uses_per_user }}</p>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="coupon.users.length > 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.user', 2) }}</h2>
          <CouponUsersTable :users="coupon.users" :coupon="coupon" />
        </div>
        <div v-if="coupon.product_variations.length === 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.productVariation', 1) }}</h2>
          <div class="flex gap-8 border p-4 rounded-md mt-4">
            <div v-for="variation in coupon.product_variations" :key="variation.id" class="flex gap-4 items-center">
              <img :src="variation.image"
                :alt="t('page.couponDetails.productImage', { product: variation.product.name })"
                class="w-12 h-12 object-cover" />
              <div class="flex flex-wrap gap-6">
                <div>
                  <p class="font-medium">{{ variation.product.name }}</p>
                  <p class="text-slate-400 text-sm">{{ t('page.couponDetails.sku') }}: {{ variation.sku }}</p>
                </div>
                <div>
                  <p>{{ t('page.couponDetails.category', 1) }}: {{ capitalize(variation.product.category.name) }}</p>
                  <p>{{ t('page.couponDetails.stock') }}: {{ variation.stock }}</p>
                </div>
                <div>
                  <p>{{ t('page.couponDetails.price') }}:
                    <span class="line-through text-slate-400">${{ variation.original_price.toFixed(2) }}</span>
                    <span class="ml-2 font-semibold text-green-700">${{ variation.discounted_price.toFixed(2) }}</span>
                    <span class="ml-2 text-sm text-green-600">(-${{ variation.discount_amount.toFixed(2) }})</span>
                  </p>
                  <p>{{ t('page.couponDetails.createdAt') }}: {{ formatDate(coupon.created_at, '.', true) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="coupon.product_variations.length > 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.productVariation', 2) }}</h2>
          <CouponProductVariationsTable :coupon="coupon" />
        </div>
        <div v-if="coupon.products.length === 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.product', 1) }}</h2>
          <div class="flex gap-8 border p-4 rounded-md mt-4">
            <div v-for="product in coupon.products" :key="product.id" class="flex gap-4 items-center">
              <div>
                <img :src="product.category.image"
                  :alt="t('page.couponDetails.productImage', { product: product.name })" class="w-12 h-12 object-cover">
              </div>
              <div class="flex gap-2">
                <div>
                  <p class="font-medium">{{ product.name }}</p>
                  <p class="text-slate-400 text-sm">{{ capitalize(product.category.name) }}</p>
                </div>
                <div>
                  <p>{{ t('page.couponDetails.gender') }}: {{ capitalize(product.gender) }}</p>
                  <p>{{ t('page.couponDetails.createdAt') }}: {{ formatDate(coupon.created_at, '.', true) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="coupon.products.length > 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.product', 2) }}</h2>
          <CouponProductsTable :coupon="coupon" />
        </div>
        <div v-if="coupon.categories.length === 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.category', 1) }}</h2>
          <div class="flex gap-8 border p-4 rounded-md mt-4">
            <div v-for="category in coupon.categories" :key="category.id" class="flex gap-4 items-center">
              <div>
                <img :src="category.image"
                  :alt="capitalize(t('page.couponDetails.categoryImage', { category: category.name }))"
                  class="w-12 h-12 object-cover">
              </div>
              <div class="flex gap-6">
                <div>
                  <p class="font-medium">{{ capitalize(category.name) }}</p>
                </div>
                <div>
                  <p>{{ t('page.couponDetails.createdAt') }}: {{ formatDate(coupon.created_at, '.', true) }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div v-else-if="coupon.categories.length > 1">
          <h2 class="text-xl font-semibold text-slate-600">{{ t('page.couponDetails.category', 2) }}</h2>
          <CouponCategoriesTable :coupon="coupon" />
        </div>
      </div>
    </div>
  </AdminDashboard>
</template>
