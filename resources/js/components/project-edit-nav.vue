<template>
    <nav class="flex flex-col w-full mb-8 md:w-1/4 md:mr-8">
        <inertia-link :href="$route('projects.edit.common', project)" :class="{ 'bg-gray-200': $route().current('projects.edit.common') }" class="inline-block px-4 py-2 mb-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-2 opacity-50 fa-fw fas fa-cog"></i> Common settings</inertia-link>
        <inertia-link :href="$route('projects.edit.env-file', project)" :class="{ 'bg-gray-200': $route().current('projects.edit.env-file') }" class="inline-block px-4 py-2 mb-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-2 opacity-50 fa-fw fas fa-key"></i> Environment file</inertia-link>
        <inertia-link :href="$route('projects.edit.shared', project)" :class="{ 'bg-gray-200': $route().current('projects.edit.shared') }" class="inline-block px-4 py-2 mb-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-2 opacity-50 fa-fw fas fa-folder"></i> Shared</inertia-link>
        <inertia-link :href="$route('projects.edit.hooks', project)" :class="{ 'bg-gray-200': $route().current('projects.edit.hooks') }" class="inline-block px-4 py-2 mb-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-2 opacity-50 fa-fw fas fa-code"></i> Hooks</inertia-link>
        <inertia-link :href="$route('projects.edit.cron-jobs', project)" :class="{ 'bg-gray-200': $route().current('projects.edit.cron-jobs') }" class="inline-block px-4 py-2 mb-1 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out bg-gray-100 rounded hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-gray-500"><i class="mr-2 opacity-50 fa-fw fas fa-clock"></i> Cron jobs</inertia-link>
    </nav>
</template>

<script>
    export default {
        props: {
            project: Object,
        },
    }
</script>
