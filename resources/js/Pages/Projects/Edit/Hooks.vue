<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <form @submit.prevent="submit">
    <div class="card">
      <div class="flex flex-col gap-4 card-body">
        <p>
          On this page, you can enter Bash scripts that will be executed on your server during the
          deployment process.<br />
          Like any other step during your deployment, if a deployment hook exits with a non-zero
          status code, the entire deployment will be cancelled. This prevents your applications from
          experiencing downtime with a broken deployment.
        </p>
        <p>
          Under the hood, Rocket uses Laravel Blade templating system to build the final
          deployment script.<br />
          You may use the following variables within your hooks:
        </p>

        <table class="w-full border border-gray-100 rounded-lg">
          <tbody class="divide-y divide-gray-100">
            <tr class="even:bg-gray-50">
              <th class="w-56 px-6 py-4 font-mono text-xs text-left text-gray-900">{!! $deploy_path !!}</th>
              <td class="px-5 py-3">Resolves to the project's root directory</td>
            </tr>
            <tr class="even:bg-gray-50">
              <th class="w-56 px-6 py-4 font-mono text-xs text-left text-gray-900">{!! $release_path !!}</th>
              <td class="px-5 py-3">Resolves to the current release path, within releases</td>
            </tr>
            <tr class="even:bg-gray-50">
              <th class="w-56 px-6 py-4 font-mono text-xs text-left text-gray-900">{!! $ref !!}</th>
              <td class="px-5 py-3">Resolves to the ref that is being deployed</td>
            </tr>
            <tr class="even:bg-gray-50">
              <th class="w-56 px-6 py-4 font-mono text-xs text-left text-gray-900">{!! $sha !!}</th>
              <td class="px-5 py-3">Resolves to the commit hash that is being deployed</td>
            </tr>
          </tbody>
        </table>

        <div class="flex flex-col items-center lg:flex-row">
          <div class="w-full mb-4 lg:w-1/3">
            <span class="font-mono font-bold">started</span>
            <p>Right before we start deploying</p>
          </div>
          <VAceEditor v-model:value="form.started" lang="sh" class="w-full border-gray-300 rounded-md shadow-sm lg:ml-4 lg:w-2/3 h-72" theme="one_dark" :print-margin="false" />
        </div>

        <div class="flex flex-col items-center lg:flex-row">
          <div class="w-full mb-4 lg:w-1/3">
            <span class="font-mono font-bold">provisioned</span>
            <p>After cloning and installing vendors dependencies</p>
          </div>
          <VAceEditor v-model:value="form.provisioned" lang="sh" class="w-full border-gray-300 rounded-md shadow-sm lg:ml-4 lg:w-2/3 h-72" theme="one_dark" :print-margin="false" />
        </div>

        <div class="flex flex-col items-center lg:flex-row">
          <div class="w-full mb-4 lg:w-1/3">
            <span class="font-mono font-bold">built</span>
            <p>Once the production assets have been built</p>
          </div>
          <VAceEditor v-model:value="form.built" lang="sh" class="w-full border-gray-300 rounded-md shadow-sm lg:ml-4 lg:w-2/3 h-72" theme="one_dark" :print-margin="false" />
        </div>

        <div class="flex flex-col items-center lg:flex-row">
          <div class="w-full mb-4 lg:w-1/3">
            <span class="font-mono font-bold">published</span>
            <p>Deployment is done and website is live</p>
          </div>
          <VAceEditor v-model:value="form.published" lang="sh" class="w-full border-gray-300 rounded-md shadow-sm lg:ml-4 lg:w-2/3 h-72" theme="one_dark" :print-margin="false" />
        </div>

        <div class="flex flex-col items-center lg:flex-row">
          <div class="w-full mb-4 lg:w-1/3">
            <span class="font-mono font-bold">finished</span>
            <p>When the plan is complete</p>
          </div>
          <VAceEditor v-model:value="form.finished" lang="sh" class="w-full border-gray-300 rounded-md shadow-sm lg:ml-4 lg:w-2/3 h-72" theme="one_dark" :print-margin="false" />
        </div>
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
import 'ace-builds/src-noconflict/mode-sh'

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
        started: this.project.hooks.started || '',
        provisioned: this.project.hooks.provisioned || '',
        built: this.project.hooks.built || '',
        published: this.project.hooks.published || '',
        finished: this.project.hooks.finished || '',
      }),
    }
  },


  methods: {
    submit() {
      this.form.put(this.route('projects.update.hooks', this.project))
    },
  },
}
</script>
