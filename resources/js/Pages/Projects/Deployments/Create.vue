<template>
  <Head title="Create Deployment" />

  <Modal>
    <form class="flex flex-col gap-6" @submit.prevent="submit">
      <div class="flex flex-col gap-4">
        <FormSelect v-model="form.type" label="Type" name="type" required :errors="form.errors.type" :options="types" />
        <FormInput v-model="form.target" label="Target" type="text" placeholder="14ef11c3949e319acf0cfb1d4683d05746e17cef" name="target" :errors="form.errors.target" required />
      </div>

      <div class="flex flex-row justify-between">
        <span />
        <button type="submit" :disabled="form.processing" class="btn btn-primary">Deploy project</button>
      </div>
    </form>
  </Modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  props: {
    project: Object,
    types: Object,
  },

  data() {
    return {
      form: useForm({
        type: 'commit',
        target: '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('projects.deployments.store', this.project))
    },
  },
}
</script>
