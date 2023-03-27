<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <form @submit.prevent="submit">
    <div class="card">
      <div class="flex flex-col gap-4 card-body">
        <p>
          On this page, you can specify cron jobs that will be appended into the server's cron scheduler.<br />
          E.g. this is useful for Laravel projects that require the use of scheduled tasks (<a class="hover:underline" href="https://laravel.com/docs/master/scheduling#starting-the-scheduler" target="_blank">laravel scheduler</a>)
        </p>
        <VAceEditor v-model:value="form.cronJobs" theme="one_dark" class="block w-full border-gray-300 rounded-md shadow-sm h-72" :print-margin="false" />
      </div>

      <div class="card-footer">
        <span />
        <button type="submit" :disabled="form.processing" class="btn btn-secondary">Save</button>
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
  },

  data() {
    return {
      form: useForm({
        cronJobs: this.project.cron_jobs || '',
      }),
    }
  },

  methods: {
    submit() {
      this.form.put(this.route('projects.update.cron-jobs', this.project))
    },
  },
}
</script>
