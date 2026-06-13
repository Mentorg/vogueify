<script setup>
import { Link } from '@inertiajs/vue3';
import { PhX } from "@phosphor-icons/vue";
import { useI18n } from 'vue-i18n';
import ActionMessage from '@Components/ActionMessage.vue';
import FormSection from '@Components/FormSection.vue';
import InputError from '@Components/InputError.vue';
import InputLabel from '@Components/InputLabel.vue';
import PrimaryButton from '@Components/PrimaryButton.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import TextInput from '@Components/TextInput.vue';
import SelectInput from '@Components/SelectInput.vue';
import { useUpdateUser } from '@/composables/user/useUpdateUser';

const props = defineProps({
  user: Object,
  countries: Array
});

const { user } = props;

const { t } = useI18n();
const {
  verificationLinkSent,
  photoPreview,
  showAddressInputs,
  form,
  updateProfileInformation,
  sendEmailVerification,
  selectNewPhoto,
  updatePhotoPreview,
  deletePhoto,
  toggleAddressInputs
} = useUpdateUser({ user });

</script>

<template>
  <FormSection @submitted="updateProfileInformation">
    <template #form>
      <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
        <input name="photo" id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">
        <InputLabel for="photo" value="Photo" />
        <div v-show="!photoPreview" class="mt-2">
          <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
        </div>
        <div v-show="photoPreview" class="mt-2">
          <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
            :style="'background-image: url(\'' + photoPreview + '\');'" />
        </div>
        <SecondaryButton type="button" @click.prevent="selectNewPhoto"
          :title="t('common.button.selectProfilePhotoTitle')" class="mt-2 me-2">
          {{ t('common.button.selectUserPhoto') }}
        </SecondaryButton>
        <SecondaryButton v-if="user.profile_photo_path" type="button" @click.prevent="deletePhoto"
          :title="t('common.button.selectProfilePhotoTitle')" class="mt-2">
          {{ t('common.button.removeUserPhoto') }}
        </SecondaryButton>
        <InputError :message="form.errors.photo" class="mt-2" />
      </div>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="title" :value="`${t('page.user.profile.basicInfo.title')}*`" />
        <SelectInput name="title" id="title" v-model="form.title" required>
          <option value="mr">{{ t('page.user.profile.basicInfo.titleMr') }}</option>
          <option value="ms">{{ t('page.user.profile.basicInfo.titleMs') }}</option>
        </SelectInput>
      </div>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="name" :value="`${t('page.user.profile.basicInfo.name')}*`" />
        <TextInput name="name" id="name" v-model="form.name" type="text" class="mt-1 block w-full" required
          autocomplete="name" />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="email" :value="`${t('page.user.profile.basicInfo.email')}*`" />
        <TextInput name="email" id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
          autocomplete="username" />
        <InputError :message="form.errors.email" class="mt-2" />
        <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
          <p class="text-sm mt-2">
            {{ t('page.user.profile.basicInfo.emailNotVerified') }}
            <Link :href="route('verification.send')" method="post" as="button"
              class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click.prevent="sendEmailVerification">
              {{ t('page.user.profile.basicInfo.sendEmailVerification') }}
            </Link>
          </p>
          <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
            {{ t('page.user.profile.basicInfo.newVerificationLink') }}
          </div>
        </div>
      </div>
      <div class="flex justify-between col-span-6 sm:col-span-12">
        <button @click.prevent="toggleAddressInputs(true)" :title="t('common.button.modifyAddressTitle')"
          class="py-2 px-8 border border-black rounded-full transition-all hover:bg-black hover:text-white">{{
            user.address !== null ? t('common.button.modifyAddress', 2) : t('common.button.modifyAddress', 1) }}</button>
        <button @click.prevent="toggleAddressInputs(false)" :title="t('common.button.hideAddressTitle')"
          class="flex justify-center items-center p-2 w-10 h-10 border border-black rounded-full transition-all hover:bg-black hover:text-white">
          <PhX size="18" />
        </button>
      </div>
      <div v-if="showAddressInputs" class="flex flex-col col-span-6 gap-6 sm:col-span-12">
        <div>
          <InputLabel for="address_line_1" :value="`${t('page.user.profile.basicInfo.address1')}*`" />
          <TextInput name="address_line_1" id="address_line_1" v-model="form.address_line_1" type="text"
            class="mt-1 block w-full" required autocomplete="address_line_1" />
          <InputError :message="form.errors.address_line_1" class="mt-2" />
        </div>
        <div>
          <InputLabel for="address_line_2" :value="t('page.user.profile.basicInfo.address2')" />
          <TextInput name="address_line_2" id="address_line_2" v-model="form.address_line_2" type="text"
            class="mt-1 block w-full" autocomplete="address_line_2" />
          <InputError :message="form.errors.address_line_2" class="mt-2" />
        </div>
        <div>
          <InputLabel for="postcode" :value="`${t('page.user.profile.basicInfo.postcode')}*`" />
          <TextInput name="postcode" id="postcode" v-model="form.postcode" type="text" class="mt-1 block w-full"
            required autocomplete="postcode" />
          <InputError :message="form.errors.postcode" class="mt-2" />
        </div>
        <div>
          <InputLabel for="city" :value="`${t('page.user.profile.basicInfo.city')}*`" />
          <TextInput name="city" id="city" v-model="form.city" type="text" class="mt-1 block w-full" required
            autocomplete="city" />
          <InputError :message="form.errors.city" class="mt-2" />
        </div>
        <div v-if="form.country_id === 'usa'" class="state">
          <InputLabel for="state" :value="t('page.user.profile.basicInfo.state')" />
          <TextInput name="state" id="state" v-model="form.state" type="text" class="mt-1 block w-full"
            autocomplete="state" />
          <InputError :message="form.errors.state" class="mt-2" />
        </div>
        <div class="col-span-6 sm:col-span-12">
          <InputLabel for="country_id" :value="`${t('page.user.profile.basicInfo.countryRegion')}*`" />
          <SelectInput name="country_id" id="country_id" v-model="form.country_id">
            <option v-for="country in countries" :value="country.id" :key="country.id">{{ country.name }}</option>
          </SelectInput>
        </div>
        <div>
          <InputLabel for="phone_number" :value="`${t('page.user.profile.basicInfo.phone')}*`" />
          <TextInput name="phone_number" id="phone_number" v-model="form.phone_number" type="text"
            class="mt-1 block w-full" autocomplete="phone_number" />
          <InputError :message="form.errors.phone_number" class="mt-2" />
        </div>
      </div>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="date_of_birth" :value="t('page.user.profile.basicInfo.birthDate')" />
        <TextInput name="date_of_birth" id="date_of_birth" v-model="form.date_of_birth" type="text"
          class="mt-1 block w-full" autocomplete="date_of_birth" />
        <InputError :message="form.errors.date_of_birth" class="mt-2" />
      </div>
    </template>
    <template #actions>
      <ActionMessage :on="form.recentlySuccessful" class="me-3">
        {{ t('page.user.profile.saved') }}
      </ActionMessage>
      <PrimaryButton :title="t('common.button.saveProfileChangesTitle')" :class="{ 'opacity-25': form.processing }"
        :disabled="form.processing">
        {{ t('common.button.save') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
