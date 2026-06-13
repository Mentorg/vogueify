import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";
import { useI18n } from "vue-i18n";

export function useDeleteUser() {
  const { t } = useI18n();
  const toast = useToast();

  const deleteUserTarget = ref(null);

  const isDeleteUserModalOpen = computed(
    () => deleteUserTarget.value !== null
  );

  const deleteUserForm = useForm({});

  const openDeleteUserModal = (targetUser) => deleteUserTarget.value = targetUser;

  const closeDeleteUserModal = () => deleteUserTarget.value = null;

  const deleteUser = (user) => {
    if (!deleteUserTarget.value) return;

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
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.userDelete.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    });
  };

  return {
    deleteUserTarget,
    isDeleteUserModalOpen,
    openDeleteUserModal,
    closeDeleteUserModal,
    deleteUser
  }
}
