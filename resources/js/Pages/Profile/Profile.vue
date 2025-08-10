<script setup>
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import UpdateProfileInformationForm from './Partials/UpdateProfileInformationForm.vue';
import UpdatePasswordForm from './Partials/UpdatePasswordForm.vue';
import TwoFactorAuthenticationForm from './Partials/TwoFactorAuthenticationForm.vue';
import DeleteUserForm from './Partials/DeleteUserForm.vue';

defineProps({
  confirmsTwoFactorAuthentication: Boolean,
  countries: Array
})

const { t } = useI18n();
</script>

<template>
  <DashboardLayout :title="t('page.user.profile.label')">
    <div class="grid grid-cols-1 gap-4 md:gap-6 md:grid-cols-2">
      <div class="flex flex-col gap-4 md:gap-6">
        <UpdateProfileInformationForm :user="$page.props.auth.user" :countries="countries" />
      </div>
      <div class="flex flex-col gap-4 md:gap-6">
        <UpdatePasswordForm />
        <TwoFactorAuthenticationForm :requires-confirmation="confirmsTwoFactorAuthentication" />
        <DeleteUserForm />
      </div>
    </div>
  </DashboardLayout>
</template>
