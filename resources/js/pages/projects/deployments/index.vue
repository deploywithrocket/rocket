<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Deployments
        </nav>

        <div class="flex flex-row items-start">
            <div class="w-full">
                <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                        <div class="flex items-center">
                            <h2>Deployments</h2>
                            <div class="mx-auto"></div>

                            <button @click="deployNow" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600"><i class="mr-1 opacity-75 fas fa-cloud-upload-alt"></i> Deploy now</button>
                            <inertia-link :href="$route('projects.deployments.create', project)" class="inline-block px-4 py-2 ml-2 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"><i class="mr-1 opacity-75 fas fa-plus"></i> Custom deployment</inertia-link>
                        </div>
                    </div>

                    <table class="w-full table-fixed">
                        <thead class="border-b border-gray-100">
                            <tr class="even:bg-gray-50">
                                <th class="w-8"></th>
                                <th class="w-48 px-5 py-3 text-sm font-semibold text-center">Release #</th>
                                <th class="w-64 px-5 py-3 text-sm font-semibold text-center">Started</th>
                                <th class="w-64 px-5 py-3 text-sm font-semibold text-center">Ended</th>
                                <th class="w-64 px-5 py-3 text-sm font-semibold text-center">Duration</th>
                                <th class="px-5 py-3 text-sm font-semibold text-center">Commit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="deployment in deployments.data" v-bind:key="deployment.id" class="cursor-pointer even:bg-gray-50 hover:bg-gray-100" @click="show(project.id, deployment.id)">
                                <td class="px-5 py-3 text-center" :class="{
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
                                <td class="px-5 py-3 text-center">{{ deployment.release }}</td>
                                <td class="px-5 py-3 text-center">
                                    <template v-if="deployment.started_at">
                                        {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <template v-if="deployment.ended_at">
                                        {{ $moment(deployment.ended_at).format('L') }} {{ $moment(deployment.ended_at).format('LTS') }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                                <td class="px-5 py-3 text-center">
                                    <template v-if="deployment.duration">
                                        {{ deployment.duration }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                                <td class="px-5 py-3">
                                    <div class="flex flex-row items-center">
                                        <div class="flex-none w-48">
                                            <template v-if="deployment.commit.from_ref">
                                                {{ deployment.commit.from_ref.replace('heads/', '') }} @
                                            </template>
                                            <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ? deployment.commit.sha.substring(0, 7) : '-' }}</a>
                                        </div>

                                        <div class="truncate">
                                            <div class="text-sm truncate">
                                                {{ deployment.commit.message }}
                                            </div>

                                            <img :src="deployment.commit.committer.avatar_url" class="inline w-4 h-4 mr-1 rounded">
                                            <a :href="'https://github.com/' + deployment.commit.committer.login" class="text-sm hover:underline" target="_blank">
                                                {{ deployment.commit.committer.login }}
                                            </a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex items-center justify-between">
                            <inertia-link
                                v-if="deployments.prev_page_url"
                                preserve-scroll
                                :href="deployments.prev_page_url"
                                class="inline-block px-4 py-2 text-sm text-gray-500 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"
                            >
                                <i class="fas fa-arrow-left"></i>
                            </inertia-link>
                            <div v-else></div>

                            <div class="text-sm text-gray-500">
                                Displaying page {{ deployments.current_page }} / {{ deployments.last_page }}
                            </div>

                            <inertia-link
                                preserve-scroll
                                v-if="deployments.next_page_url"
                                :href="deployments.next_page_url"
                                class="inline-block px-4 py-2 text-sm text-gray-500 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"
                            >
                                <i class="fas fa-arrow-right"></i>
                            </inertia-link>
                            <div v-else></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/app').default,

        props: {
            project: Object,
            deployments: Object,
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
