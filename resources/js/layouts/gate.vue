<template>
    <div class="flex flex-row items-center justify-center h-screen">
        <div class="items-center justify-center hidden w-1/2 h-screen overflow-hidden bg-gray-900 lg:flex">
            <div class="flex flex-row items-center">
                <img src="/images/logo.svg" alt="Rocket" class="mr-8">
                <div class="text-6xl font-bold leading-tight tracking-tight text-pink-500 font-brand">Rocket</div>
            </div>
        </div>

        <div class="hidden h-screen text-gray-900 bg-gray-100 lg:block">
            <svg width="50" height="100%" viewBox="0 0 300 1600" fill="currentColor" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
                <path d="M0 0H300L0 1600V0Z" />
            </svg>
        </div>

        <div class="flex flex-col items-center justify-center w-full h-screen text-gray-700 bg-gray-100 lg:w-1/2">
            <img src="/images/logo_alt.svg" alt="Rocket" class="block h-16 mx-auto mb-8 lg:hidden">
            <slot></slot>
            <div class="mt-8 text-xs text-center">&copy; 2020-2021 - Rocket {{ $page.version }}</div>
        </div>
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
