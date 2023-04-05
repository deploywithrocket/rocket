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

  <div class="mt-8 overflow-hidden divide-y divide-gray-200 card">
    <table class="w-full text-sm text-left text-gray-500 bg-white border-collapse">
      <thead class="bg-gray-50">
        <tr>
          <th class="w-10" />
          <th class="w-40 px-6 py-4 font-medium text-gray-900">Step</th>
          <th class="w-40 px-6 py-4 font-medium text-gray-900">Server</th>
          <th class="px-6 py-4 font-medium text-gray-900 w-60">Started</th>
          <th class="px-6 py-4 font-medium text-gray-900 w-60">Ended</th>
          <th class="" />
        </tr>
      </thead>

      <tbody class="border-t border-gray-200 divide-y divide-gray-200">
        <template v-for="(tasksChunk, name) in tasks" :key="name">
          <tr v-for="task in tasksChunk" :key="task.id">
            <td class="py-4 pl-4">
              <Icon v-if="task.status == 'in_progress'" icon="ri:loader-4-line" class="spin" />
              <Icon v-else-if="task.status == 'error'" icon="ri:close-line" class="text-red-500" />
              <Icon v-else-if="task.status == 'success'" icon="ri:check-line" class="text-green-500" />
            </td>
            <th class="px-6 py-4 font-medium text-gray-900">{{ taskLabels[name] }}</th>
            <td class="px-6 py-4">{{ task.server.name }}</td>
            <td class="px-6 py-4">
              <template v-if="task.started_at">
                {{ moment(task.started_at).format('L') }} {{ moment(task.started_at).format('LTS') }}
              </template>
              <template v-else>N/A</template>
            </td>
            <td class="px-6 py-4">
              <template v-if="task.ended_at">
                {{ moment(task.ended_at).format('L') }} {{ moment(task.ended_at).format('LTS') }}
              </template>
              <template v-else>N/A</template>
            </td>
            <td class="flex justify-end gap-1 px-6 py-4">
              <Link :href="route('projects.deployments.tasks.show-output', { project: project, deployment: deployment, task: task })" class="btn btn-secondary btn-square" preserve-scroll><Icon icon="ri:file-text-line" /></Link>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
  </div>
</template>

<script>
import App from '@/Layouts/App.vue'

export default {
  layout: App,

  props: {
    project: Object,
    deployment: Object,
    tasks: Object,
  },

  data() {
    return {
      taskLabels: {
        start: '🏃 Start',
        provision: '🚚 Provision',
        build: '📦 Build',
        publish: '🚀 Publish',
        finish: '🗑️ Finish',
      },
    }
  },
}
</script>
