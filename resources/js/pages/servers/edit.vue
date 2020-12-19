<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('servers.index')" class="text-gray-500 hover:underline">Servers</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <div class="text-gray-500">{{ server.name }}</div>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Edit
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    Edit {{ server.name }}
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <form-input class="mb-4" label="Name" placeholder="My production server" type="text" name="name" v-model="form.name" :errors="$page.props.errors.name" />

                        <div class="flex flex-row items-center mb-4">
                            <form-input class="w-1/4" label="User" type="text" placeholder="rocket" name="ssh_user" v-model="form.ssh_user" :errors="$page.props.errors.ssh_user" />
                            <div class="px-4"></div>
                            <form-input class="w-3/4" label="Address" type="text" placeholder="sc1.rocket.mgk.dev" name="ssh_host" v-model="form.ssh_host" :errors="$page.props.errors.ssh_host" />
                        </div>

                        <hr class="my-8">

                        <form-input class="mb-4" label="Git path" placeholder="git" type="text" name="cmd_git" v-model="form.cmd_git" :errors="$page.props.errors.cmd_git" />
                        <form-input class="mb-4" label="npm path" placeholder="npm" type="text" name="cmd_npm" v-model="form.cmd_npm" :errors="$page.props.errors.cmd_npm" />
                        <form-input class="mb-4" label="Yarn path" placeholder="yarn" type="text" name="cmd_yarn" v-model="form.cmd_yarn" :errors="$page.props.errors.cmd_yarn" />
                        <form-input class="mb-4" label="Php path" placeholder="php" type="text" name="cmd_php" v-model="form.cmd_php" :errors="$page.props.errors.cmd_php" />
                        <form-input class="mb-4" label="Composer path" placeholder="composer" type="text" name="cmd_composer" v-model="form.cmd_composer" :errors="$page.props.errors.cmd_composer" />
                        <form-input class="mb-4" label="Composer options" placeholder="--no-dev" type="text" name="cmd_composer_options" v-model="form.cmd_composer_options" :errors="$page.props.errors.cmd_composer_options" />
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
            <button @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600"><i class="mr-1 opacity-50 fas fa-times"></i> Remove this server from Rocket</button>
        </div>
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
                this.$page.props.errors = {}

                this.$inertia.put(
                    this.$route('servers.update', this.server), { ...this.form }
                )
            },
            destroy() {
                if (confirm("Do you really want to remove this server?")) {
                    this.$inertia.delete(this.$route('servers.destroy', this.server))
                }
            }
        }
    }
</script>
