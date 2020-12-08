<template>
    <form class="max-w-sm p-8 mx-auto bg-white rounded-lg shadow" @submit.prevent="submit">
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

        <button class="w-full py-3 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600 focus:outline-none">Sign In</button>
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