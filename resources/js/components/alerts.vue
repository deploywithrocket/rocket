<template>
    <div v-if="$page.flash.success || $page.flash.warning || $page.flash.error" class="w-full mb-4">
        <div class="container max-w-screen-xl">
            <div v-if="$page.flash.success" class="p-4 mb-4 text-sm text-green-800 bg-green-100 border-l border-green-400 rounded shadow-sm">
                <i class="mr-1 opacity-50 fas fa-check"></i> {{ $page.flash.success }}
            </div>
            <div v-if="$page.flash.warning" class="p-4 mb-4 text-sm text-orange-800 bg-orange-100 border-l border-orange-500 rounded shadow-sm">
                <i class="mr-1 opacity-50 fas fas-exclamation-triangle"></i> {{ $page.flash.warning }}
            </div>
            <div v-if="$page.flash.error" class="p-4 mb-4 text-sm text-red-800 bg-red-100 border-l border-red-500 rounded shadow-sm">
                <i class="mr-1 opacity-50 fas fa-times"></i> {{ $page.flash.error }}
            </div>
        </div>
    </div>
</template>

<script>
    export default {
    }
</script>