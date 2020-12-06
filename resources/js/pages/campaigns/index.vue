<template>
    <div>
        <h1 class="my-8 text-2xl font-bold">Campaigns</h1>

        <div class="p-8 bg-white rounded-lg shadow">
            <div class="flex justify-between mb-8">
                <inertia-link :href="$route('next.campaigns.create')" class="px-4 py-2 text-sm font-semibold text-white bg-pink-500 rounded hover:bg-pink-600">
                    Create campaign
                </inertia-link>
                <ul class='flex text-sm font-semibold cursor-pointer'>
                    <inertia-link :href="$route('next.campaigns.index')" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (!filter)) }" class='px-3 py-2 bg-gray-100 border-r rounded-l hover:bg-gray-200'>All</inertia-link>
                    <inertia-link :href="$route('next.campaigns.index', { filter: 'draft' })" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (filter == 'draft')) }" class='px-3 py-2 bg-gray-100 border-r hover:bg-gray-200'><span class="text-gray-500"><i class="mr-1 fal fa-pencil"></i></span> Drafts</inertia-link>
                    <inertia-link :href="$route('next.campaigns.index', { filter: 'ready' })" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (filter == 'ready')) }" class='px-3 py-2 bg-gray-100 border-r hover:bg-gray-200'><span class="text-blue-500"><i class="mr-1 fal fa-paper-plane"></i></span> Ready</inertia-link>
                    <!-- <inertia-link href="#" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (filter == 'ready')) }" class='px-3 py-2 bg-gray-100 border-r hover:bg-gray-200'><span class="text-indigo-500"><i class="mr-1 fal fa-alarm-clock"></i></span> Programmed</inertia-link> -->
                    <inertia-link :href="$route('next.campaigns.index', { filter: 'in-progress' })" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (filter == 'in-progress')) }" class='px-3 py-2 bg-gray-100 border-r hover:bg-gray-200'><span class="text-pink-500"><i class="mr-1 fal fa-hourglass-end"></i></span> In progress</inertia-link>
                    <inertia-link :href="$route('next.campaigns.index', { filter: 'finished' })" :class="{ 'text-pink-500': ($route().current('next.campaigns.index') && (filter == 'finished')) }" class='px-3 py-2 bg-gray-100 rounded-r hover:bg-gray-200'><span class="text-green-500"><i class="mr-1 fal fa-check"></i></span> Sent</inertia-link>
                </ul>
            </div>

            <table class="w-full mb-8 table-fixed">
                <thead>
                    <tr class="border-b">
                        <th class="w-8 px-2 py-2 text-sm text-center"></th>
                        <th class="px-2 py-2 text-sm text-left">Sender Name</th>
                        <th class="w-64 px-2 py-2 text-sm text-left">Message</th>
                        <th class="px-2 py-2 text-sm text-left">Created at</th>
                        <th class="px-2 py-2 text-sm text-left">Directory</th>
                        <th class="px-2 py-2 text-sm text-right">Sent / Total</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="campaign in campaigns.data" v-bind:key="campaign.id" class="border-b cursor-pointer hover:bg-gray-100" @click="show(campaign.id)">
                        <td class="px-2 py-2 text-center">
                            <span v-if="campaign.status == 'draft'" class="text-gray-500"><i class="fal fa-pencil"></i></span> <!-- Draft -->
                            <span v-if="campaign.status == 'ready'" class="text-blue-500"><i class="fal fa-paper-plane"></i></span> <!-- Ready -->
                            <span v-if="campaign.status == 'in-progress'" class="text-pink-500"><i class="fal fa-hourglass-end"></i></span> <!-- In progress -->
                            <span v-if="campaign.status == 'finished'" class="text-green-500"><i class="fal fa-check"></i></span> <!-- Sent -->
                            <span v-if="campaign.status == 'programmed'" class="text-indigo-500"><i class="fal fa-alarm-clock"></i></span> <!-- Programmed -->
                        </td>
                        <td class="px-2 py-2">{{ campaign.sender_name }}</td>
                        <td class="px-2 py-2 truncate">{{ campaign.message }}</td>
                        <td class="px-2 py-2">{{ $moment(campaign.created_at).format('L') }} {{ $moment(campaign.created_at).format('LT') }}</td>
                        <td class="px-2 py-2">{{ campaign.directory.name }}</td>
                        <td class="relative px-2 py-2 text-sm text-right">
                            {{ campaign.sent }} / {{ campaign.total }} ({{ campaign.percent }}%)

                            <div class="absolute inset-0 top-auto flex bg-gray-200">
                                <div :style="{ width: campaign.percent + '%' }" class="h-1 bg-green-500"></div>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="flex justify-between">
                <inertia-link
                    v-if="campaigns.prev_page_url"
                    preserve-scroll
                    :href="campaigns.prev_page_url"
                    class="inline-block px-4 py-2 text-sm font-bold bg-gray-100 rounded hover:bg-gray-200"
                >
                    <i class="fas fa-arrow-left"></i>
                </inertia-link>
                <div v-else></div>

                <div class="text-sm text-gray-500">
                    Displaying page {{ campaigns.current_page }} / {{ campaigns.last_page }}
                </div>

                <inertia-link
                    preserve-scroll
                    v-if="campaigns.next_page_url"
                    :href="campaigns.next_page_url"
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
            campaigns: Object,
            filter: String,
        },

        methods: {
            show(campaign_id) {
                this.$inertia.visit(this.$route('next.campaigns.show', campaign_id))
            }
        }
    }
</script>
