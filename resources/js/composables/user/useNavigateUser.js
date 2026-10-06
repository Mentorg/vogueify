import { computed } from "vue";
import { router, usePage } from "@inertiajs/vue3";

export function useNavigateUser() {
  const page = usePage();

  const activeTab = computed(() => {
    return new URL(page.url, window.location.origin)
      .searchParams
      .get('tab') ?? 'personal-information';
  });

  const navigate = (tab) => {
    router.get(
      window.location.pathname,
      {
        tab,
      },
      {
        preserveScroll: true,
        preserveState: true,
        replace: true,
      }
    );
  };

  return {
    activeTab,
    navigate,
  };
}
