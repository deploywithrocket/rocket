<template>
    <div>
        <div class="flex flex-col items-center justify-center w-full min-h-screen py-8 text-gray-700 bg-gray-100">
            <img src="/images/logo_alt.svg" alt="Rocket" class="h-16 mx-auto mb-8">
            <slot></slot>
            <div class="mt-8 text-xs text-center text-gray-400">&copy; 2020-2021 - <a href="https://deploywithrocket.dev" class="hover:underline">Rocket</a> {{ $page.version }}</div>
        </div>
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
