<script setup>
import { Head, Link } from "@inertiajs/vue3";
import { PhHeart, PhX } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import Menu from '@/Layouts/Menu.vue';
import Footer from "@/Layouts/Footer.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";
import SubmitButton from "@/Components/SubmitButton.vue";
import { useWishlist } from "@/composables/useWishlist";
import { useAddToCart } from "@/composables/cart/useAddToCart";
import { useShowProduct } from "@/composables/product/useShowProduct";
import { capitalize } from '@/utils/capitalize.js';
import { preventDecimal } from "@/utils/product";

const props = defineProps({
  product: Object,
  activeVariation: Object,
});

const { t } = useI18n();

const { product, activeVariation } = props;

const { toggleWishlist } = useWishlist();
const {
  isCartSidebarOpen,
  addToCartForm,
  addToCart,
  closeCartSidebar
} = useAddToCart({ product, activeVariation });
const {
  isStaff,
  outOfStock,
  otherVariations,
  showSizes,
  getSizeLabel,
  isSizeAvailable,
} = useShowProduct({ product, activeVariation });

</script>

<template>

  <Head :title="product.name" />
  <Menu />
  <div v-if="isCartSidebarOpen" class="fixed inset-0 bg-slate-950/85 z-40" />
  <div :class="{ 'visible': isCartSidebarOpen, 'invisible': !isCartSidebarOpen }"
    class="cartSidebar absolute bottom-0 w-full rounded-t-xl bg-white border border-slate-200 p-8 z-50 md:p-12 md:top-1/2 md:left-1/2 md:right-[unset] md:translate-x-[-50%] md:translate-y-[-50%] md:w-[90%] lg:right-0 lg:left-[unset] lg:bottom-[unset] lg:top-[4.25rem] lg:rounded-none lg:transform-none lg:w-1/3">
    <div class="flex justify-between">
      <h3>{{ t('page.product.addedToBag') }}</h3>
      <button @click.stop="closeCartSidebar" :title="t('common.button.closeCartSidebarTitle')">
        <PhX size="20" />
      </button>
    </div>
    <div class="flex flex-row mt-8 md:mt-12">
      <img v-if="activeVariation && activeVariation.image" :src="'http://vogueify.test' + activeVariation.image"
        :alt="product.name" class="w-[8rem] h-[8rem]" />
      <div class="flex flex-col gap-1 px-4">
        <p class="text-xs font-light">{{ activeVariation.sku }}</p>
        <h4 class="text-sm font-light">{{ product.name }}</h4>
        <p class="text-sm">${{ activeVariation.price }}</p>
      </div>
    </div>
    <div class="flex flex-col mt-8 gap-4">
      <Link :href="route('cart.index')" :title="t('common.button.goToCartTitle')"
        class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
        {{ t('page.product.button.viewBag') }}</Link>
      <Link :href="closeCartSidebar" :title="t('common.button.continueShoppingTitle')"
        class="flex justify-center border border-black rounded-full py-2 w-full text-sm transition-all hover:bg-slate-100 hover:text-black md:text-base">
        {{ t('common.button.continueShopping') }}</Link>
    </div>
  </div>
  <main>
    <section class="grid grid-cols-1 lg:grid-cols-2">
      <div class="relative">
        <img v-if="activeVariation && activeVariation.image" :src="'http://vogueify.test' + activeVariation.image"
          :alt="product.name" />
        <div v-if="outOfStock"
          class="absolute top-0 left-0 w-full h-full bg-slate-100/35 flex justify-center items-center">
          <div class="bg-white py-4 px-8">
            <p class="text-xl">{{ t('page.product.outOfStock') }}</p>
          </div>
        </div>
      </div>
      <div class="w-[60%] m-auto">
        <form @submit.prevent="addToCart">
          <div class="my-4">
            <div class="lg:my-8">
              <div v-if="!isStaff" class="flex justify-between">
                <p class="text-sm">{{ activeVariation.sku }}</p>
                <button
                  @click.prevent="toggleWishlist(activeVariation.id, () => activeVariation.isInWishlist = !activeVariation.isInWishlist)"
                  :title="activeVariation.isInWishlist ? t('common.button.removeFromWishlistTitle') : t('common.button.addToWishlistTitle')">
                  <PhHeart size="18" color="black" :weight="activeVariation.isInWishlist ? 'fill' : 'regular'" />
                </button>
              </div>
              <h2 class="text-xl mt-4 md:text-3xl">{{ product.name }}</h2>
              <h3 class="mt-2 md:text-lg">${{ activeVariation.price }}</h3>
            </div>
            <div v-if="!outOfStock && !isStaff" class="flex gap-4">
              <div class="w-full" v-if="showSizes">
                <InputLabel for="size_id" :value="t('common.product.size')" />
                <SelectInput name="size_id" id="size_id" v-model="addToCartForm.size_id" key="product.id">
                  <option v-for="size in activeVariation.sizes" :key="size.id" :value="size.id"
                    :disabled="size.pivot.stock < 1">
                    {{ getSizeLabel(size) }}
                    {{ !isSizeAvailable ? ` (${t('page.product.notAvailable')})` : '' }} </option>
                </SelectInput>
              </div>
              <div class="w-full">
                <InputLabel for="quantity" :value="t('common.product.quantity')" />
                <TextInput id="quantity" type="number" name="quantity" v-model.number="addToCartForm.quantity" min="1"
                  step="1" inputmode="numeric" pattern="[0-9]" @keydown="preventDecimal"
                  @input="addToCartForm.quantity = Math.max(1, Math.floor(Number(addToCartForm.quantity) || 1))"
                  class="mt-1 block w-full" />
              </div>
            </div>
          </div>
          <SubmitButton v-if="!isStaff" :processing="addToCartForm.processing" :disabled="outOfStock"
            :idle-text="outOfStock ? t('page.product.outOfStock') : t('page.product.button.placeInCart')"
            :loading-text="t('page.product.button.placingInCart')" :title="t('common.button.addToCartTitle')"
            full-width />
        </form>
        <div class="my-8 lg:my-14">
          <div v-if="otherVariations.length > 0" class="my-8">
            <h3 class="font-medium">{{ t('page.product.alsoAvailable') }}</h3>
            <div class="flex items-center gap-2 mt-4">
              <Link v-for="variation in otherVariations" :key="variation.id"
                :href="route('product.show', { product: product.slug, variation: variation.sku })"
                :title="t('common.button.goToProductVariationTitle')">
                <img :src="variation.image" :alt="product.name" class="block w-16 h-16 object-cover" />
              </Link>
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
              <p>{{ activeVariation.type.label }}</p>
            </div>
            <div v-if="activeVariation.color !== null">
              <h3 class="font-medium">{{ t('common.product.color') }}</h3>
              <p>{{ capitalize(activeVariation.color?.name) }}</p>
            </div>
            <div v-else-if="activeVariation.primary_color !== null" class="flex gap-8">
              <div>
                <h3 class="font-medium">{{ t('common.product.primaryColor') }}</h3>
                <p>{{ capitalize(activeVariation.primary_color?.name) }}</p>
              </div>
              <div>
                <h3 class="font-medium">{{ t('common.product.primaryColor') }}</h3>
                <p>{{ capitalize(activeVariation.secondary_color?.name) }}</p>
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
  <Footer />
</template>
