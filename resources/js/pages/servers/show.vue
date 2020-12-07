<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            Server
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ server.id }}
        </h1>

        <div class="flex">
            <div class="w-full">
                <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
                    <table class="w-full mb-8 table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-2">#</td>
                                <td class="px-2 py-2">{{ server.id }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Name</td>
                                <td class="px-2 py-2">{{ server.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">User</td>
                                <td class="px-2 py-2">{{ server.ssh_user }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Host</td>
                                <td class="px-2 py-2">{{ server.ssh_host }}</td>
                            </tr>

                            <tr class="border-b" v-if="server.ssh_options">
                                <td class="px-2 py-2">ssh_options</td>
                                <td class="px-2 py-2">{{ server.ssh_options }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_git">
                                <td class="px-2 py-2">cmd_git</td>
                                <td class="px-2 py-2">{{ server.cmd_git }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_npm">
                                <td class="px-2 py-2">cmd_npm</td>
                                <td class="px-2 py-2">{{ server.cmd_npm }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_yarn">
                                <td class="px-2 py-2">cmd_yarn</td>
                                <td class="px-2 py-2">{{ server.cmd_yarn }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_bower">
                                <td class="px-2 py-2">cmd_bower</td>
                                <td class="px-2 py-2">{{ server.cmd_bower }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_grunt">
                                <td class="px-2 py-2">cmd_grunt</td>
                                <td class="px-2 py-2">{{ server.cmd_grunt }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_php">
                                <td class="px-2 py-2">cmd_php</td>
                                <td class="px-2 py-2">{{ server.cmd_php }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_composer">
                                <td class="px-2 py-2">cmd_composer</td>
                                <td class="px-2 py-2">{{ server.cmd_composer }}</td>
                            </tr>
                            <tr class="border-b" v-if="server.cmd_composer_options">
                                <td class="px-2 py-2">cmd_composer_options</td>
                                <td class="px-2 py-2">{{ server.cmd_composer_options }}</td>
                            </tr>

                            <tr class="border-b">
                                <td class="px-2 py-2">Created at</td>
                                <td class="px-2 py-2">{{ $moment(server.created_at).format('L') }} {{ $moment(server.created_at).format('LT') }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div class="flex flex-row justify-between">
                        <inertia-link :href="$route('servers.edit', server)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200">Edit</inertia-link>
                    </div>
                </div>

                <button @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600">Remove this server</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            server: Object,
        },

        data() {
            return {}
        },

        mounted(){},

        methods: {
            destroy() {
                if (confirm("Do you really want to delete this server?")) {
                    this.$inertia.delete(this.$route('servers.destroy', this.server))
                }
            }
        }
    }
</script>
