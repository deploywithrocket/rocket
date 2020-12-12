<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.edit', project)" class="text-gray-500 hover:underline">Settings</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Environment file
        </nav>

        <div class="flex flex-row">
            <nav class="flex flex-col w-1/4">
                <inertia-link :href="$route('projects.edit', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-cog"></i> Common settings</inertia-link>
                <inertia-link :href="$route('projects.edit.env-file', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-key"></i> Environment file</inertia-link>
                <inertia-link :href="$route('projects.edit.shared', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-folder"></i> Shared</inertia-link>
                <inertia-link :href="$route('projects.edit.hooks', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-code"></i> Hooks</inertia-link>
                <inertia-link :href="$route('projects.edit.cron-jobs', project)" class="inline-block px-4 py-3 mb-1 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-100 rounded-lg hover:bg-gray-200"><i class="mr-2 fas fa-clock"></i> Cron jobs</inertia-link>
            </nav>

            <div class="flex flex-col items-start w-full mb-8 ml-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Environment file</h2>
                </div>
                <form @submit.prevent="submit" class="w-full">
                    <div class="w-full px-5 py-4">
                        <codemirror class="mb-4 bg-gray-100 border" v-model="form.env" :options="options" style="height: 48rem;"></codemirror>
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
            servers: Object,
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
                    env: '',
                }
            }
        },

        mounted() {
            this.form.env = this.project.env || ''
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
