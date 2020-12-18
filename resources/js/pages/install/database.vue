<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-md mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                Database
            </div>
            <form class="w-full" @submit.prevent="submit">
                <div class="w-full px-5 py-4">
                    <p class="mb-8">Create a database on your server and set the credentials using the form below.</p>

                    <form-input class="mb-4" label="Connection" type="text" name="connection" v-model="form.connection" :errors="$page.errors.connection" />
                    <form-input class="mb-4" label="Host" type="text" name="host" v-model="form.host" :errors="$page.errors.host" />
                    <form-input class="mb-4" label="Port" type="text" name="port" v-model="form.port" :errors="$page.errors.port" />
                    <form-input class="mb-4" label="Database" type="text" name="database" v-model="form.database" :errors="$page.errors.database" />
                    <form-input class="mb-4" label="Username" type="text" name="username" v-model="form.username" :errors="$page.errors.username" />
                    <form-input class="mb-4" label="Password" type="text" name="password" v-model="form.password" :errors="$page.errors.password" />
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

        props: ['env'],

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