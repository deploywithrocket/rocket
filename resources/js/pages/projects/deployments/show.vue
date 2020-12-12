<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.deployments.index', project)" class="text-gray-500 hover:underline">Deployments</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Deployment #{{ deployment.release }}
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Deployment #{{ deployment.release }}</h2>
                </div>

                <table class="w-full table-fixed">
                    <tbody>
                        <tr class="even:bg-gray-50">
                            <td class="w-1/4 px-5 py-3 text-sm font-bold">Requested at</td>
                            <td class="px-5 py-3">
                                {{ $moment(deployment.created_at).format('L') }} {{ $moment(deployment.created_at).format('LTS') }}
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Started at</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.started_at">
                                    {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Ended at</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.ended_at">
                                    {{ $moment(deployment.ended_at).format('L') }} {{ $moment(deployment.ended_at).format('LTS') }}
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Duration</td>
                            <td class="px-5 py-3">{{ deployment.duration }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Status</td>
                            <td class="px-5 py-3">{{ deployment.status }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Post-deployment ping</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.ping">
                                    {{ deployment.ping.status }} (code: {{ deployment.ping.status_code }}, in {{ deployment.ping.request_duration }}ms)
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Release</td>
                            <td class="px-5 py-3">{{ deployment.release }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Committer</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.commit.committer">
                                    <img :src="deployment.commit.committer.avatar_url" class="inline w-6 h-6 mr-1 rounded">
                                    <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">
                                        {{ deployment.commit.committer.login }}
                                    </a>
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Commit</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.commit.from_ref">
                                    {{ deployment.commit.from_ref.replace('heads/', '') }} @
                                </template>
                                <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ? deployment.commit.sha.substring(0, 7) : '-' }}</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="px-5 py-3 text-sm font-bold">Commit message</td>
                            <td class="px-5 py-3">
                                {{ deployment.commit.message }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Process output</h2>
                </div>

                <pre class="w-full h-screen px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900">{{ deployment.raw_output }}</pre>
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
            return {}
        },

        mounted(){
        },

        methods: {
        }
    }
</script>
