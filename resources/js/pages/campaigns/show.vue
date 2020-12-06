<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">
            Campaigns
            <i class="text-sm text-gray-500 fas fa-chevron-right"></i>
            {{ campaign.id }}
        </h1>

        <div class="flex">
            <div class="w-1/3 mr-4">
                <div class="flex flex-col">
                    <div v-for="(step, k) in steps" v-bind:key="k">
                        <div v-if="k != 0" class="h-4 ml-4 border-l"></div>
                        <div class="flex flex-row items-center">
                            <div class="flex items-center justify-center w-10 h-8 mr-4 text-sm font-bold bg-gray-200 rounded" :class="{
                                'text-gray-300': currentStep < k,
                                'text-gray-600 bg-gray-300': currentStep > k,
                                'bg-orange-500 text-white': currentStep == k
                            }">{{ k+1 }}</div>
                            <div class="w-full text-gray-300" :class="{ 'text-gray-600': currentStep >= k, 'font-semibold': currentStep == k }">
                                <p class="text-sm">{{ step }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="w-full">
                <div class="w-full p-8 mb-8 bg-white rounded-lg shadow">
                    <table class="w-full mb-8 table-fixed" v-if="[0, 2, 4].includes(currentStep)">
                        <tbody>
                            <tr class="border-b">
                                <td class="px-2 py-2">#</td>
                                <td class="px-2 py-2">{{ campaign.id }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Sender Name</td>
                                <td class="px-2 py-2">{{ campaign.sender_name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Message</td>
                                <td class="px-2 py-2">{{ campaign.message }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Created at</td>
                                <td class="px-2 py-2">{{ $moment(campaign.created_at).format('L') }} {{ $moment(campaign.created_at).format('LT') }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Directory</td>
                                <td class="px-2 py-2">{{ campaign.directory.name }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Status</td>
                                <td class="px-2 py-2">{{ campaign.status }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Sent</td>
                                <td class="px-2 py-2">{{ campaign.sent }}</td>
                            </tr>
                            <tr class="border-b">
                                <td class="px-2 py-2">Total</td>
                                <td class="px-2 py-2">{{ campaign.total }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <div v-if="[1, 3].includes(currentStep)" class="text-center">
                        <div class="mb-3"><i class="text-gray-500 fal fa-circle-notch fa-spin fa-2x"></i></div>

                        <div class="mb-1 text-xl font-semibold">
                            Please wait
                        </div>
                        <p class="mb-8">
                            You can stay here or safely leave this page.<br>
                            Campaign status will be updated on each refresh.
                        </p>

                        <template v-if="currentStep == 1">
                            <div class="text-xs">
                                {{ campaign.total }} / {{ campaign.directory_total }} ({{ campaign.directory_percent }}%)
                            </div>
                            <div class="w-full max-w-xs mx-auto mt-2 bg-gray-100 rounded">
                                <div class="h-2 bg-orange-500 rounded" :style="{width: campaign.directory_percent + '%'}"></div>
                            </div>
                        </template>

                        <template v-if="currentStep == 3">
                            <div class="text-xs">
                                {{ campaign.sent }} / {{ campaign.total }} ({{ campaign.percent }}%)
                            </div>
                            <div class="w-full max-w-xs mx-auto mt-2 bg-gray-100 rounded" >
                                <div class="h-2 bg-orange-500 rounded" :style="{width: campaign.percent + '%'}"></div>
                            </div>
                        </template>
                    </div>

                    <div class="flex flex-row justify-between">
                        <template v-if="currentStep == 0">
                            <inertia-link :href="$route('next.campaigns.edit', campaign)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200">Edit</inertia-link>
                            <inertia-link :href="$route('next.campaigns.prepare', campaign)" class="inline-block px-4 py-2 text-sm font-bold text-white bg-orange-500 rounded hover:bg-orange-600">Prepare campaign</inertia-link>
                        </template>

                        <template v-if="campaign.status == 'ready'"> <!-- Ready -->
                            <inertia-link :href="$route('next.campaigns.edit', campaign)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200">Edit</inertia-link>
                            <inertia-link :href="$route('next.campaigns.send', campaign)" class="inline-block px-4 py-2 text-sm font-bold text-white bg-orange-500 rounded hover:bg-orange-600">Send campaign</inertia-link>
                        </template>

                        <template v-if="campaign.status == 'finished'"> <!-- Finished -->
                            <div></div>
                            <inertia-link :href="$route('next.campaigns.send', campaign)" class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200">Retry failed sendings</inertia-link>
                        </template>
                    </div>
                </div>

                <button v-if="[0, 2, 4].includes(currentStep)" @click="destroy" class="inline-block text-sm text-red-500 hover:text-red-600">Delete this campaign</button>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/app').default,

        props: {
            campaign: Object,
        },

        data() {
            return {
                currentStep: 0,
                steps: [
                    'Create your campaign',
                    'Preparing contacts',
                    'The campaign is ready to be sent',
                    'Sending texts',
                    'Finished'
                ],
            }
        },

        mounted(){
            if (this.campaign.status == 'draft') this.currentStep = 0;
            if (this.campaign.status == 'preparing') this.currentStep = 1;
            if (this.campaign.status == 'ready') this.currentStep = 2;
            if (this.campaign.status == 'in-progress') this.currentStep = 3;
            if (this.campaign.status == 'finished') this.currentStep = 4;

            if ([1, 3].includes(this.currentStep)) {
                setTimeout(() => {
                    this.$inertia.visit(window.location.href);
                }, 5000);
            }
        },

        methods: {
            destroy() {
                if (confirm("Do you really want to delete this campaign?")) {
                    this.$inertia.delete(this.$route('next.campaigns.destroy', this.campaign.id))
                }
            }
        }
    }
</script>
