<template>
    <div class="flex flex-col items-start min-h-screen bg-gray-100">
        <nav class="w-full mb-8 bg-white border-b border-gray-200">
            <div class="container max-w-screen-xl">
                <div class="flex flex-wrap items-center">
                    <inertia-link :href="$route('index')">
                        <div class="flex flex-row items-center h-16">
                            <img src="/images/logo.svg" alt="Rocket" class="w-8 h-8">
                            <div class="ml-2 text-xl font-bold tracking-tighter text-pink-500 font-brand">
                                Rocket
                            </div>
                        </div>
                    </inertia-link>

                    <button class="ml-auto text-gray-600 md:hidden focus:outline-none" @click="toggle">
                        <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                            <path v-show="!open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                            <path v-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                        </svg>
                    </button>

                    <div :class="open ? 'flex': 'hidden'" class="flex-col flex-grow w-full py-2 -mx-1 md:flex md:py-0 md:flex-row md:items-center md:w-auto md:pl-8">
                        <inertia-link :href="$route('servers.index')"
                            :class="{
                                'bg-gray-100': $route().current('servers*')
                            }"
                            class="inline-block px-4 py-2 mx-1 mb-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out rounded md:mb-0 hover:bg-gray-200"
                        >
                            <i class="mr-1 opacity-50 fas fa-server"></i> Servers
                        </inertia-link>

                        <inertia-link :href="$route('projects.index')"
                            :class="{
                                'bg-gray-100': $route().current('projects*')
                            }"
                            class="inline-block px-4 py-2 mx-1 mb-2 text-sm font-semibold text-gray-600 transition duration-200 ease-in-out rounded md:mb-0 hover:bg-gray-200"
                        >
                            <i class="mr-1 opacity-50 fas fa-folder"></i> Projects
                        </inertia-link>

                        <div class="hidden ml-auto md:block"></div>

                        <button type="button" class="inline-block px-4 py-2 mx-1 mb-2 text-sm font-semibold text-left text-gray-600 transition duration-200 ease-in-out rounded md:mb-0 hover:bg-gray-200" @click="$inertia.post($route('logout'))"><i class="mr-1 opacity-50 fas fa-sign-out-alt"></i> Sign out</button>
                    </div>
                </div>
            </div>
        </nav>

        <alerts></alerts>

        <div class="container max-w-screen-xl text-gray-700">
            <slot></slot>
        </div>

        <footer class="w-full px-4 py-8 mt-auto text-xs text-center text-gray-400">
            &copy; 2020-2021 - <a href="https://deploywithrocket.dev" class="hover:underline">Rocket</a> {{ $page.version }}
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
