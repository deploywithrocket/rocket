<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Hooks
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <div class="mb-8">
                        <div class="mb-4">
                            On this page, you can enter Bash scripts that will be executed on your server during the deployment process.<br>
                            Like any other step during your deployment, if a deployment hook exits with a non-zero status code, the entire deployment will be cancelled. This prevents your applications from experiencing downtime with a broken deployment.
                        </div>
                        <div class="mb-4">
                            You may use the following variables within your deployment hook scripts:<br>
                            <table class="w-full table-auto">
                                <tbody>
                                    <tr class="border-b">
                                        <td class="px-2 py-2 font-mono">[[release]]</td>
                                        <td class="px-2 py-2">
                                            Resolves to the current release path, within releases
                                        </td>
                                    </tr>
                                    <tr class="border-b">
                                        <td class="px-2 py-2 font-mono">[[sha]]</td>
                                        <td class="px-2 py-2">
                                            Resolves to the commit hash that is being deployed
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex flex-row items-center mb-4">
                        <div class="w-1/3">
                            <span class="font-mono font-bold">started</span>
                            <p>Right before we start deploying</p>
                        </div>
                        <codemirror class="w-2/3 mb-4 ml-4 bg-gray-100 border h-72" v-model="form.started" :options="options"></codemirror>
                    </div>

                    <div class="flex flex-row items-center mb-4">
                        <div class="w-1/3">
                            <span class="font-mono font-bold">provisioned</span>
                            <p>After cloning and installing vendors dependencies</p>
                        </div>
                        <codemirror class="w-2/3 mb-4 ml-4 bg-gray-100 border h-72" v-model="form.provisioned" :options="options"></codemirror>
                    </div>

                    <div class="flex flex-row items-center mb-4">
                        <div class="w-1/3">
                            <span class="font-mono font-bold">built</span>
                            <p>Once the production assets have been built</p>
                        </div>
                        <codemirror class="w-2/3 mb-4 ml-4 bg-gray-100 border h-72" v-model="form.built" :options="options"></codemirror>
                    </div>

                    <div class="flex flex-row items-center mb-4">
                        <div class="w-1/3">
                            <span class="font-mono font-bold">published</span>
                            <p>Deployment is done and website is live</p>
                        </div>
                        <codemirror class="w-2/3 mb-4 ml-4 bg-gray-100 border h-72" v-model="form.published" :options="options"></codemirror>
                    </div>

                    <div class="flex flex-row items-center mb-4">
                        <div class="w-1/3">
                            <span class="font-mono font-bold">finished</span>
                            <p>When the plan is complete</p>
                        </div>
                        <codemirror class="w-2/3 mb-4 ml-4 bg-gray-100 border h-72" v-model="form.finished" :options="options"></codemirror>
                    </div>

                    <div class="flex justify-end mt-8">
                        <button class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600 focus:outline-none">Save hooks</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>

<style lang="scss">
    .CodeMirror {
        height: 100% !important;
    }
</style>

<script>
    import { codemirror } from 'vue-codemirror-lite'
    require('codemirror/mode/shell/shell')
    require('codemirror/theme/neo.css')
    require('codemirror/keymap/sublime')

    export default {
        layout: require('../../../layouts/app').default,

        components: {
            codemirror
        },

        props: {
            project: Object,
        },

        data() {
            return {
                options: {
                    tabSize: 4,
                    lineNumbers: true,
                    lineWrapping: true,
                    viewportMargin: Infinity,
                    theme: 'neo',
                    keyMap: 'sublime',
                },
                form: {
                    started: '',
                    provisioned: '',
                    built: '',
                    published: '',
                    finished: '',
                }
            }
        },

        mounted() {
            this.form.started = this.project.hooks.started || ''
            this.form.provisioned = this.project.hooks.provisioned || ''
            this.form.built = this.project.hooks.built || ''
            this.form.published = this.project.hooks.published || ''
            this.form.finished = this.project.hooks.finished || ''
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.put(
                    this.$route('projects.update.hooks', this.project), { ...this.form }
                )
            }
        }
    }
</script>
