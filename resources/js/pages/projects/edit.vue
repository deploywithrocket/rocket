<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ project.name }}
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My awesome project" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                    <form-input v-if="!repositories" class="mb-4" label="Repository URL" placeholder="git@github.com:mgkprod/rocket.git" type="text" name="repository_url" v-model="form.repository_url" :errors="$page.errors.repository_url" />
                    <form-select v-if="repositories" class="mb-4" label="Repository" name="repository_url" required v-model="form.repository_url" :errors="$page.errors.repository_url" :options="repositories" />

                    <form-input class="mb-4" label="Health check URL" placeholder="https://rocket.mgk.dev" type="text" name="health_url" v-model="form.health_url" :errors="$page.errors.health_url" />

                    <div class="flex flex-row items-center">
                        <form-select class="w-1/3 mb-4" label="Server" name="server_id" required v-model="form.server_id" :errors="$page.errors.server_id" :options="servers" />
                        <div class="px-1"></div>
                        <form-input class="w-2/3 mb-4" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" v-model="form.deploy_path" :errors="$page.errors.deploy_path" />
                    </div>

                    <form-textarea class="mb-4 font-mono" label="Environment file" placeholder='APP_NAME="My awesome project"' name="env" v-model="form.env" :errors="$page.errors.env" rows="30" />

                    <div class="flex justify-end mt-8">
                        <button class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600 focus:outline-none">Edit</button>
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
            repositories: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    repository_url: '',
                    health_url: '',
                    server_id: 0,
                    deploy_path: '',
                    env: '',
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
