<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-lg mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                Create your account
            </div>
            <form class="w-full" @submit.prevent="submit">
                <alerts class="mt-4"></alerts>

                <div class="w-full px-5 py-4">
                    <p class="mb-8">Create your own personal account.</p>

                    <form-input class="w-full mb-4" label="Name" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />
                    <form-input class="w-full mb-4" label="Email" type="email" name="email" v-model="form.email" :errors="$page.errors.email" />
                    <form-input class="w-full mb-4" label="Password" type="password" name="password" v-model="form.password" :errors="$page.errors.password" />
                    <form-input class="w-full mb-4" label="Password (confirmation)" type="password" name="password_confirmation" v-model="form.password_confirmation" :errors="$page.errors.password_confirmation" />
                </div>
                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex justify-end">
                        <button @click.prevent="skip()" class="inline-block px-4 py-2 mr-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-forward"></i> Skip</button>
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="mr-1 fas fa-angle-right"></i> Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/gate').default,

        data() {
            return {
                form: {
                    name: '',
                    email: '',
                    password: '',
                    password_confirmation: '',
                }
            }
        },

        methods: {
            skip() {
                this.$inertia.visit(this.$route('install.github.show'))
            },
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('install.user.submit'), { ...this.form }
                )
            }
        }
    }
</script>