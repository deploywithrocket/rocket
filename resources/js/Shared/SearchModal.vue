<template>
  <TransitionRoot appear as="template" :show="open">
    <Dialog as="div" class="relative z-10" @close="close">
      <TransitionChild as="template" enter="duration-200 ease-out" enter-from="opacity-0" enter-to="opacity-100" leave="duration-200 ease-in" leave-from="opacity-100" leave-to="opacity-0">
        <div class="fixed inset-0 transition-opacity bg-black/75" />
      </TransitionChild>

      <div class="fixed inset-0 overflow-y-auto">
        <div class="flex items-center justify-center min-h-full p-4 text-center">
          <TransitionChild as="template" enter="duration-200 ease-out" enter-from="opacity-0 scale-95" enter-to="opacity-100 scale-100" leave="duration-200 ease-in" leave-from="opacity-100 scale-100" leave-to="opacity-0 scale-95">
            <DialogPanel class="w-full max-w-lg p-4 overflow-hidden text-left align-middle transition-all transform bg-white rounded-lg shadow-xl">
              <FormInput v-model="form.q" placeholder="Search anything..." class="w-full" />
              <WorkInProgress class="mt-4" />
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { useForm } from '@inertiajs/vue3'
import { TransitionRoot, TransitionChild, Dialog, DialogPanel } from '@headlessui/vue'

export default {
  components: {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
  },

  props: {
    open: {
      type: Boolean,
      required: true,
    },
  },

  emits: ['update:open'],

  data() {
    return {
      form: useForm({ q: ''}),
    }
  },

  watch: {
    open(value) {
      if (!value) {
        this.form.reset()
      }
    },
  },

  methods: {
    close() {
      this.$emit('update:open', false)
    },

    submit() {
      // this.$inertia.visit(`/search?q=${this.form.q}`)
    },
  },
}
</script>