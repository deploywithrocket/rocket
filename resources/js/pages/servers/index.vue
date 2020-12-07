<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">Servers</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <div class="flex justify-between mb-8">
                <inertia-link :href="$route('servers.create')" class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600">
                    Add server
                </inertia-link>
            </div>

            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="w-8 px-2 py-2 text-sm text-center"></th>
                        <th class="px-2 py-2 text-sm text-left">Name</th>
                        <th class="px-2 py-2 text-sm text-left">User@Host</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="server in servers.data" v-bind:key="server.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(server.id)">
                        <td class="px-2 py-2 text-center"></td>
                        <td class="px-2 py-2 truncate">{{ server.name }}</td>
                        <td class="px-2 py-2 truncate">{{ server.ssh_user }}@{{ server.ssh_host }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between">
                <inertia-link
                    v-if="servers.prev_page_url"
                    preserve-scroll
                    :href="servers.prev_page_url"
                    class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200"
                >
                    <i class="fas fa-arrow-left"></i>
                </inertia-link>
                <div v-else></div>

                <div class="text-sm text-gray-500">
                    Displaying page {{ servers.current_page }} / {{ servers.last_page }}
                </div>

                <inertia-link
                    preserve-scroll
                    v-if="servers.next_page_url"
                    :href="servers.next_page_url"
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
            servers: Object,
        },

        methods: {
            show(server_id) {
                this.$inertia.visit(this.$route('servers.show', server_id))
            }
        }
    }
</script>
