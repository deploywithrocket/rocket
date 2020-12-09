<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">Servers</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <div class="flex justify-between mb-8">
                <inertia-link :href="$route('servers.create')" class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600">
                    <i class="fas fa-plus"></i> Add server
                </inertia-link>
            </div>

            <div class="grid grid-cols-1 gap-4 mb-8 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                <div v-for="server in servers.data" v-bind:key="server.id" class="p-4 border rounded">
                    <div class="mb-2 font-bold">
                        <span :class="{
                            'text-red-500': server.status == 'disconnected',
                            'text-green-500': server.status == 'connected',
                        }">&bullet;</span>
                        {{ server.name }}
                    </div>
                    <div class="mb-4 font-mono text-sm">{{ server.ssh_user }}@{{ server.ssh_host }}</div>

                    <div class="flex flex-row space-x-2">
                        <inertia-link class="px-4 py-2 text-sm font-semibold bg-gray-100 rounded hover:bg-gray-200" :href="$route('servers.edit', server)"><i class="fas fa-fw fa-edit"></i></inertia-link>
                        <inertia-link class="px-4 py-2 text-sm font-semibold bg-gray-100 rounded hover:bg-gray-200" :href="$route('servers.connection', server)"><i class="fas fa-fw fa-link"></i></inertia-link>
                        <button @click="destroy(server)" class="px-4 py-2 text-sm font-semibold bg-gray-100 rounded hover:bg-gray-200"><i class="fas fa-fw fa-times"></i></button>
                    </div>
                </div>
            </div>

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
            destroy(server) {
                if (confirm("Do you really want to remove this server?")) {
                    this.$inertia.delete(this.$route('servers.destroy', server))
                }
            }
        }
    }
</script>
