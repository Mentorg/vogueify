import { useForm } from "@inertiajs/vue3";

export function useRegisterUser() {

  const form = useForm({
    name: '',
    title: '',
    email: '',
    password: '',
    password_confirmation: '',
    terms: false,
  });

  const submit = () => {
    form.post(route('auth.register'), {
      onFinish: () => form.reset('password', 'password_confirmation'),
    });
  };

  return { form, submit }
}
