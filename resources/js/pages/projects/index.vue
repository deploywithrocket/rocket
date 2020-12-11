<template>
    <div>
        <nav class="flex items-center my-8 font-semibold">
            Projects
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                 <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <div class="flex items-center">
                        <h2>All projects</h2>
                        <div class="mx-auto"></div>
                        <inertia-link :href="$route('projects.create')" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-150 ease-in-out bg-pink-500 rounded-lg hover:bg-pink-600">
                            <i class="mr-1 fas fa-plus"></i> Add project
                        </inertia-link>
                    </div>
                </div>
                <table class="w-full table-fixed">
                    <thead>
                        <tr class="border-b border-gray-100">
                            <th class="px-5 py-3 text-sm font-semibold text-center">Name</th>
                            <th class="px-5 py-3 text-sm font-semibold text-center">Repository</th>
                            <th class="px-5 py-3 text-sm font-semibold text-center">Branch</th>
                            <th class="px-5 py-3 text-sm font-semibold text-center">Added at</th>
                            <th class="px-5 py-3 text-sm font-semibold text-center">Last deployment</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="project in projects" v-bind:key="project.id" class="cursor-pointer even:bg-gray-50 hover:bg-gray-100" @click="show(project.id)">
                            <td class="px-5 py-3 font-semibold">
                                <div class="flex flex-row items-center w-full">
                                    <img :src="project.favicon_url" class="inline w-5 h-5 mr-2" v-if="project.live_url">
                                    <div class="truncate">{{ project.name }}</div>
                                </div>
                            </td>
                            <td class="px-5 py-3 truncate">
                                <img :src="'https://github.com/' + project.repository.split('/')[0] + '.png'" class="inline w-6 h-6 mr-1 rounded">

                                {{ project.repository }}
                            </td>
                            <td class="px-5 py-3 font-mono text-center truncate">{{ project.branch }}</td>
                            <td class="px-5 py-3 text-center truncate">{{ $moment(project.created_at).format('L') }} {{ $moment(project.created_at).format('LT') }}</td>
                            <td class="px-5 py-3 text-center truncate">
                                <template v-if="project.latest_deployment">
                                    {{ $moment(project.latest_deployment.created_at).format('L') }} {{ $moment(project.latest_deployment.created_at).format('LTS') }}
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            projects: Array,
        },

        methods: {
            show(project_id) {
                this.$inertia.visit(this.$route('projects.show', project_id))
            }
        }
    }
</script>
