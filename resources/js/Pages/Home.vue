<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue'
import Modal from '@Components/Modal.vue';
import Footer from '@/Layouts/Footer.vue';
import categoryMen from "../../../public/images/category-men.png";
import categoryWomen from "../../../public/images/category-women.png";
import { useProfileCompletionPrompt } from '@/composables/useProfileCompletionPrompt';

const props = defineProps({
  categories: Array,
  latestWomenBags: Array,
});
const { t } = useI18n();
const {
  isFirstTime,
  handleUpdateProfile,
  closeModal,
} = useProfileCompletionPrompt();

</script>

<template>

  <Head :title="t('page.home.label')" />
  <Menu />
  <div class="hero h-dvh" />
  <main>
    <section class="container py-4 m-auto lg:py-10">
      <div class="flex flex-col py-6 gap-2 items-center">
        <h2 class="text-lg font-semibold lg:text-2xl">{{ t("page.home.category.heading") }}</h2>
        <p class="text-sm text-slate-500 font-semibold">{{ t("page.home.category.subheading") }}</p>
      </div>
      <div class="grid grid-cols-1 gap-x-4 gap-y-6 md:grid-cols-2 lg:grid-cols-3 justify-items-center">
        <div v-for="category in categories" :key="category.id" class="flex flex-col items-center">
          <Link :href="'/products?category=' + category.name"
            :title="t('common.button.goToCategoryTitle', { category: category.name })">
            <img v-if="category.image" :src="category.image" :alt="category.name">
            <h3 class="mt-2 text-center">{{ t(`common.category.${category.name}`, 2) }}</h3>
          </Link>
        </div>
      </div>
    </section>
    <section class="flex flex-col lg:flex-row">
      <Link href="/products?gender=men" :title="t('common.button.browseMenTitle')">
        <img :src="categoryMen" :alt="t('common.category.categoryMen')">
      </Link>
      <Link href="/products?gender=women" :title="t('common.button.browseWomenTitle')">
        <img :src="categoryWomen" :alt="t('common.category.categoryWomen')">
      </Link>
    </section>
    <section class="container py-4 m-auto lg:py-10">
      <div class="flex flex-col py-6 gap-2 items-center">
        <h2 class="text-lg font-semibold lg:text-2xl">{{ t('page.home.newIn.heading', {
          gender: t('common.gender.woman', 2), category: t('common.category.bags', 2)
        }) }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-x-4 gap-y-6 md:grid-cols-4 lg:grid-cols-4 justify-items-center">
        <div v-for="bag in latestWomenBags" :key="bag.id" class="flex flex-col items-center">
          <Link :href="route('product.show', bag.slug)" :title="t('common.button.goToProductTitle')">
            <img v-if="bag.product_variations && bag.product_variations.length > 0"
              :src="bag.product_variations[0]?.image" :alt="t('page.products.productImage', { product: bag.name })"
              class="w-full h-auto" />
          </Link>
        </div>
      </div>
      <div class="flex justify-center mt-8">
        <Link href="/products?gender=women&category=bags&home=1" :title="t('common.button.goToCategoryProductsTitle')"
          class="text-sm py-2 px-6 border border-black rounded-full transition-all hover:bg-black hover:text-white md:text-base">
          {{ t('common.button.showNow') }}
        </Link>
      </div>
    </section>
    <Modal :show="isFirstTime" @close="closeModal" :closeable="true">
      <div class="flex flex-col gap-6 py-6 px-8">
        <h2 class="text-2xl font-medium text-center">{{ t('common.modal.home.title') }}</h2>
        <p class="flex leading-8 text-center">
          {{ t('common.modal.home.content') }}
        </p>
        <div class="flex justify-center">
          <button @click="handleUpdateProfile" :title="t('common.button.updateProfileTitle')"
            class="inline-flex items-center px-4 py-2 bg-gray-800 text-white rounded-md font-semibold text-xs uppercase">
            {{ t('common.button.updateProfile') }}
          </button>
        </div>
      </div>
    </Modal>
    <Footer />
  </main>
</template>
