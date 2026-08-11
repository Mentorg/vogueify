import { computed, ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useToast } from "vue-toast-notification";
import { useI18n } from "vue-i18n";

export function useDeactivateUser() {
  const { t } = useI18n();
  const toast = useToast();

  const deactivateUserTarget = ref(null);

  const isDeactivateUserModalOpen = computed(
    () => deactivateUserTarget.value !== null
  );

  const deactivateUserForm = useForm({});

  const openDeactivateUserModal = (targetUser) => deactivateUserTarget.value = targetUser;

  const closeDeactivateUserModal = () => deactivateUserTarget.value = null;

  const deactivateUser = () => {
    if (!deactivateUserTarget.value) return;

    deactivateUserForm.delete(route('user.deactivate', deactivateUserTarget.value), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.user.userDeactivate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeDeactivateUserModal()
      },
      onError: (errors) => {
        closeDeactivateUserModal()
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.userDeactivate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      },
    });
  };

  return {
    deactivateUserTarget,
    isDeactivateUserModalOpen,
    openDeactivateUserModal,
    closeDeactivateUserModal,
    deactivateUser
  }
}
