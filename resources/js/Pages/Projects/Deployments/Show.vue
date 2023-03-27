<template>
  <Head :title="'#' + deployment.release + ' | ' + project.name" />
  <ProjectHeader :project="project" :deployment="deployment" />

  <div class="overflow-hidden card">
    <table class="w-full text-sm text-left text-gray-600 bg-white border-collapse">
      <tbody class="divide-y divide-gray-200">
        <tr>
          <td class="w-1/4 px-6 py-4 font-medium text-gray-900 bg-gray-50">Release</td>
          <td class="px-6 py-4">{{ deployment.release }}</td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Requested at</td>
          <td class="px-6 py-4">{{ moment(deployment.created_at).format('L') }} {{ moment(deployment.created_at).format('LTS') }}</td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Started at</td>
          <td class="px-6 py-4">
            <template v-if="deployment.started_at">{{ moment(deployment.started_at).format('L') }} {{ moment(deployment.started_at).format('LTS') }}</template>
            <template v-else>N/A</template>
          </td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Ended at</td>
          <td class="px-6 py-4">
            <template v-if="deployment.ended_at">{{ moment(deployment.ended_at).format('L') }} {{ moment(deployment.ended_at).format('LTS') }}</template>
            <template v-else>N/A</template>
          </td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Duration</td>
          <td class="px-6 py-4">{{ deployment.duration }}</td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Status</td>
          <td class="px-6 py-4">{{ deployment.status }}</td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Committer</td>
          <td class="px-6 py-4">
            <template v-if="deployment.commit.committer">
              <img :src="deployment.commit.committer.avatar_url" class="inline w-6 h-6 mr-1 rounded " />
              <a :href="'https://github.com/' + deployment.commit.committer.login" class="hover:underline" target="_blank">{{ deployment.commit.committer.login }}</a>
            </template>
          </td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Commit</td>
          <td class="px-6 py-4">
            <template v-if="deployment.commit.from_ref">{{ deployment.commit.from_ref.replace('heads/', '') }} @</template>
            <a :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha" class="font-mono hover:underline" target="_blank">{{ deployment.commit.sha ?deployment.commit.sha.substring(0, 7) : '-' }}</a>
          </td>
        </tr>
        <tr>
          <td class="px-6 py-4 font-medium text-gray-900 bg-gray-50">Commit message</td>
          <td class="px-6 py-4">{{ deployment.commit.message }}</td>
        </tr>
      </tbody>
    </table>
  </div>

  <div v-if="deployment.status != 'queued'" class="mt-8 overflow-hidden card">
    <div class="card-header">
      <h2>Process output</h2>
    </div>

    <div class="cursor-pointer card-header hover:bg-gray-100" @click="toggle('start')">
      <div class="flex items-center gap-2">
        <Icon v-if="deployment.status == 'in_progress' && !deployment.raw_output.provision" icon="ri:loader-4-line" class="spin" />
        <Icon v-else-if="deployment.status == 'error' && !deployment.raw_output.provision" icon="ri:close-line" class="text-red-500" />
        <Icon v-else-if="deployment.raw_output.provision" icon="ri:check-line" class="text-green-500" />

        <h3>🏃 start</h3>
      </div>
    </div>
    <pre v-if="visible.start" class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800">{{ deployment.raw_output.start }}</pre>

    <div class="cursor-pointer card-header hover:bg-gray-100" @click="toggle('provision')">
      <div class="flex items-center gap-2">
        <Icon v-if="deployment.status == 'in_progress' && !deployment.raw_output.build" icon="ri:loader-4-line" class="spin" />
        <Icon v-else-if="deployment.status == 'error' && !deployment.raw_output.build" icon="ri:close-line" class="text-red-500" />
        <Icon v-else-if="deployment.raw_output.build" icon="ri:check-line" class="text-green-500" />

        <h3>🚚 provision</h3>
      </div>
    </div>
    <pre v-if="visible.provision" class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800">{{ deployment.raw_output.provision }}</pre>

    <div class="cursor-pointer card-header hover:bg-gray-100" @click="toggle('build')">
      <div class="flex items-center gap-2">
        <Icon v-if="deployment.status == 'in_progress' && !deployment.raw_output.publish" icon="ri:loader-4-line" class="spin" />
        <Icon v-else-if="deployment.status == 'error' && !deployment.raw_output.publish" icon="ri:close-line" class="text-red-500" />
        <Icon v-else-if="deployment.raw_output.publish" icon="ri:check-line" class="text-green-500" />

        <h3>📦 build</h3>
      </div>
    </div>
    <pre v-if="visible.build" class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800">{{ deployment.raw_output.build }}</pre>

    <div class="cursor-pointer card-header hover:bg-gray-100" @click="toggle('publish')">
      <div class="flex items-center gap-2">
        <Icon v-if="deployment.status == 'in_progress' && !deployment.raw_output.finish" icon="ri:loader-4-line" class="spin" />
        <Icon v-else-if="deployment.status == 'error' && !deployment.raw_output.finish" icon="ri:close-line" class="text-red-500" />
        <Icon v-else-if="deployment.raw_output.finish" icon="ri:check-line" class="text-green-500" />

        <h3>🚀 publish</h3>
      </div>
    </div>
    <pre v-if="visible.publish" class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800">{{ deployment.raw_output.publish }}</pre>

    <div class="cursor-pointer card-header hover:bg-gray-100" @click="toggle('finish')">
      <div class="flex items-center gap-2">
        <Icon v-if="deployment.status == 'in_progress'" icon="ri:loader-4-line" class="spin" />
        <Icon v-if="deployment.status == 'error'" icon="ri:close-line" class="text-red-500" />
        <Icon v-if="deployment.status == 'success'" icon="ri:check-line" class="text-green-500" />

        <h3>🗑️ finish</h3>
      </div>
    </div>
    <pre v-if="visible.finish" class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800">{{ deployment.raw_output.finish }}</pre>
  </div>
</template>

<script>
import App from '@/Layouts/App.vue'

export default {
  layout: App,

  props: {
    project: Object,
    deployment: Object,
  },

  data() {
    return {
      visible: {
        start: false,
        provision: false,
        build: false,
        publish: false,
        finish: false,
      },
    }
  },

  mounted() {
    if (!this.deployment.raw_output) return
    if (this.deployment.raw_output.start && !this.deployment.raw_output.provision) this.visible.start = true
    if (this.deployment.raw_output.provision && !this.deployment.raw_output.build) this.visible.provision = true
    if (this.deployment.raw_output.build && !this.deployment.raw_output.publish) this.visible.build = true
    if (this.deployment.raw_output.publish && !this.deployment.raw_output.finish) this.visible.publish = true
    if (this.deployment.raw_output.finish && !this.deployment.status == 'success') this.visible.finish = true
  },

  methods: {
    toggle(name) {
      this.visible[name] = !this.visible[name]
    },
  },
}
</script>
