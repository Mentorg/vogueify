import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function usePermanentlyDeleteUser() {
  const { t } = useI18n();
  const toast = useToast();

  const deleteUserTarget = ref(null);
  const confirmationInput = ref(null);

  const isDeleteUserModalOpen = computed(
    () => deleteUserTarget.value !== null
  );

  const canDeleteUser = computed(
    () => deleteUserForm.confirmation === 'DELETE'
  );

  const deleteUserForm = useForm({
    confirmation: ''
  });

  const openDeleteUserModal = (targetUser) => {
    deleteUserForm.reset();
    deleteUserForm.clearErrors();

    deleteUserTarget.value = targetUser;

    setTimeout(() => confirmationInput.value.focus(), 250);
  }

  const closeDeleteUserModal = () => {
    deleteUserTarget.value = null;

    deleteUserForm.reset();
    deleteUserForm.clearErrors();
  }

  const deleteUser = () => {
    if (!deleteUserTarget.value || !canDeleteUser.value) return;

    deleteUserForm.delete(route('user.destroy', deleteUserTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.user.userDelete.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeDeleteUserModal()
      },
      onError: (errors) => {
        closeDeleteUserModal();
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.userDelete.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
        setTimeout(() => confirmationInput.value?.focus(), 250);
      },
    });
  };

  return {
    deleteUserTarget,
    deleteUserForm,
    confirmationInput,
    canDeleteUser,
    isDeleteUserModalOpen,
    openDeleteUserModal,
    closeDeleteUserModal,
    deleteUser
  }
}
