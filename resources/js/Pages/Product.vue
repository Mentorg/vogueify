<script setup>
import { computed, defineProps } from "vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import Menu from '@Components/Menu.vue';
import Footer from "@/Components/Footer.vue";
import InputLabel from "@/Components/InputLabel.vue";
import TextInput from "@/Components/TextInput.vue";
import SelectInput from "@/Components/SelectInput.vue";

const props = defineProps({
  product: Object,
  activeVariation: Object,
});

function capitalize(value) {
  if (!value) return '';
  return value.charAt(0).toUpperCase() + value.slice(1);
}

const currentVariation = computed(() => {
  return props.activeVariation || (props.product.productVariations?.[0] ?? null);
});

const sizeVariations = currentVariation.value.sizes.map(record => record);

const form = useForm({
  size: currentVariation.value.sizes[0]?.label,
  quantity: '1'
});
</script>

<template>

  <Head title="Products" />
  <Layout>
    <Menu />
    <main>
      <section class="grid grid-cols-1 lg:grid-cols-2">
        <img v-if="currentVariation && currentVariation.image" :src="'http://vogueify.test' + currentVariation.image"
          :alt="product.name" />
        <div class="w-[60%] m-auto">
          <div class="my-4">
            <div class="lg:my-8">
              <p class="text-sm">{{ currentVariation.sku }}</p>
              <h2 class="text-xl mt-4 md:text-3xl">{{ product.product_variations.length > 1 ? product.name + ' - ' +
                currentVariation.color.name :
                product.name }}</h2>
              <h3 class="mt-2 md:text-lg">${{ currentVariation.price }}</h3>
            </div>
            <div class="flex gap-4">
              <div class="w-full"
                v-if="product.category_id === 1 || product.category_id === 5 || (currentVariation && ['belt', 'bracelet', 'necklace', 'ring'].includes(currentVariation.type.type))">
                <InputLabel for="size"
                  :value="`Size ${product.category_id === 0 ? '(L)' : product.category_id === 5 ? '(ml)' : '(in)'}`" />
                <SelectInput name="size" id="size" v-model="form.size" key="product.id">
                  <option :disabled="size.pivot.stock < 1" v-for="size in sizeVariations" :value="size.id"
                    :key="size.id">
                    {{ size.pivot.stock < 1 ? size.label + ' (not available)' : size.label }} </option>
                </SelectInput>
              </div>
              <div class="w-full">
                <InputLabel for="quantity" value="Quantity" />
                <TextInput id="quantity" type="number" name="quantity" v-model="form.quantity"
                  class="mt-1 block w-full" />
              </div>
            </div>
          </div>
          <button
            class="bg-black border border-black py-2 w-full rounded-full text-white transition duration-500 ease-in-out hover:bg-white hover:text-black">
            Place in Cart
          </button>
          <div class="my-8 lg:my-14">
            <div v-if="product.product_variations.length > 1" class="my-8">
              <h3 class="font-medium">Also available in</h3>
              <div class="flex mt-4">
                <div v-for="variation in product.product_variations" :key="variation.id">
                  <Link v-if="variation.id !== currentVariation.id"
                    :href="route('product.show', { product: product.slug, variation: variation.sku })" class="contents">
                  <img :src="variation.image" :alt="product.name" class="w-[15%]" />
                  </Link>
                </div>
              </div>
            </div>
            <p class="my-4">{{ product.description }}</p>
            <div class="flex justify-between my-4">
              <div>
                <h3 class="font-medium">For</h3>
                <p>{{ capitalize(product.gender) }}</p>
              </div>
              <div>
                <h3 class="font-medium">Type</h3>
                <p>{{ currentVariation.type.label }}</p>
              </div>
              <div v-if="currentVariation.color !== null">
                <h3 class="font-medium">Color</h3>
                <p>{{ capitalize(currentVariation.color?.name) }}</p>
              </div>
              <div v-else-if="currentVariation.primary_color !== null" class="flex gap-8">
                <div>
                  <h3 class="font-medium">Primary Color</h3>
                  <p>{{ capitalize(currentVariation.primary_color?.name) }}</p>
                </div>
                <div>
                  <h3 class="font-medium">Secondary Color</h3>
                  <p>{{ capitalize(currentVariation.secondary_color?.name) }}</p>
                </div>
              </div>
              <div v-if="currentVariation.size">
                <h3 class="font-medium">
                  {{
                    product.category_id === 0
                      ? "Capacity"
                      : currentVariation.type === "necklace"
                        ? "Chain length"
                        : "Size"
                  }}
                </h3>
                <p>{{ currentVariation.size }}</p>
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
