import { computed, onBeforeUnmount, onMounted, ref } from "vue";

export function useScreen() {
  const screenWidth = ref(window.innerWidth);

  const isSmallScreen = computed(() => screenWidth.value < 1024);

  const handleResize = () => {
    screenWidth.value = window.innerWidth;
  }

  onMounted(() => {
    window.addEventListener('resize', handleResize);
  });

  onBeforeUnmount(() => {
    window.removeEventListener('resize', handleResize);
  });

  return isSmallScreen;
}
