<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            <inertia-link :href="$route('projects.show', project)" class="hover:underline">{{ project.name }}</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ deployment.release }}
        </h1>

        <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
            <table class="w-full table-fixed">
                <tbody>
                    <tr class="border-b">
                        <td class="w-1/4 px-2 py-2">Status</td>
                        <td class="px-2 py-2">{{ deployment.status }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 py-2">Release</td>
                        <td class="px-2 my-2">{{ deployment.release }}</td>
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 py-2">Committer</td>
                        <td class="px-2 my-2">
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
                    </tr>
                    <tr class="border-b">
                        <td class="px-2 py-2">Commit</td>
                        <td class="px-2 my-2">
                            <a :href="'https://github.com/' + deployment.commit.from_repository + '/tree/' + deployment.commit.from_branch" class="hover:underline" target="_blank">{{ deployment.commit.from_branch }}</a>@<a :href="'https://github.com/' + deployment.commit.from_repository + '/commit/' + deployment.commit.sha" class="hover:underline" target="_blank">{{ deployment.commit.sha.substring(0, 7) }}</a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <h1 class="my-8 text-2xl font-bold">
            Raw output
        </h1>

        <div class="w-full p-8 mb-8 font-mono bg-white rounded-lg shadow">
            <pre class="p-4 overflow-x-auto bg-gray-100 rounded-lg">{{ deployment.raw_output }}</pre>
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
