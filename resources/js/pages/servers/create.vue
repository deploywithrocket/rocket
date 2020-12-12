<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('servers.index')" class="text-gray-500 hover:underline">Servers</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Add
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    Add server
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <form-input class="mb-4" label="Name" placeholder="My production server" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                        <div class="flex flex-row items-center mb-4">
                            <form-input class="w-1/4" label="User" type="text" placeholder="rocket" name="ssh_user" v-model="form.ssh_user" :errors="$page.errors.ssh_user" />
                            <div class="px-4"></div>
                            <form-input class="w-3/4" label="Host" type="text" placeholder="sc1.rocket.mgk.dev" name="ssh_host" v-model="form.ssh_host" :errors="$page.errors.ssh_host" />
                        </div>
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-plus"></i> Add</button>
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
            directories: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    ssh_user: '',
                    ssh_host: '',
                }
            }
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('servers.store'), { ...this.form }
                )
            }
        }
    }
</script>
