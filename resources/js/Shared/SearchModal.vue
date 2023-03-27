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
              <form @submit.prevent="submit">
                <FormInput v-model="form.q" placeholder="Search anything..." class="w-full">
                  <template #prependIcon>
                    <Icon v-show="!loading" icon="ri:search-line" />
                    <Icon v-show="loading" icon="ri:loader-4-line" />
                  </template>
                </FormInput>
              </form>

              <div v-if="Object.keys(results).length" class="mt-4 divide-y divide-gray-200 card">
                <div v-for="(result, key) in results" :key="key" class="flex gap-4 card-body">
                  <div class="flex items-center justify-center w-10 h-10 text-xl rounded-full text-primary-500 bg-primary-100">
                    <Icon v-if="key == 'projects'" icon="ri:folder-2-fill" />
                    <Icon v-if="key == 'servers'" icon="ri:server-fill" />
                    <Icon v-if="key == 'deployments'" icon="ri:rocket-2-fill" />
                  </div>

                  <div class="text-sm">
                    <h3 class="mb-2 font-medium capitalize text-secondary-900" v-text.="key" />

                    <ul class="space-y-1">
                      <li v-for="item in result" :key="item.id">
                        <Link :href="item.url" class="text-gray-600 hover:text-gray-900 hover:underline" @click="close">{{ item.title }}</Link>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </DialogPanel>
          </TransitionChild>
        </div>
      </div>
    </Dialog>
  </TransitionRoot>
</template>

<script>
import { TransitionRoot, TransitionChild, Dialog, DialogPanel } from '@headlessui/vue'
import axios from 'axios'

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
      loading: false,
      debounce: null,
      form: { q: '' },
      results: {},
    }
  },

  watch: {
    open(value) {
      if (value) {
        this.form.q = ''
        this.results = {}
      }
    },

    form: {
      handler() {
        clearTimeout(this.debounce)

        this.debounce = setTimeout(() => {
          if (this.form.q.length > 2) {
            this.submit()
          }
        }, 500)
      },

      deep: true,
    },
  },

  methods: {
    close() {
      this.$emit('update:open', false)
    },

    submit() {
      this.loading = true
      clearTimeout(this.debounce)

      axios.post('/search', this.form)
        .then(response => { this.results = response.data })
        .catch(error => { console.log(error.response.data) })
        .finally(() => { this.loading = false })
    },
  },
}
</script>