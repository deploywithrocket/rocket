<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Add Project
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My awesome project" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />
                    <form-input class="mb-4" label="GitHub Repository" placeholder="mgkprod/rocket.git" type="text" name="repository" v-model="form.repository" :errors="$page.errors.repository" />

                    <form-select class="mb-4" label="Server" name="server_id" required v-model="form.server_id" :errors="$page.errors.server_id" :options="servers" />
                    <form-input class="mb-4" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" v-model="form.deploy_path" :errors="$page.errors.deploy_path" />

                    <div class="flex justify-end mt-8">
                        <button class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600 focus:outline-none">Add</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import {Howl, Howler} from 'howler';

    export default {
        layout: require('../../layouts/app').default,

        props: {
            servers: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    repository: '',
                    health_url: '',
                    server_id: 0,
                    deploy_path: '',
                }
            }
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('projects.store'), { ...this.form }
                )
            }
        }
    }
</script>
