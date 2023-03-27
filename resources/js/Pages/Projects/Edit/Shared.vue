<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <form @submit.prevent="submit">
    <div class="card">
      <div class="flex flex-col gap-4 card-body">
        <p>
          On this page, you can specify files and directories that Rocket will symlink
          into your release on each deployment.
        </p>
        <p>The process is split into the following steps:</p>
        <ul>
          <li class="ml-3">- Copy dir from <span class="font-mono">release_path</span> to <span class="font-mono">shared</span> if doesn’t exists,</li>
          <li class="ml-3">- Delete dir from <span class="font-mono">release_path</span>,</li>
          <li class="ml-3">- Symlink dir from <span class="font-mono">shared</span> to <span class="font-mono">release_path</span>.</li>
        </ul>
        <p>The same steps are followed for shared files.</p>
      </div>
      <div class="flex flex-col gap-4 border-t border-gray-200 card-body">
        <h3 class="font-bold">Linked directories</h3>
        <div class="mb-4">
          <div class="flex flex-wrap items-start justify-start gap-2 mb-4">
            <div v-for="(dir) in form.linkedDirs" :key="dir" class="flex flex-row items-center px-4 py-3 rounded-lg bg-gray-50">
              <span class="mr-2">{{ dir }}</span>
              <button class="p-1 text-xs font-medium text-center text-red-600 transition-all bg-white border border-gray-300 rounded-full shadow-sm hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400" @click.prevent="rmDir(dir)">
                <Icon icon="ri:close-line" />
              </button>
            </div>
          </div>

          <form @submit.prevent="addDir">
            <div>Add the following directory:</div>
            <div class="flex items-end justify-start gap-3 mt-2">
              <FormInput v-model="directoryToAdd" class="flex-grow" />
              <button type="submit" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-xs font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400">
                <Icon icon="ri:add-line" />
              </button>
            </div>
          </form>
        </div>
      </div>
      <div class="flex flex-col gap-4 border-t border-gray-200 card-body">
        <h3 class="font-bold">Linked files</h3>
        <div class="mb-4">
          <div class="flex flex-wrap items-start justify-start gap-2 mb-4">
            <div v-for="(file) in form.linkedFiles" :key="file" class="flex flex-row items-center px-4 py-3 rounded-lg bg-gray-50">
              <span class="mr-2">{{ file }}</span>
              <button class="p-1 text-xs font-medium text-center text-red-600 transition-all bg-white border border-gray-300 rounded-full shadow-sm hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400" @click.prevent="rmFile(file)">
                <Icon icon="ri:close-line" />
              </button>
            </div>
          </div>

          <form @submit.prevent="addFile">
            <div>Add the following file:</div>
            <div class="flex items-end justify-start gap-3 mt-2">
              <FormInput v-model="fileToAdd" class="flex-grow" />
              <button type="submit" class="rounded-lg border border-gray-300 bg-white px-5 py-2.5 text-center text-xs font-medium text-gray-700 shadow-sm transition-all hover:bg-gray-100 focus:ring focus:ring-gray-100 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400">
                <Icon icon="ri:add-line" />
              </button>
            </div>
          </form>
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
import _ from 'lodash'

export default {
  layout: App,

  props: {
    project: Object,
  },

  data() {
    return {
      directoryToAdd: '',
      fileToAdd: '',
      form: useForm({
        linkedDirs: this.project.linked_dirs || [],
        linkedFiles: this.project.linked_files || [],
      }),
    }
  },

  methods: {
    submit() {
      this.form.put(this.route('projects.update.shared', this.project))
    },

    addDir() {
      if (!_.find(this.form.linkedDirs, (elem) => elem == this.directoryToAdd)) this.form.linkedDirs.push(this.directoryToAdd)
      this.directoryToAdd = ''
    },

    rmDir(dir) {
      this.form.linkedDirs = _.reject(this.form.linkedDirs, (elem) => elem == dir)
    },

    addFile() {
      if (!_.find(this.form.linkedFiles, (elem) => elem == this.fileToAdd)) this.form.linkedFiles.push(this.fileToAdd)
      this.fileToAdd = ''
    },

    rmFile(file) {
      this.form.linkedFiles = _.reject(this.form.linkedFiles, (elem) => elem == file)
    },
  },
}
</script>
