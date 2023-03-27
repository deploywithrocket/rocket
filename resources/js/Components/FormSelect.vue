<template>
  <div :class="$attrs.class">
    <label v-if="label" :for="id" class="block mb-1 text-sm font-medium text-gray-700" :class="{'after:ml-0.5 after:text-red-500 after:content-[\'*\']': required}" v-text="label" />
    <select :id="id" v-bind="{...$attrs, class: null}" v-model="selected" class="block w-full text-sm rounded-md shadow-sm focus:ring focus:ring-opacity-50 disabled:cursor-not-allowed disabled:bg-gray-50 disabled:text-gray-500" :class="{ 'border-gray-300 focus:border-primary-400 focus:ring-primary-200': !errors, 'border-red-300 focus:border-red-300 focus:ring-red-200': errors, }">
      <option v-for="(label, value) in options" :key="value" :value="value">{{ label }}</option>
    </select>

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

    modelValue: {
      type: String,
      default: undefined,
    },

    required: {
      type: Boolean,
      default: false,
    },

    options: {
      type: Object,
      default: () => ({}),
    },
  },

  emits: ['update:modelValue'],

  data() {
    return {
      id: undefined,
      selected: this.modelValue,
    }
  },

  watch: {
    selected(selected) {
      this.$emit('update:modelValue', selected)
    },
  },

  created() {
    this.id = `select-input-${uuid()}`
  },
}
</script>