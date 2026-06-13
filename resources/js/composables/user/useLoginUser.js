import { ref } from "vue";
import { useForm } from "@inertiajs/vue3";

export function useLoginUser() {
  const userType = ref('admin');

  const form = useForm({
    email: '',
    password: '',
    remember: false,
  });

  const users = {
    admin: {
      email: 'admin@vogueify.com',
      password: 'adminuser'
    },
    staff: {
      email: 'janedoe@vogueify.com',
      password: 'janedoe1234'
    },
    customer: {
      email: 'johnbrown@customer.com',
      password: 'password123'
    }
  };

  const submit = () => {
    form.transform(data => ({
      ...data,
      remember: form.remember ? 'on' : '',
    })).post(route('login'), {
      onFinish: () => form.reset('password'),
    });
  };

  return {
    userType,
    form,
    users,
    submit
  }
}
