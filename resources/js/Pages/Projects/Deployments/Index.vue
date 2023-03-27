<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <template v-if="deployments.total">
    <div class="overflow-hidden card">
      <table class="w-full text-sm text-left text-gray-500 bg-white border-collapse">
        <thead class="bg-gray-50">
          <tr>
            <th class="w-10" />
            <th scope="col" class="w-40 px-6 py-4 font-medium text-gray-900">Release</th>
            <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-60">Started</th>
            <th scope="col" class="px-6 py-4 font-medium text-gray-900 w-60">Duration</th>
            <th scope="col" class="px-6 py-4 font-medium text-gray-900">Commit</th>
          </tr>
        </thead>
        <tbody class="border-t border-gray-200 divide-y divide-gray-200">
          <Link v-for="deployment in deployments.data" :key="deployment.id" :href="route('projects.deployments.show', [project, deployment])" class="table-row hover:bg-gray-50">
            <td class="py-4 pl-4" :class="{'text-green-500': deployment.status == 'success', 'text-red-500': deployment.status == 'error', 'text-gray-400': deployment.status == 'abandonned',}">
              <span v-if="deployment.status == 'success'"><Icon icon="ri:check-line" /></span>
              <span v-else-if="deployment.status == 'error' || deployment.status == 'abandonned'"><Icon icon="ri:close-line" /></span>
              <span v-else><Icon icon="ri:loader-4-line" class="spin" /></span>
            </td>
            <th class="px-6 py-4 font-medium text-gray-900">{{ deployment.release }}</th>
            <td class="px-6 py-4">
              <template v-if="deployment.started_at">
                {{ moment(deployment.started_at).format('L') }} {{ moment(deployment.started_at).format('LTS') }}
              </template>
              <template v-else>N/A</template>
            </td>
            <td class="px-6 py-4">
              <template v-if="deployment.duration">{{ deployment.duration }}</template>
              <template v-else>N/A</template>
            </td>
            <td class="px-6 py-4">
              <div class="flex flex-row items-center">
                <div class="w-full md:w-1/2 lg:w-1/3">
                  <template v-if="deployment.commit.from_ref">
                    {{ deployment.commit.from_ref.replace('heads/', '') }} @
                  </template>
                  <a
                    :href="'https://github.com/' + deployment.commit.repo + '/commit/' + deployment.commit.sha"
                    class="font-mono hover:underline" target="_blank"
                  >{{ deployment.commit.sha ?
                    deployment.commit.sha.substring(0, 7) : '-' }}</a>
                </div>
                <div class="hidden w-1/3 lg:flex">
                  <img
                    :src="deployment.commit.committer.avatar_url"
                    class="inline w-4 h-4 mr-1 rounded "
                  />
                  <a
                    :href="'https://github.com/' + deployment.commit.committer.login"
                    class="hover:underline" target="_blank"
                  >
                    {{ deployment.commit.committer.login }}
                  </a>
                </div>
                <div class="hidden md:flex md:w-1/2 lg:w-1/3">
                  <div class="truncate">
                    {{ deployment.commit.message }}
                  </div>
                </div>
              </div>
            </td>
          </Link>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center mt-6">
      <Pagination :data="deployments" />
    </div>
  </template>
  <template v-else>
    <div class="border-dashed card">
      <div class="card-body">
        <div class="relative p-5">
          <div class="text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 text-3xl rounded-full text-primary-500 bg-primary-100">
              <Icon icon="ri:rocket-2-fill" />
            </div>
            <div>
              <h3 class="text-lg font-medium text-secondary-900">Deployments</h3>
              <div class="mt-2 text-sm text-secondary-500">
                Deployments are the result of a deployment process. They are created when a deployment process is started.<br />
                Once your project is fully configured, you can do your first deployment by clicking on the button below.
              </div>
              <button class="w-48 mx-auto mt-6 btn btn-primary" @click.prevent="deployNow"><Icon icon="ri:upload-cloud-line" /> Deploy now</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </template>
</template>

<script>
import App from '@/Layouts/App.vue'

export default {
  layout: App,

  props: {
    project: Object,
    deployments: Object,
  },

  methods: {
    deployNow() {
      if (confirm('Do you really want to deploy the most recent commit from the ' + this.project.branch + ' branch?')) {
        this.$inertia.post(this.route('projects.deployments.store', this.project), {
          type: 'branch',
          target: this.project.branch,
        })
      }
    },
  },
}
</script>
