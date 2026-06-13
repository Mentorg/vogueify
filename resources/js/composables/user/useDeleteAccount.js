import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useDeleteAccount() {
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
          message: t('common.toast.user.accountDelete.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeModal()
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.accountDelete.errorMessage'),
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

  return {
    confirmingUserDeletion,
    passwordInput,
    form,
    confirmUserDeletion,
    deleteUser,
    closeModal
  }
}
