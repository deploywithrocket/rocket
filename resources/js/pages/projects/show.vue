<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('projects.index')" class="hover:underline">Projects</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ project.name }}
        </h1>

        <div class="flex">
            <div class="w-full">
                <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
                    <table class="w-full mb-8 table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-2">#</td>
                                <td class="px-2 py-2">{{ project.id }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Name</td>
                                <td class="px-2 py-2">{{ project.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Repository URL</td>
                                <td class="px-2 py-2">{{ project.repository_url }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Deploy path</td>
                                <td class="px-2 py-2">{{ project.deploy_path }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Health URL</td>
                                <td class="px-2 py-2">{{ project.health_url }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Env</td>
                                <td class="px-2 py-2">{{ project.env }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Linked dirs</td>
                                <td class="px-2 py-2">{{ project.linked_dirs }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Copied dirs</td>
                                <td class="px-2 py-2">{{ project.copied_dirs }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Server</td>
                                <td class="px-2 py-2">{{ project.server.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Created at</td>
                                <td class="px-2 py-2">{{ $moment(project.created_at).format('L') }} {{ $moment(project.created_at).format('LT') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex flex-row justify-between">
                        <inertia-link :href="$route('projects.edit', project)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300">Edit</inertia-link>
                        <div class="mx-auto"></div>
                        <inertia-link :href="$route('projects.setup', project)" class="inline-block px-4 py-2 mr-1 text-sm font-bold text-white bg-pink-500 rounded hover:bg-pink-600">Setup</inertia-link>
                        <inertia-link :href="$route('projects.deploy', project)" class="inline-block px-4 py-2 text-sm font-bold text-white bg-pink-500 rounded hover:bg-pink-600">Deploy</inertia-link>
                    </div>
                </div>

                <button @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600">Delete this project</button>
            </div>
        </div>

        <h1 class="my-8 text-2xl font-bold">Deployments</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="w-8 px-2 py-2 text-sm text-center"></th>
                        <th class="w-24 px-2 py-2 text-sm text-left">Status</th>
                        <th class="w-24 px-2 py-2 text-sm text-left">Type</th>
                        <th class="w-48 px-2 py-2 text-sm text-left">Release</th>
                        <th class="px-2 py-2 text-sm text-left">Commit</th>
                        <th class="w-64 px-2 py-2 text-sm text-left">Created at</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="deployment in deployments" v-bind:key="deployment.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(project.id, deployment.id)">
                        <td class="px-2 py-2 text-center"></td>
                        <td class="px-2 py-2">{{ deployment.status }}</td>
                        <td class="px-2 py-2">{{ deployment.type }}</td>
                        <td class="px-2 py-2">{{ deployment.release }}</td>
                        <td class="px-2 py-2 truncate">{{ deployment.commit }}</td>
                        <td class="px-2 py-2">{{ $moment(deployment.created_at).format('L') }} {{ $moment(deployment.created_at).format('LTS') }}</td>
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
            project: Object,
            deployments: Array,
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
            destroy() {
                if (confirm("Do you really want to delete this project?")) {
                    this.$inertia.delete(this.$route('projects.destroy', this.project))
                }
            }
        }
    }
</script>
