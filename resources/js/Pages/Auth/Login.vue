<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticationCard from '@Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@Components/AuthenticationCardLogo.vue';
import Checkbox from '@Components/Checkbox.vue';
import InputError from '@Components/InputError.vue';
import InputLabel from '@Components/InputLabel.vue';
import PrimaryButton from '@Components/PrimaryButton.vue';
import TextInput from '@Components/TextInput.vue';
import RadioInput from '@Components/RadioInput.vue';
import { useLoginUser } from '@/composables/user/useLoginUser';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const { t } = useI18n();
const {
  userType,
  form,
  users,
  submit
} = useLoginUser();

</script>

<template>

  <Head :title="t('page.authentication.login.label')" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>
    <div class="py-4">
      <h1 class="font-medium">{{ t('page.authentication.login.testingCredentials') }}</h1>
      <div class="flex my-2 gap-4">
        <div class="flex items-center gap-2">
          <RadioInput name="user-type" id="admin" value="admin" v-model="userType" />
          <InputLabel for="admin" :value="t('common.userType.admin')" />
        </div>
        <div class="flex items-center gap-2">
          <RadioInput name="user-type" id="staff" value="staff" v-model="userType" />
          <InputLabel for="staff" :value="t('common.userType.staff')" />
        </div>
        <div class="flex items-center gap-2">
          <RadioInput name="user-type" id="customer" value="customer" v-model="userType" />
          <InputLabel for="customer" :value="t('common.userType.customer')" />
        </div>
      </div>
      <div class="my-2">
        <div class="flex gap-2">
          <h2 class="font-medium">{{ t('page.authentication.email') }}:</h2>
          <p>{{ users[userType].email }}</p>
        </div>
        <div class="flex gap-2">
          <h2 class="font-medium">{{ t('page.authentication.password') }}:</h2>
          <p>{{ users[userType].password }}</p>
        </div>
      </div>
    </div>

    <div v-if="status" class="mb-4 font-medium text-sm text-green-600">
      {{ status }}
    </div>

    <form @submit.prevent="submit">
      <div>
        <InputLabel for="email" :value="t('page.authentication.email')" />
        <TextInput id="email" v-model="form.email" type="email" class="mt-1 block w-full" required autofocus
          autocomplete="username" />
        <InputError class="mt-2" :message="form.errors.email" />
      </div>

      <div class="mt-4">
        <InputLabel for="password" :value="t('page.authentication.password')" />
        <TextInput id="password" v-model="form.password" type="password" class="mt-1 block w-full" required
          autocomplete="current-password" />
        <InputError class="mt-2" :message="form.errors.password" />
      </div>

      <div class="flex items-center gap-4 mt-4">
        <Checkbox name="remember" id="remember" v-model="form.remember" />
        <InputLabel for="remember" :value="t('page.authentication.login.rememberMe')" />
      </div>

      <div class="flex items-center justify-end mt-4">
        <Link v-if="canResetPassword" :href="route('password.request')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          {{ t('page.authentication.login.forgotPassword') }}
        </Link>

        <Link :href="route('auth.register')" :title="t('common.button.registerTitle')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
          {{ t('page.authentication.login.noAccount') }}
        </Link>

        <PrimaryButton :title="t('common.button.loginTitle')" class="ms-4" :class="{ 'opacity-25': form.processing }"
          :disabled="form.processing">
          {{ t('common.button.login') }}
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>
