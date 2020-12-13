<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            <inertia-link :href="$route('projects.index')" class="text-gray-500 hover:underline">Projects</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.show', project)" class="text-gray-500 hover:underline">{{ project.name }}</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            <inertia-link :href="$route('projects.deployments.index', project)" class="text-gray-500 hover:underline">Deployments</inertia-link>
            <div class="px-3 text-xs text-gray-400"><i class="fas fa-chevron-right"></i></div>
            Deployment #{{ deployment.release }}
        </nav>

        <div class="w-full">
            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Deployment #{{ deployment.release }}</h2>
                </div>

                <table class="w-full table-fixed">
                    <tbody>
                        <tr class="even:bg-gray-50">
                            <td class="w-1/4 px-5 py-3 text-sm font-bold">Requested at</td>
                            <td class="px-5 py-3">
                                {{ $moment(deployment.created_at).format('L') }} {{ $moment(deployment.created_at).format('LTS') }}
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Started at</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.started_at">
                                    {{ $moment(deployment.started_at).format('L') }} {{ $moment(deployment.started_at).format('LTS') }}
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Ended at</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.ended_at">
                                    {{ $moment(deployment.ended_at).format('L') }} {{ $moment(deployment.ended_at).format('LTS') }}
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Duration</td>
                            <td class="px-5 py-3">{{ deployment.duration }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Status</td>
                            <td class="px-5 py-3">{{ deployment.status }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Post-deployment ping</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.ping">
                                    {{ deployment.ping.status }} (code: {{ deployment.ping.status_code }}, in {{ deployment.ping.request_duration }}ms)
                                </template>
                                <template v-else>
                                    N/A
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Release</td>
                            <td class="px-5 py-3">{{ deployment.release }}</td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Committer</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.commit.committer">
                                    <img :src="deployment.commit.committer.avatar_url" class="inline w-6 h-6 mr-1 rounded ">
                                    <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">
                                        {{ deployment.commit.committer.login }}
                                    </a>
                                </template>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Commit</td>
                            <td class="px-5 py-3">
                                <template v-if="deployment.commit.from_ref">
                                    {{ deployment.commit.from_ref.replace('heads/', '') }} @
                                </template>
                                <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ? deployment.commit.sha.substring(0, 7) : '-' }}</a>
                            </td>
                        </tr>
                        <tr class="even:bg-gray-50">
                            <td class="px-5 py-3 text-sm font-bold">Commit message</td>
                            <td class="px-5 py-3">
                                {{ deployment.commit.message }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex flex-col items-start w-full mb-8 overflow-hidden bg-white rounded shadow-sm">
                <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                    <h2>Process output</h2>
                </div>

                <!-- After 0.7 -->
                <template v-if="!deployment.raw_output.data">
                    <div class="w-full px-5 py-4 font-semibold bg-gray-50 hover:bg-gray-100" @click="toggle('start')">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-spin fa-spinner" v-if="deployment.status == 'in_progress' && !deployment.raw_output.provision"></i>
                            <i class="mr-2 text-red-500 fas fa-times" v-else-if="deployment.status == 'error' && !deployment.raw_output.provision"></i>
                            <i class="mr-2 text-green-500 fas fa-check" v-else-if="deployment.raw_output.provision"></i>

                            <h3>🏃 start</h3>
                        </div>
                    </div>
                    <pre
                        v-if="visible.start"
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.start }}</pre>

                    <div class="w-full px-5 py-4 font-semibold bg-gray-50 hover:bg-gray-100" @click="toggle('provision')">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-spin fa-spinner" v-if="deployment.status == 'in_progress' && !deployment.raw_output.build"></i>
                            <i class="mr-2 text-red-500 fas fa-times" v-else-if="deployment.status == 'error' && !deployment.raw_output.build"></i>
                            <i class="mr-2 text-green-500 fas fa-check" v-else-if="deployment.raw_output.build"></i>

                            <h3>🚚 provision</h3>
                        </div>
                    </div>
                    <pre
                        v-if="visible.provision"
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.provision }}</pre>

                    <div class="w-full px-5 py-4 font-semibold bg-gray-50 hover:bg-gray-100" @click="toggle('build')">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-spin fa-spinner" v-if="deployment.status == 'in_progress' && !deployment.raw_output.publish"></i>
                            <i class="mr-2 text-red-500 fas fa-times" v-else-if="deployment.status == 'error' && !deployment.raw_output.publish"></i>
                            <i class="mr-2 text-green-500 fas fa-check" v-else-if="deployment.raw_output.publish"></i>

                            <h3>📦 build</h3>
                        </div>
                    </div>
                    <pre
                        v-if="visible.build"
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.build }}</pre>

                    <div class="w-full px-5 py-4 font-semibold bg-gray-50 hover:bg-gray-100" @click="toggle('publish')">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-spin fa-spinner" v-if="deployment.status == 'in_progress' && !deployment.raw_output.finish"></i>
                            <i class="mr-2 text-red-500 fas fa-times" v-else-if="deployment.status == 'error' && !deployment.raw_output.finish"></i>
                            <i class="mr-2 text-green-500 fas fa-check" v-else-if="deployment.raw_output.finish"></i>

                            <h3>🚀 publish</h3>
                        </div>
                    </div>
                    <pre
                        v-if="visible.publish"
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.publish }}</pre>

                    <div class="w-full px-5 py-4 font-semibold bg-gray-50 hover:bg-gray-100" @click="toggle('finish')">
                        <div class="flex items-center">
                            <i class="mr-2 fas fa-spin fa-spinner" v-if="deployment.status == 'in_progress'"></i>
                            <i class="mr-2 text-red-500 fas fa-times" v-if="deployment.status == 'error'"></i>
                            <i class="mr-2 text-green-500 fas fa-check" v-if="deployment.status == 'success'"></i>

                            <h3>🗑️ finish</h3>
                        </div>
                    </div>
                    <pre
                        v-if="visible.finish"
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.finish }}</pre>
                </template>

                <!-- Before 0.7 -->
                <div v-if="deployment.raw_output.data">
                    <pre
                        class="w-full px-5 py-4 overflow-auto font-mono text-sm text-gray-200 bg-gray-900"
                    >{{ deployment.raw_output.data }}</pre>
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
            return {
                visible: {
                    start: false,
                    provision: false,
                    build: false,
                    publish: false,
                    finish: false,
                }
            }
        },

        mounted(){
            if (this.deployment.raw_output.start && !this.deployment.raw_output.provision) this.visible.start = true;
            if (this.deployment.raw_output.provision && !this.deployment.raw_output.build) this.visible.provision = true;
            if (this.deployment.raw_output.build && !this.deployment.raw_output.publish) this.visible.build = true;
            if (this.deployment.raw_output.publish && !this.deployment.raw_output.finish) this.visible.publish = true;
            if (this.deployment.raw_output.finish && !this.deployment.status == 'success') this.visible.finish = true;
        },

        methods: {
            toggle(name){
                this.visible[name] = !this.visible[name];
            }
        }
    }
</script>
