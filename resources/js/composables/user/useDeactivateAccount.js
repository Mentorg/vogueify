import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useDeactivateAccount() {
  const { t } = useI18n();
  const toast = useToast();

  const confirmingUserDeactivation = ref(false);
  const passwordInput = ref(null);

  const form = useForm({
    password: '',
  });

  const confirmUserDeactivation = () => {
    confirmingUserDeactivation.value = true;

    setTimeout(() => passwordInput.value.focus(), 250);
  };

  const closeModal = () => {
    confirmingUserDeactivation.value = false;

    form.reset();
  };

  const deactivateUser = () => {
    form.delete(route('current-user.destroy'), {
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.user.accountDeactivate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        closeModal()
      },
      onError: (errors) => {
        closeModal()
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.accountDeactivate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
        passwordInput.value.focus()
      },
      onFinish: () => form.reset(),
    });
  };

  return {
    confirmingUserDeactivation,
    passwordInput,
    form,
    confirmUserDeactivation,
    deactivateUser,
    closeModal
  }
}
