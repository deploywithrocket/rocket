<template>
  <div class="flex flex-row min-h-screen text-sm bg-white">
    <aside class="flex flex-col flex-shrink-0 w-56 gap-6 p-4 border-r border-gray-100 bg-gray-50">
      <div class="flex flex-row items-center justify-between">
        <Link :href="route('home')"><img src="/logo.svg" alt="Rocket" class="w-6 h-6 rounded-lg" /></Link>

        <button type="button" class="p-2 text-gray-600 rounded-lg hover:bg-gray-200 hover:text-gray-900 focus:outline-none" @click="searchModalVisible = true">
          <Icon icon="ri:search-line" />
        </button>
      </div>

      <nav class="flex flex-col gap-px font-medium text-gray-600">
        <Link :href="route('home')" :class="{ 'bg-gray-200 text-gray-900': route().current( 'home' ), }" class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 hover:text-gray-900"><Icon icon="ri:layout-grid-line" class="opacity-75" /> Overview</Link>
        <Link :href="route('servers.index')" :class="{ 'bg-gray-200 text-gray-900': route().current( 'servers*' ), }" class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 hover:text-gray-900"><Icon icon="ri:server-line" class="opacity-75" /> Servers</Link>
        <Link :href="route('projects.index')" :class="{ 'bg-gray-200 text-gray-900': route().current( 'projects*' ), }" class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 hover:text-gray-900"><Icon icon="ri:folder-2-line" class="opacity-75" /> Projects</Link>
      </nav>

      <div class="my-auto" />

      <div class="flex flex-col gap-px font-medium text-gray-600">
        <Link :href="route('settings')" :class="{ 'bg-gray-200 text-gray-900': route().current( 'settings' ), }" class="flex items-center gap-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 hover:text-gray-900"><Icon icon="ri:settings-4-line" class="opacity-75" /> Settings</Link>
        <Link :href="route('logout')" method="post" as="button" class="flex items-center w-full gap-2 px-3 py-2 rounded-lg cursor-pointer hover:bg-gray-200 hover:text-gray-900"><Icon icon="ri:logout-box-line" class="opacity-75" /> Sign out</Link>
      </div>

      <footer class="-mt-2 text-[0.5rem] text-gray-400">
        &copy; 2020-2023 - <a href="https://deploywithrocket.dev" class="hover:underline">Rocket</a> {{ $page.props.version }}
      </footer>
    </aside>
    <main class="flex-1 px-4 py-4 overflow-x-hidden overflow-y-auto md:py-8 md:px-12">
      <div class="w-full mx-auto max-w-7xl">
        <slot />
      </div>
    </main>

    <MomentumModal />
    <SearchModal v-model:open="searchModalVisible" />
    <Alerts />
  </div>
</template>

<script>
import { Modal as MomentumModal } from 'momentum-modal'

export default {
  components: {
    MomentumModal,
  },

  data() {
    return {
      searchModalVisible: false,
    }
  },

  mounted() {
    this._keyListener = function(e) {
      if (e.key === 'k' && (e.ctrlKey || e.metaKey)) {
        e.preventDefault()
        this.searchModalVisible = !this.searchModalVisible
      }
    }

    document.addEventListener('keydown', this._keyListener.bind(this))
  },

  beforeUnmount() {
    document.removeEventListener('keydown', this._keyListener)
  },
}
</script>
