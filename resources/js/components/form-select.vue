<template>
    <label class="block">
        <span v-if="label" class="block mb-2 text-sm font-bold text-gray-700" v-text="label"></span>

        <select class="w-full px-3 py-3 text-sm leading-tight text-gray-700 bg-gray-100 border rounded appearance-none focus:outline-none focus:shadow-outline"
                :class="{ 'border-red-500 mb-1': errors.length }"
                v-bind="$attrs"
                v-on="{ ...$listeners, input: event => $emit('input', event.target.value) }">
                <option v-for="(option, value) in options" v-bind:key="value" :value="value">{{ option }}</option>
        </select>

        <p v-if="errors.length" class="pl-1 text-xs italic text-red-500" v-text="errors[0]"></p>
    </label>
</template>

<script>
    export default {
        /**
         * Determine if the attributes are passed to the root component.
         *
         * @type {Boolean}
         */
        inheritAttrs: false,

        /**
         * Component properties.
         *
         * @type {Object}
         */
        props: {
            id: String,
            label: String,
            options: Object,
            errors: {
                type: Array,
                default: () => ([]),
            },
        },
    }
</script>