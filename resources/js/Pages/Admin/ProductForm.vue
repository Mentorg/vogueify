<script setup>
import { reactive } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { PhX } from '@phosphor-icons/vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import SelectInput from '@/Components/SelectInput.vue';
import ErrorMessage from '@/Components/ErrorMessage.vue';
import Tooltip from '@/Components/Tooltip.vue';
import { useToast } from 'vue-toast-notification';

const props = defineProps({
  categories: Array,
  types: Array,
  sizes: Array,
  colors: Array,
  errors: Object
})

const toast = useToast();

const form = reactive({
  name: '',
  description: '',
  gender: 'unisex',
  features: [{ title: '', description: '' }],
  category_id: props.categories.length ? props.categories[0].id : '',
  variations: []
})

const imagePreviews = reactive({});

const addFeature = () => {
  form.features.push({ title: '', description: '' })
}

const removeFeature = (index) => {
  form.features.splice(index, 1)
}

const addVariation = () => {
  form.variations.push({
    image: null,
    product_type_id: props.types.length ? props.types[0].id : '',
    color_id: null,
    primary_color_id: null,
    secondary_color_id: null,
    price: '',
    sku: '',
    stock: '',
    sizes: props.sizes.map(size => ({ id: size.id, stock: 0 })),
    collapsed: false
  });
}

const toggleCollapse = index => {
  form.variations[index].collapsed = !form.variations[index].collapsed;
};

const removeVariation = index => {
  form.variations.splice(index, 1);
}

const handleImageChange = (event, index) => {
  const file = event.target.files[0];
  if (file && !['image/png', 'image/jpeg', 'image/webp'].includes(file.type)) {
    alert('Only PNG, JPG, and WEBP images are allowed.');
    return;
  }
  form.variations[index].image = file;

  if (imagePreviews[index]) {
    URL.revokeObjectURL(imagePreviews[index]);
  }

  form.variations[index].image = file;

  if (file) {
    imagePreviews[index] = URL.createObjectURL(file);
  } else {
    delete imagePreviews[index];
  }
}

const getColorHex = (colorId) => {
  const color = props.colors.find(c => String(c.id) === String(colorId));
  return color?.hex_code?.startsWith('#') ? color.hex_code : `#${color?.hex_code || '000000'}`;
};

const submitForm = () => {
  const formData = new FormData()

  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('gender', form.gender);
  formData.append('category_id', form.category_id);

  form.features.forEach((feature, i) => {
    formData.append(`features[${i}][title]`, feature.title);
    formData.append(`features[${i}][description]`, feature.description);
  });

  form.variations.forEach((variation, i) => {
    if (variation.image) {
      formData.append(`variations[${i}][image]`, variation.image);
    }
    formData.append(`variations[${i}][product_type_id]`, variation.product_type_id);
    formData.append(`variations[${i}][color_id]`, variation.color_id);
    formData.append(`variations[${i}][primary_color_id]`, variation.primary_color_id);
    formData.append(`variations[${i}][secondary_color_id]`, variation.secondary_color_id);
    formData.append(`variations[${i}][price]`, variation.price);
    formData.append(`variations[${i}][sku]`, variation.sku);
    formData.append(`variations[${i}][stock]`, variation.stock);

    variation.sizes.forEach((size, j) => {
      formData.append(`variations[${i}][sizes][${j}][id]`, size.id);
      formData.append(`variations[${i}][sizes][${j}][stock]`, size.stock);
    });
  });

  router.post('/admin/products', formData, {
    forceFormData: true,
    onSuccess: () => {
      toast.open({
        message: 'Product created successfully.',
        type: 'success',
        position: 'top',
        duration: 4000,
      });
    },
    onError: () => {
      toast.open({
        message: 'Failed to create product! Please check the form for errors.',
        type: 'error',
        position: 'top',
        duration: 4000,
      });
    }
  })
}
</script>

<template>

  <Head title="Create Product" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">Add New Product</h1>
    <div class="grid grid-cols-1 w-full h-screen gap-x-8 py-8 md:grid-cols-2">
      <div>
        <form @submit.prevent="submitForm" class="flex flex-col gap-y-4 my-4">
          <div>
            <h2 class="text-xl font-medium">Base Information</h2>
            <p class="mt-2 text-sm"><span class="font-medium">Note:</span>Required fields vary by product type. Most
              fields are
              required. Colors are optional. Sizes apply depending on the product type.</p>
          </div>
          <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" name="name" v-model="form.name" class="mt-1 block w-full" />
            <ErrorMessage :message="errors.name" />
          </div>
          <div>
            <InputLabel for="description" value="Description" />
            <TextareaInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" />
            <ErrorMessage :message="errors.description" />
          </div>
          <div>
            <InputLabel for="gender" value="Gender" />
            <SelectInput name="gender" id="gender" v-model="form.gender">
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
                <TextInput :id="'feature_title_' + index" type="text" v-model="feature.title"
                  class="mt-1 block w-full" />
                <ErrorMessage :message="errors[`features.${index}.title`]" />
              </div>
              <div class="flex flex-col">
                <InputLabel :for="'feature_description_' + index" value="Feature Description" />
                <TextInput :id="'feature_description_' + index" type="text" v-model="feature.description"
                  class="mt-1 block w-full" />
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
            <SelectInput name="category" id="category" v-model="form.category_id">
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
                    <div class="flex items-center gap-2">
                      <InputLabel value="Stock" />
                      <Tooltip
                        message="Provide stock only for types of bags, scarves, sunglasses, earrings, watches." />
                    </div>
                    <TextInput v-model="variation.stock" type="number" />
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
                      <TextInput type="number" :id="`size_${index}_${sIndex}`" v-model="variation.sizes[sIndex].stock"
                        min="0" />
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
              <img v-if="imagePreviews[index]" :src="imagePreviews[index]" alt="Selected Image Preview"
                class="mt-4 max-w-full" />
              <p v-else class="text-gray-500 italic">No image selected</p>
            </div>
            <div class="grid grid-cols-4 my-4">
              <div>
                <h3 class="font-medium text-base">Price</h3>
                <p class="mt-4">${{ variation.price || "00.00" }}</p>
              </div>
              <div>
                <h3 class="font-medium text-base">Type</h3>
                <p class="mt-4 text-gray-700">
                  {{
                    types.find((t) => String(t.id) === String(variation.product_type_id))?.label || 'N/A'
                  }}
                </p>
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
              <div v-if="variation.color_id !== 'None'" class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">Color</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.primary_color_id !== 'None'" class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">Primary Color</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.primary_color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.primary_color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.secondary_color_id !== 'None'" class="flex flex-col gap-y-2">
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
