<template>
  <div class="mb-8">
    <div class="flex flex-row items-center gap-2 mb-4">
      <div class="flex flex-row items-center gap-2">
        <Link :href="deployment ? route('projects.deployments.index', [project]) : route('projects.index')" class="btn btn-tertiary btn-back"><Icon icon="ri:arrow-left-line" /></Link>
        <div>
          <div class="flex flex-row items-center gap-3">
            <h1 class="text-base font-bold">
              <Link :href="route('projects.show', [project])" class="hover:underline">{{ project.name }}</Link>
            </h1>
            <template v-if="deployment">
              <span class="text-xs text-gray-500">/</span>
              <Link :href="route('projects.deployments.show', [project,deployment])" class="text-base hover:underline">#{{ deployment.release }}</Link>
            </template>
          </div>
          <div class="flex flex-row gap-2">
            <a :href="'https://github.com/' + project.repository" target="_blank" class="flex items-center gap-1 text-gray-600 rounded-md h hover:text-gray-900 hover:underline"><Icon icon="fa:git" /> {{ project.repository }}</a>
            <a :href="project.live_url" target="_blank" class="flex items-center gap-1 text-gray-600 rounded-md hover:text-gray-900 hover:underline"><Icon icon="ri:external-link-line" /> {{ project.live_url }}</a>
          </div>
        </div>
      </div>
      <div class="mx-auto" />
      <Link :href="route('projects.deployments.create', project)" class="btn btn-secondary"><Icon icon="ri:add-line" /> Custom deployment</Link>
      <button class="btn btn-primary" @click.prevent="deployNow"><Icon icon="ri:upload-cloud-line" /> Deploy now</button>
    </div>

    <div class="flex gap-1 overflow-scroll font-medium text-gray-600">
      <Link v-for="link in links" :key="link.route" :href="route(link.route, project)" class="flex items-center flex-shrink-0 gap-1 px-2 py-1 text-gray-600 rounded-md hover:bg-gray-100 hover:text-gray-900" :class="{ 'bg-gray-100 text-gray-900': route().current(link.active || link.route) }">
        <Icon :icon="link.icon" />
        <span>{{ link.name }}</span>
      </Link>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    project: {
      type: Object,
      required: true,
    },

    deployment: {
      type: Object,
      required: false,
    },
  },

  data() {
    return {
      links: [
        { name: 'Overview', icon: 'ri:dashboard-line', route: 'projects.show' },
        { name: 'Deployments', icon: 'ri:rocket-2-line', route: 'projects.deployments.index',active: 'projects.deployments*'},
        { name: 'Settings', icon: 'ri:settings-4-line', route: 'projects.edit.common' },
        { name: 'Environment file', icon: 'ri:key-2-line', route: 'projects.edit.env-file' },
        { name: 'Shared', icon: 'ri:folder-transfer-line', route: 'projects.edit.shared' },
        { name: 'Hooks', icon: 'ri:code-s-slash-line', route: 'projects.edit.hooks' },
        { name: 'Cron jobs', icon: 'ri:time-line', route: 'projects.edit.cron-jobs' },
      ],
    }
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
