<template>
    <div>
        <nav class="flex items-center my-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Add
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    Add project
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <form-input class="mb-4" label="Name" placeholder="My awesome project" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                        <form-checkbox class="mb-4" label="This is a Laravel project" name="use_laravel_preset" v-model="form.use_laravel_preset"  :errors="$page.errors.use_laravel_preset" />

                        <div class="flex flex-row items-center mb-4">
                            <form-input class="w-1/2" label="Repository" placeholder="mgkprod/rocket" type="text" name="repository" v-model="form.repository" :errors="$page.errors.repository" />
                            <div class="px-4"></div>
                            <form-input class="w-1/2" label="Branch" placeholder="main" type="text" name="branch" v-model="form.branch" :errors="$page.errors.branch" />
                        </div>

                        <form-select class="mb-4" label="Server" name="server_id" required v-model="form.server_id" :errors="$page.errors.server_id" :options="servers" />
                        <form-input class="mb-4" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" v-model="form.deploy_path" :errors="$page.errors.deploy_path" />
                        <form-input class="mb-4" label="Environment" type="text" placeholder="production" name="environment" v-model="form.environment" :errors="$page.errors.environment" />

                        <form-input class="mb-4" label="Live URL" type="text" placeholder="https://rocket.mgk.dev" name="live_url" v-model="form.live_url" :errors="$page.errors.live_url" />
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"><i class="mr-1 fas fa-plus"></i> Add</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
                    branch: '',
                    live_url: '',
                    server_id: 0,
                    deploy_path: '',
                    environement: '',
                    use_laravel_preset: false,
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
