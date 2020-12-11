<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('servers.index')" class="hover:underline">Servers</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ server.name }}
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My production server" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                    <div class="flex flex-row items-center mb-4">
                        <form-input class="w-1/4" label="User" type="text" placeholder="rocket" name="ssh_user" v-model="form.ssh_user" :errors="$page.errors.ssh_user" />
                        <div class="px-4"></div>
                        <form-input class="w-3/4" label="Address" type="text" placeholder="sc1.rocket.mgk.dev" name="ssh_host" v-model="form.ssh_host" :errors="$page.errors.ssh_host" />
                    </div>

                    <hr class="my-8">

                    <form-input class="mb-4" label="Git path" placeholder="git" type="text" name="cmd_git" v-model="form.cmd_git" :errors="$page.errors.cmd_git" />
                    <form-input class="mb-4" label="npm path" placeholder="npm" type="text" name="cmd_npm" v-model="form.cmd_npm" :errors="$page.errors.cmd_npm" />
                    <form-input class="mb-4" label="Yarn path" placeholder="yarn" type="text" name="cmd_yarn" v-model="form.cmd_yarn" :errors="$page.errors.cmd_yarn" />
                    <form-input class="mb-4" label="Php path" placeholder="php" type="text" name="cmd_php" v-model="form.cmd_php" :errors="$page.errors.cmd_php" />
                    <form-input class="mb-4" label="Composer path" placeholder="composer" type="text" name="cmd_composer" v-model="form.cmd_composer" :errors="$page.errors.cmd_composer" />
                    <form-input class="mb-4" label="Composer options" placeholder="--no-dev" type="text" name="cmd_composer_options" v-model="form.cmd_composer_options" :errors="$page.errors.cmd_composer_options" />

                    <div class="flex justify-end mt-8">
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600 focus:outline-none">Edit</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            server: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    ssh_user: '',
                    ssh_host: '',

                    cmd_git: '',
                    cmd_npm: '',
                    cmd_yarn: '',
                    cmd_php: '',
                    cmd_composer: '',
                    cmd_composer_options: '',
                }
            }
        },

        mounted() {
            this.form = { ...this.server }
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.put(
                    this.$route('servers.update', this.server), { ...this.form }
                )
            }
        }
    }
</script>
