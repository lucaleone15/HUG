<script setup>
defineOptions({ inheritAttrs: false })

defineProps({
    modelValue: { type: Boolean, default: false },
    label:      { type: String, default: null },
    color:      { type: String, default: null },
})

defineEmits(['update:modelValue'])
</script>

<template>
    <label class="label gap-2 cursor-pointer justify-start">
        <input
            v-bind="$attrs"
            type="checkbox"
            :checked="modelValue"
            class="checkbox checkbox-sm"
            :class="color ? `checkbox-${color}` : ''"
            @change="$emit('update:modelValue', $event.target.checked)"
        />
        <span class="label-text">
            <slot>{{ label }}</slot>
        </span>
    </label>
</template>
