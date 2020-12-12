<template>
    <label class="block">
        <span v-if="label" class="block mb-2 text-sm font-bold text-gray-700" v-text="label"></span>

        <textarea class="w-full px-3 py-3 text-sm leading-tight text-gray-700 transition duration-200 ease-in-out bg-gray-100 rounded appearance-none focus:outline-none focus:ring-2 focus:ring-opacity-50 focus:ring-pink-500"
                :class="{ 'bg-red-100 mb-1': errors.length, 'font-mono': mono }"
                v-bind="$attrs"
                v-on="{ ...$listeners, input: event => $emit('input', event.target.value) }"></textarea>

        <p v-if="errors.length" class="pl-1 text-xs italic font-semibold text-red-500" v-text="errors[0]"></p>
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
            mono: {
                type: Boolean,
                default: false,
            },
            errors: {
                type: Array,
                default: () => ([]),
            },
        },
    }
</script>