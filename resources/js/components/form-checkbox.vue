<template>
    <div>
        <label class="inline-flex items-center">
            <input
                type="checkbox"
                class="w-5 h-5 text-pink-600 bg-gray-100 border rounded appearance-none form-checkbox focus:outline-none focus:border-pink-500"
                v-bind="$attrs"
                :checked="value"
                @change="$emit('input', $event.target.checked)"
            />

            <span v-if="label" class="block ml-2 text-sm font-bold text-gray-700" v-text="label"></span>
        </label>

        <p v-if="errors.length" class="pl-1 text-xs italic text-red-500" v-text="errors[0]"></p>
    </div>
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
            value: Boolean,
            errors: {
                type: Array,
                default: () => ([]),
            },
        },
    }
</script>