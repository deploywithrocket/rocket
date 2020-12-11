<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Deployments
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Create deployment
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <div class="flex flex-row items-center mb-4">
                        <form-select class="w-1/4" label="Type" name="type" required v-model="form.type" :errors="$page.errors.type" :options="{
                            commit: 'Commit',
                            branch: 'Branch',
                            tag: 'Tag',
                        }" />
                        <div class="px-4"></div>
                        <form-input class="w-3/4" label="Target" type="text" placeholder="14ef11c3949e319acf0cfb1d4683d05746e17cef" name="target" v-model="form.target" :errors="$page.errors.target" />
                    </div>

                    <div class="flex justify-end mt-8">
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600 focus:outline-none">Deploy</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/app').default,

        props: {
            project: Object,
            deployment: Object,
        },

        data() {
            return {
                form: {
                    type: 'commit',
                    target: '',
                }
            }
        },

        mounted(){
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.post(
                    this.$route('projects.deployments.store', this.project), { ...this.form }
                )
            }
        }
    }
</script>
