<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('servers.index')" class="text-gray-500 hover:underline">Servers</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <div class="text-gray-500">{{ server.name }}</div>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Connection
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    Connection status
                </div>
                <div class="w-full px-5 py-4">
                    <div class="flex items-center mx-auto mb-4 text-gray-500 lg:w-1/3">
                        <div class="relative flex items-center justify-center p-4 border rounded-full">
                            <i class="fas fa-server fa-2x"></i>

                            <div class="absolute bottom-0 right-0 text-center text-green-500" v-if="server.status == 'connected'">
                                <i class="fa fa-check-circle"></i>
                            </div>

                            <div class="absolute bottom-0 right-0 text-center text-red-500" v-if="server.status == 'disconnected'">
                                <i class="fa fa-question-circle"></i>
                            </div>
                        </div>
                        <div class="flex-grow h-1 border-b"></div>
                        <div class="relative flex items-center justify-center p-2">
                            <i class="text-yellow-500 fas fa-lock" v-if="server.status == 'connected'"></i>
                            <i class="text-red-500 fas fa-times" v-if="server.status == 'disconnected'"></i>
                        </div>
                        <div class="flex-grow h-1 border-b"></div>
                        <div class="relative flex items-center justify-center p-4 border rounded-full">
                            <i class="fas fa-rocket fa-2x"></i>
                            <div class="absolute bottom-0 right-0 text-center text-green-500">
                                <i class="fa fa-check-circle"></i>
                            </div>
                        </div>
                    </div>
                    <div class="mb-8 text-center">Current status: <span class="font-bold">{{ server.status }}</span></div>

                    <p class="mb-4">This key must be added to the server's <code class="font-mono">.ssh/authorized_keys</code> file for each user you want to run tasks as.</p>

                    <div class="p-4 mb-4 font-mono break-all bg-gray-100 rounded-lg">{{ public_key }}</div>

                    <p class="mb-4">Alternatively, you can also directly run this command on your server to allow Rocket to connect to the current user.</p>

                    <div class="p-4 mb-4 font-mono break-all bg-gray-100 rounded-lg">
                        curl -sSL {{ $route('api.servers.connect', server) }} | sh
                    </div>

                    <p class="mb-4">
                        Note: If you are restricting SSH access to your server using IP whitelisting, you must whitelist the following IP address{{ ip_addresses.length > 1 ? 'es' : '' }}: <span class="font-mono">{{ ip_addresses.join(', ') }}</span>
                    </p>
                </div>
                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex justify-end">
                        <button type="button" class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300" @click="$inertia.post($route('servers.connection.test', server))"><i class="mr-1 opacity-75 fas fa-link"></i> Test connection status</button>
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
            server: Object,
            public_key: String,
            ip_addresses: Array,
        },
    }
</script>
