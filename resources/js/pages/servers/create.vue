<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('servers.index')" class="hover:underline">Servers</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Add Server
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My production server" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                    <div class="flex flex-row items-center mb-4">
                        <form-input class="w-1/4" label="User" type="text" placeholder="rocket" name="ssh_user" v-model="form.ssh_user" :errors="$page.errors.ssh_user" />
                        <div class="px-4"></div>
                        <form-input class="w-3/4" label="Host" type="text" placeholder="sc1.rocket.mgk.dev" name="ssh_host" v-model="form.ssh_host" :errors="$page.errors.ssh_host" />
                    </div>

                    <div class="flex justify-end mt-8">
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600 focus:outline-none">Add</button>
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
