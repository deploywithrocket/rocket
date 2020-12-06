<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            Campaigns
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ campaign.id }}
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="flex items-center justify-center mb-8">
                    <div class="device device-iphone-x">
                        <div class="device-frame">
                            <div class="device-content">
                                <div class="flex flex-col items-start justify-start w-full h-full bg-white rounded-3xl">
                                    <div class="w-full p-4 pt-10 text-center bg-gray-100 border-b rounded-t-3xl">
                                        <div class="inline-block p-4 mb-2 text-lg text-white bg-pink-500 rounded-full"><i class="fas fa-paper-plane"></i></div>
                                        <form-input class="bg-white" placeholder="Sender Name" type="text" name="sender_name" v-model="form.sender_name" :errors="$page.errors.sender_name" />
                                    </div>

                                    <div class="w-full mt-auto">
                                        <div class="flex flex-col items-end justify-end p-4 ml-auto text-white">
                                            <div class="px-3 py-2 mb-2 bg-pink-500 rounded-lg" v-for="(message, k) in messages" v-bind:key="k">
                                                {{ message }}
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full p-4 pt-0">
                                        <textarea class="w-full px-3 py-3 text-sm text-gray-700 border rounded-lg appearance-none focus:outline-none focus:shadow-outline"
                                                :class="{ 'border-red-500 mb-1': $page.errors && $page.errors.message && $page.errors.message.length }"
                                                name="message"
                                                :rows="4"
                                                required
                                                v-model="form.message"
                                                ref="message"
                                        ></textarea>
                                    </div>

                                    <p v-if="$page.errors && $page.errors.message && $page.errors.message.length" class="pl-1 text-xs italic text-red-500" v-text="$page.errors && $page.errors.message && $page.errors.message[0]"></p>
                                </div>
                            </div>
                        </div>
                        <div class="device-stripe"></div>
                        <div class="device-header"></div>
                        <div class="device-sensors"></div>
                        <div class="device-btns"></div>
                        <div class="device-power"></div>
                    </div>
                </div>

                <div class="w-full max-w-md p-8 bg-white rounded-lg shadow">
                    <form-select class="mb-4" label="Directory" name="directory_id" required v-model="form.directory_id" :errors="$page.errors.directory_id" :options="directories" />

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
            campaign: Object,
            directories: Array,
        },

        data() {
            return {
                form: {
                    sender_name: '',
                    mesasge: '',
                    directory_id: 0,
                }
            }
        },

        mounted() {
            this.form.sender_name = this.campaign.sender_name
            this.form.message = this.campaign.message
            this.form.directory_id = this.campaign.directory_id
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('next.campaigns.update'), { ...this.form }
                )
            }
        }
    }
</script>
