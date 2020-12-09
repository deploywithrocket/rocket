<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Edit
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Cron jobs
        </h1>

        <form @submit.prevent="submit">
            <div class="flex flex-col items-center justify-center w-full">
                <div class="w-full p-8 bg-white rounded-lg shadow">
                    <div class="mb-8">
                        <div class="mb-4">
                            On this page, you can specify cron jobs that will be appended into the server's cron scheduler.<br>
                            E.g. this is useful for Laravel projects that require the use of scheduled tasks (<a class="hover:underline" href="https://laravel.com/docs/master/scheduling#starting-the-scheduler" target="_blank"><i class="fas fa-book"></i> laravel scheduler</a>)
                        </div>
                    </div>

                    <codemirror class="mb-4 bg-gray-100 border h-72" v-model="form.cron_jobs" :options="options"></codemirror>

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
                    cron_jobs: '',
                }
            }
        },

        mounted() {
            this.form.cron_jobs = this.project.cron_jobs || ''
        },

        methods: {
            submit() {
                this.$page.errors = {}

                this.$inertia.put(
                    this.$route('projects.update.cron-jobs', this.project), { ...this.form }
                )
            }
        }
    }
</script>
