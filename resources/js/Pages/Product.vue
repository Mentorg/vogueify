<script setup>
import { computed, defineProps, onBeforeUnmount, onMounted, ref } from "vue";
import { Head, Link, router, useForm, usePage } from "@inertiajs/vue3";
import { PhHeart, PhX } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import { useToast } from "vue-toast-notification";
import Menu from '@/Layouts/Menu.vue';
import Footer from "@/Layouts/Footer.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import useWishlist from "@/composables/useWishlist";
import { capitalize } from '@/utils/capitalize.js';

const props = defineProps({
  product: Object,
  activeVariation: Object,
});

const { t } = useI18n();
const toast = useToast();
const user = usePage().props.auth.user;
const isCartSidebarOpen = ref(false);

const { toggleWishlist } = useWishlist();

const currentVariation = computed(() => {
  return props.activeVariation || (props.product.productVariations?.[0] ?? null);
});

const sizeVariations = currentVariation.value.sizes.map(record => record);

const form = useForm({
  quantity: '1',
  price_at_time: currentVariation.value.price,
  product_variation_id: currentVariation.value.id,
  size_id: currentVariation.value.sizes
    ? (currentVariation.value.sizes.find(item => item.pivot.stock > 0)?.id ?? null)
    : null
});

const closeCartSidebar = () => {
  isCartSidebarOpen.value = !isCartSidebarOpen.value;
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.cartSidebar')) {
    isCartSidebarOpen.value = false;
  }
}

const submitForm = () => {
  if (user === null) {
    router.visit(route('login'));
    return;
  }

  if (user.email_verified_at === null) {
    router.visit(route('verification.notice'));
    return;
  }

  form
    .transform(data => ({
      ...data,
    }))
    .post(route('cart.store'), {
      onSuccess: () => {
        isCartSidebarOpen.value = true;
      },
      onError: (errors) => {
        toast.open({
          message: errors?.error || t('common.toast.product.cart.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    });
};

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});

</script>

<template>

  <Head :title="product.name" />
  <Layout>
    <Menu />
    <div v-if="isCartSidebarOpen" class="fixed inset-0 bg-slate-950/85 z-40" />
    <div :class="{ 'visible': isCartSidebarOpen, 'invisible': !isCartSidebarOpen }"
      class="cartSidebar absolute bottom-0 w-full rounded-t-xl bg-white border border-slate-200 p-8 z-50 md:p-12 md:top-1/2 md:left-1/2 md:right-[unset] md:translate-x-[-50%] md:translate-y-[-50%] md:w-[90%] lg:right-0 lg:left-[unset] lg:bottom-[unset] lg:top-[4.25rem] lg:rounded-none lg:transform-none lg:w-1/3">
      <div class="flex justify-between">
        <h3>{{ t('page.product.addedToBag') }}</h3>
        <button @click.stop="closeCartSidebar">
          <PhX size="20" />
        </button>
      </div>
      <div class="flex flex-row mt-8 md:mt-12">
        <img v-if="currentVariation && currentVariation.image" :src="'http://vogueify.test' + currentVariation.image"
          :alt="product.name" class="w-[8rem] h-[8rem]" />
        <div class="flex flex-col gap-1 px-4">
          <p class="text-xs font-light">{{ currentVariation.sku }}</p>
          <h4 class="text-sm font-light">{{ product.name }}</h4>
          <p class="text-sm">${{ currentVariation.price }}</p>
        </div>
      </div>
      <div class="flex flex-col mt-8 gap-4">
        <Link :href="route('cart.index')"
          class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
        {{ t('page.product.button.viewBag') }}</Link>
        <Link :href="closeCartSidebar"
          class="flex justify-center border border-black rounded-full py-2 w-full text-sm transition-all hover:bg-slate-100 hover:text-black md:text-base">
        {{ t('common.button.continueShopping') }}</Link>
      </div>
    </div>
    <main>
      <section class="grid grid-cols-1 lg:grid-cols-2">
        <img v-if="currentVariation && currentVariation.image" :src="'http://vogueify.test' + currentVariation.image"
          :alt="product.name" />
        <div class="w-[60%] m-auto">
          <form @submit.prevent="submitForm">
            <div class="my-4">
              <div class="lg:my-8">
                <div class="flex justify-between">
                  <p class="text-sm">{{ currentVariation.sku }}</p>
                  <button @click.prevent="toggleWishlist(currentVariation.id)">
                    <PhHeart size="18" color="black" :weight="currentVariation.isInWishlist ? 'fill' : 'regular'" />
                  </button>
                </div>
                <h2 class="text-xl mt-4 md:text-3xl">{{ product.name }}</h2>
                <h3 class="mt-2 md:text-lg">${{ currentVariation.price }}</h3>
              </div>
              <div class="flex gap-4">
                <div class="w-full"
                  v-if="product.category_id === 1 || product.category_id === 5 || (currentVariation && ['belt', 'bracelet', 'necklace', 'ring'].includes(currentVariation.type.type))">
                  <InputLabel for="size_id"
                    :value="`${t('common.product.size')} ${product.category_id === 0 ? '(L)' : product.category_id === 5 ? '(ml)' : '(in)'}`" />
                  <SelectInput name="size_id" id="size_id" v-model="form.size_id" key="product.id">
                    <option :disabled="size.pivot.stock < 1" v-for="size in sizeVariations" :value="size.id"
                      :key="size.id">
                      {{ size.pivot.stock < 1 ? size.label + ` (${t('page.product.notAvailable')})` : size.label }}
                        </option>
                  </SelectInput>
                </div>
                <div class="w-full">
                  <InputLabel for="quantity" :value="`${t('common.product.quantity')}`" />
                  <TextInput id="quantity" type="number" name="quantity" v-model="form.quantity"
                    class="mt-1 block w-full" />
                </div>
              </div>
            </div>
            <button
              class="bg-black border border-black py-2 w-full rounded-full text-white transition duration-500 ease-in-out hover:bg-white hover:text-black">
              {{ t('page.product.button.placeInCart') }}
            </button>
          </form>
          <div class="my-8 lg:my-14">
            <div v-if="product.product_variations.length > 1" class="my-8">
              <h3 class="font-medium">{{ t('page.product.alsoAvailable') }}</h3>
              <div class="flex mt-4">
                <div v-for="variation in product.product_variations" :key="variation.id">
                  <Link v-if="variation.id !== currentVariation.id"
                    :href="route('product.show', { product: product.slug, variation: variation.sku })" class="contents">
                  <img :src="variation.image" :alt="product.name" class="w-[15%]"
                    @error="console.error(`{{t('common.error.imageFail')}}:`, $event.target.src)" />
                  </Link>
                </div>
              </div>
            </div>
            <p class="my-4">{{ product.description }}</p>
            <div class="flex justify-between my-4">
              <div>
                <h3 class="font-medium">{{ t('common.product.for') }}</h3>
                <p>{{ capitalize(product.gender) }}</p>
              </div>
              <div>
                <h3 class="font-medium">{{ t('common.product.type') }}</h3>
                <p>{{ currentVariation.type.label }}</p>
              </div>
              <div v-if="currentVariation.color !== null">
                <h3 class="font-medium">{{ t('common.product.color') }}</h3>
                <p>{{ capitalize(currentVariation.color?.name) }}</p>
              </div>
              <div v-else-if="currentVariation.primary_color !== null" class="flex gap-8">
                <div>
                  <h3 class="font-medium">{{ t('common.product.primaryColor') }}</h3>
                  <p>{{ capitalize(currentVariation.primary_color?.name) }}</p>
                </div>
                <div>
                  <h3 class="font-medium">{{ t('common.product.primaryColor') }}</h3>
                  <p>{{ capitalize(currentVariation.secondary_color?.name) }}</p>
                </div>
              </div>
            </div>
            <ul class="mt-6 grid grid-cols-2">
              <li v-for="feature in product.features" :key="feature.title">
                <span class="font-medium">{{ feature.title }}</span>: {{ feature.description }}
              </li>
            </ul>
          </div>
        </div>
      </section>
    </main>
  </Layout>
  <Footer />
</template>
