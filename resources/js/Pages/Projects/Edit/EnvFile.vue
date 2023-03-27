<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <form @submit.prevent="submit">
    <div class="card">
      <div class="relative overflow-hidden rounded-t-lg">
        <div :class="{'scale-105 filter blur-sm': blurred}" class="transition-all duration-200">
          <VAceEditor v-model:value="form.env" theme="one_dark" class="block w-full" lang="ini" style="height: 48rem;" :print-margin="false" />
        </div>

        <button v-if="blurred" class="absolute inset-0 flex items-center justify-center text-4xl text-white" @click="blurred = false">
          <Icon icon="ri:eye-2-line" />
        </button>
      </div>

      <div class="card-footer">
        <span />
        <button type="submit" :disabled="form.processing" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-sm font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400">Save</button>
      </div>
    </div>
  </form>
</template>

<script>
import App from '@/Layouts/App.vue'
import { useForm } from '@inertiajs/vue3'
import { VAceEditor } from 'vue3-ace-editor'
import 'ace-builds/src-noconflict/theme-one_dark'
import 'ace-builds/src-noconflict/mode-ini'

export default {
  components: {
    VAceEditor,
  },

  layout: App,

  props: {
    project: Object,
    servers: Object,
  },

  data() {
    return {
      blurred: true,
      form: useForm({
        env: this.project.env || '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.put(this.route('projects.update.env-file', this.project))
    },
  },
}
</script>
