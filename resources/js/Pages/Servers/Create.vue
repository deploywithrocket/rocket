<template>
  <Head title="Create Server" />

  <Modal>
    <form class="flex flex-col gap-6" autocomplete="off" @submit.prevent="submit">
      <input autocomplete="false" name="hidden" type="text" style="display:none;" />

      <div class="flex flex-col gap-4">
        <FormInput v-model="form.name" label="Name" placeholder="My production server" type="text" name="name" :errors="form.errors.name" />
        <FormInput v-model="form.sshUser" label="User" type="text" placeholder="rocket" name="sshUser" :errors="form.errors.sshUser" />

        <div class="flex flex-row items-center gap-3">
          <FormInput v-model="form.sshHost" class="w-2/3" label="Host" type="text" name="sshHost" :errors="form.errors.sshHost" />
          <FormInput v-model="form.sshPort" class="w-1/3" label="Port" type="text" placeholder="22" name="sshPort" :errors="form.errors.sshPort" />
        </div>
      </div>

      <div class="flex flex-row justify-between">
        <span />
        <div class="flex justify-end">
          <button type="submit" :disabled="form.processing" class="btn btn-primary">Add server</button>
        </div>
      </div>
    </form>
  </Modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  data() {
    return {
      form: useForm({
        name: null,
        sshUser: null,
        sshHost: null,
        sshPort: 22,
      }),
    }
  },

  methods: {
    submit() {
      this.form.post(this.route('servers.store'))
    },
  },
}
</script>
