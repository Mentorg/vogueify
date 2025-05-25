<script setup>
import { ref } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import {
  PhX,
} from "@phosphor-icons/vue";
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import SelectInput from '@/Components/SelectInput.vue';

const props = defineProps({
  user: Object,
  countries: Array
});

const form = useForm({
  _method: 'PUT',
  name: props.user.name,
  title: props.user.title,
  email: props.user.email,
  photo: null,
  date_of_birth: props.user.date_of_birth ?? null,
  has_address: false,

  address_line_1: props.user.address?.address_line_1 ?? '',
  address_line_2: props.user.address?.address_line_2 ?? '',
  postcode: props.user.address?.postcode ?? '',
  city: props.user.address?.city ?? '',
  state: props.user.address?.state ?? '',
  country_id: props.user.address?.country_id ?? '',
  phone_number: props.user.address?.phone_number ?? '',
});

const verificationLinkSent = ref(null);
const photoPreview = ref(null);
const photoInput = ref(null);
const country = ref(form.country_id);
const showAddressInputs = ref(false);

const updateProfileInformation = () => {
  if (photoInput.value) {
    form.photo = photoInput.value.files[0];
  }

  form.post(route('user-profile-information.update'), {
    errorBag: 'updateProfileInformation',
    preserveScroll: true,
    onSuccess: () => clearPhotoFileInput(),
  });
};

const sendEmailVerification = () => {
  verificationLinkSent.value = true;
};

const selectNewPhoto = () => {
  photoInput.value.click();
};

const updatePhotoPreview = () => {
  const photo = photoInput.value.files[0];

  if (!photo) return;

  const reader = new FileReader();

  reader.onload = (e) => {
    photoPreview.value = e.target.result;
  };

  reader.readAsDataURL(photo);
};

const deletePhoto = () => {
  router.delete(route('current-user-photo.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      photoPreview.value = null;
      clearPhotoFileInput();
    },
  });
};

const clearPhotoFileInput = () => {
  if (photoInput.value?.value) {
    photoInput.value.value = null;
  }
};

const toggleAddressInputs = (addressValue) => {
  showAddressInputs.value = addressValue;
  form.has_address = addressValue;
}
console.log(props.user)
</script>

<template>
  <FormSection @submitted="updateProfileInformation">
    <!-- <template #title>
            Profile Information
        </template>

<template #description>
            Update your account's profile information and email address.
        </template> -->

    <template #form>
      <!-- Profile Photo -->
      <div v-if="$page.props.jetstream.managesProfilePhotos" class="col-span-6 sm:col-span-4">
        <!-- Profile Photo File Input -->
        <input id="photo" ref="photoInput" type="file" class="hidden" @change="updatePhotoPreview">

        <InputLabel for="photo" value="Photo" />

        <!-- Current Profile Photo -->
        <div v-show="!photoPreview" class="mt-2">
          <img :src="user.profile_photo_url" :alt="user.name" class="rounded-full size-20 object-cover">
        </div>

        <!-- New Profile Photo Preview -->
        <div v-show="photoPreview" class="mt-2">
          <span class="block rounded-full size-20 bg-cover bg-no-repeat bg-center"
            :style="'background-image: url(\'' + photoPreview + '\');'" />
        </div>

        <SecondaryButton class="mt-2 me-2" type="button" @click.prevent="selectNewPhoto">
          Select A New Photo
        </SecondaryButton>

        <SecondaryButton v-if="user.profile_photo_path" type="button" class="mt-2" @click.prevent="deletePhoto">
          Remove Photo
        </SecondaryButton>

        <InputError :message="form.errors.photo" class="mt-2" />
      </div>

      <!-- Title -->
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="title" value="Title*" />
        <SelectInput name="title" id="title" v-model="form.title" required>
          <option value="mr">Mr.</option>
          <option value="ms">Ms.</option>
        </SelectInput>
      </div>

      <!-- Name -->
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="name" value="Name*" />
        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required autocomplete="name" />
        <InputError :message="form.errors.name" class="mt-2" />
      </div>

      <!-- Email -->
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="email" value="Email*" />
        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required
          autocomplete="username" />
        <InputError :message="form.errors.email" class="mt-2" />

        <div v-if="$page.props.jetstream.hasEmailVerification && user.email_verified_at === null">
          <p class="text-sm mt-2">
            Your email address is unverified.

            <Link :href="route('verification.send')" method="post" as="button"
              class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
              @click.prevent="sendEmailVerification">
            Click here to re-send the verification email.
            </Link>
          </p>

          <div v-show="verificationLinkSent" class="mt-2 font-medium text-sm text-green-600">
            A new verification link has been sent to your email address.
          </div>
        </div>
      </div>

      <!-- Address details -->
      <div class="flex justify-between col-span-6 sm:col-span-12">
        <button @click="toggleAddressInputs(true)"
          class="py-2 px-8 border border-black rounded-full transition-all hover:bg-black hover:text-white">Add address
          details</button>
        <button @click="toggleAddressInputs(false)"
          class="flex justify-center items-center p-2 w-10 h-10 border border-black rounded-full transition-all hover:bg-black hover:text-white">
          <PhX size="18" />
        </button>
      </div>
      <div v-if="showAddressInputs" class="col-span-6 sm:col-span-12">
        <div>
          <InputLabel for="address_line_1" value="Address 1*" />
          <TextInput id="address_line_1" v-model="form.address_line_1" type="text" class="mt-1 block w-full" required
            autocomplete="address_line_1" />
          <InputError :message="form.errors.address_line_1" class="mt-2" />
        </div>

        <div>
          <InputLabel for="address_line_2" value="Address 2" />
          <TextInput id="address_line_2" v-model="form.address_line_2" type="text" class="mt-1 block w-full"
            autocomplete="address_line_2" />
          <InputError :message="form.errors.address_line_2" class="mt-2" />
        </div>

        <div>
          <InputLabel for="postcode" value="Postcode*" />
          <TextInput id="postcode" v-model="form.postcode" type="text" class="mt-1 block w-full" required
            autocomplete="postcode" />
          <InputError :message="form.errors.postcode" class="mt-2" />
        </div>

        <div>
          <InputLabel for="city" value="City*" />
          <TextInput id="city" v-model="form.city" type="text" class="mt-1 block w-full" required autocomplete="city" />
          <InputError :message="form.errors.city" class="mt-2" />
        </div>

        <div v-if="form.country_id === 'usa'" class="state">
          <InputLabel for="state" value="State" />
          <TextInput id="state" v-model="form.state" type="text" class="mt-1 block w-full" autocomplete="state" />
          <InputError :message="form.errors.state" class="mt-2" />
        </div>

        <!-- Country/Region -->
        <div class="col-span-6 sm:col-span-12">
          <InputLabel for="country_id" value="Country/Region*" />
          <SelectInput name="country_id" id="country_id" v-model="form.country_id">
            <option v-for="country in countries" :value="country.id" :key="country.id">{{ country.name }}</option>
          </SelectInput>
        </div>

        <div>
          <InputLabel for="phone_number" value="Phone Number" />
          <TextInput id="phone_number" v-model="form.phone_number" type="text" class="mt-1 block w-full"
            autocomplete="phone_number" />
          <InputError :message="form.errors.phone_number" class="mt-2" />
        </div>
      </div>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="date_of_birth" value="Date of Birth" />
        <TextInput id="date_of_birth" v-model="form.date_of_birth" type="text" class="mt-1 block w-full"
          autocomplete="date_of_birth" />
        <InputError :message="form.errors.date_of_birth" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <ActionMessage :on="form.recentlySuccessful" class="me-3">
        Saved.
      </ActionMessage>

      <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        Save
      </PrimaryButton>
    </template>
  </FormSection>
</template>
