<script setup>
import { Head } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { PhX } from '@phosphor-icons/vue';
import AdminDashboard from '@/Layouts/AdminDashboard.vue';
import InputLabel from '@Components/InputLabel.vue';
import TextInput from '@Components/TextInput.vue';
import TextareaInput from '@Components/TextareaInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import Tooltip from '@Components/Tooltip.vue';
import InputError from '@Components/InputError.vue';
import ProgressBar from '@Components/ProgressBar.vue';
import SubmitButton from '@Components/SubmitButton.vue';
import { useCreateProduct } from '@/composables/product/useCreateProduct';
import { useProductForm } from '@/composables/product/useProductForm';
import { capitalize } from '@/utils/capitalize';

const props = defineProps({
  categories: Array,
  types: Array,
  sizes: Array,
  colors: Array,
  errors: Object
})

const { t } = useI18n();
const { categories, types, sizes, colors } = props;
const {
  createProductForm,
  canAddVariation,
  hasVariations,
  canAddColor,
  productTypesForCategory,
  getTypeLabel,
  addVariation,
  removeVariation,
  handleImageChange,
  createProduct,
} = useCreateProduct({ categories, types, sizes });
const {
  addFeature,
  removeFeature,
  toggleCollapse,
  getColorHex,
  preventDecimal,
  sanitizeInteger,
} = useProductForm({ colors });
</script>

<template>

  <Head :title="t('page.admin.createProduct')" />
  <AdminDashboard>
    <h1 class="text-2xl font-medium">{{ t('page.admin.createProduct') }}</h1>
    <div class="grid grid-cols-1 w-full h-screen gap-x-8 py-8 md:grid-cols-2">
      <div>
        <form @submit.prevent="createProduct" class="flex flex-col gap-y-4 my-4">
          <div>
            <h2 class="text-xl font-medium">{{ t('common.form.product.headingBase') }}</h2>
            <p class="mt-2 text-sm"><span class="font-medium">{{ t('common.form.product.note') }}:</span> {{
              t('common.form.product.noteMessage') }}.</p>
          </div>
          <div>
            <InputLabel for="name" :value="t('common.form.product.name')" />
            <TextInput name="name" id="name" type="text" v-model="createProductForm.name" class="mt-1 block w-full"
              required />
            <InputError :message="createProductForm.errors.name" class="mt-2" />
          </div>
          <div>
            <InputLabel for="description" :value="t('common.form.product.description')" />
            <TextareaInput name="description" id="description" v-model="createProductForm.description" type="text"
              class="mt-1 block w-full" required />
            <InputError :message="createProductForm.errors.description" class="mt-2" />
          </div>
          <div>
            <div class="flex items-center gap-2">
              <InputLabel for="category" :value="t('common.form.product.category')" />
              <Tooltip v-if="hasVariations" :message="t('common.form.product.categoryLocked')" />
            </div>
            <SelectInput name="category" id="category" v-model.number="createProductForm.category_id"
              :disabled="hasVariations">
              <option v-for="category in categories" :value="category.id" :key="category.id">
                {{ capitalize(category.name) }}
              </option>
            </SelectInput>
            <InputError :message="createProductForm.errors.category" class="mt-2" />
          </div>
          <div>
            <p>{{ t('common.form.product.feature', 2) }}</p>
            <div v-for="(feature, index) in createProductForm.features" :key="index" class="grid grid-cols-2 gap-4">
              <div class="flex flex-col my-2">
                <InputLabel :for="`features.${index}.title`" :value="t('common.form.product.featureTitle')" />
                <TextInput :name="`features.${index}.title`" :id="`features.${index}.title`" type="text"
                  v-model="feature.title" class="mt-1 block w-full" />
                <InputError :message="createProductForm.errors[`features.${index}.title`]" class="mt-2" />
              </div>
              <div class="flex flex-col my-2">
                <InputLabel :for="`features.${index}.description`"
                  :value="t('common.form.product.featureDescription')" />
                <TextInput :name="`features.${index}.description`" :id="`features.${index}.description`" type="text"
                  v-model="feature.description" class="mt-1 block w-full" />
                <InputError :message="createProductForm.errors[`features.${index}.description`]" class="mt-2" />
              </div>
            </div>
            <div class="mt-2 flex justify-center">
              <button type="button" @click="addFeature(createProductForm)" :title="t('common.button.addFeatureTitle')"
                class="border border-black text-black text-sm mt-2 px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white">{{
                  t('common.button.addFeature') }}</button>
            </div>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <InputLabel for="gender" :value="t('common.form.product.gender')" />
              <Tooltip v-if="hasVariations" :message="t('common.form.product.genderLocked')" />
            </div>
            <SelectInput name="gender" id="gender" v-model="createProductForm.gender" :disabled="hasVariations">
              <option value="men">{{ t('common.gender.man', 2) }}</option>
              <option value="women">{{ t('common.gender.woman', 2) }}</option>
              <option value="unisex">{{ t('common.gender.unisex') }}</option>
            </SelectInput>
            <InputError :message="createProductForm.errors.gender" class="mt-2" />
          </div>
          <div class="mt-8">
            <h2 class="text-xl font-medium mb-2">{{ t('common.form.product.headingProductVariation') }}</h2>
            <div v-for="(variation, index) in createProductForm.variations" :key="index"
              class="border p-4 rounded-md mb-6">
              <div class="flex justify-between items-center mb-2">
                <h3 class="font-semibold">{{ t('common.form.product.variation', { variation: index + 1 })
                }}</h3>
                <div class="flex gap-2">
                  <button @click="toggleCollapse(createProductForm, index)" type="button"
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
                      <InputLabel :for="`variations.${index}.image`" :value="t('common.form.product.image')" />
                      <Tooltip :message="`${t('common.form.product.imageTooltip')}.`" />
                    </div>
                    <input :name="`variations.${index}.image`" :id="`variations.${index}.image`" type="file"
                      @change="e => handleImageChange(e, index)" class="mt-1" />
                    <InputError :message="createProductForm.errors[`variations.${index}.image`]" class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :for="`variations.${index}.product_type_id`"
                      :value="t('common.form.product.productType')" />
                    <SelectInput :name="`variations.${index}.product_type_id`"
                      :id="`variations.${index}.product_type_id`" v-model.number="variation.product_type_id"
                      :disabled="!productTypesForCategory.length">
                      <option v-for="type in productTypesForCategory" :key="type.id" :value="type.id">{{ type.label }}
                      </option>
                    </SelectInput>
                    <InputError :message="createProductForm.errors[`variations.${index}.product_type_id`]"
                      class="mt-2" />
                  </div>
                  <div v-if="canAddColor">
                    <InputLabel :for="`variations.${index}.color_id`" :value="t('common.form.product.color')" />
                    <SelectInput :name="`variations.${index}.color_id`" :id="`variations.${index}.color_id`"
                      v-model.number="variation.color_id">
                      <option value="">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="createProductForm.errors[`variations.${index}.color_id`]" class="mt-2" />
                  </div>
                  <div v-if="canAddColor">
                    <InputLabel :for="`variations.${index}.primary_color_id`"
                      :value="t('common.form.product.primaryColor')" />
                    <SelectInput :name="`variations.${index}.primary_color_id`"
                      :id="`variations.${index}.primary_color_id`" v-model.number="variation.primary_color_id">
                      <option value="">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="createProductForm.errors[`variations.${index}.primary_color_id`]"
                      class="mt-2" />
                  </div>
                  <div v-if="canAddColor">
                    <InputLabel :for="`variations.${index}.secondary_color_id`"
                      :value="t('common.form.product.secondaryColor')" />
                    <SelectInput :name="`variations.${index}.secondary_color_id`"
                      :id="`variations.${index}.secondary_color_id`" v-model.number="variation.secondary_color_id">
                      <option value="">{{ t('common.form.product.none') }}</option>
                      <option v-for="color in colors" :key="color.id" :value="color.id">{{ color.name }}</option>
                    </SelectInput>
                    <InputError :message="createProductForm.errors[`variations.${index}.secondary_color_id`]"
                      class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :for="`variations.${index}.price`" :value="t('common.form.product.price')" />
                    <TextInput :name="`variations.${index}.price`" :id="`variations.${index}.price`" type="number"
                      v-model.number="variation.price" min="0.01" step="0.01" inputmode="decimal" />
                    <InputError :message="createProductForm.errors[`variations.${index}.price`]" class="mt-2" />
                  </div>
                  <div>
                    <InputLabel :for="`variations.${index}.sku`" :value="t('common.form.product.sku')" />
                    <TextInput :name="`variations.${index}.sku`" :id="`variations.${index}.sku`"
                      v-model="variation.sku" />
                    <InputError :message="createProductForm.errors[`variations.${index}.sku`]" class="mt-2" />
                  </div>
                  <div v-if="!variation.sizes?.length">
                    <div class="flex items-center gap-2">
                      <InputLabel :for="`variations.${index}.stock`" :value="t('common.form.product.stock')" />
                      <Tooltip :message="t('common.form.product.stockTooltip')" />
                    </div>
                    <TextInput :name="`variations.${index}.stock`" :id="`variations.${index}.stock`" type="number"
                      v-model.number="variation.stock" min="0" step="1" inputmode="numeric" @keydown="preventDecimal"
                      @input="variation.stock = sanitizeInteger(variation.stock)" />
                    <InputError :message="createProductForm.errors[`variations.${index}.stock`]" class="mt-2" />
                  </div>
                </div>
                <div v-if="variation.sizes.length" class="mt-4">
                  <div class="flex items-center gap-2">
                    <h4 class="font-medium">{{ t('common.form.product.sizeStock') }}</h4>
                    <Tooltip :message="t('common.form.product.sizeStockTooltip')" />
                  </div>
                  <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4 mt-2">
                    <div v-for="(size, sizeIndex) in variation.sizes" :key="size.id" class="textInputs">
                      <InputLabel :for="`variations.${index}.sizes.${sizeIndex}.stock`"
                        :value="`${t('common.form.product.size')} ${size.label}`" />
                      <TextInput :name="`variations.${index}.sizes.${sizeIndex}.stock`"
                        :id="`variations.${index}.sizes.${sizeIndex}.stock`" type="number" v-model.number="size.stock"
                        min="0" step="1" inputmode="numeric" @keydown="preventDecimal"
                        @input="size.stock = sanitizeInteger(size.stock)" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <button type="button" @click="addVariation" :disabled="!canAddVariation"
              :title="t('common.button.addVariationTitle')"
              class="border border-black text-black text-sm px-6 py-2 rounded-full transition-all hover:bg-black hover:text-white disabled:border-slate-400 disabled:text-slate-400 disabled:hover:bg-white disabled:hover:cursor-not-allowed">
              {{ t('common.button.addVariation') }}
            </button>
          </div>
          <ProgressBar v-if="createProductForm.progress" :percentage="createProductForm.progress.percentage" />
          <SubmitButton :processing="createProductForm.processing" :idle-text="t('common.button.saveProduct')"
            :loading-text="t('common.button.savingProduct')" :title="t('common.button.createProductFormSubmitTitle')"
            full-width />
        </form>
      </div>
      <div>
        <div>
          <h2 class="text-xl font-medium">{{ t('common.form.product.headingPreview') }}</h2>
        </div>
        <div class="my-4">
          <div class="flex justify-between mt-4">
            <h3 class="text-lg font-medium">{{ createProductForm.name || t('common.form.product.noName') }}</h3>
          </div>
          <div class="flex flex-col">
            <h3 class="font-medium text-base">{{ t('common.form.product.gender') }}</h3>
            <p class="mt-4">{{ capitalize(createProductForm.gender) || t('common.gender.unisex')
            }}</p>
          </div>
          <div>
            <h3 class="font-medium text-base">{{ t('common.form.product.category') }}</h3>
            <p class="mt-4">{{capitalize(categories.find(c => c.id == createProductForm.category_id)?.name)}}</p>
          </div>
          <div class="flex flex-col my-4">
            <h3 class="font-medium text-base">{{ t('common.form.product.description') }}</h3>
            <p class="mt-2">{{ createProductForm.description || t('common.form.product.noDescription') }}</p>
          </div>
          <div class="flex justify-between my-4">
            <div class="flex flex-col">
              <h3 class="font-medium text-base">{{ t('common.form.product.feature', 2) }}</h3>
              <p v-if="createProductForm.features.length === 0 || createProductForm.features[0].title === ''">
                {{ t('common.form.product.noFeatures') }}</p>
              <ul v-else>
                <li v-for="(feature, index) in createProductForm.features" :key="index"
                  class="grid grid-cols-3 gap-y-2 gap-x-20 list-disc list-inside mt-4">
                  <span class="font-medium">{{ feature.title }}:</span> {{ feature.description }}
                  <button @click="removeFeature(createProductForm, index)"
                    :title="t('common.button.removeFeatureTitle')">
                    <PhX size="16" />
                  </button>
                </li>
              </ul>
            </div>
          </div>
          <div v-for="(variation, index) in createProductForm.variations" :key="index" class="my-8 py-4 border-t">
            <h3 class="text-xl font-medium">{{ t('common.form.product.variation', { variation: index + 1 }) }}</h3>
            <div>
              <img v-if="variation.preview_url" :src="variation.preview_url"
                :alt="t('common.form.product.imagePreview')" class="mt-4 max-w-full" />
              <p v-else class="text-gray-500 italic">{{ t('common.form.product.noImage') }}</p>
            </div>
            <div class="grid grid-cols-4 my-4">
              <div>
                <h3 class="font-medium text-base">{{ t('common.form.product.price') }}</h3>
                <p class="mt-4">${{ variation.price || "00.00" }}</p>
              </div>
              <div>
                <h3 class="font-medium text-base">{{ t('common.form.product.productType') }}</h3>
                <p class="mt-4 text-gray-700">
                  {{ getTypeLabel(variation) }}
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
                  <div v-for="size in variation.sizes" :key="size.id">
                    <span class="font-medium">
                      {{ t('common.form.product.size') }}
                      {{ size.label ?? t('common.form.product.unknown') }}:
                    </span>
                    <span>{{ size.stock }}</span>
                  </div>
                </div>
              </div>
            </div>
            <div class="flex gap-8 my-8">
              <div v-if="variation.color_id !== t('common.form.product.none')" class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">{{ t('common.form.product.color') }}</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.primary_color_id !== t('common.form.product.none')" class="flex flex-col gap-y-2">
                <h3 class="font-medium text-base">{{ t('common.form.product.primaryColor') }}</h3>
                <div class="flex gap-x-2 flex-wrap mt-1">
                  <div v-if="variation.primary_color_id">
                    <div class="w-8 h-8 border border-slate-300 rounded-full"
                      :style="{ backgroundColor: getColorHex(variation.primary_color_id) }" />
                  </div>
                </div>
              </div>
              <div v-if="variation.secondary_color_id !== t('common.form.product.none')" class="flex flex-col gap-y-2">
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
