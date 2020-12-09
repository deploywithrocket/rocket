<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ project.name }}
        </h1>

        <div class="flex flex-col mb-8 xl:flex-row xl:-mx-2">
            <div class="w-full p-8 mb-4 bg-white rounded-lg shadow xl:mb-0 xl:w-1/2 xl:mx-2">
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-bold">Project details</h2>

                    <table class="w-full table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="w-1/3 text-sm font-bold">Name</td>
                                <td class="p-2 truncate">{{ project.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Live URL</td>
                                <td class="p-2">
                                    <div class="flex flex-row items-center w-full">
                                        <img :src="project.favicon_url" class="inline w-6 h-6 mr-2" v-if="project.live_url">
                                        <div class="truncate">
                                            <a :href="project.live_url" target="_blank" class="hover:underline">{{ project.live_url }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Deploy path</td>
                                <td class="p-2 truncate">{{ project.deploy_path }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Environment</td>
                                <td class="p-2 truncate">{{ project.environment }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Server</td>
                                <td class="p-2">{{ project.server.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Added at</td>
                                <td class="p-2">{{ $moment(project.created_at).format('L') }} {{ $moment(project.created_at).format('LT') }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Notifies on</td>
                                <td class="p-2">
                                    <template v-if="project.discord_webhook_url">
                                        <inertia-link :href="$route('projects.test-discord-webhook', project)" class="hover:underline"><i class="fab fa-discord"></i></inertia-link>
                                    </template>
                                    <template v-else>N/A</template>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-bold">Repository</h2>

                    <table class="w-full table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="w-1/3 text-sm font-bold">Repository</td>
                                <td class="p-2 truncate">
                                    <div class="flex flex-row items-center w-full">
                                        <img :src="'https://github.com/' + project.repository.split('/')[0] + '.png'" class="inline w-6 h-6 mr-2 rounded-full">
                                        <div class="truncate">
                                            <a :href="'https://github.com/' + project.repository" target="_blank" class="hover:underline">{{ project.repository }}</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Deploy branch</td>
                                <td class="p-2 font-mono">{{ project.branch }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <inertia-link :href="$route('projects.edit', project)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-cog"></i> Settings</inertia-link>
                <inertia-link :href="$route('projects.edit.env-file', project)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-key"></i> Environment file</inertia-link>
                <inertia-link :href="$route('projects.edit.hooks', project)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-code"></i> Hooks</inertia-link>
                <inertia-link :href="$route('projects.edit.cron-jobs', project)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300"><i class="fas fa-clock"></i> Cron jobs</inertia-link>
            </div>

            <div class="w-full p-8 mb-4 bg-white rounded-lg shadow xl:mb-0 xl:w-1/2 xl:mx-2">
                <div class="mb-8">
                    <h2 class="mb-4 text-xl font-bold">Deployments</h2>

                    <table class="w-full table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="w-1/3 text-sm font-bold">Last deployment</td>
                                <td class="p-2 truncate">
                                    <template v-if="deployments && deployments[0]">
                                        {{ $moment(deployments[0].created_at).format('L') }} {{ $moment(deployments[0].created_at).format('LTS') }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                            </tr>
                             <tr class="border-b">
                                <td class="w-1/3 text-sm font-bold">Last deployment duration</td>
                                <td class="p-2 truncate">
                                    <template v-if="deployments && deployments[0]">
                                        {{ deployments[0].duration }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">Today's</td>
                                <td class="p-2 truncate">{{ deployments_stats.today }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="text-sm font-bold">This week</td>
                                <td class="p-2 truncate">{{ deployments_stats.this_week }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <button @click="deploy" class="inline-block px-4 py-2 text-sm font-bold text-white bg-pink-500 rounded hover:bg-pink-600"><i class="fas fa-upload"></i> Deploy now</button>
            </div>
        </div>

        <h1 class="my-8 text-2xl font-bold">Recent deployments</h1>

        <div class="p-8 mb-8 bg-white rounded-lg shadow">
            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="w-64 text-sm font-bold text-left">Started at</th>
                        <th class="w-32 p-2 text-sm text-left">Status</th>
                        <th class="w-48 p-2 text-sm text-left">Release</th>
                        <th class="p-2 text-sm text-left">Committer</th>
                        <th class="p-2 text-sm text-left">Commit</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="deployment in deployments" v-bind:key="deployment.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(project.id, deployment.id)">
                        <td class="p-2">
                            <template v-if="deployment.started_at">
                                {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                            </template>
                            <template v-else>
                                N/A
                            </template>
                        </td>
                        <td class="p-2">{{ deployment.status }}</td>
                        <td class="p-2">{{ deployment.release }}</td>
                        <td class="p-2 truncate">
                            <template v-if="deployment.commit.committer">
                                <img :src="deployment.commit.committer.avatar_url" class="inline w-6 h-6 mr-1 rounded-full">
                                <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">
                                    {{ deployment.commit.committer.login }}
                                </a>
                            </template>
                            <template v-else>
                                {{ deployment.commit.commit.committer.name }}
                            </template>
                        </td>
                        <td class="p-2 font-mono truncate">
                            <a :href="'https://github.com/' + deployment.commit.from_repository + '/tree/' + deployment.commit.from_branch" class="hover:underline" target="_blank">{{ deployment.commit.from_branch }}</a>@<a :href="'https://github.com/' + deployment.commit.from_repository + '/commit/' + deployment.commit.sha" class="hover:underline" target="_blank">{{ deployment.commit.sha.substring(0, 7) }}</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <button @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600"><i class="mr-1 fas fa-times"></i> Remove this project from Rocket</button>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            project: Array,
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
            deploy() {
                if (confirm("Do you really want to deploy the latest commit on branch " + this.project.branch + "?")) {
                    this.$inertia.visit(this.$route('projects.deploy', this.project))
                }
            },
            destroy() {
                if (confirm("Do you really want to remove this project?")) {
                    this.$inertia.delete(this.$route('projects.destroy', this.project))
                }
            }
        }
    }
</script>
