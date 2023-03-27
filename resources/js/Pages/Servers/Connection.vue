<template>
  <Head title="Connection" />

  <Modal>
    <div class="flex flex-col gap-4">
      <div class="flex items-center w-full max-w-sm mx-auto text-gray-500">
        <div class="relative flex items-center justify-center p-4 border rounded-full">
          <Icon icon="ri:server-line" />
          <div v-if="server.status == 'connected'" class="absolute bottom-0 right-0 text-center text-green-500"><Icon icon="ri:checkbox-circle-fill" /></div>
          <div v-if="server.status == 'disconnected'" class="absolute bottom-0 right-0 text-center text-red-500"><Icon icon="ri:question-fill" /></div>
        </div>
        <div class="flex-grow h-1 border-b" />
        <div class="relative flex items-center justify-center p-2">
          <Icon v-if="server.status == 'connected'" icon="ri:lock-2-fill" class="text-yellow-500" />
          <Icon v-if="server.status == 'disconnected'" icon="ri:close-circle-fill" class="text-red-500" />
        </div>
        <div class="flex-grow h-1 border-b" />
        <div class="relative flex items-center justify-center p-4 border rounded-full">
          <Icon icon="ri:rocket-2-fill" />
          <div class="absolute bottom-0 right-0 text-center text-green-500">
            <Icon icon="ri:checkbox-circle-fill" />
          </div>
        </div>
      </div>

      <div class="mb-8 text-center">Current status: <span class="font-bold">{{ server.status }}</span></div>

      <p>
        This key must be added to the server's <code class="font-mono">.ssh/authorized_keys</code> file for each user you want to run tasks as.
      </p>

      <div class="block w-full p-4 font-mono break-all bg-gray-100 border-transparent rounded-md">{{ publicKey }}</div>

      <p>
        Alternatively, you can also directly run this command on your server to allow Rocket to
        connect to the current user.
      </p>

      <div class="block w-full p-4 font-mono break-all bg-gray-100 border-transparent rounded-md">
        curl -sSL {{ route('api.servers.connect', server) }} | sh
      </div>

      <div class="border-t border-gray-100" />
      <div class="flex flex-row justify-between ">
        <span />
        <div class="flex justify-end">
          <form @submit.prevent="form.post(route('servers.connection.test', server))">
            <button type="submit" :disabled="form.processing" class="btn btn-secondary">Test connection status</button>
          </form>
        </div>
      </div>
    </div>
  </Modal>
</template>

<script>
import { useForm } from '@inertiajs/vue3'

export default {
  props: {
    server: Object,
    publicKey: String,
  },

  data(){
    return {
      form: useForm({}),
    }
  },
}
</script>
