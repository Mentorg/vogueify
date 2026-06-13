import { ref } from "vue";

export function useSearchProduct() {
  const query = ref('');

  const onInput = (e) => {
    query.value = e.target.value;
  }

  const goBack = () => {
    window.history.back();
  }

  return {
    query,
    onInput,
    goBack,
  }
}
