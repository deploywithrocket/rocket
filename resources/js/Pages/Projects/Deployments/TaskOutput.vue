<template>
  <Head title="Task Output" />

  <Modal dialog-panel-classes="w-full max-w-4xl overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
    <pre class="w-full px-5 py-4 overflow-auto font-mono text-xs text-gray-200 bg-gray-800 rounded-md">{{ output }}</pre>
  </Modal>
</template>

<script>
export default {
  props: {
    output: String,
  },
}
</script>

<style>
pre {
  min-height: 50vh;
}
</style>
