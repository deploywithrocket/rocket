<template>
    <div>
        <nav class="flex items-center my-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.edit', project)" class="text-gray-500 hover:underline">Settings</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Hooks
        </nav>

        <div class="flex flex-row">
            <nav class="flex flex-col w-1/4">
                <inertia-link :href="$route('projects.edit', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-cog"></i> Common settings</inertia-link>
                <inertia-link :href="$route('projects.edit.env-file', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-key"></i> Environment file</inertia-link>
                <inertia-link :href="$route('projects.edit.shared', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-folder"></i> Shared</inertia-link>
                <inertia-link :href="$route('projects.edit.hooks', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-code"></i> Hooks</inertia-link>
                <inertia-link :href="$route('projects.edit.cron-jobs', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-clock"></i> Cron jobs</inertia-link>
            </nav>

            <div class="flex flex-col items-start w-full mb-8 ml-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Hooks</h2>
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <div class="mb-8">
                            <div class="mb-4">
                                On this page, you can enter Bash scripts that will be executed on your server during the deployment process.<br>
                                Like any other step during your deployment, if a deployment hook exits with a non-zero status code, the entire deployment will be cancelled. This prevents your applications from experiencing downtime with a broken deployment.
                            </div>
                            <div class="mb-4">
                                <p>
                                    Under the hood, Rocket uses Laravel Blade templating system to build the final deployment script.<br>
                                    You may use the following variables within your hooks:
                                </p>

                                <table class="w-full table-auto">
                                    <tbody>
                                        <tr class="border-b">
                                            <td class="font-mono text-sm font-bold">{!! $deploy_path !!}</td>
                                            <td class="p-2">
                                                Resolves to the project's root directory
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="font-mono text-sm font-bold">{!! $release_path !!}</td>
                                            <td class="p-2">
                                                Resolves to the current release path, within releases
                                            </td>
                                        </tr>
                                        <tr class="border-b">
                                            <td class="font-mono text-sm font-bold">{!! $ref !!}</td>
                                            <td class="p-2">
                                                Resolves to the ref that is being deployed
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="font-mono text-sm font-bold">{!! $sha !!}</td>
                                            <td class="p-2">
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
                    </div>
                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex justify-end">
                            <button class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"><i class="fas fa-check"></i> Save</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
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
