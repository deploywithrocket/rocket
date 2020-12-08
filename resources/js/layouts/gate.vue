<template>
    <div class="flex flex-row items-center justify-center h-screen">
        <div class="hidden w-1/2 h-screen overflow-hidden bg-gradient-to-tr from-gray-900 to-gray-800 lg:block">
            <div class="relative w-full h-screen transform lg:-translate-x-16 lg:scale-110 xl:-translate-x-24 xl:scale-125 2xl:-translate-x-32 2xl:scale-150">
                <img src="/images/brand_text.svg" alt="Rocket" class="logo-over-rocket">
                <div class="rocket">
                    <div class="rocket-body">
                        <div class="body"></div>
                        <div class="fin fin-left"></div>
                        <div class="fin fin-right"></div>
                        <div class="window"></div>
                    </div>
                    <div class="exhaust-flame"></div>
                    <ul class="exhaust-fumes">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                    <ul class="star">
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                        <li></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="flex flex-col items-center justify-center w-full h-screen text-gray-700 bg-gray-100 lg:w-1/2">
            <img src="/images/logo.svg" alt="Rocket" class="block h-16 mx-auto mb-8 lg:hidden">
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
