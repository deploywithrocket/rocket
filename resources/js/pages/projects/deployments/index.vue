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

                            <button @click="deployNow" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="md:mr-1 fas fa-cloud-upload-alt"></i> <span class="hidden md:inline">Deploy now</span></button>
                            <inertia-link :href="$route('projects.deployments.create', project)" class="inline-block px-4 py-2 ml-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="opacity-50 md:mr-1 fas fa-plus"></i> <span class="hidden sm:inline">Custom deployment</span></inertia-link>
                        </div>
                    </div>

                    <table class="w-full overflow-auto table-fixed">
                        <thead class="border-b border-gray-100">
                            <tr class="even:bg-gray-50">
                                <th class="w-10"></th>
                                <th class="hidden w-40 px-5 py-3 text-sm font-semibold text-center md:table-cell">Release #</th>
                                <th class="px-5 py-3 text-sm font-semibold text-center w-60">Started</th>
                                <th class="hidden px-5 py-3 text-sm font-semibold text-center w-60 xl:table-cell">Duration</th>
                                <th class="px-5 py-3 text-sm font-semibold text-center">Commit</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="deployment in deployments.data" v-bind:key="deployment.id" class="cursor-pointer even:bg-gray-50 hover:bg-gray-100" @click="show(project.id, deployment.id)">
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
                                <td class="hidden px-5 py-3 text-center md:table-cell">{{ deployment.release }}</td>
                                <td class="px-5 py-3 text-center">
                                    <template v-if="deployment.started_at">
                                        {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
                                </td>
                                <td class="hidden px-5 py-3 text-center xl:table-cell">
                                    <template v-if="deployment.duration">
                                        {{ deployment.duration }}
                                    </template>
                                    <template v-else>
                                        N/A
                                    </template>
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

                    <div class="w-full px-5 py-4 text-sm bg-gray-50">
                        <div class="flex items-center justify-between">
                            <inertia-link
                                v-if="deployments.prev_page_url"
                                preserve-scroll
                                :href="deployments.prev_page_url"
                                class="inline-block px-4 py-2 text-sm text-gray-500 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"
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
                                class="inline-block px-4 py-2 text-sm text-gray-500 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"
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
