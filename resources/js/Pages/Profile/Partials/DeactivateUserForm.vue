<script setup>
import { useI18n } from 'vue-i18n';
import ActionSection from '@Components/ActionSection.vue';
import DangerButton from '@Components/DangerButton.vue';
import DialogModal from '@Components/DialogModal.vue';
import InputError from '@Components/InputError.vue';
import SecondaryButton from '@Components/SecondaryButton.vue';
import TextInput from '@Components/TextInput.vue';
import { useDeactivateAccount } from '@/composables/user/useDeactivateAccount';

const { t } = useI18n();

const {
  confirmingUserDeactivation,
  form,
  confirmUserDeactivation,
  deactivateUser,
  closeModal
} = useDeactivateAccount();

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
        {{ t('page.user.profile.deactivateAccount.accountDeactivationWarning') }}.
      </div>

      <div class="mt-5">
        <DangerButton @click="confirmUserDeactivation" :title="t('common.button.deactivateAccountTitle')">
          {{ t('common.button.deactivateAccount') }}
        </DangerButton>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <DialogModal :show="confirmingUserDeactivation" @close="closeModal">
        <template #title>
          {{ t('common.button.deactivateAccount') }}
        </template>

        <template #content>
          {{ t('page.user.profile.deactivateAccount.accountDeactivationConfirm') }}.
          <div class="mt-4">
            <TextInput ref="passwordInput" v-model="form.password" type="password" class="mt-1 block w-3/4"
              :placeholder="t('page.user.profile.password')" autocomplete="current-password"
              @keyup.enter="deactivateUser" />

            <InputError :message="form.errors.password" class="mt-2" />
          </div>
        </template>

        <template #footer>
          <SecondaryButton @click="closeModal" :title="t('common.button.cancelDeactivateAccountTitle')">
            {{ t('common.button.cancel') }}
          </SecondaryButton>

          <DangerButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing" @click="deactivateUser"
            :title="t('common.button.confirmDeactivateAccountTitle')" class="ms-3">
            {{ t('common.button.deactivateAccount') }}
          </DangerButton>
        </template>
      </DialogModal>
    </template>
  </ActionSection>
</template>
