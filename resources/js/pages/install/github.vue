<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-lg mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                GitHub
            </div>
            <form class="w-full" @submit.prevent="submit">
                <alerts class="mt-4"></alerts>

                <div class="w-full px-5 py-4">
                    <p class="mb-8">Create a database on your server and set the credentials using the form below.</p>

                    <form-input class="w-full mb-4" label="Client ID" type="text" name="client_id" v-model="form.client_id" :errors="$page.errors.client_id" />
                    <form-input class="w-full mb-4" label="Client secret" type="text" name="client_secret" v-model="form.client_secret" :errors="$page.errors.client_secret" />
                </div>
                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex justify-end">
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="mr-1 fas fa-angle-right"></i> Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/gate').default,

        props: ['connections', 'current_env'],

        data() {
            return {
                form: {
                    client_id: '',
                    client_secret: '',
                }
            }
        },

        mounted() {
            if (this.form.client_id == '') this.form.client_id = this.current_env.client_id
            if (this.form.client_secret == '') this.form.client_secret = this.current_env.client_secret
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('install.github.submit'), { ...this.form }
                )
            }
        }
    }
</script>