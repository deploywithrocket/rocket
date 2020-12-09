<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Environment file
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <MonacoEditor class="mb-4 bg-gray-100 border" language="ini" theme="vs-dark" v-model="form.env" :options="{ minimap: {enabled: false}}" style="height: 48rem;" />

                    <div class="flex justify-end mt-8">
                        <button class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600 focus:outline-none">Save</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<script>
    import MonacoEditor from 'vue-monaco'

    export default {
        layout: require('../../../layouts/app').default,

        components: {
            MonacoEditor
        },

        props: {
            project: Object,
            servers: Object,
        },

        data() {
            return {
                form: {
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
                    this.$route('projects.update.env-file', this.project), { ...this.form }
                )
            }
        }
    }
</script>
