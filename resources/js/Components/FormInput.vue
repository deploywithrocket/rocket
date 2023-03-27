<template>
  <div :class="$attrs.class">
    <label v-if="label" :for="id" class="block mb-1 text-sm font-medium text-gray-700" :class="{'after:ml-0.5 after:text-red-500 after:content-[\'*\']': required}" v-text="label" />
    <input :id="id" class="block w-full text-sm rounded-md shadow-sm focus:ring focus:ring-opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500" :type="type" v-bind="{...$attrs, class: null}" :value="modelValue" :class="{ 'border-gray-300 focus:border-primary-400 focus:ring-primary-200': !errors, 'border-red-300 focus:border-red-300 focus:ring-red-200': errors, }" @input="$emit('update:modelValue', $event.target.value)" />

    <p v-if="errors" class="mt-1 -mb-2 text-xs text-red-500 truncate" v-text="errors" />
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

    type: {
      type: String,
      default: 'text',
    },

    errors: {
      type: String,
      default: undefined,
    },

    modelValue: {
      type: String,
      default: undefined,
    },

    required: {
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
    this.id = `text-input-${uuid()}`
  },
}
</script>
