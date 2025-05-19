<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import axios from 'axios';
import { PhX } from '@phosphor-icons/vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';
import Tooltip from '@/Components/Tooltip.vue';

const props = defineProps({
  product: Object,
  categories: Array,
  types: Array,
  sizes: Array,
  colors: Array,
  errors: Object
});

const form = reactive({
  id: props.product.id,
  slug: props.product.slug,
  name: props.product.name,
  description: props.product.description,
  gender: props.product.gender,
  features: props.product.features ?? [],
  category_id: props.product.category_id,
  variations: props.product.product_variations?.map((variation) => ({
    id: variation.id,
    sku: variation.sku,
    stock: variation.stock,
    price: variation.price,
    image: variation.image,
    product_type_id: variation.product_type_id,
    color_id: variation.color_id,
    primary_color_id: variation.primary_color_id,
    secondary_color_id: variation.secondary_color_id,
    collapsed: false,
    sizes: variation.sizes.map((size) => ({
      id: size.id,
      label: size.label,
      stock: size.pivot?.stock ?? 0,
    })),
  })),
});

const imagePreviews = reactive({});

const addFeature = () => {
  form.features.push({ title: '', description: '' });
};

const removeFeature = (index) => {
  form.features.splice(index, 1);
};

const addVariation = () => {
  form.variations.push({
    image: null,
    product_type_id: props.types[0]?.id || '',
    color_id: null,
    primary_color_id: null,
    secondary_color_id: null,
    price: '',
    sku: '',
    stock: '',
    sizes: props.sizes.map(size => ({ id: size.id, stock: 0 })),
    collapsed: false,
  });
};

const removeVariation = (index) => {
  form.variations.splice(index, 1);
  delete imagePreviews[index];
};

const toggleCollapse = index => {
  form.variations[index].collapsed = !form.variations[index].collapsed;
};

const handleImageChange = (event, index) => {
  const file = event.target.files[0];
  if (file) {
    form.variations[index].image = file;
    imagePreviews[index] = URL.createObjectURL(file);
  }
};

const getImageUrl = (image, index) => {
  if (imagePreviews[index]) {
    return imagePreviews[index];
  }

  if (image instanceof File) {
    return URL.createObjectURL(image);
  }

  if (!image) return '';
  if (image.startsWith('http')) return image;

  return `${window.location.origin}${image.startsWith('/') ? '' : '/'}${image}`;
};

const getColorHex = (colorId) => {
  const color = props.colors.find(c => String(c.id) === String(colorId));
  return color?.hex_code?.startsWith('#') ? color.hex_code : `#${color?.hex_code || '000000'}`;
};

const submitForm = () => {
  const formData = new FormData();

  formData.append('id', form.id);
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('gender', form.gender);
  formData.append('category_id', form.category_id);

  form.features.forEach((feature, index) => {
    formData.append(`features[${index}][title]`, feature.title ?? '');
    formData.append(`features[${index}][description]`, feature.description ?? '');
  });

  form.variations.forEach((variation, vIndex) => {
    formData.append(`variations[${vIndex}][id]`, variation.id);

    if (variation.image instanceof File) {
      formData.append(`variation_images[${vIndex}]`, variation.image);
    }
    formData.append(`variation_image_indices[${vIndex}]`, variation.id);

    formData.append(`variations[${vIndex}][product_type_id]`, variation.product_type_id);
    formData.append(`variations[${vIndex}][color_id]`, variation.color_id ?? '');
    formData.append(`variations[${vIndex}][primary_color_id]`, variation.primary_color_id ?? '');
    formData.append(`variations[${vIndex}][secondary_color_id]`, variation.secondary_color_id ?? '');
    formData.append(`variations[${vIndex}][price]`, variation.price);
    formData.append(`variations[${vIndex}][sku]`, variation.sku);
    formData.append(`variations[${vIndex}][stock]`, variation.stock);

    variation.sizes.forEach((size, sIndex) => {
      formData.append(`variations[${vIndex}][sizes][${sIndex}][id]`, size.id);
      formData.append(`variations[${vIndex}][sizes][${sIndex}][stock]`, size.stock ?? 0);
    });
  });

  axios.post(route('product.update', form.slug), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-HTTP-Method-Override': 'PUT',
    },
  })
    .then(() => {
      router.visit(route('product.show', {
        product: form.slug,
        variation: form.variations?.[0]?.sku ?? null,
      }));
    })
    .catch(error => {
      if (error.response?.status === 422) {
        console.warn('⚠️ Validation failed', error.response.data.errors);
      } else {
        console.error('❌ Unexpected error:', error);
      }
    });
};

</script>

<template>

  <Head :title="product ? `Update ${product.name}` : `Update Product`" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Update {{ product.name }}</h1>
    <div class="grid grid-cols-1 w-full h-screen gap-x-8 py-8 md:grid-cols-2">
      <div>
        <form @submit.prevent="submitForm" enctype="multipart/form-data" class="flex flex-col gap-y-4 my-4">
          <div>
            <h2 class="text-xl font-medium">Base Information</h2>
            <p class="mt-2 text-sm"><span class="font-medium">Note:</span>Required fields vary by product type. Most
              fields are
              required. Colors are optional. Sizes apply depending on the product type.</p>
          </div>
          <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" name="name" v-model="form.name" class="mt-1 block w-full" required />
            <ErrorMessage :message="errors.name" />
          </div>
          <div>
            <InputLabel for="description" value="Description" />
            <TextareaInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" required />
            <ErrorMessage :message="errors.description" />
          </div>
          <div>
            <InputLabel for="gender" value="Gender" />
            <SelectInput name="gender" id="gender" v-model="form.gender" required>
              <option value="unisex">Unisex</option>
              <option value="men">Men</option>
              <option value="women">Women</option>
            </SelectInput>
            <ErrorMessage :message="errors.gender" />
          </div>
          <div>
            <p>Features</p>
            <div v-for="(feature, index) in form.features" :key="index" class="grid grid-cols-2 gap-4">
              <div class="flex flex-col">
                <InputLabel :for="'feature_title_' + index" value="Feature Title" />
                <TextInput :id="'feature_title_' + index" type="text" v-model="feature.title" class="mt-1 block w-full"
                  required />
                <ErrorMessage :message="errors[`features.${index}.title`]" />
              </div>
              <div class="flex flex-col">
                <InputLabel :for="'feature_description_' + index" value="Feature Description" />
                <TextInput :id="'feature_description_' + index" type="text" v-model="feature.description"
                  class="mt-1 block w-full" required />
                <ErrorMessage :message="errors[`features.${index}.description`]" />
              </div>
            </div>
            <div class="mt-2 flex justify-center">
              <button type="button" @click="addFeature"
                class="border border-black text-black text-sm mt-2 px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white">Add
                Feature</button>
            </div>
          </div>
          <div>
            <InputLabel for="category" value="Category" />
            <SelectInput name="category" id="category" v-model="form.category_id" required>
              <option v-for="category in categories" :value="category.id" :key="category.id">
                {{ category.name.charAt(0).toUpperCase() + category.name.slice(1) }}
              </option>
            </SelectInput>
            <ErrorMessage :message="errors.category" />
          </div>
          <div class="mt-8">
            <h2 class="text-xl font-medium mb-2">Product Variations</h2>
            <div v-for="(variation, index) in form.variations" :key="index" class="border p-4 rounded-md mb-6">
              <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold">Variation {{ index + 1 }}</h3>
                <div class="flex gap-2">
                  <button @click="toggleCollapse(index)" type="button" class="text-sm text-blue-600">
                    {{ variation.collapsed ? 'Expand' : 'Collapse' }}
                  </button>
                  <button @click="removeVariation(index)" type="button" class="text-sm text-red-600">Remove</button>
                </div>
              </div>
              <div v-show="!variation.collapsed" class="transition-all duration-300 ease-in-out">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <div class="flex items-center gap-2">
                      <InputLabel :for="'image_' + index" value="Image" />
                      <Tooltip message="Only valid formats are allowed (jpeg, png, jpg, gif, bmp, svg, webp)." />
                    </div>
                    <input :id="'image_' + index" type="file" @change="e => handleImageChange(e, index)" class="mt-1" />
                    <ErrorMessage :message="errors[`variations.${index}.image`]" />
                  </div>
                  <div>
                    <InputLabel value="Product Type" />
                    <SelectInput v-model="variation.product_type_id">
                      <option v-for="type in types" :key="type.id" :value="type.id">{{ type.label }}</option>
                    </SelectInput>
                    <ErrorMessage :message="errors[`variations.${index}.product_type_id`]" />
                  </div>
                  <div>
                    <InputLabel value="Color" />
                    <SelectInput v-model="variation.color_id">
                      <option :value="null">None</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <ErrorMessage :message="errors[`variations.${index}.color_id`]" />
                  </div>
                  <div>
                    <InputLabel value="Primary Color" />
                    <SelectInput v-model="variation.primary_color_id">
                      <option :value="null">None</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <ErrorMessage :message="errors[`variations.${index}.primary_color_id`]" />
                  </div>
                  <div>
                    <InputLabel value="Secondary Color" />
                    <SelectInput v-model="variation.secondary_color_id">
                      <option :value="null">None</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <ErrorMessage :message="errors[`variations.${index}.secondary_color_id`]" />
                  </div>
                  <div>
                    <InputLabel value="Price" />
                    <TextInput type="number" v-model="variation.price" />
                    <ErrorMessage :message="errors[`variations.${index}.price`]" />
                  </div>
                  <div>
                    <InputLabel value="SKU" />
                    <TextInput v-model="variation.sku" />
                    <ErrorMessage :message="errors[`variations.${index}.sku`]" />
                  </div>
                  <div>
                    <InputLabel value="Stock" />
                    <TextInput v-model="variation.stock" />
                    <ErrorMessage :message="errors[`variations.${index}.stock`]" />
                  </div>
                </div>
                <div class="mt-4">
                  <div class="flex items-center gap-2">
                    <h4 class="font-medium">Sizes & Stock</h4>
                    <Tooltip message="Enter the available stock quantity for each size of this product." />
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                    <div v-for="(size, sIndex) in variation.sizes" :key="sIndex">
                      <InputLabel :for="`size_${index}_${sIndex}`" :value="`Size ${props.sizes[sIndex].label}`" />
                      <TextInput type="number" :id="`size_${index}_${sIndex}`"
                        v-model.number="variation.sizes[sIndex].stock" min="0" />
                      <ErrorMessage :message="errors[`variations.${index}.sizes.${sIndex}.stock`]" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" @click="addVariation"
              class="border border-black text-black text-sm px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white">
              Add New Variation
            </button>
          </div>
          <button type="submit"
            class="bg-black border border-black text-white py-2 px-6 rounded-full transition-all hover:bg-white hover:text-black">Save
            Product</button>
        </form>
      </div>
      <div>
        <div>
          <h2 class="text-xl font-medium">Preview</h2>
        </div>
        <div class="my-4">
          <div class="flex justify-between mt-4">
            <h3 class="text-lg font-medium">{{ form.name || "No name provided" }}</h3>
          </div>
          <div class="flex flex-col">
            <h3 class="font-medium text-base">Gender</h3>
            <p class="mt-4">{{ form.gender.charAt(0).toUpperCase() + form.gender.slice(1) || "Unisex" }}</p>
          </div>
          <div>
            <h3 class="font-medium text-base">Category</h3>
            <p class="mt-4">{{categories.find(c => c.id == form.category_id)?.name.charAt(0).toUpperCase()
              + categories.find(c => c.id == form.category_id)?.name.slice(1)}}</p>
          </div>
          <div class="flex flex-col my-4">
            <h3 class="font-medium text-base">Description</h3>
            <p class="mt-2">{{ form.description || "No description provided" }}</p>
          </div>
          <div class="flex justify-between my-4">
            <div class="flex flex-col">
              <h3 class="font-medium text-base">Features</h3>
              <p v-if="form.features.length === 0 || form.features[0].title === ''">No features provided</p>
              <ul v-else>
                <li v-for="(feature, index) in form.features" :key="index"
                  class="grid grid-cols-3 gap-y-2 gap-x-20 list-disc list-inside mt-4">
                  <span class="font-medium">{{ feature.title }}:</span> {{ feature.description }}
                  <button @click="removeFeature(index)">
                    <PhX size="16" />
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div v-for="(variation, index) in form.variations" :key="index" class="my-8 py-4 border-t">
            <h3 class="text-xl font-medium">Variation {{ index + 1 }}</h3>
            <div>
              <img v-if="variation.image" :src="getImageUrl(variation.image, index)" alt="Selected Image Preview"
                class="mt-4 max-w-full rounded" />
              <p v-else class="text-gray-500 italic">No image selected</p>
            </div>
            <div class="grid grid-cols-4 my-4">
              <div>
                <h3 class="font-medium text-base">Price</h3>
                <p class="mt-4">${{ variation.price || "00.00" }}</p>
              </div>
              <div>
                <h3 class="font-medium text-base">Type</h3>
                <p class="mt-4">{{types.find((t) => String(t.id) === String(variation.product_type_id))?.label || 'N/A'
                  }}</p>
              </div>
              <div>
                <h3 class="font-medium text-base">SKU</h3>
                <p class="mt-4">{{ variation.sku || "No SKU provided" }}</p>
              </div>
            </div>
            <div class="flex justify-between flex-wrap gap-4 mt-4">
              <div class="mt-4">
                <h3 class="font-medium text-base mb-2">Stock per Size</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                  <div v-for="(sizeStock, sIndex) in variation.sizes" :key="sIndex">
                    <span class="font-medium">Size {{ props.sizes[sIndex]?.label || 'Unknown' }}: </span>
                    <span>{{ sizeStock.stock }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex gap-8 my-8">
              <div v-if="variation.color_id !== 'None' && variation.color_id !== null" class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">Color</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.primary_color_id !== 'None' && variation.primary_color_id !== null"
                class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">Primary Color</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.primary_color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.primary_color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.secondary_color_id !== 'None' && variation.secondary_color_id !== null"
                class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">Secondary Color</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.secondary_color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.secondary_color_id) }" />
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboard>
</template>
