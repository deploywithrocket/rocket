<template>
  <div :class="$attrs.class">
    <label :for="id" class="relative inline-flex items-center gap-2 cursor-pointer">
      <input :id="id" type="checkbox" class="sr-only peer" :checked="modelValue" @change="$emit('update:modelValue', $event.target.checked)" />
      <div class="h-6 w-11 rounded-full bg-gray-100 after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow after:transition-all after:content-[''] hover:bg-gray-200 peer-checked:bg-primary-600 peer-checked:after:translate-x-full peer-checked:after:border-white peer-focus:ring-4 peer-focus:ring-primary-200 peer-disabled:cursor-not-allowed peer-disabled:bg-gray-100 peer-disabled:after:bg-gray-50" />
      <span v-if="label" class="text-sm font-medium text-gray-700" :class="{'after:ml-0.5 after:text-red-500 after:content-[\'*\']': required}" v-text="label" />
    </label>

    <p v-if="errors" class="mt-1 text-xs text-red-500" v-text="errors" />
  </div>
</template>

<script>
import { v4 as uuid } from 'uuid'

export default {
  inheritAttrs: false,

  props: {
    label: {
      type: String,
      default: undefined,
    },

    errors: {
      type: String,
      default: undefined,
    },

    required: {
      type: Boolean,
      default: false,
    },

    modelValue: {
      type: Boolean,
      default: false,
    },
  },

  emits: ['update:modelValue'],

  data() {
    return {
      id: undefined,
    }
  },

  created() {
    this.id = `checkbox-${uuid()}`
  },

}
</script>
