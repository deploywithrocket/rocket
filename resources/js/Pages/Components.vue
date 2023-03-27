<template>
  <div class="flex flex-col gap-4 px-6 py-4 mt-6 text-sm bg-white rounded-lg shadow">
    <FormInput v-model="form.name" label="Name" type="text" placeholder="My project" name="name" :required="true" :errors="form.errors.name" />
    <FormSelect v-model="form.serverId" label="Server" name="serverId" :required="true" :errors="form.errors.serverId" :options="servers" />
    <FormCheckbox v-model="form.useLaravelPreset" label="This is a Laravel project" name="useLaravelPreset" :errors="form.errors.useLaravelPreset" />

    <FormInput v-model="form.name" type="text" placeholder="My project" name="name" :required="true" errors="form.errors.name" />
    <FormSelect v-model="form.serverId" name="serverId" :required="true" errors="form.errors.serverId" :options="servers" />
    <FormCheckbox v-model="form.useLaravelPreset" label="This is a Laravel project" name="useLaravelPreset" errors="form.errors.useLaravelPreset" />
  </div>
</template>

<script>
import App from '@/Layouts/App.vue'
import { useForm } from '@inertiajs/vue3'

export default {
  layout: App,

  props: {
    servers: Object,
  },

  data() {
    return {
      form: useForm({
        name: '',
        serverId: 0,
        useLaravelPreset: false,
      }),
    }
  },

}
</script>
