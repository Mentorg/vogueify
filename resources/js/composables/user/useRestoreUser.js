import { useForm } from "@inertiajs/vue3";
import { computed, ref } from "vue";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useRestoreUser() {
  const { t } = useI18n();
  const toast = useToast();

  const restoreUserTarget = ref(null);

  const isRestoreUserModalOpen = computed(
    () => restoreUserTarget.value !== null
  );

  const restoreUserForm = useForm({});

  const openRestoreUserModal = (targetUser) => restoreUserTarget.value = targetUser;

  const closeRestoreUserModal = () => restoreUserTarget.value = null;

  const restoreUser = () => {
    if (!restoreUserTarget.value) return;

    restoreUserForm.patch(route('user.restore', restoreUserTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.user.userRestore.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeRestoreUserModal()
      },
      onError: (errors) => {
        closeRestoreUserModal()
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.userRestore.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    });
  };

  return {
    restoreUserTarget,
    isRestoreUserModalOpen,
    restoreUserForm,
    openRestoreUserModal,
    closeRestoreUserModal,
    restoreUser,
  }
}
