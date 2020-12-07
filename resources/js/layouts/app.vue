<template>
    <div class="flex flex-col items-start min-h-screen bg-gray-100">
        <nav class="w-full mb-6 text-white bg-gray-900 shadow md:bg-gray-800">
            <div class="flex flex-wrap items-center">
                <inertia-link href="/home" class="h-16 bg-gray-900">
                    <img src="/images/logo_alt.svg" alt="Rocket" class="h-full px-4 py-4">
                </inertia-link>

                <button class="pr-4 ml-auto md:hidden focus:outline-none" @click="toggle">
                    <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                        <path v-show="!open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path v-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div :class="open ? 'flex': 'hidden'" class="flex-col flex-grow w-full py-2 bg-gray-800 md:flex md:py-0 md:flex-row md:items-center md:w-auto md:pl-4">
                    <inertia-link :href="$route('servers.index')" class="block px-4 my-2 text-sm text-left text-gray-200 md:px-0 md:pr-4 hover:text-white focus:outline-none">Servers</inertia-link>
                    <inertia-link :href="$route('projects.index')" class="block px-4 my-2 text-sm text-left text-gray-200 md:px-0 md:pr-4 hover:text-white focus:outline-none">Projects</inertia-link>

                    <div class="hidden ml-auto md:block"></div>

                    <button type="button" class="px-4 my-2 text-sm text-left text-gray-200 md:px-0 md:pr-4 hover:text-white focus:outline-none" @click="$inertia.post($route('logout'))">Sign out</button>
                </div>
            </div>
        </nav>

        <div class="container pb-8 text-gray-700">
            <alerts></alerts>
            <slot></slot>
        </div>

        <footer class="w-full px-4 py-2 mt-auto text-xs text-center text-gray-400">
            &copy; 2020-2021 - Rocket {{ $page.version }}
        </footer>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                open: false,
            }
        },

        methods: {
            toggle() {
                this.open = !this.open
            },
        }
    }
</script>
