<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-md mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                System requirements and permissions
            </div>
            <form class="w-full" @submit.prevent="submit">
                <div class="px-5 py-4">
                    <div v-if="_.size(min_php_version) || _.size(extensions)" class="w-full mb-4">
                        <div class="mb-4">Make sure that your server has the required php version and all the extensions mentioned below.</div>
                        <table class="w-full mb-4 border border-gray-200 table-auto" v-if="_.size(min_php_version)">
                            <tbody>
                                <tr class="even:bg-gray-50" v-for="(version) in min_php_version" v-bind:key="version">
                                    <td class="px-5 py-3 text-sm font-bold"><i class="mr-1 text-xs text-red-500 fa fa-times-circle"></i> PHP</td>
                                    <td class="px-5 py-3 font-mono text-right">{{ version }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <table class="w-full border border-gray-200 table-auto" v-if="_.size(extensions)">
                            <tbody>
                                <tr class="even:bg-gray-50" v-for="(extension) in extensions" v-bind:key="extension">
                                    <td class="px-5 py-3 text-sm font-bold"><i class="mr-1 text-xs text-red-500 fa fa-times-circle"></i> {{ extension }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div v-if="_.size(permissions)" class="w-full mb-8">
                        <div class="mb-4">Below is the list of folder permissions which are required in order for the app to work. If the permission check fails, make sure to update your folder permissions.</div>
                        <table class="w-full border border-gray-200 table-auto">
                            <tbody>
                                <tr class="even:bg-gray-50" v-for="(permission, folder) in permissions" v-bind:key="folder">
                                    <td class="px-5 py-3 text-sm font-bold"><i class="mr-1 text-xs text-red-500 fa fa-times-circle"></i> {{ folder }}</td>
                                    <td class="px-5 py-3 font-mono text-right">{{ permission }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex justify-end">
                        <button v-if="_.size(min_php_version) + _.size(permissions) + _.size(extensions) != 0" class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-200 rounded hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-1 opacity-50 fas fa-redo"></i> Retry</button>
                        <button v-if="_.size(min_php_version) + _.size(permissions) + _.size(extensions) == 0" class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="mr-1 fas fa-angle-right"></i> Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../../layouts/gate').default,

        props: ['min_php_version', 'extensions', 'permissions'],

        methods: {
            submit() {
                this.$inertia.reload()
            }
        }
    }
</script>