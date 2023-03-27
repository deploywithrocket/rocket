<template>
  <Head title="Home" />

  <div class="flex flex-row items-center mb-8">
    <div>
      <h1 class="text-base font-bold">Welcome to Rocket!</h1>
      <p class="text-sm text-gray-500">Deploy your projects with ease.</p>
    </div>
  </div>

  <WorkInProgress />
</template>

<script>
import App from '@/Layouts/App.vue'

export default {
  layout: App,
}
</script>
