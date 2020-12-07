<template>
    <div v-if="$page.flash.success || $page.flash.warning || $page.flash.error" class="pt-8 -mb-4">
        <div v-if="$page.flash.success" class="p-4 mb-4 text-sm text-white bg-green-500 rounded-lg shadow">
            <i class="mr-1 fas fa-check"></i> {{ $page.flash.success }}
        </div>
        <div v-if="$page.flash.warning" class="p-4 mb-4 text-sm text-white bg-orange-500 rounded-lg shadow">
            <i class="mr-1 fas fas-exclamation-triangle"></i> {{ $page.flash.warning }}
        </div>
        <div v-if="$page.flash.error" class="p-4 mb-4 text-sm text-white bg-red-500 rounded-lg shadow">
            <i class="mr-1 fas fa-times"></i> {{ $page.flash.error }}
        </div>
    </div>
</template>

<script>
    export default {
    }
</script>