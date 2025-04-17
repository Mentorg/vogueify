<script setup>
import { onMounted, ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import SelectInput from '@/Components/SelectInput.vue';
import TextareaInput from '@/Components/TextareaInput.vue';
import TextInput from '@/Components/TextInput.vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import axios from 'axios';

const props = defineProps({
  product: Object,
  categories: Array
});

const form = useForm({
  id: props.product.id,
  name: props.product.name,
  description: props.product.description,
  gender: props.product.gender,
  features: props.product.features ?? [],
  category_id: props.product.category_id,
  image: props.product.product_variations[0].image,
  color: props.product.product_variations[0].color,
  type: props.product.product_variations[0].type,
  price: props.product.product_variations[0].price,
  stock: props.product.product_variations[0].stock,
  sku: props.product.product_variations[0].sku
});

const imagePreview = ref(null);

onMounted(() => {
  if (typeof form.image === 'string') {
    imagePreview.value = form.image.startsWith('/storage') ? form.image : `/storage/${form.image}`
  }
})

const handleImageChange = (event) => {
  const file = event.target.files[0];
  if (file) {
    form.image = file;
    imagePreview.value = URL.createObjectURL(file);
  }
}

const addFeature = () => {
  form.features.push({ title: '', description: '' });
};

const removeFeature = (index) => {
  form.features.splice(index, 1);
};


const submitForm = () => {
  const formData = new FormData();

  // Append all primitive fields
  formData.append('id', form.id);
  formData.append('name', form.name);
  formData.append('description', form.description);
  formData.append('gender', form.gender);
  formData.append('category_id', form.category_id);
  formData.append('color', form.color);
  formData.append('type', form.type);
  formData.append('price', form.price);
  formData.append('stock', form.stock);
  formData.append('sku', form.sku);

  if (form.image instanceof File) {
    formData.append('image', form.image);
  }

  form.features.forEach((feature, index) => {
    formData.append(`features[${index}][title]`, feature.title);
    formData.append(`features[${index}][description]`, feature.description);
  });

  for (const [key, value] of formData.entries()) {
    console.log(`${key}:`, value);
  }

  axios.post(route('product.update', form.id), formData, {
    headers: {
      'Content-Type': 'multipart/form-data',
      'X-HTTP-Method-Override': 'PUT',
    },
  })
    .then(() => {
      router.visit(route('product.show', form.id))
    })
    .catch(error => {
      if (error.response?.status === 422) {
        form.setError(error.response.data.errors);
        console.warn('⚠️ Validation failed', error.response.data.errors);
      } else {
        console.error('❌ Update error:', error);
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
        <form @submit.prevent="submitForm" class="flex flex-col gap-y-4 my-4">
          <div>
            <InputLabel for="name" value="Name" />
            <TextInput id="name" type="text" name="name" v-model="form.name" class="mt-1 block w-full" required />
          </div>
          <div>
            <InputLabel for="description" value="Description" />
            <TextareaInput id="description" v-model="form.description" type="text" class="mt-1 block w-full" required />
          </div>
          <div>
            <InputLabel for="gender" value="Gender" />
            <SelectInput name="gender" id="gender" v-model="form.gender" required>
              <option value="unisex">Unisex</option>
              <option value="men">Men</option>
              <option value="women">Women</option>
            </SelectInput>
          </div>
          <div>
            <p>Features</p>
            <div v-for="(feature, index) in form.features" :key="index" class="grid grid-cols-2 gap-4">
              <div class="flex flex-col">
                <InputLabel :for="'feature_title_' + index" value="Feature Title" />
                <TextInput :id="'feature_title_' + index" type="text" v-model="feature.title" class="mt-1 block w-full"
                  required />
              </div>
              <div class="flex flex-col">
                <InputLabel :for="'feature_description_' + index" value="Feature Description" />
                <TextInput :id="'feature_description_' + index" type="text" v-model="feature.description"
                  class="mt-1 block w-full" required />
              </div>
            </div>
            <div class="mt-2 flex justify-center">
              <button type="button" @click="addFeature" class="bg-black rounded-md py-2 px-4 text-white">Add
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
          </div>
          <div>
            <InputLabel for="image" value="Image" />
            <input id="image" type="file" @change="handleImageChange" class="mt-1">
          </div>
          <div>
            <InputLabel for="color" value="Color" />
            <SelectInput name="color" id="color" v-model="form.color" required>
              <option value="black">Black</option>
              <option value="red">Red</option>
              <option value="blue">Blue</option>
              <option value="green">Green</option>
              <option value="orange">Orange</option>
              <option value="yellow">Yellow</option>
              <option value="white">White</option>
            </SelectInput>
          </div>
          <div>
            <InputLabel for="type" value="Type" />
            <SelectInput name="type" id="type" v-model="form.type" required>
              <option value="bag">Bag</option>
              <option value="shoe">Shoe</option>
              <option value="accessory">Accessory</option>
              <option value="jewelry">Jewelry</option>
              <option value="watch">Watch</option>
              <option value="perfume">Perfume</option>
            </SelectInput>
          </div>
          <div>
            <InputLabel for="price" value="Price" />
            <TextInput id="price" name="price" v-model="form.price" type="number" class="mt-1 block w-full" required />
          </div>
          <div>
            <InputLabel for="stock" value="Stock" />
            <TextInput id="stock" name="stock" v-model="form.stock" type="number" class="mt-1 block w-full" required />
          </div>
          <div>
            <InputLabel for="sku" value="SKU" />
            <TextInput id="sku" name="sku" v-model="form.sku" type="text" class="mt-1 block w-full" required />
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
          <div class="my-4">
            <div v-if="imagePreview">
              <img :src="imagePreview" alt="Image Preview" class="mt-2 rounded border shadow max-w-full max-h-80" />
            </div>
            <p v-else class="text-gray-400">No image selected</p>
          </div>
          <div class="flex justify-between mt-4">
            <h3 class="text-lg font-medium">{{ form.name || "No name provided" }}</h3>
            <p class="text-lg">${{ form.price || "00.00" }}</p>
          </div>
          <div class="flex justify-between flex-wrap gap-4 mt-4">
            <div class="flex flex-col gap-y-2">
              <h3 class="font-medium text-base">Size</h3>
              <div class="flex gap-x-2">
                <div
                  class="w-10 h-10 border border-black rounded-md line-clamp-none flex items-center justify-center text-lg">
                  S</div>
                <div
                  class="w-10 h-10 border border-black rounded-md line-clamp-none flex items-center justify-center text-lg">
                  M</div>
                <div
                  class="w-10 h-10 border border-black rounded-md line-clamp-none flex items-center justify-center text-lg">
                  L</div>
              </div>
            </div>
            <div class="flex flex-col gap-y-2">
              <h3 class="font-medium text-base">Color</h3>
              <div class="flex gap-x-2 mt-1">
                <div class="bg-black border border-black w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'black', 'hidden': form.color !== 'black' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-red-500 border border-red-500 w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'red', 'hidden': form.color !== 'red' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-blue-500 border border-blue-500 w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'blue', 'hidden': form.color !== 'blue' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-green-500 border border-green-500 w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'green', 'hidden': form.color !== 'green' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-orange-500 border border-orange-500 w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'orange', 'hidden': form.color !== 'orange' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-yellow-500 border border-yellow-500 w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'yellow', 'hidden': form.color !== 'yellow' }" size="24"
                    color="white" class="h-full justify-self-center" />
                </div>
                <div class="bg-white border border-black w-8 h-8 rounded-full">
                  <PhCheck :class="{ 'flex': form.color === 'white', 'hidden': form.color !== 'white' }" size="24"
                    color="black" class="h-full justify-self-center" />
                </div>
              </div>
            </div>
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
                    <PhX size="24" />
                  </button>
                </li>
              </ul>
            </div>
            <div class="flex flex-col">
              <h3 class="font-medium text-base">Gender</h3>
              <p class="mt-4">{{ form.gender.charAt(0).toUpperCase() + form.gender.slice(1) || "Unisex" }}</p>
            </div>
          </div>
          <div class="grid grid-cols-4 my-4">
            <div>
              <h3 class="font-medium text-base">Category</h3>
              <p class="mt-4">{{categories.find(c => c.id == form.category_id)?.name.charAt(0).toUpperCase()
                + categories.find(c => c.id == form.category_id)?.name.slice(1)}}</p>
            </div>
            <div>
              <h3 class="font-medium text-base">Type</h3>
              <p class="mt-4">{{ form.type.charAt(0).toUpperCase() + form.type.slice(1) || "No type selected" }}</p>
            </div>
            <div>
              <h3 class="font-medium text-base">Stock</h3>
              <p class="mt-4">{{ form.stock || 0 }}</p>
            </div>
            <div>
              <h3 class="font-medium text-base">SKU</h3>
              <p class="mt-4">{{ form.sku || "No SKU provided" }}</p>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminDashboard>
</template>
