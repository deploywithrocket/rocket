<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <form class="flex-1 space-y-8" @submit.prevent="submit">
    <div class="card">
      <div class="flex flex-col gap-4 card-body">
        <FormInput v-model="form.name" label="Name" placeholder="My awesome project" type="text" name="name" :errors="form.errors.name" required />

        <div class="flex flex-row items-center gap-3">
          <FormInput v-model="form.repository" class="w-2/3" label="Repository" placeholder="mgkprod/rocket" type="text" name="repository" :errors="form.errors.repository" required />
          <FormInput v-model="form.branch" class="w-1/3" label="Branch" placeholder="main" type="text" name="branch" :errors="form.errors.branch" required />
        </div>

        <FormInput v-model="form.liveUrl" label="Live URL" type="text" placeholder="https://rocket.mgk.dev" name="liveUrl" :errors="form.errors.liveUrl" />
        <FormSelect v-model="form.serverId" label="Server" name="serverId" required :errors="form.errors.serverId" :options="servers" />
        <FormInput v-model="form.deployPath" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deploy_path" :errors="form.errors.deployPath" required />
        <FormInput v-model="form.environment" label="Environment" type="text" placeholder="production" name="environment" :errors="form.errors.environment" required />
      </div>
      <div class="card-footer">
        <span />
        <button type="submit" :disabled="form.processing" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400">Save</button>
      </div>
    </div>
  </form>

  <div class="mt-8">
    <button class="btn btn-tertiary" @click="destroy">
      <Icon icon="ri:close-line" class="text-red-500" /> Remove this project from Rocket
    </button>
  </div>
</template>

<script>
import App from '@/Layouts/App.vue'
import { useForm } from '@inertiajs/vue3'

export default {
  layout: App,

  props: {
    project: Object,
    servers: Object,
  },

  data() {
    return {
      form: useForm({
        name: this.project.name,
        repository: this.project.repository,
        branch: this.project.branch,
        liveUrl: this.project.live_url,
        serverId: this.project.server_id,
        deployPath: this.project.deploy_path,
        environment: this.project.environment,
      }),
    }
  },

  methods: {
    submit() {
      this.form.put(this.route('projects.update.common', this.project))
    },

    destroy() {
      if (confirm('Do you really want to remove this project from Rocket?')) {
        this.$inertia.delete(this.route('projects.destroy', this.project))
      }
    },
  },
}
</script>
