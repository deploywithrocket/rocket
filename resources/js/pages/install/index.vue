<template>
    <div class="w-full">
        <div class="flex flex-col items-start w-full max-w-md mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 font-semibold bg-gray-50">
                Welcome to Rocket!
            </div>
            <form class="w-full" @submit.prevent="submit">
                <div class="w-full px-5 py-4">
                    <p class="mb-4">
                        If you've made it to this page, you've already done the hardest part!<br>
                        However, there are a few steps left before you make your first deployment.
                    </p>
                    <p>Click "Next" to begin the installation</p>
                </div>
                <div class="w-full px-5 py-4 text-sm bg-gray-50">
                    <div class="flex justify-end">
                        <button class="inline-block px-4 py-2 text-sm font-semibold text-white transition duration-200 ease-in-out bg-pink-500 rounded hover:bg-pink-600 focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"><i class="mr-1 fas fa-angle-right"></i> Next</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../../layouts/gate').default,

        methods: {
            submit() {
                this.$inertia.visit(this.$route('install.system'))
            }
        }
    }
</script>