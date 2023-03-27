<template>
  <Head title="Servers" />

  <div class="flex flex-row items-center mb-8">
    <div>
      <h1 class="text-base font-bold">Servers</h1>
      <p class="text-sm text-gray-500">Set targets that will host your projects.</p>
    </div>
    <div class="mx-auto" />
    <Link :href="route('servers.create')" class="btn btn-primary">
      <Icon icon="ri:add-line" /> Add
    </Link>
  </div>

  <template v-if="servers.total">
    <div class="divide-y divide-gray-200 card">
      <div v-for="server in servers.data" :key="server.id" class="flex flex-row items-center card-body">
        <div class="flex-1 space-y-2">
          <div class="font-medium text-gray-900">{{ server.name }}</div>
          <div class="space-x-1">
            <span class="px-2 py-1 text-xs font-semibold text-gray-600 bg-gray-100 rounded-full">{{ server.ssh_user }}@{{ server.ssh_host }}:{{ server.ssh_port }}</span>
            <span v-if="server.status == 'connected'" class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-green-600 rounded-full bg-green-50">
              <Icon icon="ri:checkbox-circle-fill" /> Connected
            </span>
            <span v-else class="inline-flex items-center gap-1 px-2 py-1 text-xs font-semibold text-red-600 rounded-full bg-red-50">
              <Icon icon="ri:close-circle-fill" />Disconnected
            </span>
          </div>
        </div>
        <div class="flex justify-end flex-shrink-0 gap-1">
          <Link class="btn btn-secondary btn-square" :href="route('servers.connection', server)"><Icon icon="ri:link" /></Link>
          <Link class="btn btn-secondary btn-square" :href="route('servers.edit', server)"><Icon icon="ri:edit-line" /></Link>
        </div>
      </div>
    </div>
    <div class="flex justify-center mt-8">
      <Pagination :data="servers" />
    </div>
  </template>
  <template v-else>
    <div class="border-dashed card">
      <div class="card-body">
        <div class="relative p-5">
          <div class="text-center">
            <div class="flex items-center justify-center w-16 h-16 mx-auto mb-5 text-3xl rounded-full text-primary-500 bg-primary-100">
              <Icon icon="ri:server-fill" />
            </div>
            <div>
              <h3 class="text-lg font-medium text-secondary-900">Servers</h3>
              <div class="mt-2 text-sm text-secondary-500">
                Servers are the targets that will host the projects you deploy with Rocket.<br />
                You can add as many servers as you want.
              </div>
              <Link :href="route('servers.create')" class="w-24 mx-auto mt-6 btn btn-primary">
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
    servers: Object,
  },
}
</script>
