<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AuthenticationCard from '@/Components/AuthenticationCard.vue';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import Checkbox from '@/Components/Checkbox.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

defineProps({
  canResetPassword: Boolean,
  status: String,
});

const { t } = useI18n();
const userType = ref('admin');

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const users = {
  admin: {
    email: 'admin@vogueify.com',
    password: 'adminuser'
  },
  staff: {
    email: 'janedoe@vogueify.com',
    password: 'janedoe1234'
  },
  customer: {
    email: 'johnbrown@customer.com',
    password: 'password123'
  }
};

const submit = () => {
  form.transform(data => ({
    ...data,
    remember: form.remember ? 'on' : '',
  })).post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>

  <Head :title="t('page.authentication.login.label')" />

  <AuthenticationCard>
    <template #logo>
      <AuthenticationCardLogo />
    </template>
    <div class="py-4">
      <h1 class="font-medium">Testing Credentials</h1>
      <div class="flex my-2 gap-4">
        <div class="flex items-center gap-2">
          <input type="radio" name="user-type" value="admin" v-model="userType" id="admin">
          <label for="admin">Admin</label>
        </div>
        <div class="flex items-center gap-2">
          <input type="radio" name="user-type" value="staff" v-model="userType" id="staff">
          <label for="staff">Staff</label>
        </div>
        <div class="flex items-center gap-2">
          <input type="radio" name="user-type" value="customer" v-model="userType" id="customer">
          <label for="customer">Customer</label>
        </div>
      </div>
      <div class="my-2">
        <div class="flex gap-2">
          <h2 class="font-medium">Email:</h2>
          <p>{{ users[userType].email }}</p>
        </div>
        <div class="flex gap-2">
          <h2 class="font-medium">Password:</h2>
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

      <div class="block mt-4">
        <label class="flex items-center">
          <Checkbox v-model:checked="form.remember" name="remember" />
          <span class="ms-2 text-sm text-gray-600">{{ t('page.authentication.login.rememberMe') }}</span>
        </label>
      </div>

      <div class="flex items-center justify-end mt-4">
        <Link v-if="canResetPassword" :href="route('password.request')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        {{ t('page.authentication.login.forgotPassword') }}
        </Link>

        <Link :href="route('auth.register')"
          class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
        {{ t('page.authentication.login.noAccount') }}
        </Link>

        <PrimaryButton class="ms-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
          {{ t('common.button.login') }}
        </PrimaryButton>
      </div>
    </form>
  </AuthenticationCard>
</template>
