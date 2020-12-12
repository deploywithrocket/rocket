<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            Servers
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <div class="flex items-center">
                        <h2>All servers</h2>
                        <div class="mx-auto"></div>
                        <inertia-link :href="$route('servers.create')" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500">
                            <i class="mr-1 opacity-50 fas fa-plus"></i> Add server
                        </inertia-link>
                    </div>
                </div>
                <div class="w-full px-5 py-4">
                    <div class="grid grid-cols-1 gap-4 mb-8 lg:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4">
                        <div v-for="server in servers.data" v-bind:key="server.id" class="p-4 border border-gray-100 rounded ">
                            <div class="mb-2 font-bold">
                                <span :class="{
                                    'text-red-500': server.status == 'disconnected',
                                    'text-green-500': server.status == 'connected',
                                }">&bullet;</span>
                                {{ server.name }}
                            </div>
                            <div class="mb-4 font-mono text-sm">{{ server.ssh_user }}@{{ server.ssh_host }}</div>

                            <div class="flex flex-row">
                                <inertia-link class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500" :href="$route('servers.edit', server)"><i class="mr-1 opacity-50 fas fa-edit"></i> Edit</inertia-link>
                                <inertia-link class="inline-block px-4 py-2 ml-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500" :href="$route('servers.connection', server)"><i class="opacity-50 fas fa-link"></i></inertia-link>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex items-center justify-between">
                        <inertia-link
                            v-if="servers.prev_page_url"
                            preserve-scroll
                            :href="servers.prev_page_url"
                            class="inline-block px-4 py-2 text-sm text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"
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
                            class="inline-block px-4 py-2 text-sm text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"
                        >
                            <i class="fas fa-arrow-right"></i>
                        </inertia-link>
                        <div v-else></div>
                    </div>
                </div>
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
    }
</script>
