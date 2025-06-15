<script setup>
import { computed, defineProps, onBeforeUnmount, onMounted, ref } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import Menu from '@/Components/Menu.vue';
import { PhCaretRight, PhHeart, PhTrash, PhXCircle } from '@phosphor-icons/vue';
import Footer from '@/Components/Footer.vue';
import DialogModal from '@/Components/DialogModal.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import DangerButton from '@/Components/DangerButton.vue';

const props = defineProps({
  cartItems: Array,
})
const form = useForm({});

const wishlist = usePage().props.auth.wishlist;
const localWishlist = ref([...wishlist]);
const openMenu = ref(null);
const itemToDelete = ref(null);

const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

const addToWishlist = async (productVariationId) => {
  const existing = localWishlist.value.find(item => item.product_variation_id === productVariationId);

  if (existing) {
    try {
      const response = await fetch(route('wishlist.destroy', existing.id), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ _method: 'DELETE' })
      });

      if (!response.ok) throw new Error('Failed to remove from wishlist');

      localWishlist.value = localWishlist.value.filter(item => item.id !== existing.id);
    } catch (error) {
      console.error('Error removing from wishlist:', error);
    }

  } else {
    try {
      const response = await fetch(route('wishlist.store'), {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({ product_variation_id: productVariationId })
      });

      if (!response.ok) throw new Error('Failed to add to wishlist');

      const newItem = await response.json();

      localWishlist.value.push(newItem);
    } catch (error) {
      console.error('Error adding to wishlist:', error);
    }
  }
};

const confirmItemDeletion = (item) => {
  itemToDelete.value = item;
}

const destroy = (id) => {
  form.delete(route('cart.delete', id), {
    preserveScroll: true,
    onSuccess: () => closeModal(),
    onError: () => console.log('Failed to delete cart item!')
  })
}

const closeModal = () => {
  itemToDelete.value = null;
  form.reset()
}

const handleClickOutside = (event) => {
  if (!event.target.closest('.context-menu-wrapper')) {
    openMenu.value = null;
  }
}

const screenWidth = ref(window.innerWidth);

const isTablet = computed(() => screenWidth.value < 1024);

const handleResize = () => {
  screenWidth.value = window.innerWidth;
}

onMounted(() => {
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
});
</script>

<template>

  <Head title="My Shopping Bag" />
  <Layout>
    <Menu />
    <main>
      <section v-if="cartItems.length > 0" class="bg-slate-50 grid lg:grid-cols-[2fr,1fr] h-fit w-full gap-8">
        <div class="px-4 md:px-8 lg:px-16">
          <div class="flex flex-col gap-4">
            <div class="flex flex-col justify-between order-2 lg:order-1 lg:py-10 lg:flex-row">
              <h1 class="order-2 lg:order-1 lg:text-2xl">My Shopping Cart <span
                  class="text-xs text-slate-500 lg:text-sm">({{
                    cartItems.length
                  }})</span>
              </h1>
              <Link href="/" class="order-1 mb-8 text-center lg:order-2 text-sm underline lg:text-base lg:mb-0">Continue
              Shopping
              </Link>
            </div>
            <div v-if="isTablet" v-for="item in cartItems" class="bg-white order-3 lg:order-2">
              <div class="flex justify-between items-center border-b">
                <div class="grid grid-cols-[1fr,4fr]">
                  <div class="place-content-center place-items-center">
                    <img v-if="item.product_variation && item.product_variation.image"
                      :src="'http://vogueify.test' + item.product_variation.image"
                      :alt="item.product_variation.product.name"
                      class="w-full h-full p-1 md:w-[95%] md:h-[95%] md:p-0" />
                  </div>
                  <div class="flex flex-col justify-center ml-4">
                    <p class="text-xs">{{ item.product_variation.sku }}</p>
                    <h2 class="md:text-lg">{{ item.product_variation.product.name }}</h2>
                  </div>
                </div>
                <div class="mr-2">
                  <Link
                    :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })">
                  <PhCaretRight :size="18" />
                  </Link>
                </div>
              </div>
              <div class="p-2 md:p-3">
                <ul>
                  <li v-for="feature in item.product_variation.product.features" :key="feature.title"
                    class="flex justify-between my-1">
                    <p class="text-xs">{{ feature.title }}</p>
                    <p class="text-xs">{{ feature.description }}</p>
                  </li>
                </ul>
                <div class="flex justify-between items-end mt-6">
                  <div class="flex flex-col">
                    <div v-if="item.size !== null" class="flex gap-2">
                      <h4 class="text-sm">Size:</h4>
                      <p class="text-sm">{{ item.size.label }}</p>
                    </div>
                    <div class="flex gap-2">
                      <h4 class="text-sm">Quantity:</h4>
                      <p class="text-sm">{{ item.quantity }}</p>
                    </div>
                  </div>
                  <div class="flex flex-col items-end">
                    <h3 class="text-lg">${{ item.price_at_time * item.quantity }}</h3>
                    <p v-if="item.quantity > 1" class="text-xs text-slate-500">${{ item.price_at_time }} per piece</p>
                  </div>
                </div>
              </div>
              <div class="flex divide-x-2">
                <button @click.prevent="addToWishlist(item.product_variation.id)"
                  class="flex items-center gap-2 text-xs border-t border-t-slate-200 w-full justify-center py-2 transition-all md:text-sm">
                  <PhHeart size="14" color="red"
                    :weight="localWishlist.some(record => record.product_variation_id === item.product_variation.id) ? 'fill' : 'regular'" />
                  Add to Wishlist
                </button>
                <button @click="confirmItemDeletion(item)"
                  class="flex items-center gap-2 text-xs border-t border-t-slate-200 w-full justify-center py-2 transition-all md:text-sm">
                  <PhXCircle size="14" />
                  Remove
                </button>
              </div>
            </div>
            <div v-else v-for="item in cartItems" class="bg-white grid grid-cols-2 order-3 lg:order-2">
              <div class="place-content-center place-items-center">
                <img v-if="item.product_variation && item.product_variation.image"
                  :src="'http://vogueify.test' + item.product_variation.image"
                  :alt="item.product_variation.product.name" class="w-[95%] h-[95%]" />
              </div>
              <div class="flex flex-col h-full border-l">
                <div class="flex flex-col h-full">
                  <div class="flex items-center justify-between border-b py-6 px-8">
                    <div>
                      <p class="text-sm">{{ item.product_variation.sku }}</p>
                      <h2 class="text-2xl">{{ item.product_variation.product.name }}</h2>
                    </div>
                    <Link
                      :href="route('product.show', { product: item.product_variation.product.slug, variation: item.product_variation.sku })">
                    <PhCaretRight :size="24" />
                    </Link>
                  </div>
                  <div class="flex-1 py-6 px-8">
                    <ul>
                      <li v-for="feature in item.product_variation.product.features" :key="feature.title"
                        class="flex justify-between">
                        <p>{{ feature.title }}</p>
                        <p>{{ feature.description }}</p>
                      </li>
                    </ul>
                  </div>
                  <div class="flex items-end justify-between mt-auto py-6 px-8">
                    <div class="flex flex-col">
                      <div v-if="item.size !== null" class="flex gap-2">
                        <h4>Size:</h4>
                        <p>{{ item.size.label }}</p>
                      </div>
                      <div class="flex gap-2">
                        <h4>Quantity:</h4>
                        <p>{{ item.quantity }}</p>
                      </div>
                    </div>
                    <div class="flex flex-col items-end">
                      <h3 class="text-2xl">${{ item.price_at_time * item.quantity }}</h3>
                      <p v-if="item.quantity > 1" class="text-xs text-slate-500">${{ item.price_at_time }} per piece</p>
                    </div>
                  </div>
                </div>
                <div class="flex divide-x-2">
                  <button @click.prevent="addToWishlist(item.product_variation.id)"
                    class="flex items-center gap-2 text-lg border-t border-t-slate-200 w-full justify-center py-3 transition-all hover:bg-black hover:text-white">
                    <PhHeart size="18" color="red"
                      :weight="localWishlist.some(record => record.product_variation_id === item.product_variation.id) ? 'fill' : 'regular'" />
                    Add to Wishlist
                  </button>
                  <button @click="confirmItemDeletion(item)"
                    class="flex items-center gap-2 text-lg border-t border-t-slate-200 w-full justify-center py-3 transition-all hover:bg-black hover:text-white">
                    <PhXCircle :size="18" />
                    Remove
                  </button>
                </div>
              </div>
            </div>
            <div class="flex order-1 mt-8 lg:order-3 lg:my-10">
              <Link href="#!"
                class="bg-black flex justify-center border border-black rounded-full py-2 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base">
              Proceed to Checkout</Link>
            </div>
          </div>
          <DialogModal :show="itemToDelete !== null" @close="closeModal">
            <template #title>
              Delete {{ itemToDelete.product_variation.product.name }}?
            </template>
            <template #content>
              Are you sure you want to delete {{ itemToDelete.product_variation.product.name }}?
            </template>
            <template #footer>
              <SecondaryButton @click="closeModal">Cancel</SecondaryButton>
              <DangerButton class="ms-3" @click="destroy(itemToDelete.id)">
                <PhTrash :size="16" color="white" class="mr-2" />
                Delete
              </DangerButton>
            </template>
          </DialogModal>
        </div>
        <div class="bg-white py-10 px-6 md:px-10">
          <ul class="flex flex-col gap-2">
            <li class="flex justify-between lg:text-lg">
              <p>Subtotal</p>
              <span>
                ${{cartItems.length > 0
                  ? cartItems
                    .map(item => item.price_at_time * item.quantity)
                    .reduce((total, current) => total + current, 0)
                    .toFixed(2)
                  : '0.00'
                }}
              </span>
            </li>
            <li class="flex justify-between :text-lg">
              <p>Shipping</p><span>$0.00</span>
            </li>
            <li class="flex flex-col">
              <div class="flex justify-between">
                <p class=":text-lg">Tax</p>
                <span class=":text-lg">$0.00</span>
              </div>
              <p class="text-xs text-slate-500">Will be calculated according to your delivery address</p>
            </li>
            <li class="flex justify-between mt-4 text-lg">
              <p>Total</p>
              <span>
                ${{cartItems.length > 0
                  ? cartItems
                    .map(item => item.price_at_time * item.quantity)
                    .reduce((total, current) => total + current, 0)
                    .toFixed(2)
                  : '0.00'
                }}
              </span>
            </li>
          </ul>
          <div>
            <Link href="#!"
              class="bg-black flex justify-center border border-black rounded-full py-2 mt-8 w-full text-sm text-white transition-all hover:bg-white hover:text-black lg:text-base">
            Proceed to Checkout</Link>
          </div>
        </div>
      </section>
      <section v-else class="w-full h-fit">
        <div class="flex flex-col items-center w-full h-full py-[5rem] lg:py-[8rem]">
          <img src="../../../public/images/empty-bag.png" alt="Empty bag" class="w-fit">
          <p class="text-lg">Your shopping bag is empty</p>
          <Link href="/"
            class="bg-black flex justify-center border border-black rounded-full py-2 px-8 mt-4 w-fit text-sm text-white transition-all hover:bg-white hover:text-black md:text-base">
          Start Shopping</Link>
        </div>
      </section>
      <Footer />
    </main>
  </Layout>
</template>
