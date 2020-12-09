<template>
    <div class="max-w-sm p-8 mx-auto bg-white rounded-lg shadow lg:max-w-3xl">
        <div class="mb-8">
            <h1 class="text-2xl font-bold">Welcome to <span class="text-pink-500">Rocket</span></h1>
            Ready for take off!
        </div>

        <div v-if="!github_account">
            <div class="mb-2">In order to deploy private projects, you have to connect your GitHub account.</div>

            <a href="/auth/github" class="px-4 py-2 text-sm font-semibold bg-gray-100 rounded hover:bg-gray-200"><i class="fab fa-github"></i> Connect to GitHub</a>
        </div>
        <div v-else>
            <i class="fab fa-github"></i> Connected to {{ github_account.provider_user_name }}
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
