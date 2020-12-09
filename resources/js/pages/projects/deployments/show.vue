<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Deployments
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ deployment.release }}
        </h1>

        <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
            <table class="w-full table-fixed">
                <tbody>
                    <tr class="border-b">
                        <td class="w-1/4 px-2 text-sm font-bold">Requested at</td>
                        <td class="p-3">
                            {{ $moment(deployment.created_at).format('L') }} {{ $moment(deployment.created_at).format('LTS') }}
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Started at</td>
                        <td class="p-3">
                            <template v-if="deployment.started_at">
                                {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                            </template>
                            <template v-else>
                                N/A
                            </template>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Ended at</td>
                        <td class="p-3">
                            <template v-if="deployment.ended_at">
                                {{ $moment(deployment.ended_at).format('L') }} {{ $moment(deployment.ended_at).format('LTS') }}
                            </template>
                            <template v-else>
                                N/A
                            </template>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Duration</td>
                        <td class="p-3">{{ deployment.duration }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Status</td>
                        <td class="p-3">{{ deployment.status }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Release</td>
                        <td class="p-3 font-mono">{{ deployment.release }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Committer</td>
                        <td class="p-3">
                            <template v-if="deployment.commit.committer">
                                <img :src="deployment.commit.committer.avatar_url" class="inline w-6 h-6 mr-1 rounded-full">
                                <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">
                                    {{ deployment.commit.committer.login }}
                                </a>
                            </template>
                        </td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 text-sm font-bold">Commit</td>
                        <td class="p-3">
                            <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ? deployment.commit.sha.substring(0, 7) : '-' }}</a>
                            <template v-if="deployment.commit.from_ref">
                                (<span class="font-mono">{{ deployment.commit.from_ref }}</span>)
                            </template>
                        </td>
                    </tr>
                    <tr>
                        <td class="px-2 text-sm font-bold">Commit message</td>
                        <td class="p-3">
                            {{ deployment.commit.message }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h1 class="my-8 text-2xl font-bold">
            Process output
        </h1>

        <pre class="w-full p-8 mb-8 overflow-x-auto font-mono text-sm text-white bg-gray-900 rounded-lg shadow">{{ deployment.raw_output }}</pre>

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
