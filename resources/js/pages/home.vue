<template>
    <div>
        <nav class="flex items-center mb-8 font-semibold">
            Home
        </nav>

        <div class="flex flex-col items-start w-full mx-auto mb-8 overflow-hidden bg-white rounded shadow-sm">
            <div class="w-full px-5 py-4 bg-gray-50">
                <h2 class="font-semibold">Welcome!</h2>
            </div>
            <div class="w-full px-5 py-4">
                <div v-if="!github_account">
                    In order to deploy private projects, you have to connect your GitHub account.
                </div>
                <div v-else>
                    We are now ready for take off!
                </div>
            </div>
            <div class="w-full px-5 py-4 text-sm bg-gray-50">
                <div class="flex justify-end">
                    <a href="/auth/github" class="inline-block px-4 py-2 text-sm font-semibold text-gray-600 transition duration-150 ease-in-out bg-gray-200 rounded-lg hover:bg-gray-300"><i class="mr-1 opacity-75 fab fa-github"></i>
                        <span v-if="!github_account">
                            Connect to GitHub
                        </span>
                        <span v-else>
                            Reconnect to GitHub
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        layout: require('../layouts/app').default,

        props: {
            github_account: Object,
        }
    }
</script>
