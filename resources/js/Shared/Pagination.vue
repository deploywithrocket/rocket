<template>
  <nav aria-label="Pagination">
    <ul class="inline-flex items-center space-x-1 text-sm rounded-md">
      <li>
        <Link :disabled="!data.prev_page_url" as="button" :href="data.prev_page_url || '#'" class="inline-flex items-center px-2 py-2 space-x-2 font-medium text-gray-500 bg-white border border-gray-300 rounded-full hover:bg-gray-50 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400"><Icon icon="ri:arrow-left-line" /></Link>
      </li>
      <li>
        <span class="inline-flex items-center px-4 py-2 space-x-1 text-gray-500 rounded-md">Page <b class="mx-1">{{ data.current_page }}</b> of <b class="ml-1">{{ data.last_page }}</b></span>
      </li>
      <li>
        <Link :disabled="!data.next_page_url" as="button" :href="data.next_page_url || '#'" class="inline-flex items-center px-2 py-2 space-x-2 font-medium text-gray-500 bg-white border border-gray-300 rounded-full hover:bg-gray-50 disabled:cursor-not-allowed disabled:border-gray-100 disabled:bg-gray-50 disabled:text-gray-400"><Icon icon="ri:arrow-right-line" /></Link>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: {
    data: {
      type: Object,
      required: true,
    },
  },
}
</script>