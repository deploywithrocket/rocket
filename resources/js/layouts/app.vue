<template>
    <div class="flex flex-col items-start min-h-screen bg-gray-100">
        <nav class="w-full mb-6 text-white bg-gray-900 shadow md:bg-gray-800">
            <div class="flex flex-wrap items-center">
                <inertia-link href="/home" class="h-16 bg-gray-900">
                    <img src="/images/logo_alt.svg" alt="textgrid" class="h-full px-4 py-4">
                </inertia-link>

                <button class="pr-4 ml-auto md:hidden focus:outline-none" @click="toggle">
                    <svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
                        <path v-show="!open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path v-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>

                <div :class="open ? 'flex': 'hidden'" class="flex-col flex-grow w-full py-2 bg-gray-800 md:flex md:py-0 md:flex-row md:items-center md:w-auto">
                    <popper
                        trigger="clickToOpen"
                        :options="{ placement: 'right' }"
                    >
                        <div class="w-56 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg">
                            <inertia-link :href="$route('next.campaigns.index')" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">All campaigns</inertia-link>
                            <!-- <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">My models</a> -->
                        </div>

                        <button slot="reference" class="px-4 my-2 text-sm text-gray-200 md:px-0 md:pl-4 hover:text-white focus:outline-none">
                            Campaigns
                        </button>
                    </popper>

                    <!-- <popper
                        trigger="clickToOpen"
                        :options="{ placement: 'right' }"
                    >
                        <div class="w-56 py-1 mt-2 origin-top-right bg-white rounded-md shadow-lg">
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">Directories</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">Browse contacts</a>
                            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900">Campaign exclusion list</a>
                        </div>

                        <button slot="reference" class="px-4 my-2 text-sm text-gray-200 md:px-0 md:pl-4 hover:text-white focus:outline-none">
                            Contacts
                        </button>
                    </popper> -->

                    <div class="hidden ml-auto md:block"></div>

                    <inertia-link class="px-4 my-2 text-sm text-gray-200 md:px-0 md:pr-4 hover:text-white" :href="$route('dashboard')" target="_blank">Legacy app</inertia-link>
                    <button type="button" class="px-4 my-2 text-sm text-left text-gray-200 md:px-0 md:pr-4 hover:text-white focus:outline-none" @click="$inertia.post($route('logout'))">Sign out</button>
                </div>
            </div>
        </nav>

        <div class="container pb-8 text-gray-700">
            <slot></slot>
        </div>

        <footer class="w-full px-4 py-2 mt-auto text-xs text-center text-gray-400">
            &copy; 2020 - TextGrid {{ $page.version }}
        </footer>
    </div>
</template>

<script>
    import Popper from 'vue-popperjs';

    export default {
        components: {
            'popper': Popper
        },

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
