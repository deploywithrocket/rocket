<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">Projects</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <div class="flex justify-between mb-8">
                <inertia-link :href="$route('projects.create')" class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600">
                    <i class="fas fa-plus"></i> Add project
                </inertia-link>
            </div>

            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="p-2 text-sm text-left">Name</th>
                        <th class="p-2 text-sm text-left">Repository</th>
                        <th class="p-2 text-sm text-left">Branch</th>
                        <th class="p-2 text-sm text-left">Added at</th>
                        <th class="p-2 text-sm text-left">Last deployment</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="project in projects" v-bind:key="project.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(project.id)">
                        <td class="p-2 truncate">{{ project.name }}</td>
                        <td class="p-2 truncate">
                            <img :src="'https://github.com/' + project.repository.split('/')[0] + '.png'" class="inline w-6 h-6 mr-1 rounded-full">

                            {{ project.repository }}
                        </td>
                        <td class="p-2 font-mono truncate">{{ project.branch }}</td>
                        <td class="p-2 truncate">{{ $moment(project.created_at).format('L') }} {{ $moment(project.created_at).format('LT') }}</td>
                        <td class="p-2 truncate">
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
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            projects: Object,
        },

        methods: {
            show(project_id) {
                this.$inertia.visit(this.$route('projects.show', project_id))
            }
        }
    }
</script>
