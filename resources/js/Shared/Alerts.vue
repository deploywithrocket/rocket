<template>
  <div class="fixed right-4 top-4 w-full space-y-2 max-w-[400px]">
    <div v-for="notification in notifications" :key="notification.id">
      <Transition
        enter-active-class="duration-200 ease-out"
        enter-from-class="transform scale-75 opacity-0"
        enter-to-class="scale-100 opacity-100"
        leave-active-class="duration-200 ease-in"
        leave-from-class="scale-100 opacity-100"
        leave-to-class="transform scale-75 opacity-0"
      >
        <div v-show="notification.show" class="flex p-4 space-x-4 text-sm bg-white border border-gray-200 shadow-lg rounded-xl">
          <div v-if="notification.type == 'success'" class="flex items-center justify-center w-10 h-10 text-xl text-green-500 bg-green-100 rounded-full"><Icon icon="ri:checkbox-circle-fill" /></div>
          <div v-else-if="notification.type == 'error'" class="flex items-center justify-center w-10 h-10 text-xl text-red-500 bg-red-100 rounded-full"><Icon icon="ri:close-circle-fill" /></div>
          <div v-else-if="notification.type == 'warning'" class="flex items-center justify-center w-10 h-10 text-xl text-yellow-500 bg-yellow-100 rounded-full"><Icon icon="ri:alert-fill" /></div>
          <div class="flex-1">
            <h4 v-if="notification.title" class="pr-6 font-medium text-secondary-900">{{ notification.title }}</h4>
            <div class="mt-1 text-secondary-500">{{ notification.text }}</div>
          </div>
        </div>
      </Transition>
    </div>
  </div>
</template>

<script>
import { v4 } from 'uuid'

export default {
  data() {
    return {
      notifications: [],
    }
  },

  watch: {
    $page: {
      handler() { this.handleFlashMessages() },
      deep: true,
    },
  },

  mounted() {
    this.handleFlashMessages()
  },

  methods: {
    handleFlashMessages() {
      if (this.$page.props.flash.success) this.notify({ type: 'success', title: 'Success', text: this.$page.props.flash.success})
      if (this.$page.props.flash.warning) this.notify({ type: 'warning', title: 'Warning', text: this.$page.props.flash.warning })
      if (this.$page.props.flash.error) this.notify({ type: 'error', title: 'Error', text: this.$page.props.flash.error })
    },

    notify(notification) {
      notification.id = v4()
      notification.show = false
      this.notifications.push(notification)

      setTimeout(() => this.notifications.find((n) => n.id === notification.id).show = true, 50)
      setTimeout(() => this.notifications.find((n) => n.id === notification.id).show = false, 5000)
    },
  },
}
</script>
