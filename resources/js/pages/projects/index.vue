<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">Projects</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <div class="flex justify-between mb-8">
                <inertia-link :href="$route('projects.create')" class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600">
                    Add project
                </inertia-link>
            </div>

            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="w-8 px-2 py-2 text-sm text-center"></th>
                        <th class="px-2 py-2 text-sm text-left">Name</th>
                        <th class="px-2 py-2 text-sm text-left">Repository URL</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="project in projects.data" v-bind:key="project.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(project.id)">
                        <td class="px-2 py-2 text-center"></td>
                        <td class="px-2 py-2 truncate">{{ project.name }}</td>
                        <td class="px-2 py-2 truncate">{{ project.repository_url }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between">
                <inertia-link
                    v-if="projects.prev_page_url"
                    preserve-scroll
                    :href="projects.prev_page_url"
                    class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200"
                >
                    <i class="fas fa-arrow-left"></i>
                </inertia-link>
                <div v-else></div>

                <div class="text-sm text-gray-500">
                    Displaying page {{ projects.current_page }} / {{ projects.last_page }}
                </div>

                <inertia-link
                    preserve-scroll
                    v-if="projects.next_page_url"
                    :href="projects.next_page_url"
                    class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200"
                >
                    <i class="fas fa-arrow-right"></i>
                </inertia-link>
                <div v-else></div>
            </div>
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
                this.$inertia.visit(this.$route('next.project.show', project_id))
            }
        }
    }
</script>
