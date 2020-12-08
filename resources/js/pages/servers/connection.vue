<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            <inertia-link :href="$route('servers.index')" class="hover:underline">Servers</inertia-link>
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ server.name }}
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            Connection
        </h1>

        <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
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

            <div class="mb-4">This key must be added to the server's <code class="font-mono">.ssh/authorized_keys</code> file for each user you want to run tasks as.</div>

            <div class="p-4 mb-4 font-mono break-all bg-gray-100 rounded-lg">{{ public_key }}</div>

            <div class="mb-4">Alternatively, you can also directly run this command on your server to allow Rocket to connect to the current user.</div>

            <div class="p-4 mb-4 font-mono break-all bg-gray-100 rounded-lg">
                curl -sSL {{ $route('api.servers.connect', server) }} | sh
            </div>

            <div class="flex flex-row justify-between">
                <button type="button" class="inline-block px-4 py-2 text-sm font-bold bg-gray-200 rounded hover:bg-gray-300" @click="$inertia.post($route('servers.connection.test', server))">Test connection status</button>
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
        },
    }
</script>
