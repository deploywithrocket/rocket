<template>
    <form class="w-full max-w-sm p-8 mx-auto bg-white rounded-lg shadow-md" @submit.prevent="submit">
        <h1 class="mb-8 text-2xl font-bold">Sign in to your account</h1>

        <form-input class="mb-4"
                label="Email"
                placeholder="Your Email Address"
                v-model="form.email"
                :errors="$page.errors.email"
                required
                autofocus
                autocomplete="email" />

        <form-input class="mb-8"
                label="Password"
                placeholder="Your Password"
                type="password"
                v-model="form.password"
                :errors="$page.errors.password"
                required
                autocomplete="current-password" />

        <button class="inline-block w-full px-4 py-3 text-sm font-semibold text-center text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg shadow-md focus:border-brand-700 active:bg-brand-700 focus:outline-none focus:shadow-outline hover:bg-pink-600">Sign In</button>
    </form>
</template>

<script>
    export default {
        layout: require('../../layouts/gate').default,

        data() {
            return {
                form: {
                    email: '',
                    password: '',
                    remember: false,
                }
            }
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('login'), { ...this.form }
                )

                this.form.password = ''
            }
        }
    }
</script>