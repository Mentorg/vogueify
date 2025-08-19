<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useToast } from 'vue-toast-notification';
import { useI18n } from 'vue-i18n';
import ActionMessage from '@/Components/ActionMessage.vue';
import FormSection from '@/Components/FormSection.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';

const { t } = useI18n();
const toast = useToast();
const passwordInput = ref(null);
const currentPasswordInput = ref(null);

const form = useForm({
  current_password: '',
  password: '',
  password_confirmation: '',
});

const updatePassword = () => {
  form.put(route('user-password.update'), {
    errorBag: 'updatePassword',
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('common.toast.user.passwordUpdate.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      form.reset()
    },
    onError: () => {
      if (form.errors.password) {
        toast.open({
          message: `${t('common.toast.user.passwordUpdate.errorMessage')}! ` + errors.error,
          type: 'error',
          position: 'top',
          duration: 4000,
        });
        form.reset('password', 'password_confirmation');
        passwordInput.value.focus();
      }

      if (form.errors.current_password) {
        toast.open({
          message: `${t('common.toast.user.passwordUpdate.errorMessage')}! ` + errors.error,
          type: 'error',
          position: 'top',
          duration: 4000,
        });
        form.reset('current_password');
        currentPasswordInput.value.focus();
      }
    },
  });
};
</script>

<template>
  <FormSection @submitted="updatePassword">
    <template #title>
      {{ t('page.user.profile.updatePassword.label') }}
    </template>

    <template #description>
      {{ t('page.user.profile.updatePassword.passwordInstructions') }}.
    </template>

    <template #form>
      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="current_password" :value="t('page.user.profile.updatePassword.currentPassword')" />
        <TextInput id="current_password" ref="currentPasswordInput" v-model="form.current_password" type="password"
          class="mt-1 block w-full" autocomplete="current-password" />
        <InputError :message="form.errors.current_password" class="mt-2" />
      </div>

      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="password" :value="t('page.user.profile.updatePassword.newPassword')" />
        <TextInput id="password" ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-full"
          autocomplete="new-password" />
        <InputError :message="form.errors.password" class="mt-2" />
      </div>

      <div class="col-span-6 sm:col-span-12">
        <InputLabel for="password_confirmation" :value="t('page.user.profile.updatePassword.confirmPassword')" />
        <TextInput id="password_confirmation" v-model="form.password_confirmation" type="password"
          class="mt-1 block w-full" autocomplete="new-password" />
        <InputError :message="form.errors.password_confirmation" class="mt-2" />
      </div>
    </template>

    <template #actions>
      <ActionMessage :on="form.recentlySuccessful" class="me-3">
        {{ t('page.user.profile.saved') }}.
      </ActionMessage>

      <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
        {{ t('common.button.save') }}
      </PrimaryButton>
    </template>
  </FormSection>
</template>
