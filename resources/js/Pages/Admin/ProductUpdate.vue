<script setup>
import { Head } from '@inertiajs/vue3';
import { PhX } from '@phosphor-icons/vue';
import { useI18n } from 'vue-i18n';
import InputLabel from '@Components/InputLabel.vue';
import SelectInput from '@Components/SelectInput.vue';
import TextareaInput from '@Components/TextareaInput.vue';
import TextInput from '@Components/TextInput.vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import Tooltip from '@Components/Tooltip.vue';
import InputError from '@Components/InputError.vue';
import ProgressBar from '@Components/ProgressBar.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import { useUpdateProduct } from '@/composables/product/useUpdateProduct';
import { useProductForm } from '@/composables/product/useProductForm';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  product: Object,
  categories: Array,
  types: Array,
  sizes: Array,
  colors: Array,
  errors: Object
});

const { t } = useI18n();
const { product, types, sizes, colors } = props;
const {
  updateProductForm,
  updateProduct,
  addVariation,
  removeVariation,
  handleImageChange,
} = useUpdateProduct({ product, sizes, types });
const {
  addFeature,
  removeFeature,
  toggleCollapse,
  getColorHex,
} = useProductForm({ colors });

</script>

<template>

  <Head :title="t('page.admin.updateProduct', { product: product.name })" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.updateProduct', { product: product.name }) }}</h1>
    <div class="grid grid-cols-1 w-full h-screen gap-x-8 py-8 md:grid-cols-2">
      <div>
        <form @submit.prevent="updateProduct" enctype="multipart/form-data" class="flex flex-col gap-y-4 my-4">
          <div>
            <h2 class="text-xl font-medium">{{ t('common.form.product.headingBase') }}</h2>
            <p class="mt-2 text-sm"><span class="font-medium">{{ t('common.form.product.note') }}:</span> {{
              t('common.form.product.noteMessage') }}.</p>
          </div>
          <div>
            <InputLabel for="name" :value="t('common.form.product.name')" />
            <TextInput id="name" type="text" name="name" v-model="updateProductForm.name" class="mt-1 block w-full"
              reqired />
            <InputError :message="updateProductForm.errors.name" class="mt-2" />
          </div>
          <div>
            <InputLabel for="description" :value="t('common.form.product.description')" />
            <TextareaInput id="description" v-model="updateProductForm.description" type="text"
              class="mt-1 block w-full" required />
            <InputError :message="updateProductForm.errors.description" class="mt-2" />
          </div>
          <div>
            <InputLabel for="category" :value="t('common.form.product.category')" />
            <SelectInput name="category" id="category" v-model="updateProductForm.category_id">
              <option v-for="category in categories" :value="category.id" :key="category.id">
                {{ capitalize(category.name) }}
              </option>
            </SelectInput>
            <InputError :message="updateProductForm.errors.category" class="mt-2" />
          </div>
          <div>
            <p>{{ t('common.form.product.feature', 2) }}</p>
            <div v-for="(feature, index) in updateProductForm.features" :key="feature.title"
              class="grid grid-cols-2 gap-4">
              <div class="flex flex-col my-2">
                <InputLabel :for="'feature_title_' + index" :value="t('common.form.product.featureTitle')" />
                <TextInput :id="'feature_title_' + index" type="text" v-model="feature.title" class="mt-1 block w-full"
                  required />
                <InputError :message="updateProductForm.errors[`features.${index}.title`]" class="mt-2" />
              </div>
              <div class="flex flex-col my-2">
                <InputLabel :for="'feature_description_' + index"
                  :value="t('common.form.product.featureDescription')" />
                <TextInput :id="'feature_description_' + index" type="text" v-model="feature.description"
                  class="mt-1 block w-full" required />
                <InputError :message="updateProductForm.errors[`features.${index}.description`]" class="mt-2" />
              </div>
            </div>
            <div class="mt-2 flex justify-center">
              <button type="button" @click="addFeature(updateProductForm)" :title="t('common.button.addFeatureTitle')"
                class="border border-black text-black text-sm mt-2 px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white">{{
                  t('common.button.addFeature') }}</button>
            </div>
          </div>
          <div>
            <InputLabel for="gender" :value="t('common.form.product.gender')" />
            <SelectInput name="gender" id="gender" v-model="updateProductForm.gender">
              <option value="unisex">{{ t('common.gender.unisex') }}</option>
              <option value="men">{{ t('common.gender.man') }}</option>
              <option value="women">{{ t('common.gender.woman') }}</option>
            </SelectInput>
            <InputError :message="updateProductForm.errors.gender" class="mt-2" />
          </div>
          <div class="mt-8">
            <h2 class="text-xl font-medium mb-2">{{ t('common.form.product.headingProductVariation') }}</h2>
            <div v-for="(variation, index) in updateProductForm.variations" :key="variation.id"
              class="border p-4 rounded-md mb-6">
              <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold">{{ t('common.form.product.variation', { variation: index + 1 })
                }}</h3>
                <div class="flex gap-2">
                  <button @click="toggleCollapse(updateProductForm, index)" type="button"
                    :title="variation.collapsed ? t('common.form.product.expandTitle') : t('common.form.product.collapseTitle')"
                    class="text-sm text-blue-600">
                    {{ variation.collapsed ? t('common.form.product.expand') : t('common.form.product.collapse') }}
                  </button>
                  <button @click="removeVariation(index)" type="button"
                    :title="t('common.form.product.removeVariationTitle')" class="text-sm text-red-600">{{
                      t('common.button.remove') }}</button>
                </div>
              </div>
              <div v-show="!variation.collapsed" class="transition-all duration-300 ease-in-out">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <div class="flex items-center gap-2">
                      <InputLabel :for="'image_' + index" :value="t('common.form.product.image')" />
                      <Tooltip :message="`${t('common.form.product.imageTooltip')}.`" />
                    </div>
                    <input :id="'image_' + index" type="file" @change="e => handleImageChange(e, index)" class="mt-1" />
                    <InputError :message="updateProductForm.errors[`variations.${index}.image`]" class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.productType')" />
                    <SelectInput v-model="variation.product_type_id">
                      <option v-for="type in types" :key="type.id" :value="type.id">{{ type.label }}</option>
                    </SelectInput>
                    <InputError :message="updateProductForm.errors[`variations.${index}.product_type_id`]"
                      class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.color')" />
                    <SelectInput v-model="variation.color_id">
                      <option :value="null">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="updateProductForm.errors[`variations.${index}.color_id`]" class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.primaryColor')" />
                    <SelectInput v-model="variation.primary_color_id">
                      <option :value="null">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="updateProductForm.errors[`variations.${index}.primary_color_id`]"
                      class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.secondaryColor')" />
                    <SelectInput v-model="variation.secondary_color_id">
                      <option :value="null">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="updateProductForm.errors[`variations.${index}.secondary_color_id`]"
                      class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.price')" />
                    <TextInput type="number" v-model="variation.price" min="0.01" step="0.01" required />
                    <InputError :message="updateProductForm.errors[`variations.${index}.price`]" class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :value="t('common.form.product.sku')" />
                    <TextInput v-model="variation.sku" />
                    <InputError :message="updateProductForm.errors[`variations.${index}.sku`]" class="mt-2" />
                  </div>
                  <div v-if="!variation.sizes || Object.keys(variation.sizes).length === 0">
                    <div class="flex items-center gap-2">
                      <InputLabel :value="t('common.form.product.stock')" />
                      <Tooltip :message="t('common.form.product.stockTooltip')" />
                    </div>
                    <TextInput v-model="variation.stock" type="number" min="0" />
                    <InputError :message="updateProductForm.errors[`variations.${index}.stock`]" class="mt-2" />
                  </div>
                </div>
                <div v-if="variation.sizes.length > 0" class="mt-4">
                  <div class="flex items-center gap-2">
                    <h4 class="font-medium">{{ t('common.form.product.sizeStock') }}</h4>
                    <Tooltip :message="t('common.form.product.sizeStockTooltip')" />
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                    <div v-for="(size, sIndex) in variation.sizes" :key="size.id">
                      <InputLabel :for="`size_${index}_${sIndex}`"
                        :value="`${t('common.form.product.size')} ${size.label ?? t('common.form.product.unknown')}`" />
                      <TextInput type="number" :id="`size_${index}_${sIndex}`"
                        v-model.number="variation.sizes[sIndex].stock" min="0" />
                      <InputError :message="updateProductForm.errors[`variations.${index}.sizes.${sIndex}.stock`]"
                        class="mt-2" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" @click="addVariation" :title="t('common.button.addVariationTitle')"
              class="border border-black text-black text-sm px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white">
              {{ t('common.button.addVariation') }}
            </button>
          </div>
          <ProgressBar v-if="updateProductForm.progress" :percentage="updateProductForm.progress.percentage" />
          <SubmitButton :processing="updateProductForm.processing" :idle-text="t('common.button.updateProduct')"
            :loading-text="t('common.button.updatingProduct')" :title="t('common.button.updateProductFormSubmitTitle')"
            full-width />
        </form>
      </div>
      <div>
        <div>
          <h2 class="text-xl font-medium">{{ t('common.form.product.headingPreview') }}</h2>
        </div>
        <div class="my-4">
          <div class="flex justify-between mt-4">
            <h3 class="text-lg font-medium">{{ updateProductForm.name || t('common.form.product.noName') }}</h3>
          </div>
          <div class="flex flex-col">
            <h3 class="font-medium text-base">{{ t('common.form.product.gender') }}</h3>
            <p class="mt-4">{{ capitalize(updateProductForm.gender) || t('common.gender.unisex')
            }}</p>
          </div>
          <div>
            <h3 class="font-medium text-base">{{ t('common.form.product.category') }}</h3>
            <p class="mt-4">{{capitalize(categories.find(c => c.id == updateProductForm.category_id)?.name)}}</p>
          </div>
          <div class="flex flex-col my-4">
            <h3 class="font-medium text-base">{{ t('common.form.product.description') }}</h3>
            <p class="mt-2">{{ updateProductForm.description || t('common.form.product.noDescription') }}</p>
          </div>
          <div class="flex justify-between my-4">
            <div class="flex flex-col">
              <h3 class="font-medium text-base">{{ t('common.form.product.feature', 2) }}</h3>
              <p v-if="updateProductForm.features.length === 0 || updateProductForm.features[0].title === ''">{{
                t('common.form.product.noFeatures') }}</p>
              <ul v-else>
                <li v-for="(feature, index) in updateProductForm.features" :key="feature.title"
                  class="grid grid-cols-3 gap-y-2 gap-x-20 list-disc list-inside mt-4">
                  <span class="font-medium">{{ feature.title }}:</span> {{ feature.description }}
                  <button @click="removeFeature(updateProductForm, index)"
                    :title="t('common.button.removeFeatureTitle')">
                    <PhX size="16" />
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div v-for="(variation, index) in updateProductForm.variations" :key="variation.id"
            class="my-8 py-4 border-t">
            <h3 class="text-xl font-medium">{{ t('common.form.product.variation', { variation: index + 1 }) }}</h3>
            <div>
              <img v-if="variation.preview_url" :src="variation.preview_url"
                :alt="t('common.form.product.imagePreview')" class="mt-4 max-w-full rounded" />
              <p v-else class="text-gray-500 italic">{{ t('common.form.product.noImage') }}</p>
            </div>
            <div class="grid grid-cols-4 my-4">
              <div>
                <h3 class="font-medium text-base">{{ t('common.form.product.price') }}</h3>
                <p class="mt-4">${{ variation.price || "00.00" }}</p>
              </div>
              <div>
                <h3 class="font-medium text-base">{{ t('common.form.product.productType') }}</h3>
                <p class="mt-4">
                  {{
                    types.find((t) => String(t.id) === String(variation.product_type_id))?.label || 'N/A'
                  }}
                </p>
              </div>
              <div>
                <h3 class="font-medium text-base">{{ t('common.form.product.sku') }}</h3>
                <p class="mt-4">{{ variation.sku || t('common.form.product.noSku') }}</p>
              </div>
            </div>
            <div class="flex justify-between flex-wrap gap-4 mt-4">
              <div class="mt-4">
                <h3 class="font-medium text-base mb-2">{{ t('common.form.product.stockPerSize') }}</h3>
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                  <div v-for="(sizeStock, sIndex) in variation.sizes" :key="sizeStock.id">
                    <span>
                      {{ t('common.form.product.size') }}: {{ sizeStock.label ?? t('common.form.product.unknown') }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex gap-8 my-8">
              <div
                v-if="variation.color_id !== null && variation.color_id !== t('common.form.product.none') && variation.color_id !== undefined"
                class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">{{ t('common.form.product.color') }}</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div
                    v-if="variation.color_id !== null && variation.color_id !== t('common.form.product.none') && variation.color_id !== undefined">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.color_id) }" />
                  </div>
                </div>
              </div>
              <div
                v-if="variation.primary_color_id !== t('common.form.product.none') && variation.primary_color_id !== null"
                class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">{{ t('common.form.product.primaryColor') }}</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.primary_color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.primary_color_id) }" />
                  </div>
                </div>
              </div>
              <div
                v-if="variation.secondary_color_id !== t('common.form.product.none') && variation.secondary_color_id !== null"
                class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">{{ t('common.form.product.secondaryColor') }}</h3>
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
