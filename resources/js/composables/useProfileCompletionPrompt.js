import { computed, onMounted, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";

export function useProfileCompletionPrompt() {
  const user = computed(() => usePage().props.auth.user);
  const isFirstTime = ref(false);

  onMounted(() => {
    if (user.value?.is_first_login) {
      isFirstTime.value = true;
    }
  });

  const handleUpdateProfile = () => {
    isFirstTime.value = false;
    router.patch(route('updateFirstTimeLogin', { user: user.value.id }), {}, {
      preserveScroll: true,
      onSuccess: () => {
        router.visit(route('profile'))
      }
    });
  };

  const closeModal = () => {
    isFirstTime.value = false;
    router.patch(route('updateFirstTimeLogin', { user: user.value.id }), {}, {
      preserveScroll: true,
    });
  };

  return {
    isFirstTime,
    handleUpdateProfile,
    closeModal
  }
}
