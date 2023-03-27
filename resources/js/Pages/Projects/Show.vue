<template>
  <Head :title="project.name" />
  <ProjectHeader :project="project" />

  <div class="overflow-hidden card">
    <table class="w-full text-sm text-left text-gray-600 bg-white border-collapse">
      <tbody class="divide-y divide-gray-200">
        <tr>
          <th class="w-56 px-6 py-4 font-medium text-gray-900 bg-gray-50">Deployments today</th>
          <td class="px-6 py-4">{{ deploymentsStats.today }}</td>
        </tr>
        <tr>
          <th class="w-56 px-6 py-4 font-medium text-gray-900 bg-gray-50">Deployments this week</th>
          <td class="px-6 py-4">{{ deploymentsStats.thisWeek }}</td>
        </tr>
        <tr>
          <th class="w-56 px-6 py-4 font-medium text-gray-900 bg-gray-50">Latest duration</th>
          <td class="px-6 py-4">{{ deploymentsStats.latestDuration }}</td>
        </tr>
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
    deployments: Array,
    deploymentsStats: Object,
  },

  data() {
    return {}
  },

  mounted() {
  },

  methods: {
    show(project_id, deployment_id) {
      this.$inertia.visit(this.route('projects.deployments.show', [project_id, deployment_id]))
    },

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
