import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";
import { useI18n } from "vue-i18n";
import { useToast } from "vue-toast-notification";

export function useUpdateUser({ user = {} }) {
  const { t } = useI18n();
  const toast = useToast();
  const verificationLinkSent = ref(null);
  const photoPreview = ref(null);
  const photoInput = ref(null);
  const showAddressInputs = ref(!!user.address);

  const form = useForm({
    _method: 'PUT',
    name: user.name,
    title: user.title,
    email: user.email,
    photo: null,
    date_of_birth: user.date_of_birth ?? null,
    has_address: !!user.address,

    address_line_1: user.address?.address_line_1 ?? '',
    address_line_2: user.address?.address_line_2 ?? '',
    postcode: user.address?.postcode ?? '',
    city: user.address?.city ?? '',
    state: user.address?.state ?? '',
    country_id: user.address?.country_id ?? '',
    phone_number: user.address?.phone_number ?? '',
  });

  const updateProfileInformation = () => {
    if (photoInput.value) {
      form.photo = photoInput.value.files[0];
    }

    form.post(route('user-profile-information.update'), {
      errorBag: 'updateProfileInformation',
      preserveScroll: true,
      onSuccess: () => {
        toast.open({
          message: t('common.toast.user.profileUpdate.successMessage'),
          type: 'success',
          position: 'top',
          duration: 4000,
        });
        photoPreview.value = null;
        clearPhotoFileInput();
      },
      onError: (errors) => {
        toast.open({
          message: Object.values(errors)?.[0] || t('common.toast.user.profileUpdate.errorMessage'),
          type: 'error',
          position: 'top',
          duration: 4000,
        });
      }
    });
  };

  const sendEmailVerification = () => {
    verificationLinkSent.value = true;
  };

  const selectNewPhoto = () => {
    photoInput.value?.click();
  };

  const updatePhotoPreview = () => {
    const photo = photoInput.value.files[0];

    if (!photo) return;

    const reader = new FileReader();

    reader.onload = ({ target }) => {
      photoPreview.value = target?.result;
    };

    reader.readAsDataURL(photo);
  };

  const deletePhoto = () => {
    router.delete(route('current-user-photo.destroy'), {
      preserveScroll: true,
      onSuccess: () => {
        photoPreview.value = null;
        clearPhotoFileInput();
      },
    });
  };

  const clearPhotoFileInput = () => {
    if (photoInput.value?.value) {
      photoInput.value.value = null;
    }
  };

  const toggleAddressInputs = (enabled) => {
    showAddressInputs.value = enabled;
    form.has_address = enabled;
  }

  return {
    verificationLinkSent,
    photoPreview,
    photoInput,
    showAddressInputs,
    form,
    updateProfileInformation,
    sendEmailVerification,
    selectNewPhoto,
    updatePhotoPreview,
    deletePhoto,
    toggleAddressInputs
  }
}
