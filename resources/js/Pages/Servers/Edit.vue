<template>
  <Head title="Edit Server" />

  <Modal>
    <div>
      <form class="flex flex-col gap-6" @submit.prevent="submit">
        <div class="flex flex-col gap-4">
          <FormInput v-model="form.name" label="Name" placeholder="My production server" type="text" name="name" :errors="form.errors.name" />
          <FormInput v-model="form.sshUser" label="User" type="text" placeholder="rocket" name="sshUser" :errors="form.errors.sshUser" />

          <div class="flex flex-row items-center gap-2">
            <FormInput v-model="form.sshHost" class="w-2/3" label="Host" type="text" name="sshHost" :errors="form.errors.sshHost" />
            <FormInput v-model="form.sshPort" class="w-1/3" label="Port" type="text" placeholder="22" name="sshPort" :errors="form.errors.sshPort" />
          </div>
        </div>
        <div class="border-t border-gray-100" />
        <div class="flex flex-col gap-4">
          <FormInput v-model="form.cmdGit" label="Git path" placeholder="git" type="text" name="cmd_git" :errors="form.errors.cmdGit" />
          <FormInput v-model="form.cmdNpm" label="npm path" placeholder="npm" type="text" name="cmdNpm" :errors="form.errors.cmdNpm" />
          <FormInput v-model="form.cmdYarn" label="Yarn path" placeholder="yarn" type="text" name="cmdYarn" :errors="form.errors.cmdYarn" />
          <FormInput v-model="form.cmdPhp" label="Php path" placeholder="php" type="text" name="cmdPhp" :errors="form.errors.cmdPhp" />
          <FormInput v-model="form.cmdComposer" label="Composer path" placeholder="composer" type="text" name="cmdComposer" :errors="form.errors.cmdComposer" />
          <FormInput v-model="form.cmdComposerOptions" label="Composer options" placeholder="--no-dev" type="text" name="cmdComposerOptions" :errors="form.errors.cmdComposerOptions" />
        </div>
        <div class="flex flex-row justify-between ">
          <span />
          <div class="flex justify-end">
            <button type="submit" :disabled="form.processing" class="btn btn-primary">Save</button>
          </div>
        </div>
      </form>

      <button class="btn btn-tertiary " @click="destroy">
        <Icon icon="ri:close-line" class="text-red-500" /> Remove this server from Rocket
      </button>
    </div>
  </Modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {

  props: ['server'],

  data() {
    return {
      form: useForm({
        name: this.server.name,
        sshUser: this.server.ssh_user,
        sshHost: this.server.ssh_host,
        sshPort: this.server.ssh_port,
        cmdGit: this.server.cmd_git,
        cmdNpm: this.server.cmd_npm,
        cmdYarn: this.server.cmd_yarn,
        cmdPhp: this.server.cmd_php,
        cmdComposer: this.server.cmd_composer,
        cmdComposerOptions: this.server.cmd_composer_options,
      }),
    }
  },

  methods: {
    submit() {
      this.form.put(this.route('servers.update', this.server)      )
    },

    destroy() {
      if (confirm('Do you really want to remove this server from Rocket?')) {
        this.$inertia.delete(this.route('servers.destroy', this.server))
      }
    },
  },
}
</script>
