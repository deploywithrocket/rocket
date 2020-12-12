<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Overview
        </nav>

        <div class="flex flex-col items-start lg:flex-row lg:-mx-4">
            <div class="lg:max-w-sm lg:mx-4">
                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 bg-gray-50">
                        <div class="relative flex items-center justify-center flex-none w-20 h-20 mx-auto bg-white border-4 border-gray-100 rounded-full">
                            <img :src="project.favicon_url" class="inline w-10 h-10" v-if="project.live_url">

                            <div class="absolute bottom-0 right-0 text-center text-green-500" v-if="deployments && deployments[0] && deployments[0].ping && deployments[0].ping.status == 'success'">
                                <i class="fa fa-check-circle"></i>
                            </div>
                            <div class="absolute bottom-0 right-0 text-center text-red-500" v-if="deployments && deployments[0] && deployments[0].ping && deployments[0].ping.status == 'failed'">
                                <i class="fa fa-times-circle"></i>
                            </div>
                        </div>

                        <div class="mt-4 text-center">
                            <div>
                                <h2 class="inline font-semibold">{{ project.name }}</h2>
                            </div>
                            <div>
                                {{ deployments && deployments[0] ? 'Last deployed ' + $moment(deployments[0].created_at).fromNow() : 'Never deployed' }}
                            </div>
                        </div>
                    </div>

                    <table class="w-full table-fixed">
                        <tbody>
                            <tr class="even:bg-gray-50">
                                <td class="w-1/3 px-5 py-3 text-sm font-bold">Live URL</td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-row items-center w-full">
                                        <img :src="project.favicon_url" class="inline w-5 h-5 mr-2" v-if="project.live_url">
                                        <div class="truncate">
                                            <a :href="project.live_url" target="_blank" class="hover:underline">{{ project.live_url }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="even:bg-gray-50">
                                <td class="w-1/3 px-5 py-3 text-sm font-bold">Repository</td>
                                <td class="px-5 py-3 truncate">
                                    <div class="flex flex-row items-center w-full">
                                        <img :src="'https://github.com/' + project.repository.split('/')[0] + '.png'" class="inline w-6 h-6 mr-2 rounded ">
                                        <div class="truncate">
                                            <a :href="'https://github.com/' + project.repository" target="_blank" class="hover:underline">{{ project.repository }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="even:bg-gray-50">
                                <td class="px-5 py-3 text-sm font-bold">Deploy branch</td>
                                <td class="px-5 py-3">{{ project.branch }}</td>
                            </tr>
                            <tr class="even:bg-gray-50">
                                <td class="px-5 py-3 text-sm font-bold">Push to deploy</td>
                                <td class="px-5 py-3">
                                    {{ project.push_to_deploy ? 'Enabled' : 'Disabled' }}
                                </td>
                            </tr>
                            <tr class="even:bg-gray-50">
                                <td class="px-5 py-3 text-sm font-bold">Notifies on</td>
                                <td class="px-5 py-3">
                                    <template v-if="project.discord_webhook_url">
                                        <inertia-link :href="$route('projects.test-discord-webhook', project)" class="hover:underline"><i class="fab fa-discord"></i></inertia-link>
                                    </template>
                                    <template v-else>N/A</template>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex flex-row w-full px-5 py-4 text-sm text-right text-gray-600 lg:px-6 bg-gray-50">
                        <inertia-link :href="$route('projects.edit', project)" class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-cog"></i> Settings</inertia-link>
                        <div class="mx-auto"></div>
                        <button @click="deployNow" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="mr-1 fas fa-cloud-upload-alt"></i> Deploy now</button>
                    </div>
                </div>

                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <h2>Show the world you launch rockets!</h2>
                    </div>

                    <div class="px-5 py-4">
                        <img :src="'https://img.shields.io/endpoint?style=flat-square&url=' + $route('api.projects.shield', project)" alt="Shield" class="mb-4">
                        This shield shows the status and the date of your project's last deployment.
                    </div>
                </div>
            </div>

            <div class="w-full lg:mx-4">
                <div class="flex flex-row">
                    <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                        <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                            <h2>Usage indicators</h2>
                        </div>

                        <table class="w-full table-fixed">
                            <tbody>
                                <tr class="even:bg-gray-50">
                                    <td class="w-1/2 px-5 py-3 text-sm font-bold">Deployments today</td>
                                    <td class="px-5 py-3">{{ deployments_stats.today }}</td>
                                </tr>
                                <tr class="even:bg-gray-50">
                                    <td class="px-5 py-3 text-sm font-bold">Deployments this week</td>
                                    <td class="px-5 py-3">{{ deployments_stats.this_week }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <div class="flex items-center">
                            <h2>Recent deployments</h2>
                            <div class="mx-auto"></div>
                            <inertia-link :href="$route('projects.deployments.index', project)" class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-rocket"></i> Show more</inertia-link>
                        </div>
                    </div>

                    <table class="w-full table-fixed">
                        <thead class="border-b border-gray-100">
                            <tr class="even:bg-gray-50">
                                <th class="w-10"></th>
                                <th class="w-48 px-5 py-3 text-sm font-semibold text-center">Requested</th>
                                <th class="px-5 py-3 text-sm font-semibold text-center">Commit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="deployment in deployments" v-bind:key="deployment.id" class="cursor-pointer even:bg-gray-50 hover:bg-gray-100" @click="show(project.id, deployment.id)">
                                <td class="py-3 text-center" :class="{
                                    'text-green-500': deployment.status == 'success',
                                    'text-red-500': deployment.status == 'error',
                                    'text-gray-400': deployment.status == 'abandonned',
                                }">
                                    <span v-if="deployment.status != 'queued' && deployment.status != 'in_progress'">
                                        &bull;
                                    </span>
                                    <span v-else>
                                        <i class="fas fa-spinner fa-spin"></i>
                                    </span>
                                </td>
                                <td class="px-5 py-3">
                                    {{ $moment(deployment.created_at).fromNow() }}
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-row items-center">
                                        <div class="w-full md:w-1/2 lg:w-1/3">
                                            <template v-if="deployment.commit.from_ref">
                                                {{ deployment.commit.from_ref.replace('heads/', '') }} @
                                            </template>
                                            <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ? deployment.commit.sha.substring(0, 7) : '-' }}</a>
                                        </div>
                                        <div class="hidden w-1/3 lg:flex">
                                            <img :src="deployment.commit.committer.avatar_url" class="inline w-4 h-4 mr-1 rounded ">
                                            <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">
                                                {{ deployment.commit.committer.login }}
                                            </a>
                                        </div>
                                        <div class="hidden md:flex md:w-1/2 lg:w-1/3">
                                            <div class="truncate">
                                                {{ deployment.commit.message }}
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            project: Object,
            deployments: Array,
            deployments_stats: Object,
        },

        data() {
            return {}
        },

        mounted(){
        },

        methods: {
            show(project_id, deployment_id) {
                this.$inertia.visit(this.$route('projects.deployments.show', [project_id, deployment_id]))
            },
            deployNow() {
                if (confirm("Do you really want to deploy the most recent commit from the " + this.project.branch + " branch?")) {
                    this.$inertia.post(this.$route('projects.deployments.store', this.project), {
                        type: 'branch',
                        target: this.project.branch,
                    })
                }
            }
        }
    }
</script>
