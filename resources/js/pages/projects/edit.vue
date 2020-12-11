<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My awesome project" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                    <div class="flex flex-row items-center mb-4">
                        <form-input class="w-1/2" label="Repository" placeholder="mgkprod/rocket" type="text" name="repository" v-model="form.repository" :errors="$page.errors.repository" />
                        <div class="px-4"></div>
                        <form-input class="w-1/2" label="Branch" placeholder="main" type="text" name="branch" v-model="form.branch" :errors="$page.errors.branch" />
                    </div>

                    <form-checkbox class="mb-4" label="Trigger a deployment when code is pushed" name="push_to_deploy" v-model="form.push_to_deploy" :errors="$page.errors.push_to_deploy" />

                    <form-select class="mb-4" label="Server" name="server_id" required v-model="form.server_id" :errors="$page.errors.server_id" :options="servers" />
                    <form-input class="mb-4" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" v-model="form.deploy_path" :errors="$page.errors.deploy_path" />
                    <form-input class="mb-4" label="Environment" type="text" placeholder="production" name="environment" v-model="form.environment" :errors="$page.errors.environment" />

                    <form-input class="mb-4" label="Live URL" type="text" placeholder="https://rocket.mgk.dev" name="live_url" v-model="form.live_url" :errors="$page.errors.live_url" />

                    <form-input class="mb-4" label="Discord Webhook" type="text" placeholder="https://discord.com/api/webhooks/..." name="discord_webhook_url" v-model="form.discord_webhook_url" :errors="$page.errors.discord_webhook_url" />

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
            project: Object,
            servers: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    repository_url: '',
                    live_url: '',
                    server_id: 0,
                    deploy_path: '',
                    environment: '',
                    discord_webhook_url: '',
                    push_to_deploy: '',
                }
            }
        },

        mounted() {
            this.form = { ...this.project }
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.put(
                    this.$route('projects.update', this.project), { ...this.form }
                )
            }
        }
    }
</script>
