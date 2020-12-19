<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-lg mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                Configure the app name and URL
            </div>
            <form class="w-full" @submit.prevent="submit">
                <alerts class="mt-4"></alerts>

                <div class="w-full px-5 py-4">
                    <p class="mb-4">Define the name of your Rocket instance as well as the URL through which you will access it.</p>

                    <form-input class="w-full mb-4" label="Name" type="text" name="name" v-model="form.name" :errors="$page.props.errors.name" />
                    <form-input class="w-full mb-4" label="URL" type="text" name="url" v-model="form.url" :errors="$page.props.errors.url" />
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
        layout: require('../../../layouts/gate').default,

        props: ['current_env'],

        data() {
            return {
                form: {
                    name: '',
                    url: '',
                }
            }
        },

        mounted() {
            if (this.form.name == '') this.form.name = this.current_env.name
            if (this.form.url == '') this.form.url = this.current_env.url
        },

        methods: {
            submit() {
                this.$page.props.errors = {}

                this.$inertia.post(
                    this.$route('install.app.submit'), { ...this.form }
                )
            }
        }
    }
</script>