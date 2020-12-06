<template>
    <div class="flex items-center justify-center min-h-screen bg-gray-100">
        <div class="container max-w-md text-gray-700">
            <img src="/images/logo.svg" alt="textgrid" class="h-16 mx-auto mb-8">
            <slot></slot>
            <div class="mt-8 text-xs text-center">&copy; 2020 - TextGrid {{ $page.version }}</div>
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
