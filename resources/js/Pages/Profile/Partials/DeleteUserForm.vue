<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ActionSection from '@/Components/ActionSection.vue';
import DangerButton from '@/Components/DangerButton.vue';
import DialogModal from '@/Components/DialogModal.vue';
import InputError from '@/Components/InputError.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useToast } from 'vue-toast-notification';

const { t } = useI18n();
const toast = useToast();
const confirmingUserDeletion = ref(false);
const passwordInput = ref(null);

const form = useForm({
  password: '',
});

const confirmUserDeletion = () => {
  confirmingUserDeletion.value = true;

  setTimeout(() => passwordInput.value.focus(), 250);
};

const deleteUser = () => {
  form.delete(route('current-user.destroy'), {
    preserveScroll: true,
    onSuccess: () => {
      toast.open({
        message: `${t('page.user.profile.deleteUser.successMessage')}.`,
        type: 'success',
        position: 'top',
        duration: 4000,
      });
      closeModal()
    },
    onError: () => {
      toast.open({
        message: `{${t('page.user.profile.deleteUser.errorMessage')}}! ` + errors.error,
        type: 'error',
        position: 'top',
        duration: 4000,
      });
      passwordInput.value.focus()
    },
    onFinish: () => form.reset(),
  });
};

const closeModal = () => {
  confirmingUserDeletion.value = false;

  form.reset();
};
</script>

<template>
  <ActionSection>
    <!-- <template #title>
            Delete Account
        </template>

<template #description>
            Permanently delete your account.
        </template> -->

    <template #content>
      <div class="max-w-xl text-sm text-gray-600">
        {{ t('page.user.profile.deleteUser.accountDeletionWarning') }}.
      </div>

      <div class="mt-5">
        <DangerButton @click="confirmUserDeletion">
          {{ t('common.button.deleteAccount') }}
        </DangerButton>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <DialogModal :show="confirmingUserDeletion" @close="closeModal">
        <template #title>
          {{ t('common.button.deleteAccount') }}
        </template>

        <template #content>
          {{ t('page.user.profile.deleteUser.accountDeletionConfirm') }}.
          <div class="mt-4">
            <TextInput ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4"
              :placeholder="t('page.user.profile.password')" autocomplete="current-password"
              @keyup.enter="deleteUser" />

            <InputError :message="form.errors.password" class="mt-2" />
          </div>
        </template>

        <template #footer>
          <SecondaryButton @click="closeModal">
            {{ t('common.button.cancel') }}
          </SecondaryButton>

          <DangerButton class="ms-3" :class="{ 'opacity-25': form.processing }" :disabled="form.processing"
            @click="deleteUser">
            {{ t('common.button.deleteAccount') }}
          </DangerButton>
        </template>
      </DialogModal>
    </template>
  </ActionSection>
</template>
