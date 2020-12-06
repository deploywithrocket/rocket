<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            Servers
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Add Server
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <form-input class="mb-4" label="Name" placeholder="My production server" type="text" name="name" v-model="form.name" :errors="$page.errors.name" />

                    <div class="flex flex-row items-center">
                        <form-input class="mb-4 w-1/4" label="User" type="text" placeholder="rocket" name="user" v-model="form.user" :errors="$page.errors.user" />
                        <div class="px-2">@</div>
                        <form-input class="mb-4 w-3/4" label="Address" type="text" placeholder="sc1.rocket.mgk.dev" name="address" v-model="form.address" :errors="$page.errors.address" />
                    </div>

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
            directories: Object,
        },

        data() {
            return {
                form: {
                    name: '',
                    user: '',
                    address: '',
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
