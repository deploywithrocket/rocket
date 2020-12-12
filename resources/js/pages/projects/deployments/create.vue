<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.deployments.index', project)" class="text-gray-500 hover:underline">Deployments</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Create
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    Create deployment
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <div class="flex flex-row items-center mb-4">
                            <form-select class="w-1/4" label="Type" name="type" required v-model="form.type" :errors="$page.errors.type" :options="{
                                commit: 'Commit',
                                branch: 'Branch',
                                tag: 'Tag',
                            }" />
                            <div class="px-4"></div>
                            <form-input class="w-3/4" label="Target" type="text" placeholder="14ef11c3949e319acf0cfb1d4683d05746e17cef" name="target" v-model="form.target" :errors="$page.errors.target" />
                        </div>
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-rocket"></i> Deploy</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
