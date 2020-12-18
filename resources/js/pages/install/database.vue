<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-lg mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                Database
            </div>
            <form class="w-full" @submit.prevent="submit">
                <alerts class="mt-4"></alerts>

                <div class="w-full px-5 py-4">
                    <p class="mb-8">Create a database on your server and set the credentials using the form below.</p>

                    <form-select class="w-full mb-4" label="Connection" type="text" name="connection" v-model="form.connection" :errors="$page.errors.connection" :options="connections" />
                    <form-input class="w-full mb-4" label="Host" type="text" name="host" v-model="form.host" :errors="$page.errors.host" />
                    <form-input class="w-full mb-4" label="Port" type="text" name="port" v-model="form.port" :errors="$page.errors.port" />
                    <form-input class="w-full mb-4" label="Database" type="text" name="database" v-model="form.database" :errors="$page.errors.database" />
                    <form-input class="w-full mb-4" label="Username" type="text" name="username" v-model="form.username" :errors="$page.errors.username" />
                    <form-input class="w-full mb-4" label="Password" type="password" name="password" v-model="form.password" :errors="$page.errors.password" />
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
                    connection: '',
                    host: '',
                    port: '',
                    database: '',
                    username: '',
                    password: '',
                }
            }
        },

        mounted() {
            if (this.form.connection == '') this.form.connection = this.current_env.connection
            if (this.form.host == '') this.form.host = this.current_env.host
            if (this.form.port == '') this.form.port = this.current_env.port
            if (this.form.database == '') this.form.database = this.current_env.database
            if (this.form.username == '') this.form.username = this.current_env.username
            if (this.form.password == '') this.form.password = this.current_env.password
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('install.database.submit'), { ...this.form }
                )

                this.form.password = ''
            }
        }
    }
</script>