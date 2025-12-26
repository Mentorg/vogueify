<script setup>
import { computed, defineProps, onMounted, ref } from 'vue';
import { Head, Link, router, usePage } from "@inertiajs/vue3";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue'
import Modal from '@/Components/Modal.vue';
import Footer from '@/Layouts/Footer.vue';
import categoryMen from "../../../public/images/category-men.png";
import categoryWomen from "../../../public/images/category-women.png";

const props = defineProps({
  categories: Array,
  latestWomenBags: Array,
});

const { t } = useI18n();
const user = computed(() => usePage().props.auth.user);
const isFirstTime = ref(false);

onMounted(() => {
  if (user.value?.is_first_login) {
    isFirstTime.value = true;
  }
});

const handleUpdateProfile = () => {
  isFirstTime.value = false;
  router.patch(route('updateFirstTimeLogin', { user: user.value.id }), {}, {
    preserveScroll: true,
    onSuccess: () => {
      router.visit(route('profile'))
    }
  });
};

const closeModal = () => {
  isFirstTime.value = false;
  router.patch(route('updateFirstTimeLogin', { user: user.value.id }), {}, {
    preserveScroll: true,
  });
};

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
        <div v-for="category in props.categories" :key="category.id" class="flex flex-col items-center">
          <Link :href="'/products?category=' + category.name">
            <img v-if="category.image" :src="category.image" :alt="category.name">
            <h3 class="mt-2 text-center">{{ t(`common.category.${category.name}`, 2) }}</h3>
          </Link>
        </div>
      </div>
    </section>
    <section class="flex flex-col lg:flex-row">
      <Link href="/products?gender=men">
        <img :src="categoryMen" alt="Shop men's products">
      </Link>
      <Link href="/products?gender=women">
        <img :src="categoryWomen" alt="Shop women's products">
      </Link>
    </section>
    <section class="container py-4 m-auto lg:py-10">
      <div class="flex flex-col py-6 gap-2 items-center">
        <h2 class="text-lg font-semibold lg:text-2xl">{{ t('page.home.newIn.heading', {
          gender: t('common.gender.woman', 2), category: t('common.category.bags', 2)
        }) }}</h2>
      </div>
      <div class="grid grid-cols-1 gap-x-4 gap-y-6 md:grid-cols-4 lg:grid-cols-4 justify-items-center">
        <div v-for="bag in props.latestWomenBags" :key="bag.id" class="flex flex-col items-center">
          <Link :href="route('product.show', bag.slug)">
            <img v-if="bag.product_variations && bag.product_variations.length > 0"
              :src="bag.product_variations[0]?.image" alt="Product Image" class="w-full h-auto" />
          </Link>
        </div>
      </div>
      <div class="flex justify-center mt-8">
        <Link href="/products?gender=women&category=bags&home=1"
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
