<template>
  <Head title="Sign In" />

  <div class="w-full">
    <div class="flex flex-col items-start w-full max-w-sm mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
      <div class="w-full px-5 py-4 font-semibold bg-gray-50">
        Sign in to your account
      </div>
      <form class="w-full" @submit.prevent="submit">
        <Alerts class="mt-4" />

        <div class="w-full px-5 py-4">
          <FormInput v-model="form.email" type="email" class="mb-4" label="Email" placeholder="Your Email Address" :errors="form.errors.email" required autofocus autocomplete="email" />
          <FormInput v-model="form.password" class="mb-4" label="Password" placeholder="Your Password" type="password" :errors="form.errors.password" required autocomplete="current-password" />
        </div>
        <div class="w-full px-5 py-4 text-sm bg-gray-50">
          <div class="flex justify-end">
            <button type="submit" :disabled="form.processing" class="rounded-lg border border-primary-500 bg-primary-500 px-5 py-2.5 text-center text-sm font-medium text-white shadow-sm transition-all hover:border-primary-700 hover:bg-primary-700 focus:ring focus:ring-primary-200 disabled:cursor-not-allowed disabled:border-primary-300 disabled:bg-primary-300"><Icon icon="ri:login-box-line" /> Sign In</button>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import Gate from '@/Layouts/Gate.vue'
import { useForm } from '@inertiajs/vue3'

export default {
  layout: Gate,

  data() {
    return {
      form: useForm({
        email: null,
        password: null,
        remember: false,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('login'), {
        onSuccess: () => this.form.reset('password'),
      })
    },
  },
}
</script>