<template>
  <div class="fixed right-4 top-4 w-full space-y-2 max-w-[400px]">
    <div v-for="notification in notifications" :key="notification.id" class="p-4 text-sm bg-white border border-gray-200 shadow-lg rounded-xl">
      <div class="flex space-x-4">
        <div v-if="notification.type == 'success'" class="flex items-center justify-center w-10 h-10 text-xl text-green-500 bg-green-100 rounded-full"><Icon icon="ri:checkbox-circle-fill" /></div>
        <div v-else-if="notification.type == 'error'" class="flex items-center justify-center w-10 h-10 text-xl text-red-500 bg-red-100 rounded-full"><Icon icon="ri:close-circle-fill" /></div>
        <div v-else-if="notification.type == 'warning'" class="flex items-center justify-center w-10 h-10 text-xl text-yellow-500 bg-yellow-100 rounded-full"><Icon icon="ri:alert-fill" /></div>
        <div class="flex-1">
          <h4 v-if="notification.title" class="pr-6 font-medium text-secondary-900">{{ notification.title }}</h4>
          <div class="mt-1 text-secondary-500">{{ notification.text }}</div>
        </div>
      </div>
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
      this.notifications.push(notification)
      setTimeout(() => this.removeNotification(notification.id), 5000)
    },

    removeNotification(notificationId) {
      this.notifications = this.notifications.filter((notification) => notification.id !== notificationId)
    },
  },
}
</script>
