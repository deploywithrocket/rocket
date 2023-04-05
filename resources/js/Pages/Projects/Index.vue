<template>
  <Head title="Projects" />

  <div class="flex flex-row items-center mb-8">
    <div>
      <h1 class="text-base font-bold">Projects</h1>
      <p class="text-sm text-gray-500">Add projects or applications that you want to deploy.</p>
    </div>
    <div class="mx-auto" />
    <Link :href="route('projects.create')" class="btn btn-primary">
      <Icon icon="ri:add-line" /> Add
    </Link>
  </div>

  <template v-if="projects.total">
    <div class="overflow-hidden card">
      <table class="w-full text-sm text-left text-gray-500 bg-white border-collapse">
        <thead class="bg-gray-50">
          <tr>
            <th class="px-6 py-4 font-medium text-gray-900">Name</th>
            <th class="px-6 py-4 font-medium text-gray-900">Repository</th>
            <th class="px-6 py-4 font-medium text-gray-900">Branch</th>
            <th class="px-6 py-4 font-medium text-gray-900">Last deployment</th>
          </tr>
        </thead>
        <tbody class="border-t border-gray-200 divide-y divide-gray-200">
          <Link v-for="project in projects.data" :key="project.id" :href="route('projects.show', project)" class="table-row hover:bg-gray-50">
            <th class="px-6 py-4 font-medium text-gray-900">
              <img v-if="project.live_url" :src="project.favicon_url" class="inline w-6 h-6 mr-1" />
              {{ project.name }}
            </th>
            <td class="px-6 py-4">
              <img :src="'https://github.com/' + project.repository.split('/')[0] + '.png'" class="inline w-6 h-6 mr-1 rounded" />
              {{ project.repository }}
            </td>
            <td class="px-6 py-4">{{ project.branch }}</td>
            <td class="px-6 py-4">
              <template v-if="project.latest_deployment">{{ moment(project.latest_deployment.created_at).format('L') }} {{ moment(project.latest_deployment.created_at).format('LTS') }}</template>
              <template v-else>N/A</template>
            </td>
          </Link>
        </tbody>
      </table>
    </div>
    <div class="flex justify-center mt-8">
      <Pagination :data="projects" />
    </div>
  </template>
  <template v-else>
    <div class="border-dashed card">
      <div class="card-body">
        <div class="relative p-5">
          <div class="text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 text-3xl rounded-full text-primary-500 bg-primary-100">
              <Icon icon="ri:folder-2-fill" />
            </div>
            <div>
              <h3 class="text-lg font-medium text-secondary-900">Projects</h3>
              <div class="mt-2 text-sm text-secondary-500">
                Projects are the applications that you want to deploy.<br />
                They can be a Laravel application, a static site, or anything else.
              </div>
              <Link :href="route('projects.create')" class="w-24 mx-auto mt-6 btn btn-primary">
                <Icon icon="ri:add-line" /> Add
              </Link>
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
    projects: Object,
  },
}
</script>
