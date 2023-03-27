<template>
  <Head title="Create Project" />

  <Modal>
    <form class="flex flex-col gap-6" autocomplete="off" @submit.prevent="submit">
      <input autocomplete="false" name="hidden" type="text" style="display:none;" />

      <div class="flex flex-col gap-4">
        <FormInput v-model="form.name" label="Name" placeholder="My awesome project" type="text" name="name" :required="true" :errors="form.errors.name" />

        <div class="flex flex-row items-center gap-3">
          <FormInput v-model="form.repository" class="w-2/3" label="Repository" placeholder="mgkprod/rocket" type="text" :required="true" name="repository" :errors="form.errors.repository" />
          <FormInput v-model="form.branch" class="w-1/3" label="Deploy branch" placeholder="main" type="text" name="branch" :required="true" :errors="form.errors.branch" />
        </div>

        <FormSelect v-model="form.serverId" label="Server" name="serverId" required :errors="form.errors.serverId" :options="servers" />
        <FormInput v-model="form.deployPath" label="Deploy path" type="text" placeholder="/home/websites/rocket" name="deployPath" :required="true" :errors="form.errors.deployPath" />
        <FormInput v-model="form.environment" label="Environment" type="text" placeholder="production" name="environment" :required="true" :errors="form.errors.environment" />
        <FormInput v-model="form.liveUrl" label="Live URL" type="text" placeholder="https://rocket.mgk.dev" name="liveUrl" :errors="form.errors.liveUrl" />
      </div>

      <div class="flex flex-col gap-3 p-6 text-sm text-gray-500 rounded-md bg-gray-50">
        <b>Project options</b>
        <FormCheckbox v-model="form.useLaravelPreset" label="This is a Laravel project" name="useLaravelPreset" :errors="form.errors.useLaravelPreset" />
      </div>

      <div class="flex flex-row justify-between">
        <span />
        <button type="submit" :disabled="form.processing" class="btn btn-primary">Create project</button>
      </div>
    </form>
  </Modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  props: {
    servers: {
      type: Object,
      required: true,
    },
  },

  data() {
    return {
      form: useForm({
        name: null,
        repository: null,
        branch: null,
        liveUrl: null,
        serverId: null,
        deployPath: null,
        environment: null,
        useLaravelPreset: false,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('projects.store'))
    },
  },
}
</script>
