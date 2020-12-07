<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            Project
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ project.id }}
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ deployment.id }}
        </h1>

        <div class="flex">
            <div class="w-full">
                <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
                    <table class="w-full mb-8 table-fixed">
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-2">#</td>
                                <td class="px-2 py-2">{{ deployment.id }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Status</td>
                                <td class="px-2 py-2">{{ deployment.status }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Type</td>
                                <td class="px-2 my-2">{{ deployment.type }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Release</td>
                                <td class="px-2 my-2">{{ deployment.release }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Commit</td>
                                <td class="px-2 my-2">{{ deployment.commit }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <h1 class="my-8 text-2xl font-bold">
            Raw output
        </h1>

        <div class="flex">
            <div class="w-full">
                <div class="w-full p-8 mb-8 font-mono bg-white rounded-lg shadow">
                    <pre class="p-4 overflow-x-auto bg-gray-100 rounded-lg">{{ deployment.raw_output }}</pre>
                </div>
            </div>
        </div>

    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/app').default,

        props: {
            project: Object,
            deployment: Object,
        },

        data() {
            return {}
        },

        mounted(){
        },

        methods: {
        }
    }
</script>
